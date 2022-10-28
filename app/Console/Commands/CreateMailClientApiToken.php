<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class CreateMailClientApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbux:token:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate token for client';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $apiToken = Uuid::uuid4()->toString();

        SystemSetting::setSetting('client_api_key', $apiToken);

        $this->info("New api token generated: {$apiToken}");
    }
}
