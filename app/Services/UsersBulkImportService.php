<?php

namespace App\Services;

use App\DTO\CustomBonusData;
use App\DTO\UsersBulkImport\BulkUser;
use App\DTO\UsersBulkImport\ColumnMatching;
use App\DTO\UsersBulkImport\ImportReport;
use App\DTO\CredentialData;
use App\Models\UsersBulkImport;
use App\Models\UsersBulkImportRow;
use App\Jobs\UsersBulkImport as UsersBulkImportJob;
use App\Models\User;
use App\Models\Partner;
use App\Enums\UsersBulkImport\ImportMode;
use Carbon\Carbon;

class UsersBulkImportService
{
    const PREVIEW_USERS_COUNT = 10;

    /**
     * @param string|array $data
     * @param string       $title
     * @param string       $mode
     *
     * @return UsersBulkImport|null
     */
    public function prepareImport($data, $title, $mode, $partner)
    {
        \DB::beginTransaction();

        $success = true;

        try {
            $rows = is_array($data) ? \HStr::convertToBulkRowFormat($data[0]) : \HStr::splitByNewLine($data);

            $bulkImport = new UsersBulkImport();
            $bulkImport->count = count($rows);
            $bulkImport->title = $title;
            $bulkImport->mode = $mode;
            $bulkImport->partner_id = $partner->id;
            $bulkImport->save();
            $bulkImportId = $bulkImport->id;

            foreach ($rows as $row) {
                $bulkRow = new UsersBulkImportRow();
                $bulkRow->users_bulk_import_id = $bulkImportId;
                $bulkRow->data = $row;
                $bulkRow->save();
            }
        } catch (\Exception $exception) {
            $success = false;
            \DB::rollBack();
            \Log::error($exception->getMessage(), compact('exception'));
        }

        if ($success) {
            \DB::commit();

            return $bulkImport;
        }

        return null;
    }

    /**
     * @param UsersBulkImport $usersBulkImport
     * @param bool            $immediately
     *
     * @return ImportReport|null
     */
    public function import(UsersBulkImport $usersBulkImport, $immediately = false)
    {
        if ($immediately) {
            $rows = $usersBulkImport->usersBulkImportRows()->whereNull('processed_at')->get();
            $report = new ImportReport();

            if (!$rows->isEmpty()) {
                $columns = \HStr::splitByTab($rows[0]->data);
                $matching = new ColumnMatching($columns);

                foreach ($rows as $row) {
                    \DB::beginTransaction();

                    $success = true;

                    try {
                        $result = $this->processRow($row, $matching, $usersBulkImport->mode);
                    } catch (\Exception $exception) {
                        $success = false;
                        \DB::rollBack();
                        \Log::error($exception->getMessage(), compact('exception'));
                    }

                    if ($success) {
                        $report->update($result);
                        \DB::commit();
                    }
                }
            }

            return $report;
        } else {
            dispatch(new UsersBulkImportJob($usersBulkImport->id));
        }
    }

    /**
     * @param BulkUser $bulkUser
     * @param Partner  $partner
     * @param string   $mode
     *
     * @return string
     */
    public function importBulkUser(BulkUser $bulkUser, Partner $partner, $mode)
    {
        $user = User::model()->findByPartnerAndEmailOrPhone($partner, $bulkUser->email, $bulkUser->phone);

        if ($user) {
            if (ImportMode::CREATE_NEW_SKIP_EXISTING == $mode) {
                $result = ImportReport::SKIPPED;
            } else {
                $result = ImportReport::UPDATED;
            }
        } else {
            if (ImportMode::SKIP_NEW_UPDATE_EXISTING == $mode) {
                $result = ImportReport::SKIPPED;
            } else {
                $result = ImportReport::CREATED;

                $data = CredentialData::make([
                    'email' => $bulkUser->email,
                    'phone' => $bulkUser->phone,
                    'name' => $bulkUser->name,
                    'password' => null,
                    'signup_type' => User::SIGNUP_TYPE_GIVE_BONUS,
                ]);
                $user = app(UserService::class)->createClient($data, $partner);
            }
        }

        if (ImportReport::SKIPPED != $result) {
            app(UserService::class)->giveCustomBonusToUser(
                new CustomBonusData($user, $bulkUser->balance, \Auth::user())
            );
        }

        return $result;
    }

    /**
     * @param Partner        $partner
     * @param string         $row
     * @param ColumnMatching $matching
     *
     * @return BulkUser
     */
    private function getBulkUser(Partner $partner, $row, ColumnMatching $matching)
    {
        $columns = is_array($row) ? $row : \HStr::splitByTab($row);
        $user = new BulkUser();

        foreach ($matching->toArray() as $key => $value) {
            if (ColumnMatching::NOT_DETECTED != $value) {
                $user->$key = $columns[$value];
            }
        }

        if ($user->phone) {
            $user->phone = \HUser::normalizePhone($user->phone, $partner->default_country);
        }

        if ($user->email) {
            $user->email = \HUser::normalizeEmail($user->email);
        }

        return $user;
    }

    /**
     * @param Partner        $partner
     * @param string         $data
     * @param ColumnMatching $matching
     * @param int|null       $limit
     *
     * @return array
     */
    public function getBulkUsers($partner, $data, $limit = null)
    {
        $bulkUsers = [];
        $rows = \HStr::splitByNewLine($data);

        if ($rows) {
            $columns = \HStr::splitByTab($rows[0]);

            $matching = new ColumnMatching($columns);
            $count = 0;

            foreach ($rows as $row) {
                if ($limit && (++$count > $limit)) {
                    break;
                }

                $bulkUsers[] = $this->getBulkUser($partner, $row, $matching);
            }
        }

        return $bulkUsers;
    }

    /**
     * @param Partner  $partner
     * @param array    $array
     * @param int|null $limit
     *
     * @return array
     */
    public function getBulkUsersFromArray($partner, array $array, $limit = null)
    {
        $bulkUsers = [];

        $matching = new ColumnMatching($array[0][0]);
        $count = 0;

        foreach ($array[0] as $row) {
            if ($limit && (++$count > $limit)) {
                break;
            }

            $bulkUsers[] = $this->getBulkUser($partner, $row, $matching);
        }

        return $bulkUsers;
    }

    /**
     * @param UsersBulkImportRow $row
     * @param ColumnMatching     $matching
     * @param $mode
     *
     * @return string
     */
    public function processRow(UsersBulkImportRow $row, ColumnMatching $matching, $mode)
    {
        $partner = $row->usersBulkImport->partner;
        $bulkUser = $this->getBulkUser($partner, $row->data, $matching);
        $result = $this->importBulkUser($bulkUser, $partner, $mode);
        $this->finishProcessRow($row, $result);

        return $result;
    }

    /**
     * @param UsersBulkImportRow $row
     * @param string             $result
     */
    private function finishProcessRow(UsersBulkImportRow $row, $result)
    {
        switch ($result) {
            case ImportReport::SKIPPED:
                $row->is_skipped = true;

                break;

            case ImportReport::CREATED:
                $row->is_new_user = true;

                break;

            case ImportReport::UPDATED:
                $row->is_existing_user = true;

                break;
        }

        $row->processed_at = Carbon::now();
        $row->save();
    }
}
