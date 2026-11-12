<?php

namespace App\Console\Commands;

use App\Listeners\SendQueueBusyAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class MonitorQueueDepthCommand extends Command
{
    protected $signature = 'queue:monitor-depth {--connection=} {--queue=}';

    protected $description = 'Monitor configured queues against the threshold in settings';

    public function handle(): int
    {
        $targets = $this->targets();

        if ($targets === []) {
            return self::FAILURE;
        }

        $threshold = max(1, (int) setting('notification', 'queue_depth_threshold', 100));
        $exitCode = self::SUCCESS;

        foreach ($targets as [$connection, $queue]) {
            $size = (int) Queue::connection($connection)->size($queue);

            if ($size < $threshold) {
                // The alert's cooldown should not outlive the busy period, otherwise
                // a fresh incident can be muted by the previous one.
                Cache::forget(SendQueueBusyAlert::cooldownKey($connection, $queue));
                $this->components->info(
                    "Queue {$connection}:{$queue} is below the threshold; alert cooldown cleared."
                );
            }

            $this->components->info(
                "Checking {$connection}:{$queue} against a threshold of {$threshold}."
            );

            $result = $this->call('queue:monitor', [
                'queues' => $connection.':'.$queue,
                '--max' => $threshold,
            ]);

            if ($result !== self::SUCCESS) {
                $exitCode = $result;
            }
        }

        return $exitCode;
    }

    /**
     * @return array<int, array{0: string, 1: string}>
     */
    private function targets(): array
    {
        $connection = trim((string) $this->option('connection'));
        $queue = trim((string) $this->option('queue'));

        if ($connection !== '' || $queue !== '') {
            if ($connection === '' || $queue === '') {
                $this->components->error('Provide both --connection and --queue, or neither.');

                return [];
            }

            return [[$connection, $queue]];
        }

        $configured = (string) setting('notification', 'queue_monitor_queues', 'database:default');
        $targets = [];

        foreach (explode(',', $configured) as $entry) {
            $entry = trim($entry);

            if ($entry === '') {
                continue;
            }

            $parts = array_map('trim', explode(':', $entry, 2));

            if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
                $this->components->error("Invalid monitored queue \"{$entry}\"; use connection:queue.");

                return [];
            }

            $targets[$parts[0].':'.$parts[1]] = $parts;
        }

        if ($targets === []) {
            $this->components->error('No monitored queues are configured.');

            return [];
        }

        return array_values($targets);
    }
}
