<?php

use Faker\Factory as Faker;
use App\Models\RigMaster;
use App\Repositories\RigMasterRepository;

trait MakeRigMasterTrait
{
    /**
     * Create fake instance of RigMaster and save it in database
     *
     * @param array $rigMasterFields
     * @return RigMaster
     */
    public function makeRigMaster($rigMasterFields = [])
    {
        /** @var RigMasterRepository $rigMasterRepo */
        $rigMasterRepo = App::make(RigMasterRepository::class);
        $theme = $this->fakeRigMasterData($rigMasterFields);
        return $rigMasterRepo->create($theme);
    }

    /**
     * Get fake instance of RigMaster
     *
     * @param array $rigMasterFields
     * @return RigMaster
     */
    public function fakeRigMaster($rigMasterFields = [])
    {
        return new RigMaster($this->fakeRigMasterData($rigMasterFields));
    }

    /**
     * Get fake data of RigMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeRigMasterData($rigMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'RigDescription' => $fake->word,
            'companyID' => $fake->word,
            'oldID' => $fake->randomDigitNotNull,
            'isRig' => $fake->randomDigitNotNull
        ], $rigMasterFields);
    }
}
