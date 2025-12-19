<?php
/**
 * =============================================
 * -- File Name : FixedAssetMasterReferredHistory.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Nazir
 * -- Create date : 7 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FixedAssetMasterReferredHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="faReferredID",
 *          description="faReferredID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faID",
 *          description="faID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentSystemID",
 *          description="departmentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docOriginDocumentSystemID",
 *          description="docOriginDocumentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="docOriginDocumentID",
 *          description="docOriginDocumentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docOriginSystemCode",
 *          description="docOriginSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="docOrigin",
 *          description="docOrigin",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docOriginDetailID",
 *          description="docOriginDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="faAssetDept",
 *          description="faAssetDept",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCode",
 *          description="itemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faCode",
 *          description="faCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetCodeS",
 *          description="assetCodeS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="faUnitSerialNo",
 *          description="faUnitSerialNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetDescription",
 *          description="assetDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="COMMENTS",
 *          description="COMMENTS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="groupTO",
 *          description="groupTO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depMonth",
 *          description="depMonth",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="DEPpercentage",
 *          description="DEPpercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="faCatID",
 *          description="faCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faSubCatID",
 *          description="faSubCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faSubCatID2",
 *          description="faSubCatID2",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="faSubCatID3",
 *          description="faSubCatID3",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="COSTUNIT",
 *          description="COSTUNIT",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="costUnitRpt",
 *          description="costUnitRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="AUDITCATOGARY",
 *          description="AUDITCATOGARY",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PARTNUMBER",
 *          description="PARTNUMBER",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="MANUFACTURE",
 *          description="MANUFACTURE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemPath",
 *          description="itemPath",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemPicture",
 *          description="itemPicture",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="IMAGE",
 *          description="IMAGE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UNITASSIGN",
 *          description="UNITASSIGN",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UHITASSHISTORY",
 *          description="UHITASSHISTORY",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="USEDBY",
 *          description="USEDBY",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="USEBYHISTRY",
 *          description="USEBYHISTRY",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="LOCATION",
 *          description="LOCATION",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currentLocation",
 *          description="currentLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="LOCATIONHISTORY",
 *          description="LOCATIONHISTORY",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedForDisposal",
 *          description="selectedForDisposal",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DIPOSED",
 *          description="DIPOSED",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetdisposalMasterAutoID",
 *          description="assetdisposalMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RESONDISPO",
 *          description="RESONDISPO",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CASHDISPOSAL",
 *          description="CASHDISPOSAL",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="COSTATDISP",
 *          description="COSTATDISP",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="ACCDEPDIP",
 *          description="ACCDEPDIP",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="PROFITLOSSDIS",
 *          description="PROFITLOSSDIS",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="TECHNICAL_HISTORY",
 *          description="TECHNICAL_HISTORY",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="costglCodeSystemID",
 *          description="costglCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="COSTGLCODE",
 *          description="COSTGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="COSTGLCODEdes",
 *          description="COSTGLCODEdes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accdepglCodeSystemID",
 *          description="accdepglCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ACCDEPGLCODE",
 *          description="ACCDEPGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ACCDEPGLCODEdes",
 *          description="ACCDEPGLCODEdes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depglCodeSystemID",
 *          description="depglCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DEPGLCODE",
 *          description="DEPGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DEPGLCODEdes",
 *          description="DEPGLCODEdes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="dispglCodeSystemID",
 *          description="dispglCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DISPOGLCODE",
 *          description="DISPOGLCODE",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="DISPOGLCODEdes",
 *          description="DISPOGLCODEdes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="selectedYN",
 *          description="selectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetType",
 *          description="assetType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierIDRentedAsset",
 *          description="supplierIDRentedAsset",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tempRecord",
 *          description="tempRecord",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toolsCondition",
 *          description="toolsCondition",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedforJobYN",
 *          description="selectedforJobYN",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class FixedAssetMasterReferredHistory extends Model
{

    public $table = 'erp_fa_asset_master_referred_history';

    const CREATED_AT = 'createdDateAndTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'faReferredID';



    public $fillable = [
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
        'documentDate',
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
        'timestamp',
        'accumulated_depreciation_amount_rpt',
        'accumulated_depreciation_amount_lcl',
        'is_acc_dep',
        'accumulated_depreciation_date',
           'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'faReferredID' => 'integer',
        'faID' => 'integer',
        'departmentSystemID' => 'integer',
        'departmentID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'docOriginDocumentSystemID' => 'integer',
        'docOriginDocumentID' => 'string',
        'docOriginSystemCode' => 'integer',
        'docOrigin' => 'string',
        'docOriginDetailID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'faAssetDept' => 'integer',
        'serialNo' => 'integer',
        'itemCode' => 'integer',
        'faCode' => 'string',
        'assetCodeS' => 'string',
        'faUnitSerialNo' => 'string',
        'assetDescription' => 'string',
        'COMMENTS' => 'string',
        'groupTO' => 'integer',
        'depMonth' => 'float',
        'DEPpercentage' => 'float',
        'faCatID' => 'integer',
        'faSubCatID' => 'integer',
        'faSubCatID2' => 'integer',
        'faSubCatID3' => 'integer',
        'COSTUNIT' => 'float',
        'costUnitRpt' => 'float',
        'AUDITCATOGARY' => 'integer',
        'PARTNUMBER' => 'string',
        'MANUFACTURE' => 'string',
        'itemPath' => 'string',
        'itemPicture' => 'string',
        'IMAGE' => 'string',
        'UNITASSIGN' => 'string',
        'UHITASSHISTORY' => 'string',
        'USEDBY' => 'string',
        'USEBYHISTRY' => 'string',
        'LOCATION' => 'string',
        'currentLocation' => 'integer',
        'LOCATIONHISTORY' => 'integer',
        'selectedForDisposal' => 'integer',
        'DIPOSED' => 'integer',
        'assetdisposalMasterAutoID' => 'integer',
        'RESONDISPO' => 'string',
        'CASHDISPOSAL' => 'float',
        'COSTATDISP' => 'float',
        'ACCDEPDIP' => 'float',
        'PROFITLOSSDIS' => 'float',
        'TECHNICAL_HISTORY' => 'string',
        'costglCodeSystemID' => 'integer',
        'COSTGLCODE' => 'string',
        'COSTGLCODEdes' => 'string',
        'accdepglCodeSystemID' => 'integer',
        'ACCDEPGLCODE' => 'string',
        'ACCDEPGLCODEdes' => 'string',
        'depglCodeSystemID' => 'integer',
        'DEPGLCODE' => 'string',
        'DEPGLCODEdes' => 'string',
        'dispglCodeSystemID' => 'integer',
        'DISPOGLCODE' => 'string',
        'DISPOGLCODEdes' => 'string',
        'RollLevForApp_curr' => 'boolean',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'boolean',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedPc' => 'string',
        'createdDateTime' => 'string',
        'selectedYN' => 'integer',
        'assetType' => 'integer',
        'supplierIDRentedAsset' => 'integer',
        'tempRecord' => 'integer',
        'toolsCondition' => 'integer',
        'selectedforJobYN' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('companySystemID',  $type);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsDisposed($query)
    {
        return $query->where('DIPOSED',  0);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCategory($query, $category)
    {
        return $query->where('faCatID',  $category);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsSelectedForDisposal($query)
    {
        return $query->where('selectedForDisposal',  0);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsApproved($query)
    {
        return $query->where('approved',  -1);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeAssetType($query,$assetType)
    {
        return $query->where('assetType',  $assetType);
    }

    public function category_by(){
        return $this->belongsTo('App\Models\FixedAssetCategory','faCatID','faCatID');
    }

    public function sub_category_by(){
        return $this->belongsTo('App\Models\FixedAssetCategorySub','faSubCatID','faCatSubID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'faID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function grvdetail_by()
    {
        return $this->belongsTo('App\Models\GRVDetails', 'docOriginDetailID', 'grvDetailsID');
    }

    public function depperiod_by()
    {
        return $this->hasMany('App\Models\FixedAssetDepreciationPeriod', 'faID', 'faID');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function finance_category()
    {
        return $this->belongsTo('App\Models\AssetFinanceCategory','AUDITCATOGARY','faFinanceCatID');
    }

    public function asset_type()
    {
        return $this->belongsTo('App\Models\AssetType','assetType','typeID');
    }

    public function group_to()
    {
        return $this->belongsTo('App\Models\FixedAssetMaster','groupTO','faID');
    }

    public function departmentmaster(){
        return $this->belongsTo('App\Models\DepartmentMaster','departmentSystemID','departmentSystemID');
    }

    public function assettypemaster(){
        return $this->belongsTo('App\Models\AssetType','assetType','typeID');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierIDRentedAsset', 'supplierCodeSystem');
    }

    public function sub_category_by2(){
        return $this->belongsTo('App\Models\FixedAssetCategorySub','faSubCatID2','faCatSubID');
    }

    public function sub_category_by3(){
        return $this->belongsTo('App\Models\FixedAssetCategorySub','faSubCatID3','faCatSubID');
    }

    public function posttogl_by()
    {
        return $this->belongsTo('App\Models\ChartOfAccount','postToGLCodeSystemID','chartOfAccountSystemID');
    }



}
