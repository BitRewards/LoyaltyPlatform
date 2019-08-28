<?php

namespace App\Console\Commands\Partners;

use App\Models\PartnerGroup;
use App\Services\Persons\PartnerGroupMerger;
use Illuminate\Console\Command;

class AddPartnerToGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partner:merge-groups {master_group_id} {slave_group_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges two partner groups into one';

    /**
     * @var PartnerGroupMerger
     */
    private $partnerGroupMerger;

    /**
     * Create a new command instance.
     */
    public function __construct(PartnerGroupMerger $merger)
    {
        parent::__construct();
        $this->partnerGroupMerger = $merger;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            \DB::beginTransaction();

            $masterGroupId = $this->argument('master_group_id');
            $slaveGroupId = $this->argument('slave_group_id');

            $masterGroup = PartnerGroup::findOrFail($masterGroupId);
            $slaveGroup = PartnerGroup::findOrFail($slaveGroupId);

            $this->partnerGroupMerger->merge($masterGroup, $slaveGroup);

            $slaveGroup->delete();

            \DB::commit();
        } catch (\Exception $ex) {
            $this->error("{$ex->getFile()}:{$ex->getLine()} {$ex->getMessage()}");
            \DB::rollBack();
        }
    }
}
