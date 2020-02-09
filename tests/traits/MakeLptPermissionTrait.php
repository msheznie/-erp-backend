<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\LptPermission;
use App\Repositories\LptPermissionRepository;

trait MakeLptPermissionTrait
{
    /**
     * Create fake instance of LptPermission and save it in database
     *
     * @param array $lptPermissionFields
     * @return LptPermission
     */
    public function makeLptPermission($lptPermissionFields = [])
    {
        /** @var LptPermissionRepository $lptPermissionRepo */
        $lptPermissionRepo = \App::make(LptPermissionRepository::class);
        $theme = $this->fakeLptPermissionData($lptPermissionFields);
        return $lptPermissionRepo->create($theme);
    }

    /**
     * Get fake instance of LptPermission
     *
     * @param array $lptPermissionFields
     * @return LptPermission
     */
    public function fakeLptPermission($lptPermissionFields = [])
    {
        return new LptPermission($this->fakeLptPermissionData($lptPermissionFields));
    }

    /**
     * Get fake data of LptPermission
     *
     * @param array $lptPermissionFields
     * @return array
     */
    public function fakeLptPermissionData($lptPermissionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'empID' => $fake->word,
            'employeeSystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'isLPTReview' => $fake->randomDigitNotNull,
            'isLPTClose' => $fake->randomDigitNotNull,
            'createdBy' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $lptPermissionFields);
    }
}
