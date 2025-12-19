<?php

use Faker\Factory as Faker;
use App\Models\Designation;
use App\Repositories\DesignationRepository;

trait MakeDesignationTrait
{
    /**
     * Create fake instance of Designation and save it in database
     *
     * @param array $designationFields
     * @return Designation
     */
    public function makeDesignation($designationFields = [])
    {
        /** @var DesignationRepository $designationRepo */
        $designationRepo = App::make(DesignationRepository::class);
        $theme = $this->fakeDesignationData($designationFields);
        return $designationRepo->create($theme);
    }

    /**
     * Get fake instance of Designation
     *
     * @param array $designationFields
     * @return Designation
     */
    public function fakeDesignation($designationFields = [])
    {
        return new Designation($this->fakeDesignationData($designationFields));
    }

    /**
     * Get fake data of Designation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDesignationData($designationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'designation' => $fake->word,
            'designation_O' => $fake->word,
            'localName' => $fake->word,
            'jobCode' => $fake->word,
            'jobDecipline' => $fake->randomDigitNotNull,
            'businessFunction' => $fake->randomDigitNotNull,
            'appraisalTemplateID' => $fake->randomDigitNotNull,
            'createdPCid' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $designationFields);
    }
}
