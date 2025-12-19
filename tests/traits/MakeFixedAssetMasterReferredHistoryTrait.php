<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetMasterReferredHistory;
use App\Repositories\FixedAssetMasterReferredHistoryRepository;

trait MakeFixedAssetMasterReferredHistoryTrait
{
    /**
     * Create fake instance of FixedAssetMasterReferredHistory and save it in database
     *
     * @param array $fixedAssetMasterReferredHistoryFields
     * @return FixedAssetMasterReferredHistory
     */
    public function makeFixedAssetMasterReferredHistory($fixedAssetMasterReferredHistoryFields = [])
    {
        /** @var FixedAssetMasterReferredHistoryRepository $fixedAssetMasterReferredHistoryRepo */
        $fixedAssetMasterReferredHistoryRepo = App::make(FixedAssetMasterReferredHistoryRepository::class);
        $theme = $this->fakeFixedAssetMasterReferredHistoryData($fixedAssetMasterReferredHistoryFields);
        return $fixedAssetMasterReferredHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetMasterReferredHistory
     *
     * @param array $fixedAssetMasterReferredHistoryFields
     * @return FixedAssetMasterReferredHistory
     */
    public function fakeFixedAssetMasterReferredHistory($fixedAssetMasterReferredHistoryFields = [])
    {
        return new FixedAssetMasterReferredHistory($this->fakeFixedAssetMasterReferredHistoryData($fixedAssetMasterReferredHistoryFields));
    }

    /**
     * Get fake data of FixedAssetMasterReferredHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetMasterReferredHistoryData($fixedAssetMasterReferredHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'faID' => $fake->randomDigitNotNull,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'docOriginDocumentSystemID' => $fake->randomDigitNotNull,
            'docOriginDocumentID' => $fake->word,
            'docOriginSystemCode' => $fake->randomDigitNotNull,
            'docOrigin' => $fake->word,
            'docOriginDetailID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'faAssetDept' => $fake->randomDigitNotNull,
            'serialNo' => $fake->randomDigitNotNull,
            'itemCode' => $fake->randomDigitNotNull,
            'faCode' => $fake->word,
            'assetCodeS' => $fake->word,
            'faUnitSerialNo' => $fake->word,
            'assetDescription' => $fake->text,
            'COMMENTS' => $fake->text,
            'groupTO' => $fake->randomDigitNotNull,
            'dateAQ' => $fake->date('Y-m-d H:i:s'),
            'dateDEP' => $fake->date('Y-m-d H:i:s'),
            'depMonth' => $fake->randomDigitNotNull,
            'DEPpercentage' => $fake->randomDigitNotNull,
            'faCatID' => $fake->randomDigitNotNull,
            'faSubCatID' => $fake->randomDigitNotNull,
            'faSubCatID2' => $fake->randomDigitNotNull,
            'faSubCatID3' => $fake->randomDigitNotNull,
            'COSTUNIT' => $fake->randomDigitNotNull,
            'costUnitRpt' => $fake->randomDigitNotNull,
            'AUDITCATOGARY' => $fake->randomDigitNotNull,
            'PARTNUMBER' => $fake->word,
            'MANUFACTURE' => $fake->word,
            'itemPath' => $fake->word,
            'itemPicture' => $fake->word,
            'IMAGE' => $fake->word,
            'UNITASSIGN' => $fake->word,
            'UHITASSHISTORY' => $fake->text,
            'USEDBY' => $fake->word,
            'USEBYHISTRY' => $fake->text,
            'LOCATION' => $fake->word,
            'currentLocation' => $fake->randomDigitNotNull,
            'LOCATIONHISTORY' => $fake->randomDigitNotNull,
            'selectedForDisposal' => $fake->randomDigitNotNull,
            'DIPOSED' => $fake->randomDigitNotNull,
            'disposedDate' => $fake->date('Y-m-d H:i:s'),
            'assetdisposalMasterAutoID' => $fake->randomDigitNotNull,
            'RESONDISPO' => $fake->text,
            'CASHDISPOSAL' => $fake->randomDigitNotNull,
            'COSTATDISP' => $fake->randomDigitNotNull,
            'ACCDEPDIP' => $fake->randomDigitNotNull,
            'PROFITLOSSDIS' => $fake->randomDigitNotNull,
            'TECHNICAL_HISTORY' => $fake->text,
            'costglCodeSystemID' => $fake->randomDigitNotNull,
            'COSTGLCODE' => $fake->word,
            'COSTGLCODEdes' => $fake->word,
            'accdepglCodeSystemID' => $fake->randomDigitNotNull,
            'ACCDEPGLCODE' => $fake->word,
            'ACCDEPGLCODEdes' => $fake->word,
            'depglCodeSystemID' => $fake->randomDigitNotNull,
            'DEPGLCODE' => $fake->word,
            'DEPGLCODEdes' => $fake->word,
            'dispglCodeSystemID' => $fake->randomDigitNotNull,
            'DISPOGLCODE' => $fake->word,
            'DISPOGLCODEdes' => $fake->word,
            'RollLevForApp_curr' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'lastVerifiedDate' => $fake->date('Y-m-d H:i:s'),
            'timesReferred' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedPc' => $fake->word,
            'createdDateAndTime' => $fake->date('Y-m-d H:i:s'),
            'createdDateTime' => $fake->word,
            'selectedYN' => $fake->randomDigitNotNull,
            'assetType' => $fake->randomDigitNotNull,
            'supplierIDRentedAsset' => $fake->randomDigitNotNull,
            'tempRecord' => $fake->randomDigitNotNull,
            'toolsCondition' => $fake->randomDigitNotNull,
            'selectedforJobYN' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetMasterReferredHistoryFields);
    }
}
