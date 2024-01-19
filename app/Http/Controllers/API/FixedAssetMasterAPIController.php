<?php
/**
 * =============================================
 * -- File Name : FixedAssetMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Asset master
 * -- REVISION HISTORY
 * -- Date: 05-November 2018 By: Fayas Description: Added new functions named as generateAssetInsuranceReport(),
 *             exportAssetInsurance()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\DocumentCodeGenerate;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateFixedAssetMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetMasterAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Models\AssetType;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\BudgetConsumedData;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCategorySub;
use App\Models\FixedAssetCost;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetInsuranceDetail;
use App\Models\FixedAssetMaster;
use App\Models\FixedAssetMasterReferredHistory;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\InsurancePolicyType;
use App\Models\Location;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\FixedAssetCostRepository;
use App\Repositories\FixedAssetMasterRepository;
use App\Traits\AuditTrial;
use App\Traits\UserActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\CompanyFinancePeriod;
use App\Models\FixedAssetDepreciationMaster;
use App\helper\CreateAccumulatedDepreciation;
use App\helper\CreateExcel;
/**
 * Class FixedAssetMasterController
 * @package App\Http\Controllers\API
 */
class FixedAssetMasterAPIController extends AppBaseController
{
    /** @var  FixedAssetMasterRepository */
    private $fixedAssetMasterRepository;
    private $fixedAssetCostRepository;

    public function __construct(FixedAssetMasterRepository $fixedAssetMasterRepo, FixedAssetCostRepository $fixedAssetCostRepo)
    {
        $this->fixedAssetMasterRepository = $fixedAssetMasterRepo;
        $this->fixedAssetCostRepository = $fixedAssetCostRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasters",
     *      summary="Get a listing of the FixedAssetMasters.",
     *      tags={"FixedAssetMaster"},
     *      description="Get all FixedAssetMasters",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/FixedAssetMaster")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->fixedAssetMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetMasters = $this->fixedAssetMasterRepository->all();

        return $this->sendResponse($fixedAssetMasters->toArray(), 'Fixed Asset Masters retrieved successfully');
    }

    /**
     * @param CreateFixedAssetMasterAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetMasters",
     *      summary="Store a newly created FixedAssetMaster in storage",
     *      tags={"FixedAssetMaster"},
     *      description="Store FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetMasterAPIRequest $request)
    {
        $input = $request->all();
        $assetSerialNoArr = $input['assetSerialNo'];
        $itemImgaeArr = $input['itemImage'];
        $itemPicture = $input['itemPicture'];
        $input = array_except($request->all(), 'assetSerialNo', 'itemImage');
        $input = $this->convertArrayToValue($input);
        $input['assetSerialNo'] = $assetSerialNoArr;

        DB::beginTransaction();
        try {

            if(isset($input['faCatID']) && empty($input['faCatID'])){
                return $this->sendError("Main Category is required", 500);
            }

            if(isset($input['faSubCatID']) && empty($input['faSubCatID'])){
                return $this->sendError("Sub Category is required",500);
            }

                foreach ($input['assetSerialNo'] as $assetSN) {
                    if (empty($assetSN['faUnitSerialNo'])) {
                        return $this->sendError("Asset Serial No is required", 500);
                    }
                }


            $messages = [
                'dateDEP.after_or_equal' => 'Depreciation Date cannot be less than Date aqquired',
                'assetSerialNo.*.required' => 'Asset Serial No is required',
                'assetSerialNo.*.unique' => 'The FA Serial-No has already been taken',
                'AUDITCATOGARY.required' => 'Audit Category is required',
            ];
            $validator = \Validator::make($request->all(), [
                'dateAQ' => 'required|date',
                'dateDEP' => 'required|date|after_or_equal:dateAQ',
                'AUDITCATOGARY' => 'required',
                'assetSerialNo.*.faUnitSerialNo' => 'required|unique:erp_fa_asset_master,faUnitSerialNo',
            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }


            if (isset($input['itemPicture'])) {
                if ($itemImgaeArr[0]['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than ".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                }
            }

            $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
            $awsPolicy = Helper::checkPolicy($input['companySystemID'], 50);

            $grvDetailsID = $input['grvDetailsID'];
            $grvDetails = GRVDetails::with(['grv_master'])->find($grvDetailsID);
            if ($grvDetails) {

                $assetSerialNoCount = count($input['assetSerialNo']);

                $input['serviceLineSystemID'] = $grvDetails->grv_master->serviceLineSystemID;
                $segment = SegmentMaster::find($input['serviceLineSystemID']);
                if ($segment) {
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }

                $company = Company::find($input['companySystemID']);
                if ($company) {
                    $input['companyID'] = $company->CompanyID;
                }

                $department = DepartmentMaster::find($input['departmentSystemID']);
                if ($department) {
                    $input['departmentID'] = $department->DepartmentID;
                }

                $input["documentSystemID"] = 22;
                $input["documentID"] = 'FA';

                $input['assetType'] = 1;
                $input['supplierIDRentedAsset'] = $grvDetails->grv_master->supplierID;

                if (isset($input['dateAQ'])) {
                    if ($input['dateAQ']) {
                        $input['dateAQ'] = new Carbon($input['dateAQ']);
                    }
                }

                if (isset($input['dateDEP'])) {
                    if ($input['dateDEP']) {
                        $input['dateDEP'] = new Carbon($input['dateDEP']);
                    }
                }

                if (isset($input['lastVerifiedDate'])) {
                    if ($input['lastVerifiedDate']) {
                        $input['lastVerifiedDate'] = new Carbon($input['lastVerifiedDate']);
                    }
                }

                $lastSerialNumber = 1;
                $lastSerial = FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }


                $auditCategory = isset($input['AUDITCATOGARY']) ? $input['AUDITCATOGARY'] : null;
                if ($grvDetails["noQty"]) {
                    if ($grvDetails->noQty < 1) {
                        // $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

                        $documentCodeData = DocumentCodeGenerate::generateAssetCode($auditCategory, $input['companySystemID'], $input['serviceLineSystemID'],$input['faCatID'],$input['faSubCatID']);
                        
                        if ($documentCodeData['status']) {
                            $documentCode = $documentCodeData['documentCode'];
                            $searchDocumentCode = str_replace("\\", "\\\\", $documentCode);
                            $checkForDuplicateCode = FixedAssetMaster::where('faCode', $searchDocumentCode)
                                                                     ->first();

                            if ($checkForDuplicateCode) {
                                return $this->sendError("Asset code is already found.", 500);
                            }

                        } else {
                            return $this->sendError("Asset code is not configured.", 500);
                        }

                        if ($input['assetSerialNo'][0]['faUnitSerialNo']) {
                            $input["faUnitSerialNo"] = $input['assetSerialNo'][0]['faUnitSerialNo'];
                        }
                        $input["serialNo"] = $lastSerialNumber;
                        $input['docOriginDocumentSystemID'] = $grvDetails->grv_master->documentSystemID;
                        $input['docOriginDocumentID'] = $grvDetails->grv_master->documentID;
                        $input['docOriginSystemCode'] = $grvDetails->grv_master->grvAutoID;
                        $input['docOrigin'] = $grvDetails->grv_master->grvPrimaryCode;
                        $input['docOriginDetailID'] = $grvDetailsID;
                        $input["itemCode"] = $grvDetails->itemCode;
                        $input["PARTNUMBER"] = $grvDetails->item_by->secondaryItemCode;
                        $input["faCode"] = $documentCode;
                        $input['createdPcID'] = gethostname();
                        $input['createdUserID'] = \Helper::getEmployeeID();
                        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $input['createdDateAndTime'] = date('Y-m-d H:i:s');
                        $input["timestamp"] = date('Y-m-d H:i:s');
                        unset($input['grvDetailsID']);
                        unset($input['itemPicture']);
                        $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

                        if ($itemPicture) {
                            $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                            $extension = $itemImgaeArr[0]['filetype'];
                            $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMasters['faID'] . '.' . $extension;

                            if ($awsPolicy) {
                                $path = $input['companyID']. '/G_ERP/' .$input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                            } else {
                                $path = $input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                            }

                            $data['itemPath'] = $path;
                            Storage::disk($disk)->put($path, $decodeFile);
                            $fixedAssetMasters = $this->fixedAssetMasterRepository->update($data, $fixedAssetMasters['faID']);
                        }

                        $checkDuplicate = FixedAssetCost::where('assetID',$fixedAssetMasters['faCode'])
                                                         ->count();

                        if($checkDuplicate > 0)
                        {
                            return $this->sendError('Already created asset costing for '. $fixedAssetMasters['faCode'], 500);
                        }

                        $cost['originDocumentSystemCode'] = $grvDetails->grv_master->grvAutoID;
                        $cost['originDocumentID'] = $grvDetails->grv_master->grvPrimaryCode;
                        $cost['faID'] = $fixedAssetMasters['faID'];
                        $cost['itemCode'] = $input["itemCode"];
                        $cost['assetID'] = $fixedAssetMasters['faCode'];
                        $cost['assetDescription'] = $fixedAssetMasters['assetDescription'];
                        $cost['costDate'] = $input['dateAQ'];
                        $cost['localCurrencyID'] = $grvDetails->localCurrencyID;
                        $cost['localAmount'] = $grvDetails->landingCost_LocalCur * $grvDetails->noQty;
                        $cost['rptCurrencyID'] = $grvDetails->companyReportingCurrencyID;
                        $cost['rptAmount'] = $grvDetails->landingCost_RptCur * $grvDetails->noQty;
                        $this->fixedAssetCostRepository->create($cost);

                        // maintain assetAllocatedQty
                        GRVDetails::where('grvDetailsID', $grvDetailsID)->update(['assetAllocatedQty'=>$grvDetails->noQty]);

                    } else {

                        $ceil_qty = ceil($grvDetails->noQty);

                        $qtyRange = range(1, $ceil_qty-$grvDetails->assetAllocatedQty);

                  
                        $assetAllocatedQty = $grvDetails->assetAllocatedQty;
                        if ($qtyRange) {
                            foreach ($qtyRange as $key => $qty) {
                                // $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

                                $documentCodeData = DocumentCodeGenerate::generateAssetCode($auditCategory, $input['companySystemID'], $input['serviceLineSystemID'],$input['faCatID'],$input['faSubCatID']);
                                
                                if ($documentCodeData['status']) {
                                    $documentCode = $documentCodeData['documentCode'];
                                    $searchDocumentCode = str_replace("\\", "\\\\", $documentCode);
                                    $checkForDuplicateCode = FixedAssetMaster::where('faCode', $searchDocumentCode)
                                                                             ->first();

                                    if ($checkForDuplicateCode) {
                                        return $this->sendError("Asset code is already found.", 500);
                                    }

                                } else {
                                    return $this->sendError("Asset code is not configured.", 500);
                                }
                                if ($qty <= $assetSerialNoCount) {
                                    if ($input['assetSerialNo'][$key]['faUnitSerialNo']) {
                                        $input["faUnitSerialNo"] = $input['assetSerialNo'][$key]['faUnitSerialNo'];
                                    }
                                }
                                $input["serialNo"] = $lastSerialNumber;
                                $input['docOriginDocumentSystemID'] = $grvDetails->grv_master->documentSystemID;
                                $input['docOriginDocumentID'] = $grvDetails->grv_master->documentID;
                                $input['docOriginSystemCode'] = $grvDetails->grv_master->grvAutoID;
                                $input['docOrigin'] = $grvDetails->grv_master->grvPrimaryCode;
                                $input['docOriginDetailID'] = $grvDetailsID;
                                $input["itemCode"] = $grvDetails->itemCode;
                                $input["PARTNUMBER"] = $grvDetails->item_by->secondaryItemCode;
                                $input["faCode"] = $documentCode;
                                $input['createdPcID'] = gethostname();
                                $input['createdUserID'] = \Helper::getEmployeeID();
                                $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                                $input['createdDateAndTime'] = date('Y-m-d H:i:s');
                                $input["timestamp"] = date('Y-m-d H:i:s');
                                unset($input['grvDetailsID']);
                                unset($input['itemPicture']);
                                $lastSerialNumber++;
                                $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

                                if ($itemPicture) {
                                    $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                                    $extension = $itemImgaeArr[0]['filetype'];
                                    $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMasters['faID'] . '.' . $extension;

                                    if ($awsPolicy) {
                                        $path = $input['companyID']. '/G_ERP/' .$input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                                    } else {
                                        $path = $input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                                    }
                                    $data['itemPath'] = $path;
                                    Storage::disk($disk)->put($path, $decodeFile);
                                    $fixedAssetMasters = $this->fixedAssetMasterRepository->update($data, $fixedAssetMasters['faID']);
                                }

                                $checkDuplicate = FixedAssetCost::where('assetID',$fixedAssetMasters['faCode'])
                                                                 ->count();

                                if($checkDuplicate > 0)
                                {
                                    return $this->sendError('Already created asset costing for ' .$fixedAssetMasters['faCode'], 500);
                                }
                              
                                $cost['originDocumentSystemCode'] = $grvDetails->grv_master->grvAutoID;
                                $cost['originDocumentID'] = $grvDetails->grv_master->grvPrimaryCode;
                                $cost['faID'] = $fixedAssetMasters['faID'];
                                $cost['itemCode'] = $input["itemCode"];
                                $cost['assetID'] = $fixedAssetMasters['faCode'];
                                $cost['assetDescription'] = $fixedAssetMasters['assetDescription'];
                                $cost['costDate'] = $input['dateAQ'];
                                $cost['localCurrencyID'] = $grvDetails->localCurrencyID;
                                $cost['localAmount'] = $grvDetails->landingCost_LocalCur;
                                $cost['rptCurrencyID'] = $grvDetails->companyReportingCurrencyID;
                                $cost['rptAmount'] = $grvDetails->landingCost_RptCur;
                                $this->fixedAssetCostRepository->create($cost);
                                $assetAllocatedQty++;
                            }
                        }

                        $allocate_qty = $assetAllocatedQty;
                        if($ceil_qty > $assetAllocatedQty)
                        {
                            $allocate_qty = $ceil_qty;
                        }
       

                        GRVDetails::where('grvDetailsID', $grvDetailsID)->update(['assetAllocationDoneYN' => -1,'assetAllocatedQty'=>$allocate_qty]);
                    }
                   
                    DB::commit();
                }
            }
            return $this->sendResponse([], 'Fixed Asset Master saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function create(CreateFixedAssetMasterAPIRequest $request)
    {

       
        $input = $request->all();
        $itemImgaeArr = $input['itemImage'];
        $itemPicture = $input['itemPicture'];
        $input = array_except($request->all(), 'itemImage');    
        $accumulated_amount = $input['accumulated_depreciation_amount_rpt'];

        // if($input['assetType'] == 1  && ($accumulated_amount > 0 && $accumulated_amount != null) )
        // {
        //     $is_pending_job_exist = FixedAssetDepreciationMaster::where('approved','=',0)->where('is_acc_dep','=',0)->where('is_cancel','=',0)->where('companySystemID' ,'=', $input['companySystemID'])->count();
        //     if($is_pending_job_exist > 0)
        //     {
        //         return $this->sendError('There are Monthly Depreciation pending for confirmation and approval, thus this asset creation cannot be processed', 500);

        //     }

        // }
        $input = $this->convertArrayToValue($input);
        
        $input['COSTUNIT'] = floatval($input['COSTUNIT']);

        if(isset($input['isCurrencySame']) && $input['isCurrencySame']==true){

            if(isset($input['costUnitRpt'])){
                if($input['costUnitRpt'] > 0){
                    $input['COSTUNIT'] = $input['costUnitRpt'];
                }
            }

            if(isset($input['accumulated_depreciation_amount_rpt'])){
                if($input['accumulated_depreciation_amount_rpt'] > 0){
                    $input['accumulated_depreciation_amount_lcl'] = $input['accumulated_depreciation_amount_rpt'];
                }
            }

            if(isset($input['salvage_value_rpt'])){
                if($input['salvage_value_rpt']> 0){
                    $input['salvage_value'] = $input['salvage_value_rpt'];
                }
            }
        }
        
        if(doubleval($input['salvage_value_rpt']) >  (doubleval($input['costUnitRpt']))) {
            return $this->sendError("Salvage Value Cannot be greater than Unit Price", 500);
        }

        if(doubleval($input['salvage_value_rpt']) < 0) {
            return $this->sendError("Salvage value cannot be less than Zero", 500);
        }

        if($input['assetType'] == 1){
            if(empty($input['depMonth']) || $input['depMonth'] == 0){
                return $this->sendError("Life time in Years cannot be Blank or Zero, update the lifetime of the asset to proceed", 500);
            }
        } else {
            if(isset($input['depMonth']) && $input['depMonth'] == ''){
                $input['depMonth'] = 0;
            }
        }

        if(isset($input['COSTUNIT']) && $input['COSTUNIT'] > 0 ){
            if(isset($input['costUnitRpt']) && $input['costUnitRpt'] <= 0 ){
                return $this->sendError('Unit Price(Rpt) can’t be Zero when Unit Price(Local) has a value',500);
            }
        }

        if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] > 0){
            if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] <= 0 ){
                return $this->sendError('Acc. Depreciation(Rpt) can’t be Zero when Acc. Depreciation (Local) has a value',500);
            }
        }

        if(isset($input['salvage_value']) && $input['salvage_value'] > 0){
            if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] <= 0 ){
                return $this->sendError('Residual Value(Rpt) can’t be Zero when Residual Value(Local) has a value',500);
            }
        }


        if(isset($input['costUnitRpt']) && $input['costUnitRpt'] > 0 ){
            if(isset($input['COSTUNIT']) && $input['COSTUNIT'] <= 0 ){
                return $this->sendError('Unit Price(Local) can’t be Zero when Unit Price(Rpt) has a value',500);
            }
        }

        if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] > 0){
            if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] <= 0 ){
                return $this->sendError('Acc. Depreciation(Local) can’t be Zero when Acc. Depreciation (Rpt) has a value',500);
            }
        }

        if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] > 0){
            if(isset($input['salvage_value']) && $input['salvage_value'] <= 0 ){
                return $this->sendError('Residual Value(Local) can’t be Zero when Residual Value(Rpt) has a value',500);
            }
        }

        
        DB::beginTransaction();
        try {
            $messages = [
                'dateDEP.after_or_equal' => 'Depreciation Date cannot be less than Date aquired',
                'documentDate.before_or_equal' => 'Document Date cannot be greater than DEP Date',
                'faUnitSerialNo.unique' => 'The FA Serial-No has already been taken',
                'AUDITCATOGARY.required' => 'Audit Category is required',
            ];
            $validator = \Validator::make($request->all(), [
                'dateAQ' => 'required|date',
                'AUDITCATOGARY' => 'required',
                'dateDEP' => 'required|date|after_or_equal:dateAQ',
                'documentDate' => 'required|date|before_or_equal:dateDEP',
                'faUnitSerialNo' => 'required|unique:erp_fa_asset_master',
            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if (isset($input['itemPicture'])) {
                if ($itemImgaeArr[0]['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                }
            }

            $input['serviceLineSystemID'] = $input["serviceLineSystemID"];
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            if ($segment) {
                $input['serviceLineCode'] = $segment->ServiceLineCode;
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $department = DepartmentMaster::find($input['departmentSystemID']);
            if ($department) {
                $input['departmentID'] = $department->DepartmentID;
            }

            if (isset($input['postToGLYN'])) {
                if ($input['postToGLYN']) {
                    $chartOfAccount = ChartOfAccount::find($input['postToGLCodeSystemID']);
                    if (!empty($chartOfAccount)) {
                        $input['postToGLCode'] = $chartOfAccount->AccountCode;
                    }
                    $input['postToGLYN'] = 1;
                } else {
                    $input['postToGLYN'] = 0;
                }
            } else {
                $input['postToGLYN'] = 0;
            }

            $input["documentSystemID"] = 22;
            $input["documentID"] = 'FA';

            if (isset($input['dateAQ'])) {
                if ($input['dateAQ']) {
                    $input['dateAQ'] = new Carbon($input['dateAQ']);
                }
            }

            if (isset($input['dateDEP'])) {
                if ($input['dateDEP']) {
                    $input['dateDEP'] = new Carbon($input['dateDEP']);
                }
            }

            if (isset($input['accumulated_depreciation_date'])) {
                if ($input['accumulated_depreciation_date']) {
                    $input['accumulated_depreciation_date'] = new Carbon($input['accumulated_depreciation_date']);
                }
            }

            if (isset($input['lastVerifiedDate'])) {
                if ($input['lastVerifiedDate']) {
                    $input['lastVerifiedDate'] = new Carbon($input['lastVerifiedDate']);
                }
            }

            if (isset($input['documentDate'])) {
                if ($input['documentDate']) {
                    $input['documentDate'] = new Carbon($input['documentDate']);
                }
            }

            $lastSerialNumber = 1;
            $lastSerial = FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $auditCategory = isset($input['AUDITCATOGARY']) ? $input['AUDITCATOGARY'] : null;
            $documentCodeData = DocumentCodeGenerate::generateAssetCode($auditCategory, $input['companySystemID'], $input['serviceLineSystemID'],$input['faCatID'],$input['faSubCatID']);
            if ($documentCodeData['status']) {
                $documentCode = $documentCodeData['documentCode'];
                $searchDocumentCode = str_replace("\\", "\\\\", $documentCode);
                $checkForDuplicateCode = FixedAssetMaster::where('faCode', $searchDocumentCode)
                                                         ->first();

                if ($checkForDuplicateCode) {
                    return $this->sendError("Asset code is already found.", 500);
                }

                $input["serialNo"] = null;
            } else {
                return $this->sendError("Asset code is not configured.", 500);
                // $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                // $input["serialNo"] = $lastSerialNumber;
            }

            $input["faCode"] = $documentCode;
            $input["faBarcode"] = $documentCode;

            if(isset($input['isCurrencySame']) && $input['isCurrencySame'] == true) {
                if ($input['costUnitRpt']) {
                    $input['COSTUNIT'] = $input['costUnitRpt'];
                }
                if ($input['salvage_value_rpt']) {
                    $input['salvage_value'] = $input['salvage_value_rpt'];
                }
                if ($input['accumulated_depreciation_amount_rpt']) {
                    $input['accumulated_depreciation_amount_lcl'] = $input['accumulated_depreciation_amount_rpt'];
                }
            }


            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $input['createdDateAndTime'] = date('Y-m-d H:i:s');
            unset($input['itemPicture']);


 
           $fixedAssetMasters = $this->fixedAssetMasterRepository->create($input);

            if ($itemPicture) {
                $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                $extension = $itemImgaeArr[0]['filetype'];
                $data['itemPicture'] = $input['companyID'] . '_' . $input["documentID"] . '_' . $fixedAssetMasters['faID'] . '.' . $extension;

                $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
                $awsPolicy = Helper::checkPolicy($input['companySystemID'], 50);

                if ($awsPolicy) {
                    $path = $input['companyID']. '/G_ERP/' .$input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                } else {
                    $path = $input["documentID"] . '/' . $fixedAssetMasters['faID'] . '/' . $data['itemPicture'];
                }
                $data['itemPath'] = $path;
                Storage::disk($disk)->put($path, $decodeFile);
                $fixedAssetMasters = $this->fixedAssetMasterRepository->update($data, $fixedAssetMasters['faID']);
            }

 


            DB::commit();
            return $this->sendResponse($fixedAssetMasters, 'Fixed Asset Master saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Display the specified FixedAssetMaster",
     *      tags={"FixedAssetMaster"},
     *      description="Get FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        return $this->sendResponse($fixedAssetMaster->toArray(), 'Fixed Asset Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Update the specified FixedAssetMaster in storage",
     *      tags={"FixedAssetMaster"},
     *      description="Update FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FixedAssetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetMasterAPIRequest $request)
    {
        $input = $request->all();
        $itemImgaeArr = isset($input['itemImage']) ? $input['itemImage'] : array();
        $itemPicture  = isset($input['itemPicture']) ? $input['itemPicture'] : '';
        $input = array_except($request->all(), 'itemImage');
        $input = $this->convertArrayToValue($input);

        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }
     
        if(isset($input['accumulated_depreciation_amount_rpt']))
        {
            $accumulated_amount = $input['accumulated_depreciation_amount_rpt'];

            if($input['assetType'] == 1  && ($accumulated_amount > 0 && $accumulated_amount != null) )
            {
                $is_pending_job_exist = FixedAssetDepreciationMaster::where('approved','=',0)->where('is_acc_dep','=',0)->where('is_cancel','=',0)->where('companySystemID' ,'=', $input['companySystemID'])->count();
                if($is_pending_job_exist > 0)
                {
                    return $this->sendError('There are Monthly Depreciation pending for confirmation and approval, thus this asset creation cannot be processed', 500);
    
                }
    
            }
        }


        if(isset($input['assetType']) && $input['assetType'] == 1){
            if(empty($input['depMonth']) || $input['depMonth'] == 0){
                return $this->sendError("Life time in Years cannot be Blank or Zero, update the lifetime of the asset to proceed", 500);
            }
        } else {
            if(isset($input['depMonth']) && $input['depMonth'] == ''){
                $input['depMonth'] = 0;
            }
        }


        if(isset($input['salvage_value_rpt']))
        {
            if(doubleval($input['salvage_value_rpt']) >  (doubleval($fixedAssetMaster->costUnitRpt))) {
                return $this->sendError("Salvage Value Cannot be greater than Unit Price", 500);
            }
    
            if(doubleval($input['salvage_value_rpt']) < 0) {
                return $this->sendError("Salvage value cannot be less than Zero", 500);
            }
        }


        // check already approved
        if($fixedAssetMaster->approved == -1){
            // check restriction policy enabled
            $chkRestrctPolicy = Helper::checkRestrictionByPolicy($input['companySystemID'], 7);
            if(!$chkRestrctPolicy){
                return $this->sendError('Document already approved',500);
            }

            // check is there any depreciation
            $depAsset = FixedAssetDepreciationPeriod::ofAsset($id)->whereHas('master_by', function ($q) {
                $q->where('approved', -1);
            })->exists();
            if($depAsset){
                // check finance grouping input is changed
                if(isset($input['AUDITCATOGARY'])){
                    if($input['AUDITCATOGARY'] != $fixedAssetMaster->AUDITCATOGARY){
                        return $this->sendError('Document Already Have Depreciations. You Cannot Update Finance Grouping Details',500);
                    }
                }
            }


            // When Changing the Serviceline/Finance Grouping, check whether there is depreciation document without Confirmation or Approval.
            $depAssetNotApproved = FixedAssetDepreciationPeriod::ofAsset($id)->whereHas('master_by', function ($q) {
                $q->where('approved', 0);
            })->exists();
            if($depAssetNotApproved){
                // check finance grouping input is changed
                if((isset($input['AUDITCATOGARY']) && $input['AUDITCATOGARY'] != $fixedAssetMaster->AUDITCATOGARY) || (isset($input['serviceLineSystemID']) && $input['serviceLineSystemID'] != $fixedAssetMaster->serviceLineSystemID)){
                    return $this->sendError('Asset has been pulled to open depreciation document. Approve/Delete the document & try again',500);
                }

            }
        }

        $fixedAssetMasterOld = $fixedAssetMaster->toArray();
        DB::beginTransaction();
        try {

            $messages = [
                'dateDEP.after_or_equal' => 'Depreciation Date cannot be less than Date aqquired',
                'documentDate.before_or_equal' => 'Document Date cannot be greater than DEP Date',
                'faUnitSerialNo.unique' => 'The FA Serial-No has already been taken',
                'faBarcode.unique' => 'The Barcode has already been taken',
            ];
            $validator = \Validator::make($request->all(), [
                'dateAQ' => 'required|date',
                'dateDEP' => 'required|date|after_or_equal:dateAQ',
                'documentDate' => 'required|date|before_or_equal:dateDEP',
                'faUnitSerialNo' => ['required',Rule::unique('erp_fa_asset_master')->ignore($id, 'faID')],
                'faBarcode' => ['required', Rule::unique('erp_fa_asset_master')->ignore($id, 'faID')],

            ], $messages);


            if($fixedAssetMaster->approved != -1){
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
            }

            if (isset($input['itemPicture']) && $input['itemPicture']) {
                if ($itemImgaeArr && $itemImgaeArr[0] && $itemImgaeArr[0]['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                }
            }

            $department = DepartmentMaster::find($input['departmentSystemID']);
            if ($department) {
                $input['departmentID'] = $department->DepartmentID;
            }

            if (isset($input['postToGLYN'])) {
                if ($input['postToGLYN']) {
                    $chartOfAccount = ChartOfAccount::find($input['postToGLCodeSystemID']);
                    if (!empty($chartOfAccount)) {
                        $input['postToGLCode'] = $chartOfAccount->AccountCode;
                    }
                    $input['postToGLYN'] = 1;
                } else {
                    $input['postToGLYN'] = 0;
                    $input['postToGLCode'] = null;
                    $input['postToGLCodeSystemID'] = null;
                }
            } else {
                $input['postToGLYN'] = 0;
                $input['postToGLCode'] = null;
                $input['postToGLCodeSystemID'] = null;
            }

            if (isset($input['dateAQ'])) {
                if ($input['dateAQ']) {
                    $input['dateAQ'] = new Carbon($input['dateAQ']);
                }
            }

            if (isset($input['dateDEP'])) {
                if ($input['dateDEP']) {
                    $input['dateDEP'] = new Carbon($input['dateDEP']);
                }
            }

            if (isset($input['accumulated_depreciation_date'])) {
                if ($input['accumulated_depreciation_date']) {
                    $input['accumulated_depreciation_date'] = new Carbon($input['accumulated_depreciation_date']);
                }
            }


            if (isset($input['documentDate'])) {
                if ($input['documentDate']) {
                    $input['documentDate'] = new Carbon($input['documentDate']);
                }
            }

            if (isset($input['lastVerifiedDate'])) {
                if ($input['lastVerifiedDate']) {
                    $input['lastVerifiedDate'] = new Carbon($input['lastVerifiedDate']);
                }
            }

            if (isset($input['documentDate'])) {
                if ($input['documentDate']) {
                    $input['documentDate'] = new Carbon($input['documentDate']);
                }
            }

            if(isset($input['isCurrencySame']) && $input['isCurrencySame'] == true) {
                if (isset($input['costUnitRpt'])) {
                    if($input['costUnitRpt'] > 0 || $input['COSTUNIT'] != $input['costUnitRpt']) {
                        $input['COSTUNIT'] = $input['costUnitRpt'];
                    }
                }
                if (isset($input['salvage_value_rpt'])) {
                    if($input['salvage_value_rpt'] > 0 || $input['salvage_value'] != $input['salvage_value_rpt']) {
                        $input['salvage_value'] = $input['salvage_value_rpt'];
                    }
                }
                if (isset($input['accumulated_depreciation_amount_rpt'])) {
                    if($input['accumulated_depreciation_amount_rpt'] > 0 || $input['accumulated_depreciation_amount_lcl'] != $input['accumulated_depreciation_amount_rpt']){
                        $input['accumulated_depreciation_amount_lcl'] = $input['accumulated_depreciation_amount_rpt'];
                    }
                }
            }

            if(isset($input['serviceLineSystemID']) && $input['serviceLineSystemID'] > 0 && $input['serviceLineSystemID'] != $fixedAssetMaster->serviceLineSystemID){
                $segment = SegmentMaster::find($input['serviceLineSystemID']);
                if ($segment) {
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }
            }


            if(isset($input['COSTUNIT']) && $input['COSTUNIT'] > 0 ){
                if(isset($input['costUnitRpt']) && $input['costUnitRpt'] <= 0 ){
                    return $this->sendError('Unit Price(Rpt) can’t be Zero when Unit Price(Local) has a value',500);
                }
            }

            if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] > 0){
                if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] <= 0 ){
                    return $this->sendError('Acc. Depreciation(Rpt) can’t be Zero when Acc. Depreciation (Local) has a value',500);
                }
            }

            if(isset($input['salvage_value']) && $input['salvage_value'] > 0){
                if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] <= 0 ){
                    return $this->sendError('Residual Value(Rpt) can’t be Zero when Residual Value(Local) has a value',500);
                }
            }


            if(isset($input['costUnitRpt']) && $input['costUnitRpt'] > 0 ){
                if(isset($input['COSTUNIT']) && $input['COSTUNIT'] <= 0 ){
                    return $this->sendError('Unit Price(Local) can’t be Zero when Unit Price(Rpt) has a value',500);
                }
            }

            if(isset($input['accumulated_depreciation_amount_rpt']) && $input['accumulated_depreciation_amount_rpt'] > 0){
                if(isset($input['accumulated_depreciation_amount_lcl']) && $input['accumulated_depreciation_amount_lcl'] <= 0 ){
                    return $this->sendError('Acc. Depreciation(Local) can’t be Zero when Acc. Depreciation (Rpt) has a value',500);
                }
            }

            if(isset($input['salvage_value_rpt']) && $input['salvage_value_rpt'] > 0){
                if(isset($input['salvage_value']) && $input['salvage_value'] <= 0 ){
                    return $this->sendError('Residual Value(Local) can’t be Zero when Residual Value(Rpt) has a value',500);
                }
            }


            if ($fixedAssetMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

                $params = array('autoID' => $id, 'company' => $fixedAssetMaster->companySystemID, 'document' => $fixedAssetMaster->documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
                }
            }

            /** @var FixedAssetMaster $fixedAssetMaster */
            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
            $input["timestamp"] = date('Y-m-d H:i:s');
            unset($input['itemPicture']);

            if($fixedAssetMaster && $fixedAssetMaster->approved == -1){
                $amendableData = array_only($input,['departmentSystemID','departmentID','serviceLineSystemID','serviceLineCode','assetDescription','MANUFACTURE','COMMENTS','LOCATION','lastVerifiedDate','faCatID','faSubCatID','faSubCatID2','faSubCatID3','AUDITCATOGARY','COSTGLCODE','ACCDEPGLCODE','DEPGLCODE','DISPOGLCODE', 'accdepglCodeSystemID', 'costglCodeSystemID', 'depglCodeSystemID', 'dispglCodeSystemID']);

                $fixedAssetMaster = $this->fixedAssetMasterRepository->update($amendableData, $id);
            } else {
                $fixedAssetMaster = $this->fixedAssetMasterRepository->update($input, $id);
            }


            /*Activity log*/

            $employee = Helper::getEmployeeInfo();
            if($fixedAssetMaster && $fixedAssetMaster->approved == -1){

                $old_array = array_only($fixedAssetMasterOld,['departmentSystemID','departmentID','serviceLineSystemID','serviceLineCode','assetDescription','MANUFACTURE','COMMENTS','LOCATION','lastVerifiedDate','faCatID','faSubCatID','faSubCatID2','faSubCatID3','AUDITCATOGARY','COSTGLCODE','ACCDEPGLCODE','DEPGLCODE','DISPOGLCODE']);
                $modified_array = array_only($input,['departmentSystemID','departmentID','serviceLineSystemID','serviceLineCode','assetDescription','MANUFACTURE','COMMENTS','LOCATION','lastVerifiedDate','faCatID','faSubCatID','faSubCatID2','faSubCatID3','AUDITCATOGARY','COSTGLCODE','ACCDEPGLCODE','DEPGLCODE','DISPOGLCODE']);
                // update in to user log table
                foreach ($old_array as $key => $old){
                    if(isset($modified_array[$key]) && $old != $modified_array[$key]){
                        $description = $employee->empName." Amend Asset Costing - ".$key." (".$fixedAssetMaster->faID.") from ".$old." To ".$modified_array[$key]."";
                        UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$fixedAssetMaster->documentSystemID,$fixedAssetMaster->companySystemID,$fixedAssetMaster->faID,$description,$modified_array[$key],$old,$key);
                    }

                }
            }

            if ($itemPicture && isset($itemImgaeArr[0]['file'])) {
                $decodeFile = base64_decode($itemImgaeArr[0]['file']);
                $extension = $itemImgaeArr[0]['filetype'];
                $data['itemPicture'] = $fixedAssetMaster->companyID . '_' . $fixedAssetMaster->documentID . '_' . $fixedAssetMaster['faID'] . '.' . $extension;

                $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
                $awsPolicy = Helper::checkPolicy($input['companySystemID'], 50);

                if ($awsPolicy) {
                    $path = $fixedAssetMaster->companyID. '/G_ERP/' .$fixedAssetMaster->documentID . '/' . $fixedAssetMaster['faID'] . '/' . $data['itemPicture'];
                } else {
                    $path = $fixedAssetMaster->documentID . '/' . $fixedAssetMaster['faID'] . '/' . $data['itemPicture'];
                }

                $data['itemPath'] = $path;
                Storage::disk($disk)->put($path, $decodeFile);

                $fixedAssetMaster = $this->fixedAssetMasterRepository->update($data, $fixedAssetMaster['faID']);

                if($fixedAssetMaster->approved == -1)
                UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$fixedAssetMaster->documentSystemID,$fixedAssetMaster->companySystemID,$fixedAssetMaster->faID,$employee->empName." Amend Asset Costing (".$fixedAssetMaster->faID.") itemPicture",$path,$path,'itemPicture');
            }

            DB::commit();

            return $this->sendResponse($fixedAssetMaster->toArray(), 'FixedAssetMaster updated successfully');

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getLine().$exception->getMessage());
        }


    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetMasters/{id}",
     *      summary="Remove the specified FixedAssetMaster from storage",
     *      tags={"FixedAssetMaster"},
     *      description="Delete FixedAssetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetMaster->delete();

        return $this->sendResponse($id, 'Fixed Asset Master deleted successfully');
    }


    public function getAllocationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $companyCurrency = \Helper::companyCurrency($companyId);

        $department = DepartmentMaster::showInCombo()->get();

        $serviceline = SegmentMaster::isActive()->ofCompany($subCompanies)->get();

        $assetType = AssetType::all();

        $assetFinanceCategory = AssetFinanceCategory::all();

        foreach($assetFinanceCategory as $asf) {
            $asf->financeCatDescription = htmlspecialchars_decode($asf->financeCatDescription);
        }
        
        $checkUnqieStatusOfAssetCodeFormula = AssetFinanceCategory::whereNotNull('formula')
                                                                  ->get()
                                                                  ->pluck('formula')
                                                                  ->toArray();


        $fixedAssetCategory = FixedAssetCategory::ofCompany($subCompanies)->get();

        $insuranceType = InsurancePolicyType::all();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->whereIN('companySystemID', $subCompanies)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $location = Location::all();

        $output = array(
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'companyCurrency' => $companyCurrency,
            'department' => $department,
            'serviceline' => $serviceline,
            'assetType' => $assetType,
            'assetFinanceCategory' => $assetFinanceCategory,
            'fixedAssetCategory' => $fixedAssetCategory,
            'supplier' => $supplier,
            'location' => $location,
            'auditCategoryEditable' => (count(array_unique($checkUnqieStatusOfAssetCodeFormula)) == 1) ? true : false,
            'insuranceType' => $insuranceType
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getFixedAssetSubCat(Request $request)
    {
        $faCatID = $request->faCatID;
        $faCatID = (array)$faCatID;
        $faCatID= collect($faCatID)->pluck('id');
        $subCategory = FixedAssetCategorySub::ofCompany([$request->companySystemID])->byFaCatIDMultiSelect([$request->faCatID])->get();
        return $this->sendResponse($subCategory, 'Record retrieved successfully');
    }

    public function getFinanceGLCode(Request $request)
    {
        $subCategory = AssetFinanceCategory::with(['costaccount', 'accdepaccount', 'depaccount', 'disaccount'])->find($request->faFinanceCatID);
        return $this->sendResponse($subCategory, 'Record retrieved successfully');
    }

    public function getFAGrvDetailsByID(Request $request)
    {
        $subCategory = GRVDetails::with(['grv_master' => function($query){
            $query->with(['segment_by','supplier_by','location_by' => function($query){
                $query->with(['location']);
            }]);
        }, 'item_by' => function($query) {
            $query->with(['asset_category']);
        }

        ])->find($request->grvDetailsID);
        return $this->sendResponse($subCategory, 'Record retrieved successfully');
    }


    public function getAllAllocationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $assetAllocation = $this->fixedAssetMasterRepository->fixedAssetMasterListQuery($request, $input, $search);

        return \DataTables::eloquent($assetAllocation)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('grvDetailsID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAllCostingByCompany(Request $request)
    {
        $input = $request->all();


        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved','auditCategory','mainCategory','subCategory','assetTypeID'));
        $isDeleted = (isset($input['is_deleted']) && $input['is_deleted']==1)?1:0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetCositng = FixedAssetMaster::with(['category_by', 'sub_category_by', 'finance_category','asset_type'])->ofCompany($subCompanies);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCositng->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('mainCategory', $input)) {
            if ($input['mainCategory']) {
                $assetCositng->where('faCatID', $input['mainCategory']);
            }
        }

        if (array_key_exists('subCategory', $input)) {
            if ($input['subCategory']) {
                $assetCositng->where('faSubCatID', $input['subCategory']);
            }
        }

        if (array_key_exists('assetTypeID', $input)) {
            if ($input['assetTypeID']) {
                $assetCositng->where('assetType', $input['assetTypeID']);
            }
        } 

        if (array_key_exists('auditCategory', $input)) {
            if ($input['auditCategory']) {
                $assetCositng->where('AUDITCATOGARY', $input['auditCategory']);
            }
        }

        // get only deleted
        if($isDeleted==1){
            $assetCositng->onlyTrashed();
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%")
                    ->orWhere('docOrigin', 'LIKE', "%{$search}%")
                    ->orWhere('faUnitSerialNo', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAssetCostingByID($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->with(['confirmed_by', 'group_to', 'posttogl_by', 'disposal_by','supplier','department'])->findWithoutFail($id);
        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $fixedAssetCosting = FixedAssetCost::with(['localcurrency', 'rptcurrency'])->ofFixedAsset($id)->get();
        $groupedAsset = $this->fixedAssetMasterRepository->findWhere(['groupTO' => $id, 'approved' => -1]);
        $depAsset = FixedAssetDepreciationPeriod::ofAsset($id)->with('master_by')->whereHas('master_by', function ($q) {
            $q->where('approved', -1);
        })->get();
        $insurance = FixedAssetInsuranceDetail::with(['policy_by', 'location_by'])->ofAsset($id)->get();

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }
        $financeCat = AssetFinanceCategory::find($fixedAssetMaster->AUDITCATOGARY);
        $output = ['isAuditEnabled' => $financeCat->enableEditing,'fixedAssetMaster' => $fixedAssetMaster, 'fixedAssetCosting' => $fixedAssetCosting, 'groupedAsset' => $groupedAsset, 'depAsset' => $depAsset, 'insurance' => $insurance];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');
    }

     public function assetCostingForPrint(Request $request)
    {
        $input = $request->all();
        
        $fixedAssetMaster = $this->fixedAssetMasterRepository->with(['confirmed_by', 'group_to', 'posttogl_by', 'disposal_by','supplier','department', 'departmentmaster', 'category_by', 'sub_category_by', 'sub_category_by2', 'sub_category_by3', 'location', 'assettypemaster', 'finance_category'])->findWithoutFail($input['id']);
        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $output = ['fixedAssetMaster' => $fixedAssetMaster];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');
    }

    function assetCostingReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['faID'];
            $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);
            $emails = array();
            if (empty($fixedAssetMaster)) {
                return $this->sendError('Fixed Asset Master not found');
            }


            if ($fixedAssetMaster->approved == -1) {
                return $this->sendError('You cannot reopen this Asset costing it is already fully approved');
            }

            if ($fixedAssetMaster->RollLevForApp_curr > 1) {
                return $this->sendError('You cannot reopen this Asset costing it is already partially approved');
            }

            if ($fixedAssetMaster->confirmedYN == 0) {
                return $this->sendError('You cannot reopen this Asset costing, it is not confirmed');
            }

            $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
                'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->fixedAssetMasterRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $fixedAssetMaster->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $fixedAssetMaster->faCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $fixedAssetMaster->faCode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $fixedAssetMaster->companySystemID)
                ->where('documentSystemCode', $fixedAssetMaster->faID)
                ->where('documentSystemID', $fixedAssetMaster->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $fixedAssetMaster->companySystemID)
                        ->where('documentSystemID', $fixedAssetMaster->documentSystemID)
                        ->first();

                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => 'Policy not found for this document'];
                    }

                    $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                        ->where('companySystemID', $documentApproval->companySystemID)
                        ->where('documentSystemID', $documentApproval->documentSystemID);

                    $approvalList = $approvalList
                        ->with(['employee'])
                        ->groupBy('employeeSystemID')
                        ->get();

                    foreach ($approvalList as $da) {
                        if ($da->employee) {
                            $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                                'companySystemID' => $documentApproval->companySystemID,
                                'docSystemID' => $documentApproval->documentSystemID,
                                'alertMessage' => $subject,
                                'emailAlertMessage' => $body,
                                'docSystemCode' => $documentApproval->documentSystemCode);
                        }
                    }

                    $sendEmail = \Email::sendEmail($emails);
                    if (!$sendEmail["success"]) {
                        return ['success' => false, 'message' => $sendEmail["message"]];
                    }
                }
            }

            DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $fixedAssetMaster->companySystemID)
                ->where('documentSystemID', $fixedAssetMaster->documentSystemID)
                ->delete();

            /*Audit entry*/
            AuditTrial::createAuditTrial($fixedAssetMaster->documentSystemID,$id,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($fixedAssetMaster->toArray(), 'Asset Costing reopened successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getCostingApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetCost = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_asset_master.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode',
                'erp_fa_category.catDescription as catDescription',
                'erp_fa_categorysub.catDescription as subCatDescription'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [22])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_fa_asset_master', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'faID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_fa_asset_master.companySystemID', $companyId)
                    ->where('erp_fa_asset_master.approved', 0)
                    ->where('erp_fa_asset_master.confirmedYN', 1)
                    ->whereNull('erp_fa_asset_master.deleted_at');
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_fa_category', 'erp_fa_category.faCatID', 'erp_fa_asset_master.faCatID')
            ->leftJoin('erp_fa_categorysub', 'erp_fa_categorysub.faCatSubID', 'erp_fa_asset_master.faSubCatID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [22])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $assetCost = [];
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getCostingApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $assetCost = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_asset_master.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode',
                'erp_fa_category.catDescription as catDescription',
                'erp_fa_categorysub.catDescription as subCatDescription')
            ->join('erp_fa_asset_master', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'faID')
                    ->where('erp_fa_asset_master.companySystemID', $companyId)
                    ->where('erp_fa_asset_master.confirmedYN', 1)
                    ->whereNull('erp_fa_asset_master.deleted_at');
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_fa_category', 'erp_fa_category.faCatID', 'erp_fa_asset_master.faCatID')
            ->leftJoin('erp_fa_categorysub', 'erp_fa_categorysub.faCatSubID', 'erp_fa_asset_master.faSubCatID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [22])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAssetCostingMaster(Request $request)
    {
        $input = $request->all();

        $output = $this->fixedAssetMasterRepository
            ->with(['confirmed_by', 'approved_by' => function ($query) {
                $query->with('employee');
                $query->where('documentSystemID', 22);
            }, 'created_by', 'modified_by','audit_trial.modified_by'])->findWithoutFail($input['faID']);

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function generateAssetInsuranceReport(Request $request)
    {

        $input = $request->all();
        //$input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetInsurance = $this->assetInsuranceReport($input, $search);

        return \DataTables::of($assetInsurance)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('erp_fa_asset_master.faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function exportAssetInsuranceReport(Request $request)
    {

        $type = $request->type;
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        //$input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));
        $search = $request->input('search.value');
        $assetInsurance = $this->assetInsuranceReport($input, $search)->orderBy('erp_fa_asset_master.faID', $sort)->get();
        if ($assetInsurance) {
            $x = 0;
            foreach ($assetInsurance as $val) {
                $data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Asset Type'] = $val->AssetType;
                $data[$x]['Category'] = $val->Category;
                $data[$x]['Asset Code'] = $val->AssetCode;
                $data[$x]['Asset Description'] = $val->AssetDescription;
                $data[$x]['Serial Number'] = $val->SerialNumber;
                $data[$x]['Date AQ'] = \Helper::dateFormat($val->dateAQ);
                $data[$x]['Date DEP'] = \Helper::dateFormat($val->dateDEP);
                $data[$x]['DEP Percentage'] = $val->DEPpercentage;
                $data[$x]['Cost Local'] = number_format($val->CostLocal, 3);
                $data[$x]['Dep Local'] = number_format($val->DepLocal, 3);
                $data[$x]['Cost Rpt'] = number_format($val->CostRpt, 3);
                $data[$x]['Dep Rpt'] = number_format($val->DepRpt, 3);
                $data[$x]['Departmentt'] = $val->department;
                $data[$x]['Policy Type'] = $val->policyType;
                $data[$x]['Policy Number'] = $val->policyNumber;
                $data[$x]['Date From'] = \Helper::dateFormat($val->dateFrom);
                $data[$x]['Date To'] = \Helper::dateFormat($val->dateTo);
                $data[$x]['Insurer Name'] = $val->insurerName;
                $x++;
            }
        } else {
            $data = array();
        }


        $fileName = 'asset_insurance_report';
        $path = 'asset/report/asset_insurance_report/excel/';
        $companyMaster = Company::find(isset($request->companyId)?$request->companyId: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $basePath = CreateExcel::process($data,$type,$fileName,$path, $detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }

        
    }

    public function assetInsuranceReport($input, $search)
    {
        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        if (array_key_exists('asOfDate', $input) && $input['asOfDate']) {
            $asOfDate = new Carbon($input['asOfDate']);
        } else {
            $asOfDate = Carbon::now();
        }

        $asOfDateFormat = $asOfDate->format('Y-m-d');

        $assetInsurance = FixedAssetMaster::select(DB::raw("erp_fa_asset_master.companyID,
                                                            erp_fa_asset_master.faID,
                                                            erp_fa_asset_master.faCode AS AssetCode,
                                                            erp_fa_asset_master.assetDescription AS AssetDescription,
                                                            erp_fa_asset_master.faUnitSerialNo AS SerialNumber,
                                                            erp_fa_asset_master.dateAQ,
                                                            erp_fa_asset_master.dateDEP, 
                                                            erp_fa_asset_master.DEPpercentage,
                                                            erp_fa_asset_master.COSTUNIT AS CostLocal,
                                                            erp_fa_asset_master.costUnitRpt AS CostRpt,
                                                            erp_fa_assettype.typeDes AS AssetType,
                                                            erp_fa_financecategory.financeCatDescription AS Category,
                                                            serviceline.ServiceLineDes AS department,
                                                            erp_fa_insurancepolicytypes.policyDescription AS policyType,
                                                            erp_fa_insurancedetails.policyNumber,
                                                            erp_fa_insurancedetails.dateOfInsurance AS dateFrom,
                                                            erp_fa_insurancedetails.dateOfExpiry AS dateTo,
                                                            erp_fa_insurancedetails.insurerName,
                                                            dep.depLocal AS DepLocal,
                                                            dep.depRpt AS DepRpt"))
            ->whereIn('erp_fa_asset_master.companySystemID', $subCompanies)
            ->where('erp_fa_asset_master.approved', -1)
            // ->where('erp_fa_asset_master.DIPOSED', 0)
            ->whereDate('erp_fa_asset_master.dateAQ', '<=', $asOfDate)
            ->leftJoin('erp_fa_assettype', 'erp_fa_assettype.typeID', '=', 'erp_fa_asset_master.assetType')
            ->leftJoin('erp_fa_financecategory', 'erp_fa_financecategory.faFinanceCatID', '=', 'erp_fa_asset_master.AUDITCATOGARY')
            ->leftJoin('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_fa_asset_master.serviceLineSystemID')
            ->leftJoin('erp_fa_insurancedetails', 'erp_fa_insurancedetails.faID', '=', 'erp_fa_asset_master.faID')
            ->leftJoin('erp_fa_insurancepolicytypes', 'erp_fa_insurancepolicytypes.insurancePolicyTypesID', '=', 'erp_fa_insurancedetails.policy')
            ->leftJoin(DB::raw('(SELECT
                        erp_fa_assetdepreciationperiods.faID,
                        sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depLocal,
                        sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depRpt 
                        FROM
                            erp_fa_depmaster
                            INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID 
                        WHERE
                            erp_fa_depmaster.approved = - 1 
                            AND DATE(erp_fa_depmaster.depDate) <= ' . $asOfDateFormat . '
                        GROUP BY
                        erp_fa_assetdepreciationperiods.faID) as dep'),
                function ($join) {
                    $join->on('erp_fa_asset_master.faID', '=', 'dep.faID');
                });


        /*if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetInsurance = $assetInsurance->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }*/

        return $assetInsurance;
    }


    public function getAssetCostingViewByFaID($id)
    {
        /** @var FixedAssetMaster $fixedAssetMaster */

        $fixedAssetMaster = $this->fixedAssetMasterRepository->with(['confirmed_by', 'group_to', 'department', 'departmentmaster', 'assettypemaster', 'supplier', 'finance_category', 'category_by', 'sub_category_by', 'sub_category_by2', 'sub_category_by2'])->findWithoutFail($id);
        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }
        $fixedAssetCosting = FixedAssetCost::with(['localcurrency', 'rptcurrency'])->ofFixedAsset($id)->get();
        $groupedAsset = $this->fixedAssetMasterRepository->findWhere(['groupTO' => $id, 'approved' => -1]);
        $depAsset = FixedAssetDepreciationPeriod::ofAsset($id)->get();
        $insurance = FixedAssetInsuranceDetail::with(['policy_by', 'location_by'])->ofAsset($id)->get();

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found');
        }

        $output = ['fixedAssetMaster' => $fixedAssetMaster, 'fixedAssetCosting' => $fixedAssetCosting, 'groupedAsset' => $groupedAsset, 'depAsset' => $depAsset, 'insurance' => $insurance];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');

    }


    function referBackCosting(Request $request)
    {

        DB::beginTransaction();
        try {
            $input = $request->all();
            $faID = $input['faID'];

            $fixedAsset = $this->fixedAssetMasterRepository->findWithoutFail($faID);
            if (empty($fixedAsset)) {
                return $this->sendError('Fixed Asset Master not found');
            }

            if ($fixedAsset->refferedBackYN != -1) {
                return $this->sendError('You cannot amend this document');
            }

            $fixedAssetArray = $fixedAsset->toArray();

            $storefixedAssetHistory = FixedAssetMasterReferredHistory::create($fixedAssetArray);

            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $faID)
                ->where('companySystemID', $fixedAsset->companySystemID)
                ->where('documentSystemID', $fixedAsset->documentSystemID)
                ->get();

            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $fixedAsset->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();

            $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

            $deleteApproval = DocumentApproved::where('documentSystemCode', $faID)
                ->where('companySystemID', $fixedAsset->companySystemID)
                ->where('documentSystemID', $fixedAsset->documentSystemID)
                ->delete();

            if ($deleteApproval) {
                $fixedAsset->refferedBackYN = 0;
                $fixedAsset->confirmedYN = 0;
                $fixedAsset->confirmedByEmpSystemID = null;
                $fixedAsset->confirmedByEmpID = null;
                $fixedAsset->confirmedDate = null;
                $fixedAsset->RollLevForApp_curr = 1;
                $fixedAsset->save();
            }

            DB::commit();
            return $this->sendResponse($fixedAsset->toArray(), 'Fixed asset amended successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getPostToGLAccounts(request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = ChartOfAccountsAssigned::where('companySystemID', $companyID)
            ->where('isAssigned', -1)
            ->where('isActive', 1);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('AccountCode', 'LIKE', "%{$search}%")
                    ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function assetCostingUpload(request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['assetExcelUpload'];
            $input = array_except($request->all(), 'assetExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $finalData = [];
            $formatChk = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->first()->toArray();

            if (count($formatChk) > 0) {
                if (!isset($formatChk['asset_description']) || !isset($formatChk['serial_no'])) {
                    return $this->sendError('Uploaded data format is invalid', 500);
                }
            }

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('asset_description', 'serial_no'))->get()->toArray();

            $uploadSerialNumber = array_filter(collect($record)->pluck('serial_no')->toArray());
            $uploadSerialNumberUnique = array_unique($uploadSerialNumber);

            $fixedAsset = FixedAssetMaster::ofCompany([$input['companySystemID']])->pluck('faUnitSerialNo')->toArray();

            if (count($record) > 0) {
                // check for duplicate serial number in db
                $chkDuplicateAssetInDB = array_intersect($fixedAsset,$uploadSerialNumber);

                // check for duplicate serial number in uploaded asset
                if ((count($uploadSerialNumber) != count($uploadSerialNumberUnique)) || count($chkDuplicateAssetInDB) > 0) {
                    return $this->sendError('The uploaded assets has duplicate serial numbers, please check and re-upload', 500);
                }

                $lastSerial = FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }

                $department = DepartmentMaster::find($input['departmentSystemID']);
                if ($department) {
                    $input['departmentID'] = $department->DepartmentID;
                }

                $segment = SegmentMaster::find($input['serviceLineSystemID']);
                if ($segment) {
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }

                $company = Company::find($input['companySystemID']);
                if ($company) {
                    $input['companyID'] = $company->CompanyID;
                }

                foreach ($record as $val) {
                    if ($val['asset_description'] || $val['serial_no']) {
                        $data = [];
                        $documentCode = ($input['companyID'] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                        $data['assetDescription'] = $val['asset_description'];
                        $data['faUnitSerialNo'] = $val['serial_no'];
                        $data['serialNo'] = $lastSerialNumber;
                        $data['faCode'] = $documentCode;
                        $data['supplierIDRentedAsset'] = $input['supplierID'];
                        $data['faCatID'] = $input['faCatID'];
                        $data['faSubCatID'] = $input['faSubCatID'];
                        $data['faSubCatID2'] = $input['faSubCatID2'];
                        $data['faSubCatID3'] = $input['faSubCatID3'];
                        $data['faSubCatID3'] = $input['faSubCatID3'];
                        $data['documentID'] = 'FA';
                        $data['documentSystemID'] = 22;
                        $data['companySystemID'] = $input['companySystemID'];
                        $data['companyID'] = $input['companyID'];
                        $data['serviceLineSystemID'] = $input['serviceLineSystemID'];
                        $data['serviceLineCode'] = $input['serviceLineCode'];
                        $data['departmentSystemID'] = $input['departmentSystemID'];
                        $data['departmentID'] = $input['departmentID'];
                        $data['dateAQ'] = NOW();
                        $data['MANUFACTURE'] = '-';
                        $data['assetType'] = 2;
                        $data['confirmedYN'] = 1;
                        $data['confirmedByEmpSystemID'] = \Helper::getEmployeeSystemID();
                        $data['confirmedByEmpID'] = \Helper::getEmployeeID();
                        $data['confirmedDate'] = NOW();
                        $data['approved'] = -1;
                        $data['approvedDate'] = NOW();
                        $data['postedDate'] = NOW();
                        $data['approvedByUserID'] = \Helper::getEmployeeID();
                        $data['approvedByUserSystemID'] = \Helper::getEmployeeSystemID();
                        $data['createdPcID'] = gethostname();
                        $data['createdUserID'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $data['timestamp'] = NOW();
                        $finalData[] = $data;
                        $lastSerialNumber++;
                    }
                }
            } else {
                return $this->sendError('No Records found!', 500);
            }

            if (count($finalData) > 0) {
                foreach (array_chunk($finalData, 500) as $t) {
                    FixedAssetMaster::insert($t);
                }
            }
            Storage::disk($disk)->delete('app/' . $originalFileName);
            DB::commit();
            return $this->sendResponse([], 'Assets uploaded successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
        //Storage::disk('local')->delete($originalFileName);

    }

    public function downloadAssetTemplate(Request $request)
    {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID']);
        if ($exists = Storage::disk($disk)->exists('asset_master_template/asset_upload_template.xlsx')) {
            return Storage::disk($disk)->download('asset_master_template/asset_upload_template.xlsx', 'asset_upload_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    public function exportAssetMaster(Request $request){
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'mainCategory', 'subCategory'));

        $type = $input['type'];

        $assetCositng = FixedAssetMaster::with(['category_by','sub_category_by','finance_category','asset_type','group_to','supplier','disposal_by','department','departmentmaster','confirmed_by','posttogl_by','sub_category_by2','sub_category_by3'])->where('companySystemID', $input['companyID']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCositng->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('mainCategory', $input)) {
            if ($input['mainCategory'] && !is_null($input['mainCategory'])) {
                $assetCositng->where('faCatID', $input['mainCategory']);
            }
        }

        if (array_key_exists('subCategory', $input)) {
            if ($input['subCategory'] && !is_null($input['subCategory'])) {
                $assetCositng->where('faSubCatID', $input['subCategory']);
            }
        }

        if (array_key_exists('auditCategory', $input)) {
            if ($input['auditCategory']) {
                $assetCositng->where('AUDITCATOGARY', $input['auditCategory']);
            }
        }

        $output = $assetCositng->orderBy('faID','desc')->get();

        if (count($output) > 0) {
            $x = 0;
            foreach ($output as $val) {
                $data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Department Code'] = $val->departmentID;
                $data[$x]['Department'] = $val->departmentmaster? $val->departmentmaster->DepartmentDescription:'';
                $data[$x]['ServiceLine Code'] = $val->serviceLineCode;
                $data[$x]['ServiceLine'] = $val->department?$val->department->ServiceLineDes:'';
                $data[$x]['FA Code'] = $val->faCode;
                $data[$x]['Asset Description'] = $val->assetDescription;
                $data[$x]['Serial No'] = $val->faUnitSerialNo;
                $data[$x]['Comments'] = $val->COMMENTS;
                $data[$x]['Manufacture'] = $val->MANUFACTURE;
                $data[$x]['Date Acquired'] = \Helper::dateFormat($val->dateAQ);
                $data[$x]['Dep Date Start'] = \Helper::dateFormat($val->dateDEP);
                $data[$x]['Life time in years'] = $val->depMonth;
                $data[$x]['Dep %'] = $val->DEPpercentage;
                $data[$x]['GRV No'] = $val->docOrigin;
                $data[$x]['Main Cat'] = $val->category_by?$val->category_by->catDescription:'';
                $data[$x]['Sub Cat'] = $val->sub_category_by?$val->sub_category_by->catDescription:'';
                $data[$x]['Sub Cat2'] = $val->sub_category_by2?$val->sub_category_by2->catDescription:'';
                $data[$x]['Sub Cat3'] = $val->sub_category_by3?$val->sub_category_by3->catDescription:'';
                $data[$x]['Audit Category'] = $val->finance_category?$val->finance_category->financeCatDescription:'';
                $data[$x]['Cost Account'] = $val->COSTGLCODE.' - '.$val->COSTGLCODEdes;
                $data[$x]['Acc Dep GL Code'] = $val->ACCDEPGLCODE.' - '.$val->ACCDEPGLCODEdes;
                $data[$x]['Dep GL Code'] = $val->DEPGLCODE.' - '.$val->DEPGLCODEdes;
                $data[$x]['Dis Po GL Code'] = $val->DISPOGLCODE.' - '.$val->DISPOGLCODEdes;
                $data[$x]['Post to GL Account'] = $val->posttogl_by?$val->posttogl_by->AccountCode .' - '.$val->posttogl_by->AccountDescription:'';
                $data[$x]['Asset Type'] = $val->asset_type?$val->asset_type->typeDes:'';
                $data[$x]['Supplier Code'] = $val->supplier?$val->supplier->primarySupplierCode:'';
                $data[$x]['Supplier Name'] = $val->supplier? $val->supplier->supplierName:'';
                $data[$x]['Disposed Date'] = \Helper::dateFormat($val->disposedDate);
                $data[$x]['Last Physical Verified Date'] = \Helper::dateFormat($val->lastVerifiedDate);
                $data[$x]['Unit Price(Local)'] = $val->COSTUNIT;
                $data[$x]['Unit Price(Rpt)'] = $val->costUnitRpt;

                if ($val->confirmedYN == 1) {
                    $data[$x]['Confirmed Status'] = 'Yes';
                } else {
                    $data[$x]['Confirmed Status'] = 'No';
                }
                $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->confirmedDate);
                $data[$x]['Confirmed By'] = $val->confirmed_by?$val->confirmed_by->empName:'';
                if ($val->approved == -1) {
                    $data[$x]['Approved Status'] = 'Yes';
                } else {
                    $data[$x]['Approved Status'] = 'No';
                }
                $data[$x]['Approved Date'] = \Helper::dateFormat($val->approvedDate);
                $x++;
            }
        } else {
            $data = array();
        }
        
        $fileName = 'asset_cosing';
        $path = 'asset/costing/asset_cosing/excel/';
        $basePath = CreateExcel::process($data,$type,$fileName,$path);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }



    }

    public function amendAssetCostingReview(Request $request)
    {
        $input = $request->all();

        $id = isset($input['id'])?$input['id']:0;

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = $this->fixedAssetMasterRepository->findWithoutFail($id);
        if (empty($masterData)) {
            return $this->sendError('Fixed Asset Master not found');
        }   

        $accumulated_amount = $masterData->accumulated_depreciation_amount_rpt;


        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this asset costing, it is not confirmed');
        }

        $isAccDepExists = FixedAssetDepreciationPeriod::where('faID',$id)->whereHas('master_by', function ($q) {
           $q->where('is_acc_dep',1)
                ->where('confirmedYN','=',0)
                ->where('is_cancel',0);
        });

        $isMonthlyExists = FixedAssetDepreciationPeriod::where('faID',$id)->whereHas('master_by', function ($q) {
            $q->where('is_acc_dep',0);
        })->count();

            if($isMonthlyExists > 0 )
            {
                return $this->sendError('This asset cannot be returned back to amend. Monthly Depreciation has already been generated for this asset');

            }
            
            if(!$isAccDepExists->exists() && $masterData->assetType == 1 && ($accumulated_amount > 0 && $accumulated_amount != null) )
            {
                return $this->sendError('This asset cannot be returned back to amend. An approved accumulated depreciation document is linked with this asset. Refer-back the Acc. depreciation and try again'); 
            }

     


            $emailBody = '<p>' . $masterData->faCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
            $emailSubject = $masterData->faCode . ' has been return back to amend';
    
            DB::beginTransaction();
            try {
    
                //sending email to relevant party
                if ($masterData->confirmedYN == 1) {
                    $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $id);
                }
    
                $documentApproval = DocumentApproved::where('companySystemID', $masterData->companySystemID)
                    ->where('documentSystemCode', $id)
                    ->where('documentSystemID', $masterData->documentSystemID)
                    ->get();
    
                foreach ($documentApproval as $da) {
                    if ($da->approvedYN == -1) {
                        $emails[] = array('empSystemID' => $da->employeeSystemID,
                            'companySystemID' => $masterData->companySystemID,
                            'docSystemID' => $masterData->documentSystemID,
                            'alertMessage' => $emailSubject,
                            'emailAlertMessage' => $emailBody,
                            'docSystemCode' => $id);
                    }
                }
    
                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return $this->sendError($sendEmail["message"], 500);
                }
    
                //deleting from approval table
                $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                    ->where('companySystemID', $masterData->companySystemID)
                    ->where('documentSystemID', $masterData->documentSystemID)
                    ->delete();
    
                //deleting from general ledger table
                $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
                    ->where('companySystemID', $masterData->companySystemID)
                    ->where('documentSystemID', $masterData->documentSystemID)
                    ->delete();
    
                // delete asset costing
                if(is_null($masterData->docOriginSystemCode)){
                    $fixedAssetCosting = FixedAssetCost::ofFixedAsset($id)->delete();
                }
    
                //deleting budget consumption
                $deletebudgetData = BudgetConsumedData::where('documentSystemCode', $id)
                    ->where('companySystemID', $masterData->companySystemID)
                    ->where('documentSystemID', $masterData->documentSystemID)
                    ->delete();
    
                // updating fields
                $masterData->confirmedYN = 0;
                $masterData->confirmedByEmpSystemID = null;
                $masterData->confirmedByEmpID = null;
                $masterData->confirmedDate = null;
                $masterData->RollLevForApp_curr = 1;
    
                $masterData->approved = 0;
                $masterData->approvedByUserSystemID = null;
                $masterData->approvedByUserID = null;
                $masterData->approvedDate = null;
                $masterData->postedDate = null;
                $masterData->save();
                
                if($masterData->assetType == 1 && ($accumulated_amount > 0 && $accumulated_amount != null))
                {
                    $depId = $isAccDepExists->first()->depMasterAutoID;
                    FixedAssetDepreciationMaster::where('depMasterAutoID', $depId)->update(['is_cancel'=>-1]);

                    FixedAssetDepreciationPeriod::where('depMasterAutoID', $depId)->delete();

                }
                AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');
    
                DB::commit();
                return $this->sendResponse($masterData->toArray(), 'Asset costing amend saved successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->sendError($exception->getMessage());
            }
        
      

        // checking document matched in depreciation
        // $depAsset = FixedAssetDepreciationPeriod::ofAsset($id)->whereHas('master_by', function ($q) {
        // })->count();

        // if($depAsset > 0){
        //     return $this->sendError('You cannot return back to amend this asset costing,Depreciation has been run for the particular asset');
        // }

    }

    public function assetCostingRemove(Request $request){
        $input = $request->all();
        $id = isset($input['id'])?$input['id']:0;
        $comment = isset($input['comment'])?$input['comment']:0;
        $employee = Helper::getEmployeeInfo();

        if($id == 0){
            return $this->sendError('ID not found',500);
        }

        if($comment == '' || $comment==null){
            return $this->sendError('Comment is required',500);
        }

        /** @var FixedAssetMaster $fixedAssetMaster */
        $fixedAssetMaster = $this->fixedAssetMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetMaster)) {
            return $this->sendError('Fixed Asset Master not found',500);
        }

        if($fixedAssetMaster->approved == -1){
            return $this->sendError('Approved asset cannot be deleted',500);
        }

        if($fixedAssetMaster->confirmedYN == 1){
            return $this->sendError('Confirmed asset cannot be deleted',500);
        }
        $fixedAssetMasterOld = $fixedAssetMaster;
        $fixedAssetMaster->deleteComment = $comment;
        $fixedAssetMaster->serialNo = 0;
        $fixedAssetMaster->faUnitSerialNo = null;
        $fixedAssetMaster->save();

        // soft delete
        $fixedAssetMaster->delete();

        // update grv details assetAllocationDoneYN,assetAllocatedQty
        if($fixedAssetMasterOld->docOriginDetailID){
            $grvDetails = GRVDetails::find($fixedAssetMasterOld->docOriginDetailID);
            $grvDetails->assetAllocatedQty = $grvDetails->assetAllocatedQty-1;
            $grvDetails->assetAllocationDoneYN = 0;
            $grvDetails->save();
        }

        //Should be removed from asset cost
        FixedAssetCost::where('faID',$fixedAssetMasterOld->faID)->delete();

        // add to user activity log
        UserActivityLogger::createUserActivityLogArray($employee->employeeSystemID,$fixedAssetMasterOld->documentSystemID,$fixedAssetMasterOld->companySystemID,$fixedAssetMasterOld->faID,$employee->empName." Delete Asset Costing (".$fixedAssetMasterOld->faID.")",'','','itemPicture');

        return $this->sendResponse($id, 'Fixed Asset Master deleted successfully');
    }

}
