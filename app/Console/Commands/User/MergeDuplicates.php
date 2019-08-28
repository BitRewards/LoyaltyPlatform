<?php

namespace App\Console\Commands\User;

use App\Models\Partner;
use Illuminate\Console\Command;
use App\Services\UserService;
use App\Models\User;

class MergeDuplicates extends Command
{
    protected $signature = 'user:mergeDuplicates {mode=test}';

    protected $description = 'Merge duplicate users by partner\'s primary auth method';

    /**
     * Execute the console command.
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $this->_populateNormalizedColumns();

        $isProductionMode = 'prod' == $this->argument('mode');

        if ($isProductionMode) {
            if (!$this->confirm('Are you sure to run in production mode? Running this command may cause critical issues?')) {
                exit(1);
            }
        }

        $this->_mergeByPrimaryAuthMethod($isProductionMode);
    }

    private function _populateNormalizedColumns()
    {
        User::orderBy('id')->chunk(500, function ($users) {
            foreach ($users as $user) {
                /* @var User $user */
                $user->email_normalized = isset($user->email) && !empty($user->email) ? \HUser::normalizeEmail($user->email) : null;
                $user->phone_normalized = isset($user->phone) ? \HUser::normalizePhone($user->phone, $user->partner->default_country) : null;
                $user->save();
            }
        });
    }

    /**
     * @param $isProductionMode
     *
     * @throws \Throwable
     */
    private function _mergeByPrimaryAuthMethod($isProductionMode)
    {
        \DB::transaction(function () use ($isProductionMode) {
            $partners = Partner::orderBy('id')->get();

            foreach ($partners as $partner) {
                /* @var Partner $partner */
                $primaryAuthMethod = $partner->getAuthMethod();

                if (Partner::AUTH_METHOD_PHONE === $primaryAuthMethod) {
                    $this->_mergeByIntlPhone($partner, $isProductionMode);
                } else {
                    $this->_mergeByNormalizedEmail($partner, $isProductionMode);
                }
            }
        });
    }

    /**
     * @param $partner
     * @param $isProductionMode
     *
     * @throws \Throwable
     */
    private function _mergeByIntlPhone($partner, $isProductionMode)
    {
        return $this->_mergeByField(
            $partner,
            $isProductionMode,
            'phone',
            'phone_normalized',
            'phone_confirmed_at',
            'email',
            'email_normalized');
    }

    /**
     * @param $partner
     * @param $isProductionMode
     *
     * @throws \Throwable
     */
    private function _mergeByNormalizedEmail($partner, $isProductionMode)
    {
        return $this->_mergeByField(
            $partner,
            $isProductionMode,
            'email',
            'email_normalized',
            'email_confirmed_at',
            'phone',
            'phone_normalized');
    }

    /**
     * @param $partner
     * @param $isProductionMode
     * @param $primaryField
     * @param $primaryFieldNormalized
     * @param $secondaryField
     * @param $secondaryFieldNormalized
     *
     * @throws \Throwable
     */
    private function _mergeByField(
        $partner, $isProductionMode, $primaryField, $primaryFieldNormalized, $primaryFieldConfirmedColumnName,
        $secondaryField, $secondaryFieldNormalized)
    {
        \DB::transaction(function () use (
            $partner, $isProductionMode, $primaryField, $primaryFieldNormalized, $primaryFieldConfirmedColumnName,
            $secondaryField, $secondaryFieldNormalized) {
            $mergedUsersIds = [];
            $secondaryFieldDuplicates = [];
            User::whereNotNull($primaryField)
                ->whereRaw("COALESCE({$primaryField}, '') != ''")
                ->whereRaw("COALESCE({$primaryFieldNormalized}, '') != ''")
                ->where('partner_id', $partner->id)
                ->orderBy('id', 'asc')->chunk(500,
                    function ($users) use (&$mergedUsersIds, $isProductionMode, $primaryField, $primaryFieldNormalized, $primaryFieldConfirmedColumnName, $secondaryField, $secondaryFieldNormalized, &$secondaryFieldDuplicates) {
                        foreach ($users as $user) {
                            /* @var User $user */
                            if (!$user->exists() || in_array($user->id, $mergedUsersIds)) {
                                // maybe this user was merged in process already
                                continue;
                            }

                            $dupesQuery = User::where('partner_id', $user->partner_id)
                                ->where($primaryFieldNormalized, $user->{$primaryFieldNormalized});

                            $cnt = $dupesQuery->count();

                            $mainUser = $user;

                            if ($cnt > 1) {
                                $this->info(sprintf("%d duplicate rows for user #%d with {$primaryFieldNormalized} %s", $cnt - 1, $user->id, $user->{$primaryFieldNormalized}));

                                $phoneQuery = clone $dupesQuery;
                                $userWithConfirmedAuth = $phoneQuery->whereNotNull($primaryFieldConfirmedColumnName)->first();
                                $mainUser = $userWithConfirmedAuth ?? $user;

                                $otherUsers = $dupesQuery->where('id', '!=', $mainUser->id)->get();

                                $this->info(sprintf('MainUser #%d. Merging other %d users', $mainUser->id, $otherUsers->count()));
                                $this->info("Main user (#{$mainUser->id}): ");
                                $this->info(json_encode($mainUser->getAttributes(), JSON_PRETTY_PRINT));

                                $mergedUsersIds[] = $mainUser->id;

                                foreach ($otherUsers as $secondUser) {
                                    /* @var User $secondUser */
                                    $this->info("Other user (#{$secondUser->id}): ");
                                    $this->info(json_encode($secondUser->getAttributes(), JSON_PRETTY_PRINT));
                                    $this->info(json_encode($secondUser->transactions->pluck('id')));

                                    $mergedUsersIds[] = $secondUser->id;

                                    if ($isProductionMode) {
                                        $secondSecondaryField = $secondUser->{$secondaryField};
                                        $secondSecondaryFieldNormalized = $secondUser->{$secondaryFieldNormalized};

                                        app(UserService::class)->merge($mainUser, $secondUser, true, false);

                                        if (!isset($mainUser->{$secondaryField}) && isset($secondSecondaryField)) {
                                            $mainUser->{$secondaryField} = $secondSecondaryField;
                                        }

                                        if (!isset($mainUser->{$secondaryFieldNormalized}) && isset($secondSecondaryFieldNormalized)) {
                                            $mainUser->{$secondaryFieldNormalized} = $secondSecondaryFieldNormalized;
                                        }

                                        $mainUser->save();
                                    }
                                }
                            }

                            if (0 !== strcmp($mainUser->{$primaryField}, $mainUser->{$primaryFieldNormalized})) {
                                $this->info(sprintf("Replacing {$primaryField} for user #%d with {$primaryField} %s (old one %s)", $mainUser->id, $mainUser->{$primaryFieldNormalized}, $mainUser->{$primaryField}));

                                if ($isProductionMode) {
                                    $mainUser->{$primaryField} = $mainUser->{$primaryFieldNormalized};
                                    $mainUser->save();
                                }
                            }

                            // verify that we don't violate unique constraint by secondary field
                            if (isset($mainUser->{$secondaryFieldNormalized})) {
                                // find conflicts
                                $usersWithSameSecondaryField = User::where('partner_id', $mainUser->partner_id)
                                    ->where($secondaryFieldNormalized, $mainUser->{$secondaryFieldNormalized})
                                    ->where('id', '!=', $mainUser->id)
                                    ->get();

                                if ($usersWithSameSecondaryField->count()) {
                                    // populate dupes for future
                                    $key = $mainUser->{$secondaryFieldNormalized};

                                    if (!isset($secondaryFieldDuplicates[$key])) {
                                        $secondaryFieldDuplicates[$key] = [];
                                    }

                                    $secondaryFieldDuplicates[$key][] = $mainUser;

                                    foreach ($usersWithSameSecondaryField as $possibleConflictedUser) {
                                        if ($possibleConflictedUser->exists() && !in_array($possibleConflictedUser->id,
                                                $mergedUsersIds)) {
                                            $secondaryFieldDuplicates[$key][] = $possibleConflictedUser;
                                            /*$this->info(sprintf("Merge error. Duplicate {$secondaryField}: %s",
                                                $mainUser->{$secondaryFieldNormalized}));*/
                                        }
                                    }
                                } else {
                                    if (0 !== strcmp($mainUser->{$secondaryField}, $mainUser->{$secondaryFieldNormalized})) {
                                        $this->info(sprintf("Replacing {$secondaryField} for user #%d with {$secondaryField} %s (old one %s)", $mainUser->id, $mainUser->{$secondaryFieldNormalized}, $mainUser->{$secondaryField}));

                                        if ($isProductionMode) {
                                            $mainUser->{$secondaryField} = $mainUser->{$secondaryFieldNormalized};
                                            $mainUser->save();
                                        }
                                    }
                                }
                            }
                        }
                    });

            if (!empty($secondaryFieldDuplicates)) {
                $this->info(sprintf('Partner #%d. Cleaning secondary duplicates...', $partner->id));
            }

            foreach ($secondaryFieldDuplicates as $value => $rows) {
                $usersWithoutPrimary = User::whereNotNull($secondaryFieldNormalized)
                    ->where($secondaryFieldNormalized, $value)
                    ->where('partner_id', $partner->id)
                    ->whereNotIn('id', $mergedUsersIds)
                    ->whereRaw("COALESCE({$primaryFieldNormalized}, '') = ''")
                    ->orderBy('created_at', 'asc')->get();

                $usersWithPrimary = User::whereNotNull($secondaryFieldNormalized)
                    ->where($secondaryFieldNormalized, $value)
                    ->where('partner_id', $partner->id)
                    ->whereNotIn('id', $mergedUsersIds)
                    ->whereRaw("COALESCE({$primaryFieldNormalized}, '') != ''")
                    ->orderBy('created_at', 'asc')->get();

                if (1 === $usersWithPrimary->count() && $usersWithoutPrimary->count()) {
                    // merge users without primary
                    $mainUser = $usersWithPrimary->first();

                    $mergedUsersIds[] = $mainUser->id;

                    foreach ($usersWithoutPrimary as $secondUser) {
                        /* @var User $secondUser */
                        $this->info("Merge user #{$secondUser->id} to #{$mainUser->id}: ");
                        $this->info(json_encode($secondUser->getAttributes(), JSON_PRETTY_PRINT));
                        $this->info(json_encode($secondUser->transactions->pluck('id')));

                        if ($isProductionMode) {
//                            app(UserService::class)->merge($mainUser, $secondUser, true, false);
                            $secondSecondaryField = $secondUser->{$secondaryField};
                            $secondSecondaryFieldNormalized = $secondUser->{$secondaryFieldNormalized};

                            app(UserService::class)->merge($mainUser, $secondUser, true, false);

                            if (!isset($mainUser->{$secondaryField}) && isset($secondSecondaryField)) {
                                $mainUser->{$secondaryField} = $secondSecondaryField;
                            }

                            if (!isset($mainUser->{$secondaryFieldNormalized}) && isset($secondSecondaryFieldNormalized)) {
                                $mainUser->{$secondaryFieldNormalized} = $secondSecondaryFieldNormalized;
                            }

                            $mainUser->save();
                        }
                        $mergedUsersIds[] = $secondUser->id;
                    }

                    if ($isProductionMode) {
                        $mainUser->{$secondaryField} = $mainUser->{$secondaryFieldNormalized};
                        $mainUser->save();
                    }
                } elseif ($usersWithPrimary->count() > 1 && $usersWithoutPrimary->count()) {
                    $this->info(sprintf("Merge error! Duplicate {$secondaryFieldNormalized} '%s'", $value));
                } elseif ($usersWithPrimary->count() > 0 && 0 === $usersWithoutPrimary->count()) {
                    $mainUser = $usersWithPrimary->shift();

                    $mergedUsersIds[] = $mainUser->id;

                    foreach ($usersWithPrimary as $user) {
                        $this->info(sprintf('Merge issue! Cleaning data for user #%d', $user->id));
                        $this->info(json_encode($user->getAttributes(), JSON_PRETTY_PRINT));
                        $this->info(sprintf("Remove {$secondaryField} '%s'", $user->{$secondaryField}));

                        if ($isProductionMode) {
                            $user->{$secondaryField} = null;
                            $user->save();
                        }

                        $mergedUsersIds[] = $user->id;
                    }

                    if ($isProductionMode) {
                        $mainUser->{$secondaryField} = $mainUser->{$secondaryFieldNormalized};
                        $mainUser->save();
                    }
                } else {
                    // $this->info(sprintf("Merge error! Wrong condition. {$secondaryFieldNormalized} '%s'", $value));
                }
            }
        });
    }
}
