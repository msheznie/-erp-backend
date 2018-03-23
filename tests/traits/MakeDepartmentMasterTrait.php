<?php

use Faker\Factory as Faker;
use App\Models\DepartmentMaster;
use App\Repositories\DepartmentMasterRepository;

trait MakeDepartmentMasterTrait
{
    /**
     * Create fake instance of DepartmentMaster and save it in database
     *
     * @param array $departmentMasterFields
     * @return DepartmentMaster
     */
    public function makeDepartmentMaster($departmentMasterFields = [])
    {
        /** @var DepartmentMasterRepository $departmentMasterRepo */
        $departmentMasterRepo = App::make(DepartmentMasterRepository::class);
        $theme = $this->fakeDepartmentMasterData($departmentMasterFields);
        return $departmentMasterRepo->create($theme);
    }

    /**
     * Get fake instance of DepartmentMaster
     *
     * @param array $departmentMasterFields
     * @return DepartmentMaster
     */
    public function fakeDepartmentMaster($departmentMasterFields = [])
    {
        return new DepartmentMaster($this->fakeDepartmentMasterData($departmentMasterFields));
    }

    /**
     * Get fake data of DepartmentMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDepartmentMasterData($departmentMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'DepartmentID' => $fake->word,
            'DepartmentDescription' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'depImage' => $fake->word,
            'masterLevel' => $fake->randomDigitNotNull,
            'companyLevel' => $fake->randomDigitNotNull,
            'listOrder' => $fake->randomDigitNotNull,
            'isReport' => $fake->randomDigitNotNull,
            'ReportMenu' => $fake->word,
            'menuInitialImage' => $fake->word,
            'menuInitialSelectedImage' => $fake->word,
            'showInCombo' => $fake->randomDigitNotNull,
            'hrLeaveApprovalLevels' => $fake->randomDigitNotNull,
            'managerfield' => $fake->word,
            'isFunctionalDepartment' => $fake->randomDigitNotNull,
            'isReportGroupYN' => $fake->randomDigitNotNull,
            'hrObjectiveSetting' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $departmentMasterFields);
    }
}
