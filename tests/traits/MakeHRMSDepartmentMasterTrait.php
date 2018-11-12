<?php

use Faker\Factory as Faker;
use App\Models\HRMSDepartmentMaster;
use App\Repositories\HRMSDepartmentMasterRepository;

trait MakeHRMSDepartmentMasterTrait
{
    /**
     * Create fake instance of HRMSDepartmentMaster and save it in database
     *
     * @param array $hRMSDepartmentMasterFields
     * @return HRMSDepartmentMaster
     */
    public function makeHRMSDepartmentMaster($hRMSDepartmentMasterFields = [])
    {
        /** @var HRMSDepartmentMasterRepository $hRMSDepartmentMasterRepo */
        $hRMSDepartmentMasterRepo = App::make(HRMSDepartmentMasterRepository::class);
        $theme = $this->fakeHRMSDepartmentMasterData($hRMSDepartmentMasterFields);
        return $hRMSDepartmentMasterRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSDepartmentMaster
     *
     * @param array $hRMSDepartmentMasterFields
     * @return HRMSDepartmentMaster
     */
    public function fakeHRMSDepartmentMaster($hRMSDepartmentMasterFields = [])
    {
        return new HRMSDepartmentMaster($this->fakeHRMSDepartmentMasterData($hRMSDepartmentMasterFields));
    }

    /**
     * Get fake data of HRMSDepartmentMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeHRMSDepartmentMasterData($hRMSDepartmentMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'DepartmentDescription' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'ServiceLineCode' => $fake->word,
            'CompanyID' => $fake->word,
            'showInCombo' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSDepartmentMasterFields);
    }
}
