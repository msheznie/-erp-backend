<?php

use Faker\Factory as Faker;
use App\Models\TicketMaster;
use App\Repositories\TicketMasterRepository;

trait MakeTicketMasterTrait
{
    /**
     * Create fake instance of TicketMaster and save it in database
     *
     * @param array $ticketMasterFields
     * @return TicketMaster
     */
    public function makeTicketMaster($ticketMasterFields = [])
    {
        /** @var TicketMasterRepository $ticketMasterRepo */
        $ticketMasterRepo = App::make(TicketMasterRepository::class);
        $theme = $this->fakeTicketMasterData($ticketMasterFields);
        return $ticketMasterRepo->create($theme);
    }

    /**
     * Get fake instance of TicketMaster
     *
     * @param array $ticketMasterFields
     * @return TicketMaster
     */
    public function fakeTicketMaster($ticketMasterFields = [])
    {
        return new TicketMaster($this->fakeTicketMasterData($ticketMasterFields));
    }

    /**
     * Get fake data of TicketMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTicketMasterData($ticketMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'ticketNo' => $fake->word,
            'ticketMonth' => $fake->word,
            'ticketYear' => $fake->word,
            'contractRefNo' => $fake->word,
            'regName' => $fake->randomDigitNotNull,
            'regNo' => $fake->word,
            'companyID' => $fake->word,
            'clientID' => $fake->word,
            'ticketCategory' => $fake->randomDigitNotNull,
            'serviceLine' => $fake->word,
            'fieldName' => $fake->randomDigitNotNull,
            'fieldType' => $fake->randomDigitNotNull,
            'wellNo' => $fake->word,
            'wellType' => $fake->randomDigitNotNull,
            'comments' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'ticketStatus' => $fake->randomDigitNotNull,
            'ticketStatusEmpID' => $fake->word,
            'ticketStatusDate' => $fake->date('Y-m-d H:i:s'),
            'ticketStatusComment' => $fake->word,
            'BillingStatus' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedBy' => $fake->word,
            'confrmedDate' => $fake->date('Y-m-d H:i:s'),
            'confirmedComment' => $fake->word,
            'JobAcheived' => $fake->randomDigitNotNull,
            'jobNetworkNo' => $fake->word,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'primaryUnitAssetID' => $fake->randomDigitNotNull,
            'jobSupervisor' => $fake->word,
            'Temperature' => $fake->randomDigitNotNull,
            'Depth' => $fake->randomDigitNotNull,
            'timeBaseLeftLocation' => $fake->date('Y-m-d H:i:s'),
            'TimeDateArrive' => $fake->date('Y-m-d H:i:s'),
            'TimedateRigup' => $fake->date('Y-m-d H:i:s'),
            'Timedatejobstra' => $fake->date('Y-m-d H:i:s'),
            'Timedatejobend' => $fake->date('Y-m-d H:i:s'),
            'Timedateleaveloc' => $fake->date('Y-m-d H:i:s'),
            'Totalhourloac' => $fake->randomDigitNotNull,
            'TotalOperatingHours' => $fake->randomDigitNotNull,
            'jobScheduledYNBM' => $fake->randomDigitNotNull,
            'jobScheduledEmpIDBM' => $fake->word,
            'jobScheduledDateBM' => $fake->date('Y-m-d H:i:s'),
            'jobScheduledCommentBM' => $fake->word,
            'jobStartedYNBM' => $fake->randomDigitNotNull,
            'jobStartedEmpIDBM' => $fake->word,
            'jobStartedDateBM' => $fake->date('Y-m-d H:i:s'),
            'jobStartedCommentBM' => $fake->word,
            'jobEndYNSup' => $fake->randomDigitNotNull,
            'jobEndEmpIDSup' => $fake->word,
            'jobEndDateSup' => $fake->date('Y-m-d H:i:s'),
            'jobEndCommentSup' => $fake->word,
            'ticketTypeMaster' => $fake->word,
            'ticketType' => $fake->randomDigitNotNull,
            'selectedBillingYN' => $fake->randomDigitNotNull,
            'processSelectTemp' => $fake->randomDigitNotNull,
            'estimatedServiceValue' => $fake->randomDigitNotNull,
            'estimatedProductValue' => $fake->randomDigitNotNull,
            'revenueYear' => $fake->randomDigitNotNull,
            'revenueMonth' => $fake->randomDigitNotNull,
            'ticketServiceValue' => $fake->randomDigitNotNull,
            'ticketProductValue' => $fake->randomDigitNotNull,
            'ticketNature' => $fake->randomDigitNotNull,
            'ticketClientSerial' => $fake->randomDigitNotNull,
            'companyComment' => $fake->text,
            'clientComment' => $fake->text,
            'opDept' => $fake->randomDigitNotNull,
            'poNumber' => $fake->word,
            'tempPerformaMasID' => $fake->randomDigitNotNull,
            'tempPerformaCode' => $fake->word,
            'cancelledYN' => $fake->randomDigitNotNull,
            'ticketCancelledDesc' => $fake->word,
            'EngID' => $fake->word,
            'ticketManulNo' => $fake->word,
            'contractUID' => $fake->randomDigitNotNull,
            'oldNoUpdate' => $fake->randomDigitNotNull,
            'JobFailure' => $fake->randomDigitNotNull,
            'isFail' => $fake->word,
            'customerRep' => $fake->word,
            'companyRep' => $fake->word,
            'customerRepContact' => $fake->word,
            'country' => $fake->word,
            'isWeb' => $fake->word,
            'assginBaseManager' => $fake->word,
            'assginSuperviser' => $fake->word,
            'callout' => $fake->randomDigitNotNull,
            'rigClosedDate' => $fake->date('Y-m-d H:i:s'),
            'serviceEntry' => $fake->word,
            'submissionDate' => $fake->date('Y-m-d H:i:s'),
            'batchNo' => $fake->randomDigitNotNull,
            'callOutDate' => $fake->word,
            'sqauditCategoryID' => $fake->randomDigitNotNull,
            'querySentYN' => $fake->word,
            'querySentDate' => $fake->word,
            'querySentBy' => $fake->word,
            'financeApprovedYN' => $fake->word,
            'financeApprovedDate' => $fake->word,
            'financeApprovedBy' => $fake->word,
            'isDeleted' => $fake->randomDigitNotNull,
            'deletedBy' => $fake->word,
            'deletedDate' => $fake->date('Y-m-d H:i:s'),
            'deletedComment' => $fake->word,
            'jobDescID' => $fake->randomDigitNotNull,
            'secondComments' => $fake->text
        ], $ticketMasterFields);
    }
}
