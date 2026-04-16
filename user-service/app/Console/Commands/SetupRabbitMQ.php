<?php

namespace App\Console\Commands;

use App\Services\RabbitMQService;
use Illuminate\Console\Command;


class SetupRabbitMQ extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'rabbitmq:setup';
    protected $description = 'Setup RabbitMQ exchanges and queues';

    public function handle()
    {
        //
        app(RabbitMQService::class)->setup();
        $this->info('RabbitMQ setup completed successfully.');
    }
}
