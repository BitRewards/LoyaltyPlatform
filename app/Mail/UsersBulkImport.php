<?php

namespace App\Mail;

use App\Administrator;
use App\Mail\Base\AdministratorNotification;
use App\DTO\UsersBulkImport\ImportReport;

class UsersBulkImport extends AdministratorNotification
{
    private $report;

    /**
     * @param Administrator $administrator
     * @param ImportReport  $report
     */
    public function __construct(Administrator $administrator, ImportReport $report)
    {
        parent::__construct($administrator);
        $this->report = $report;
    }

    protected function getTemplateName(): string
    {
        return 'emails.users-bulk-import';
    }

    protected function getTemplateVariables(): array
    {
        return [
            'report' => $this->report,
        ];
    }

    protected function getSubject(): string
    {
        return __('Successfully processed entries: %count%', $this->report->total);
    }
}
