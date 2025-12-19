<?php

namespace App\Repositories;

use App\Models\FixedAssetMasterReferredHistory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FixedAssetMasterReferredHistoryRepository
 * @package App\Repositories
 * @version December 7, 2018, 5:02 am UTC
 *
 * @method FixedAssetMasterReferredHistory findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetMasterReferredHistory find($id, $columns = ['*'])
 * @method FixedAssetMasterReferredHistory first($columns = ['*'])
*/
class FixedAssetMasterReferredHistoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'faID',
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
        'docOriginDocumentSystemID',
        'docOriginDocumentID',
        'docOriginSystemCode',
        'docOrigin',
        'docOriginDetailID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'faAssetDept',
        'serialNo',
        'itemCode',
        'faCode',
        'assetCodeS',
        'faUnitSerialNo',
        'assetDescription',
        'COMMENTS',
        'groupTO',
        'dateAQ',
        'dateDEP',
        'depMonth',
        'DEPpercentage',
        'faCatID',
        'faSubCatID',
        'faSubCatID2',
        'faSubCatID3',
        'COSTUNIT',
        'costUnitRpt',
        'AUDITCATOGARY',
        'PARTNUMBER',
        'MANUFACTURE',
        'itemPath',
        'itemPicture',
        'IMAGE',
        'UNITASSIGN',
        'UHITASSHISTORY',
        'USEDBY',
        'USEBYHISTRY',
        'LOCATION',
        'currentLocation',
        'LOCATIONHISTORY',
        'selectedForDisposal',
        'DIPOSED',
        'disposedDate',
        'assetdisposalMasterAutoID',
        'RESONDISPO',
        'CASHDISPOSAL',
        'COSTATDISP',
        'ACCDEPDIP',
        'PROFITLOSSDIS',
        'TECHNICAL_HISTORY',
        'costglCodeSystemID',
        'COSTGLCODE',
        'COSTGLCODEdes',
        'accdepglCodeSystemID',
        'ACCDEPGLCODE',
        'ACCDEPGLCODEdes',
        'depglCodeSystemID',
        'DEPGLCODE',
        'DEPGLCODEdes',
        'dispglCodeSystemID',
        'DISPOGLCODE',
        'DISPOGLCODEdes',
        'RollLevForApp_curr',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'lastVerifiedDate',
        'timesReferred',
        'refferedBackYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedUserSystemID',
        'modifiedPc',
        'createdDateAndTime',
        'createdDateTime',
        'selectedYN',
        'assetType',
        'supplierIDRentedAsset',
        'tempRecord',
        'toolsCondition',
        'selectedforJobYN',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetMasterReferredHistory::class;
    }
}
