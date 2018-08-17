<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetMaster;
use App\Repositories\FixedAssetMasterRepository;

trait MakeFixedAssetMasterTrait
{
    /**
     * Create fake instance of FixedAssetMaster and save it in database
     *
     * @param array $fixedAssetMasterFields
     * @return FixedAssetMaster
     */
    public function makeFixedAssetMaster($fixedAssetMasterFields = [])
    {
        /** @var FixedAssetMasterRepository $fixedAssetMasterRepo */
        $fixedAssetMasterRepo = App::make(FixedAssetMasterRepository::class);
        $theme = $this->fakeFixedAssetMasterData($fixedAssetMasterFields);
        return $fixedAssetMasterRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetMaster
     *
     * @param array $fixedAssetMasterFields
     * @return FixedAssetMaster
     */
    public function fakeFixedAssetMaster($fixedAssetMasterFields = [])
    {
        return new FixedAssetMaster($this->fakeFixedAssetMasterData($fixedAssetMasterFields));
    }

    /**
     * Get fake data of FixedAssetMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetMasterData($fixedAssetMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
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
            'COSTGLCODE' => $fake->word,
            'COSTGLCODEdes' => $fake->word,
            'ACCDEPGLCODE' => $fake->word,
            'ACCDEPGLCODEdes' => $fake->word,
            'DEPGLCODE' => $fake->word,
            'DEPGLCODEdes' => $fake->word,
            'DISPOGLCODE' => $fake->word,
            'DISPOGLCODEdes' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'lastVerifiedDate' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'selectedYN' => $fake->randomDigitNotNull,
            'assetType' => $fake->randomDigitNotNull,
            'supplierIDRentedAsset' => $fake->randomDigitNotNull,
            'tempRecord' => $fake->randomDigitNotNull,
            'toolsCondition' => $fake->randomDigitNotNull,
            'selectedforJobYN' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetMasterFields);
    }
}
