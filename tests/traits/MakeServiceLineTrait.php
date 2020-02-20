<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ServiceLine;
use App\Repositories\ServiceLineRepository;

trait MakeServiceLineTrait
{
    /**
     * Create fake instance of ServiceLine and save it in database
     *
     * @param array $serviceLineFields
     * @return ServiceLine
     */
    public function makeServiceLine($serviceLineFields = [])
    {
        /** @var ServiceLineRepository $serviceLineRepo */
        $serviceLineRepo = \App::make(ServiceLineRepository::class);
        $theme = $this->fakeServiceLineData($serviceLineFields);
        return $serviceLineRepo->create($theme);
    }

    /**
     * Get fake instance of ServiceLine
     *
     * @param array $serviceLineFields
     * @return ServiceLine
     */
    public function fakeServiceLine($serviceLineFields = [])
    {
        return new ServiceLine($this->fakeServiceLineData($serviceLineFields));
    }

    /**
     * Get fake data of ServiceLine
     *
     * @param array $serviceLineFields
     * @return array
     */
    public function fakeServiceLineData($serviceLineFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'ServiceLineCode' => $fake->word,
            'serviceLineMasterCode' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'ServiceLineDes' => $fake->word,
            'locationID' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'isPublic' => $fake->randomDigitNotNull,
            'isServiceLine' => $fake->randomDigitNotNull,
            'isDepartment' => $fake->randomDigitNotNull,
            'isMaster' => $fake->randomDigitNotNull,
            'consoleCode' => $fake->word,
            'consoleDescription' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $serviceLineFields);
    }
}
