<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\UsersBulkImportService;
use App\Models\UsersBulkImport as BulkImport;
use App\Mail\UsersBulkImport as UsersBulkImportMail;

class UsersBulkImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $usersBulkImportId;

    public function __construct($usersBulkImportId)
    {
        $this->usersBulkImportId = $usersBulkImportId;
    }

    public function handle()
    {
        $bulkImport = BulkImport::find($this->usersBulkImportId);
        $report = app(UsersBulkImportService::class)->import($bulkImport, true);

        if ($report && $report->total) {
            \Mail::queue(new UsersBulkImportMail($bulkImport->partner->mainAdministrator, $report));
        }
    }
}
