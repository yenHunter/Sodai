<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MonitorQueueDepthCommand extends Command
{
    protected $signature = 'queue:monitor-depth {--connection=database} {--queue=default}';

    protected $description = 'Run the queue-size monitor with the threshold configured in settings';

    public function handle(): int
    {
        $threshold = max(1, (int) setting('notification', 'queue_depth_threshold', 100));

        $this->components->info("Checking queue depth against a threshold of {$threshold}.");

        return $this->call('queue:monitor', [
            'queues' => $this->option('connection').':'.$this->option('queue'),
            '--max' => $threshold,
        ]);
    }
}
