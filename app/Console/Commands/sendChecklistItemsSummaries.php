<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendCheckedItemSummary;

class sendChecklistItemsSummaries extends Command
{
    protected $signature = 'team:send-checklist-items-summaries';
    protected $description = 'Queue checklist items summaries';

    public function handle()
    {
        SendCheckedItemSummary::dispatch();
        $this->info("Queue checklist items summaries - Done.");
    }
}
