<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\AllocationMaster;
use App\Repositories\AllocationMasterRepository;

trait MakeAllocationMasterTrait
{
    /**
     * Create fake instance of AllocationMaster and save it in database
     *
     * @param array $allocationMasterFields
     * @return AllocationMaster
     */
    public function makeAllocationMaster($allocationMasterFields = [])
    {
        /** @var AllocationMasterRepository $allocationMasterRepo */
        $allocationMasterRepo = \App::make(AllocationMasterRepository::class);
        $theme = $this->fakeAllocationMasterData($allocationMasterFields);
        return $allocationMasterRepo->create($theme);
    }

    /**
     * Get fake instance of AllocationMaster
     *
     * @param array $allocationMasterFields
     * @return AllocationMaster
     */
    public function fakeAllocationMaster($allocationMasterFields = [])
    {
        return new AllocationMaster($this->fakeAllocationMasterData($allocationMasterFields));
    }

    /**
     * Get fake data of AllocationMaster
     *
     * @param array $allocationMasterFields
     * @return array
     */
    public function fakeAllocationMasterData($allocationMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'Desciption' => $fake->word,
            'DesCode' => $fake->word,
            'timesstamp' => $fake->date('Y-m-d H:i:s')
        ], $allocationMasterFields);
    }
}
