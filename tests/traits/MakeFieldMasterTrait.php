<?php

use Faker\Factory as Faker;
use App\Models\FieldMaster;
use App\Repositories\FieldMasterRepository;

trait MakeFieldMasterTrait
{
    /**
     * Create fake instance of FieldMaster and save it in database
     *
     * @param array $fieldMasterFields
     * @return FieldMaster
     */
    public function makeFieldMaster($fieldMasterFields = [])
    {
        /** @var FieldMasterRepository $fieldMasterRepo */
        $fieldMasterRepo = App::make(FieldMasterRepository::class);
        $theme = $this->fakeFieldMasterData($fieldMasterFields);
        return $fieldMasterRepo->create($theme);
    }

    /**
     * Get fake instance of FieldMaster
     *
     * @param array $fieldMasterFields
     * @return FieldMaster
     */
    public function fakeFieldMaster($fieldMasterFields = [])
    {
        return new FieldMaster($this->fakeFieldMasterData($fieldMasterFields));
    }

    /**
     * Get fake data of FieldMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFieldMasterData($fieldMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'fieldShortCode' => $fake->word,
            'fieldName' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'companyId' => $fake->word
        ], $fieldMasterFields);
    }
}
