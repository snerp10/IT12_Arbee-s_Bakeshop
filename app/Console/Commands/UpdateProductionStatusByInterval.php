<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Production;

class UpdateProductionStatusByInterval extends Command
{
    protected $signature = 'production:update-status-interval';
    protected $description = 'Update production batch statuses based on defined time intervals';

    public function handle()
    {
        $updated = 0;
        Production::whereIn('status', [Production::STATUS_PENDING, Production::STATUS_IN_PROGRESS])
            ->get()
            ->each(function ($batch) use (&$updated) {
                $nextStatus = $batch->getNextStatusByInterval();
                if ($nextStatus) {
                    $batch->status = $nextStatus;
                    $batch->save();
                    $updated++;
                    $this->info("Batch #{$batch->batch_number} status updated to {$nextStatus}");
                }
            });
        $this->info("Total batches updated: $updated");
        return 0;
    }
}
