<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\SegmentRights;
use App\Repositories\SegmentRightsRepository;

trait MakeSegmentRightsTrait
{
    /**
     * Create fake instance of SegmentRights and save it in database
     *
     * @param array $segmentRightsFields
     * @return SegmentRights
     */
    public function makeSegmentRights($segmentRightsFields = [])
    {
        /** @var SegmentRightsRepository $segmentRightsRepo */
        $segmentRightsRepo = \App::make(SegmentRightsRepository::class);
        $theme = $this->fakeSegmentRightsData($segmentRightsFields);
        return $segmentRightsRepo->create($theme);
    }

    /**
     * Get fake instance of SegmentRights
     *
     * @param array $segmentRightsFields
     * @return SegmentRights
     */
    public function fakeSegmentRights($segmentRightsFields = [])
    {
        return new SegmentRights($this->fakeSegmentRightsData($segmentRightsFields));
    }

    /**
     * Get fake data of SegmentRights
     *
     * @param array $segmentRightsFields
     * @return array
     */
    public function fakeSegmentRightsData($segmentRightsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyrightsID' => $fake->randomDigitNotNull,
            'employeeSystemID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedPcID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $segmentRightsFields);
    }
}
