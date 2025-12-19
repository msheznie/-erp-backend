<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\LeaveMaster;
use App\Repositories\LeaveMasterRepository;

trait MakeLeaveMasterTrait
{
    /**
     * Create fake instance of LeaveMaster and save it in database
     *
     * @param array $leaveMasterFields
     * @return LeaveMaster
     */
    public function makeLeaveMaster($leaveMasterFields = [])
    {
        /** @var LeaveMasterRepository $leaveMasterRepo */
        $leaveMasterRepo = \App::make(LeaveMasterRepository::class);
        $theme = $this->fakeLeaveMasterData($leaveMasterFields);
        return $leaveMasterRepo->create($theme);
    }

    /**
     * Get fake instance of LeaveMaster
     *
     * @param array $leaveMasterFields
     * @return LeaveMaster
     */
    public function fakeLeaveMaster($leaveMasterFields = [])
    {
        return new LeaveMaster($this->fakeLeaveMasterData($leaveMasterFields));
    }

    /**
     * Get fake data of LeaveMaster
     *
     * @param array $leaveMasterFields
     * @return array
     */
    public function fakeLeaveMasterData($leaveMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'leaveCode' => $fake->word,
            'leavetype' => $fake->word,
            'deductSalary' => $fake->randomDigitNotNull,
            'restrictDays' => $fake->randomDigitNotNull,
            'isAttachmentMandatory' => $fake->randomDigitNotNull,
            'managerDeadline' => $fake->randomDigitNotNull,
            'maxDays' => $fake->randomDigitNotNull,
            'allowMultipleLeave' => $fake->randomDigitNotNull,
            'isProbation' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $leaveMasterFields);
    }
}
