<?php

namespace App\Console\Commands;

use App\Listeners\SendQueueBusyAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

class MonitorQueueDepthCommand extends Command
{
    protected $signature = 'queue:monitor-depth {--connection=database} {--queue=default}';

    protected $description = 'Run the queue-size monitor with the threshold configured in settings';

    public function handle(): int
    {
        $connection = (string) $this->option('connection');
        $queue = (string) $this->option('queue');
        $threshold = max(1, (int) setting('notification', 'queue_depth_threshold', 100));
        $size = (int) Queue::connection($connection)->size($queue);

        if ($size < $threshold) {
            // The alert's cooldown should not outlive the busy period, otherwise
            // a fresh incident can be muted by the previous one.
            Cache::forget(SendQueueBusyAlert::cooldownKey($connection, $queue));
            $this->components->info("Queue {$queue} is below the threshold; alert cooldown cleared.");
        }

        $this->components->info("Checking queue depth against a threshold of {$threshold}.");

        return $this->call('queue:monitor', [
            'queues' => $connection.':'.$queue,
            '--max' => $threshold,
        ]);
    }
}
