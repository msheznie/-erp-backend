<?php

use Faker\Factory as Faker;
use App\Models\ApprovalLevel;
use App\Repositories\ApprovalLevelRepository;

trait MakeApprovalLevelTrait
{
    /**
     * Create fake instance of ApprovalLevel and save it in database
     *
     * @param array $approvalLevelFields
     * @return ApprovalLevel
     */
    public function makeApprovalLevel($approvalLevelFields = [])
    {
        /** @var ApprovalLevelRepository $approvalLevelRepo */
        $approvalLevelRepo = App::make(ApprovalLevelRepository::class);
        $theme = $this->fakeApprovalLevelData($approvalLevelFields);
        return $approvalLevelRepo->create($theme);
    }

    /**
     * Get fake instance of ApprovalLevel
     *
     * @param array $approvalLevelFields
     * @return ApprovalLevel
     */
    public function fakeApprovalLevel($approvalLevelFields = [])
    {
        return new ApprovalLevel($this->fakeApprovalLevelData($approvalLevelFields));
    }

    /**
     * Get fake data of ApprovalLevel
     *
     * @param array $postFields
     * @return array
     */
    public function fakeApprovalLevelData($approvalLevelFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'serviceLineWise' => $fake->randomDigitNotNull,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'levelDescription' => $fake->word,
            'noOfLevels' => $fake->randomDigitNotNull,
            'valueWise' => $fake->randomDigitNotNull,
            'valueFrom' => $fake->randomDigitNotNull,
            'valueTo' => $fake->randomDigitNotNull,
            'isCategoryWiseApproval' => $fake->randomDigitNotNull,
            'categoryID' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $approvalLevelFields);
    }
}
