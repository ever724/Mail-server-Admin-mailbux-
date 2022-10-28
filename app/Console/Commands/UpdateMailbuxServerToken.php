<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use App\Services\MailbuxService;
use Illuminate\Console\Command;

class UpdateMailbuxServerToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbux:server:token:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Mailbux Server Token';

    /**
     * @var MailbuxService
     */
    private $mailbuxService;

    /**
     * Create a new command instance.
     */
    public function __construct(MailbuxService $mailbuxService)
    {
        parent::__construct();

        $this->mailbuxService = $mailbuxService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activeAutoRefresh = SystemSetting::getSetting('mailbux_active');

        if ($activeAutoRefresh) {
            $this->mailbuxService->obtainAuth();
        }
    }
}
