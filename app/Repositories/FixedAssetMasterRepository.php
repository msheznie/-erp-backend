<?php

namespace App\Repositories;

use App\Models\FixedAssetMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FixedAssetMasterRepository
 * @package App\Repositories
 * @version August 17, 2018, 10:25 am UTC
 *
 * @method FixedAssetMaster findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetMaster find($id, $columns = ['*'])
 * @method FixedAssetMaster first($columns = ['*'])
*/
class FixedAssetMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'departmentSystemID',
        'departmentID',
        'serviceLineSystemID',
        'serviceLineCode',
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
        'COSTGLCODE',
        'COSTGLCODEdes',
        'ACCDEPGLCODE',
        'ACCDEPGLCODEdes',
        'DEPGLCODE',
        'DEPGLCODEdes',
        'DISPOGLCODE',
        'DISPOGLCODEdes',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedDate',
        'approved',
        'approvedDate',
        'lastVerifiedDate',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
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
        return FixedAssetMaster::class;
    }
}
