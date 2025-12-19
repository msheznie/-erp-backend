<?php

use Faker\Factory as Faker;
use App\Models\OutletUsers;
use App\Repositories\OutletUsersRepository;

trait MakeOutletUsersTrait
{
    /**
     * Create fake instance of OutletUsers and save it in database
     *
     * @param array $outletUsersFields
     * @return OutletUsers
     */
    public function makeOutletUsers($outletUsersFields = [])
    {
        /** @var OutletUsersRepository $outletUsersRepo */
        $outletUsersRepo = App::make(OutletUsersRepository::class);
        $theme = $this->fakeOutletUsersData($outletUsersFields);
        return $outletUsersRepo->create($theme);
    }

    /**
     * Get fake instance of OutletUsers
     *
     * @param array $outletUsersFields
     * @return OutletUsers
     */
    public function fakeOutletUsers($outletUsersFields = [])
    {
        return new OutletUsers($this->fakeOutletUsersData($outletUsersFields));
    }

    /**
     * Get fake data of OutletUsers
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOutletUsersData($outletUsersFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'userID' => $fake->randomDigitNotNull,
            'wareHouseID' => $fake->randomDigitNotNull,
            'counterID' => $fake->randomDigitNotNull,
            'isActive' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $outletUsersFields);
    }
}
