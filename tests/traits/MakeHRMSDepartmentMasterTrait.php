<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HrmsDepartmentMaster;
use App\Repositories\HrmsDepartmentMasterRepository;

trait MakeHrmsDepartmentMasterTrait
{
    /**
     * Create fake instance of HrmsDepartmentMaster and save it in database
     *
     * @param array $hrmsDepartmentMasterFields
     * @return HrmsDepartmentMaster
     */
    public function makeHrmsDepartmentMaster($hrmsDepartmentMasterFields = [])
    {
        /** @var HrmsDepartmentMasterRepository $hrmsDepartmentMasterRepo */
        $hrmsDepartmentMasterRepo = \App::make(HrmsDepartmentMasterRepository::class);
        $theme = $this->fakeHrmsDepartmentMasterData($hrmsDepartmentMasterFields);
        return $hrmsDepartmentMasterRepo->create($theme);
    }

    /**
     * Get fake instance of HrmsDepartmentMaster
     *
     * @param array $hrmsDepartmentMasterFields
     * @return HrmsDepartmentMaster
     */
    public function fakeHrmsDepartmentMaster($hrmsDepartmentMasterFields = [])
    {
        return new HrmsDepartmentMaster($this->fakeHrmsDepartmentMasterData($hrmsDepartmentMasterFields));
    }

    /**
     * Get fake data of HrmsDepartmentMaster
     *
     * @param array $hrmsDepartmentMasterFields
     * @return array
     */
    public function fakeHrmsDepartmentMasterData($hrmsDepartmentMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'DepartmentDescription' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'ServiceLineCode' => $fake->word,
            'CompanyID' => $fake->word,
            'showInCombo' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hrmsDepartmentMasterFields);
    }
}
