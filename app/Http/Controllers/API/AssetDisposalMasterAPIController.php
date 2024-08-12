<?php
/**
 * =============================================
 * -- File Name : AssetDisposalMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD forAsset disposal master
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\helper\TaxService;
use App\Http\Requests\API\CreateAssetDisposalMasterAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalMasterAPIRequest;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalDetailReferred;
use App\Models\AssetDisposalMaster;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\AssetDisposalReferred;
use App\Models\AssetDisposalType;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CustomerAssigned;
use App\Models\SupplierCurrency;
use App\Models\CustomerMaster;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\FixedAssetMaster;
use App\Models\GeneralLedger;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\TaxVatCategories;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\AssetDisposalMasterRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Services\ValidateDocumentAmend;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetDisposalMasterController
 * @package App\Http\Controllers\API
 */
class AssetDisposalMasterAPIController extends AppBaseController
{
    /** @var  AssetDisposalMasterRepository */
    private $assetDisposalMasterRepository;

    public function __construct(AssetDisposalMasterRepository $assetDisposalMasterRepo)
    {
        $this->assetDisposalMasterRepository = $assetDisposalMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalMasters",
     *      summary="Get a listing of the AssetDisposalMasters.",
     *      tags={"AssetDisposalMaster"},
     *      description="Get all AssetDisposalMasters",
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
     *                  @SWG\Items(ref="#/definitions/AssetDisposalMaster")
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
        $this->assetDisposalMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->assetDisposalMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetDisposalMasters = $this->assetDisposalMasterRepository->all();

        return $this->sendResponse($assetDisposalMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_master')]));
    }

    /**
     * @param CreateAssetDisposalMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetDisposalMasters",
     *      summary="Store a newly created AssetDisposalMaster in storage",
     *      tags={"AssetDisposalMaster"},
     *      description="Store AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalMaster")
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
     *                  ref="#/definitions/AssetDisposalMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetDisposalMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'companyFinanceYearID' => 'required',
            'companyFinancePeriodID' => 'required',
            'narration' => 'required',
            'disposalType' => 'required',
            'disposalDocumentDate' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        } else {
            $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
            $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 9;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);

        $input['disposalDocumentDate'] = new Carbon($input['disposalDocumentDate']);

        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($input['disposalDocumentDate'] >= $monthBegin) && ($input['disposalDocumentDate'] <= $monthEnd)) {
        } else {
            return $this->sendError(trans('custom.disposal_date_is_not_within_financial_period'), 500);
        }

        $checkDisposalType = AssetDisposalType::where('disposalTypesID',$input['disposalType'])
                                                ->where('activeYN',1)
                                                ->first();

        if (empty($checkDisposalType)) {
            return $this->sendError(trans('custom.please_select_an_active_disposal_type'), 500);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
        }

        $toCompany = Company::find($input['toCompanySystemID']);
        if ($toCompany) {
            $input['toCompanyID'] = $toCompany->CompanyID;
        }

        $documentMaster = DocumentMaster::find($input['documentSystemID']);
        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $lastSerial = AssetDisposalMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }

        if ($companyFinanceYear["message"]) {
            $startYear = $companyFinanceYear["message"]['bigginingDate'];
            $finYearExp = Carbon::parse($startYear);
            $finYear = $finYearExp->year;
        } else {
            $finYear = date("Y");
        }
        if ($documentMaster) {
            $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['disposalDocumentCode'] = $documentCode;
        }
        $input['serialNo'] = $lastSerialNumber;
        $input['revenuePercentage'] = (float)$input['revenuePercentage'];
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $assetDisposalMasters = $this->assetDisposalMasterRepository->create($input);

        return $this->sendResponse($assetDisposalMasters->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_disposal_master')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetDisposalMasters/{id}",
     *      summary="Display the specified AssetDisposalMaster",
     *      tags={"AssetDisposalMaster"},
     *      description="Get AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalMaster",
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
     *                  ref="#/definitions/AssetDisposalMaster"
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
        /** @var AssetDisposalMaster $assetDisposalMaster */
        $assetDisposalMaster = $this->assetDisposalMasterRepository->with(['financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 41);
        },'audit_trial.modified_by','customer'])->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_master')]));
        }

        return $this->sendResponse($assetDisposalMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_disposal_master')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetDisposalMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetDisposalMasters/{id}",
     *      summary="Update the specified AssetDisposalMaster in storage",
     *      tags={"AssetDisposalMaster"},
     *      description="Update AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetDisposalMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetDisposalMaster")
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
     *                  ref="#/definitions/AssetDisposalMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetDisposalMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);

            $company_id = $input['companySystemID'];

            $masterData = AssetDisposalMaster::whereHas('disposal_type' , function ($query) use($company_id) {
                $query->whereHas('chartofaccount',function($query) use($company_id){
                    $query->whereHas('chartofaccount_assigned',function($query) use($company_id){
                        $query->where('companySystemID',$company_id);
                    });
                });
            })->find($id);

            if(!isset($masterData))
            {
                return $this->sendError('Chart of account for selected disposal type is not assigned to company', 500);
            }
           

         


            /** @var AssetDisposalMaster $assetDisposalMaster */
            $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

            if (empty($assetDisposalMaster)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_master')]));
            }

            $companySystemID = $assetDisposalMaster->companySystemID;
            $documentSystemID = $assetDisposalMaster->documentSystemID;

            $toCompany = Company::find($input['toCompanySystemID']);
            $toCompanyName = '';
            if ($toCompany) {
                $input['toCompanyID'] = $toCompany->CompanyID;
                $toCompanyName = $toCompany->CompanyName;
            }

            $input['disposalDocumentDate'] = new Carbon($input['disposalDocumentDate']);

            if ($assetDisposalMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

                $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
                if (!$companyFinanceYear["success"]) {
                    return $this->sendError($companyFinanceYear["message"], 500, ['type' => 'confirm']);
                } else {
                    $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                    $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
                }

                $inputParam = $input;
                $inputParam["departmentSystemID"] = 9;
                $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
                if (!$companyFinancePeriod["success"]) {
                    return $this->sendError($companyFinancePeriod["message"], 500, ['type' => 'confirm']);
                } else {
                    $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                    $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
                }

                unset($inputParam);

                $monthBegin = $input['FYPeriodDateFrom'];
                $monthEnd = $input['FYPeriodDateTo'];

                if (($input['disposalDocumentDate'] >= $monthBegin) && ($input['disposalDocumentDate'] <= $monthEnd)) {
                } else {
                    return $this->sendError(trans('custom.asset_disposal_date_is_not_within_financial_period'), 500, ['type' => 'confirm']);
                }

                $input['disposalType'] = isset($input['disposalType'])?$input['disposalType']:0;
                $checkDisposalType = AssetDisposalType::where('disposalTypesID',$input['disposalType'])
                                                       ->where('activeYN',1)
                                                       ->first();

                if (empty($checkDisposalType)) {
                    return $this->sendError(trans('custom.please_select_an_active_disposal_type'), 500, ['type' => 'confirm']);
                }

                $disposalDetailExist = AssetDisposalDetail::with(['asset_by' => function ($query) {
                    $query->with(['group_all_to']);
                }])->where('assetdisposalMasterAutoID', $id)->get();

                if (empty($disposalDetailExist)) {
                    return $this->sendError(trans('custom.asset_disposal_document_cannot_confirm_without_details'), 500, ['type' => 'confirm']);
                }


                if ($assetDisposalMaster->disposalType == 1) {

                    $checkGLIsAssigned = ChartOfAccountsAssigned::checkCOAAssignedStatus($checkDisposalType->chartOfAccountID, $assetDisposalMaster->companySystemID);
                    if (!$checkGLIsAssigned) {
                        return $this->sendError('Inter company sales chart of account is not assigned to - From company', 500);
                    }

                    //For customer check
                    $customermaster = CustomerMaster::where('companyLinkedToSystemID', $assetDisposalMaster->toCompanySystemID)->first();

                    if (empty($customermaster)) {
                        return $this->sendError(trans('custom.there_is_no_customer_created_to_the_selected_company_please_create_a_customer'), 500, ['type' => 'confirm']);
                    }

                    //if the customer is assigned
                    $customer = CustomerAssigned::select('*')->where('companySystemID', $assetDisposalMaster->companySystemID)->where('isAssigned', '-1')->where('customerCodeSystem', $customermaster->customerCodeSystem)->first();

                    if (empty($customer)) {
                        return $this->sendError(trans('custom.there_is_no_customer_assigned_to_the_selected_company_please_assign_the_customer'), 500, ['type' => 'confirm']);
                    }
                    //checking selected customer is active
                    $customer = CustomerAssigned::select('*')->where('companySystemID', $assetDisposalMaster->companySystemID)->where('isActive', '1')->where('customerCodeSystem', $customermaster->customerCodeSystem)->first();

                    if (empty($customer)) {
                        return $this->sendError(trans('custom.is_not_active', ['attribute' => trans('custom.assigned_customer')]), 500, ['type' => 'confirm']);
                    }

                    //For supplier companySystemID
                    $suppliermaster = SupplierMaster::where('companyLinkedToSystemID', $assetDisposalMaster->companySystemID)->first();

                    if (empty($suppliermaster)) {
                        return $this->sendError(trans('custom.there_is_no_supplier_created_to_the_selected_company_please_create_a_supplier'), 500, ['type' => 'confirm']);
                    }

                    //If the supplier is not assigned
                    $supplier = SupplierAssigned::select('*')->where('companySystemID', $assetDisposalMaster->toCompanySystemID)->where('isAssigned', '-1')->where('supplierCodeSytem', $suppliermaster->supplierCodeSystem)->first();

                    if (empty($supplier)) {
                        return $this->sendError(trans('custom.there_is_no_supplier_assigned_to_the_selected_company_please_assign_the_supplier'), 500, ['type' => 'confirm']);
                    }

                    //checking selected supplier is active
                    $supplier = SupplierAssigned::select('*')->where('companySystemID', $assetDisposalMaster->toCompanySystemID)->where('isActive', '1')->where('supplierCodeSytem', $suppliermaster->supplierCodeSystem)->first();

                    if (empty($supplier)) {
                        return $this->sendError(trans('custom.is_not_active', ['attribute' => trans('custom.assigned_supplier')]), 500, ['type' => 'confirm']);
                    }

                    $checkRevenueAc = SystemGlCodeScenarioDetail::getGlByScenario($assetDisposalMaster->companySystemID, $assetDisposalMaster->documentSystemID, "asset-disposal-inter-company-sales");

                    if (is_null($checkRevenueAc)) {
                        return $this->sendError('Please configure income from sales', 500);
                    }


                    $disposalDocumentDate = (new Carbon($assetDisposalMaster->disposalDocumentDate))->format('Y-m-d');

                    $fromCompanyFinanceYear = CompanyFinanceYear::where('companySystemID', $assetDisposalMaster->toCompanySystemID)
                        ->whereDate('bigginingDate', '<=', $disposalDocumentDate)
                        ->whereDate('endingDate', '>=', $disposalDocumentDate)
                        ->first();

                    if (!$fromCompanyFinanceYear) {
                        return $this->sendError("To company finance year is not found", 500, ['type' => 'confirm']);
                    }

                    $fromCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $assetDisposalMaster->toCompanySystemID)
                                                                    ->where('departmentSystemID', 10)
                                                                    ->where('companyFinanceYearID', $fromCompanyFinanceYear->companyFinanceYearID)
                                                                    ->whereDate('dateFrom', '<=', $disposalDocumentDate)
                                                                    ->whereDate('dateTo', '>=', $disposalDocumentDate)
                                                                    ->first();

                    if (!$fromCompanyFinancePeriod) {
                        return $this->sendError("To company finance period is not found", 500, ['type' => 'confirm']);
                    }

                    $fromCompanyData = Company::find($company_id);

                    if ($fromCompanyData) {
                        $checkSupplierCurrency = SupplierCurrency::where('supplierCodeSystem', $suppliermaster->supplierCodeSystem)
                                                                 ->where('currencyID', $fromCompanyData->reportingCurrency)
                                                                 ->where('isAssigned', -1)
                                                                 ->first();

                        if (!$checkSupplierCurrency) {
                            return $this->sendError("Reporting currency of from company is not assign to the supplier of To company", 500, ['type' => 'confirm']);
                        }
                    }
                }

                if ($assetDisposalMaster->disposalType == 1) {

                    $itemAssignToCompany = ItemAssigned::where('companySystemID', $assetDisposalMaster->toCompanySystemID)->where('isActive', 1)->where('isAssigned', -1)->pluck('itemCodeSystem')->toArray();

                    $finalError = array(
                        'itemcode_not_exist' => array(),
                        'itemcode_not_assigned_to_company' => array(),
                    );
                    $error_count = 0;

                    foreach ($disposalDetailExist as $val) {
                        if (empty($val->itemCode) || $val->itemCode == 0) {
                            array_push($finalError['itemcode_not_exist'], 'FA' . ' | ' . $val->faCode);
                            $error_count++;
                        } else {
                            if (!in_array($val->itemCode, $itemAssignToCompany)) {
                                array_push($finalError['itemcode_not_assigned_to_company'], 'FA' . ' | ' . $val->faCode);
                                $error_count++;
                            }
                        }
                    }
                    $finalError['company'] = [$toCompanyName];
                    $confirm_error = array('type' => 'itemcode_not_exist', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError(trans('custom.there_are_few_assets_not_linked_to_an_item_code_please_link_it_before_you_confirm'), 500, $confirm_error);
                    }
                }

                if ($input['confirmType'] == 1) {
                    $finalError = array(
                        'asset_group_to' => array(),
                    );
                    $error_count = 0;
                    foreach ($disposalDetailExist as $val) {
                        if (count($val->asset_by->group_all_to) > 0) {
                            array_push($finalError['asset_group_to'], 'FA' . ' | ' . $val->faCode);
                            $error_count++;
                        }
                    }

                    $confirm_error = array('type' => 'asset_group_to', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError(trans('custom.there_is_are_asset_s_grouped'), 500, $confirm_error);
                    }
                }

                unset($input['confirmType']);

                $params = array('autoID' => $id, 'company' => $companySystemID, 'document' => $documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
                }
            }

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

            $assetDisposalMaster = $this->assetDisposalMasterRepository->update($input, $id);
            DB::commit();
            return $this->sendResponse($assetDisposalMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_disposal_master')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetDisposalMasters/{id}",
     *      summary="Remove the specified AssetDisposalMaster from storage",
     *      tags={"AssetDisposalMaster"},
     *      description="Delete AssetDisposalMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetDisposalMaster",
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
        /** @var AssetDisposalMaster $assetDisposalMaster */
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal_master')]));
        }

        if ($assetDisposalMaster->confirmedYN == 1) {
            return $this->sendError(trans('custom.you_cannot_delete_confirmed_document'));
        }

        $assetDisposalMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.asset_disposal_master')]));
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function getAllDisposalByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'confirmedYN', 'approved'));

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

        $assetCositng = AssetDisposalMaster::with(['disposal_type', 'created_by'])->ofCompany($subCompanies);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCositng->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCositng->where('approvedYN', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $assetCositng->whereMonth('disposalDocumentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $assetCositng->whereYear('disposalDocumentDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('disposalDocumentCode', 'LIKE', "%{$search}%");
                $query->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('assetdisposalMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getDisposalFormData(Request $request)
    {
        $companyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();
        $companyCurrency = \Helper::companyCurrency($companyId);
        $companyFinanceYear = \Helper::companyFinanceYear($companyId,1);
        $disposalType = AssetDisposalType::where('activeYN',1)->get();
        $customer = CustomerAssigned::ofCompany($companyId)->where('isAssigned', '-1')->where('isActive', '1')->get();
        $month = Months::all();
        $companies = \Helper::allCompanies();
        $years = AssetDisposalMaster::selectRaw("YEAR(createdDateTime) as year")
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();
        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'companyCurrency' => $companyCurrency,
            'companyFinanceYear' => $companyFinanceYear,
            'month' => $month,
            'years' => $years,
            'disposalType' => $disposalType,
            'customer' => $customer,
            'companies' => $companies,
        );
        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    function getAllAssetsForDisposal(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assets = FixedAssetMaster::selectRaw('*,false as isChecked')->with(['depperiod_by' => function ($query) use ($input) {
                $query->selectRaw('IFNULL(SUM(depAmountRpt),0) as depAmountRpt,IFNULL(SUM(depAmountLocal),0) as depAmountLocal,faID');
                $query->where('companySystemID', $input['companySystemID']);
                $query->groupBy('faID');
            },
            'depperiod_period' => function ($query) use ($input) {
                $query->selectRaw('DATE_FORMAT(depForFYperiodEndDate, "%d-%m-%Y") as depForFYperiodEndDate,faID');
                $query->where('companySystemID', $input['companySystemID']);
                $query->orderByRaw("STR_TO_DATE(depMonthYear, '%m/%Y') DESC")
                    ->first();
            }])
            ->isDisposed()->ofCompany([$input['companySystemID']])->isSelectedForDisposal()->isApproved();

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assets = $assets->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%");
                $query->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assets)
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

    function disposalReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['assetdisposalMasterAutoID'];
            $assetDisposal = $this->assetDisposalMasterRepository->findWithoutFail($id);
            $emails = array();
            if (empty($assetDisposal)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal')]));
            }

            if ($assetDisposal->approved == -1) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_asset_disposal_it_is_already_fully_approved'));
            }

            if ($assetDisposal->RollLevForApp_curr > 1) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_asset_disposal_it_is_already_partially_approved'));
            }

            if ($assetDisposal->confirmedYN == 0) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_asset_disposal_it_is_not_confirmed'));
            }

            $updateInput = ['confirmedYN' => 0, 'confimedByEmpSystemID' => null, 'confimedByEmpID' => null,
                'confirmedByEmpName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->assetDisposalMasterRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $assetDisposal->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $assetDisposal->disposalDocumentCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $assetDisposal->disposalDocumentCode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $assetDisposal->companySystemID)
                ->where('documentSystemCode', $assetDisposal->assetdisposalMasterAutoID)
                ->where('documentSystemID', $assetDisposal->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $assetDisposal->companySystemID)
                        ->where('documentSystemID', $assetDisposal->documentSystemID)
                        ->first();

                    if (empty($companyDocument)) {
                        return $this->sendError(trans('custom.policy_not_found_for_this_document'));
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
                ->where('companySystemID', $assetDisposal->companySystemID)
                ->where('documentSystemID', $assetDisposal->documentSystemID)
                ->delete();

            /*Audit entry*/
            AuditTrial::createAuditTrial($assetDisposal->documentSystemID,$id,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($assetDisposal->toArray(), trans('custom.asset_disposal_reopened_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getDisposalApprovalByUser(Request $request)
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
        $capitalization = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_fa_asset_disposalmaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'erp_fa_asset_disposaltypes.typeDescription',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID')
                    ->on('erp_documentapproved.departmentSystemID', '=', 'employeesdepartments.departmentSystemID');
                $query->whereIn('employeesdepartments.documentSystemID', [41])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_fa_asset_disposalmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'assetdisposalMasterAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_fa_asset_disposalmaster.companySystemID', $companyId)
                    ->where('erp_fa_asset_disposalmaster.approvedYN', 0)
                    ->where('erp_fa_asset_disposalmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_fa_asset_disposaltypes', 'disposalTypesID', 'disposalType')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [41])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $capitalization = $capitalization->where(function ($query) use ($search) {
                $query->where('disposalDocumentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $capitalization = [];
        }

        return \DataTables::of($capitalization)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('assetdisposalMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getDisposalApprovedByUser(Request $request)
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
        $capitalization = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_asset_disposalmaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'erp_fa_asset_disposaltypes.typeDescription',
                'documentSystemCode')
            ->join('erp_fa_asset_disposalmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'assetdisposalMasterAutoID')
                    ->where('erp_fa_asset_disposalmaster.companySystemID', $companyId)
                    ->where('erp_fa_asset_disposalmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_fa_asset_disposaltypes', 'disposalTypesID', 'disposalType')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [41])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $capitalization = $capitalization->where(function ($query) use ($search) {
                $query->where('disposalDocumentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($capitalization)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('assetdisposalMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function referBackDisposal(Request $request){
        DB::beginTransaction();
        try {
            $input = $request->all();
            $assetdisposalMasterAutoID = $input['assetdisposalMasterAutoID'];

            $assetdisposal = $this->assetDisposalMasterRepository->findWithoutFail($assetdisposalMasterAutoID);
            if (empty($assetdisposal)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal')]));
            }

            if ($assetdisposal->refferedBackYN != -1) {
                return $this->sendError(trans('custom.you_cannot_amend_this_document'));
            }

            $assetdisposalArray = $assetdisposal->toArray();

            $storeADHistory = AssetDisposalReferred::create($assetdisposalArray);

            $fetchADDetails = AssetDisposalDetail::OfMaster($assetdisposalMasterAutoID)
                ->get();

            if (!empty($fetchADDetails)) {
                foreach ($fetchADDetails as $caDetail) {
                    $caDetail['timesReferred'] = $assetdisposal->timesReferred;
                }
            }

            $caDetailArray = $fetchADDetails->toArray();

            $storePVDetailHistory = AssetDisposalDetailReferred::insert($caDetailArray);


            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $assetdisposalMasterAutoID)
                ->where('companySystemID', $assetdisposal->companySystemID)
                ->where('documentSystemID', $assetdisposal->documentSystemID)
                ->get();

            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $assetdisposal->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();

            $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

            $deleteApproval = DocumentApproved::where('documentSystemCode', $assetdisposalMasterAutoID)
                ->where('companySystemID', $assetdisposal->companySystemID)
                ->where('documentSystemID', $assetdisposal->documentSystemID)
                ->delete();

            if ($deleteApproval) {
                $assetdisposal->refferedBackYN = 0;
                $assetdisposal->confirmedYN = 0;
                $assetdisposal->confimedByEmpSystemID = null;
                $assetdisposal->confimedByEmpID = null;
                $assetdisposal->confirmedDate = null;
                $assetdisposal->RollLevForApp_curr = 1;
                $assetdisposal->save();
            }

            DB::commit();
            return $this->sendResponse($assetdisposal->toArray(), trans('custom.asset_disposal_amended_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function amendAssetDisposalReview(Request $request)
    {
        $input = $request->all();

        $id = isset($input['id'])?$input['id']:0;

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = $this->assetDisposalMasterRepository->findWithoutFail($id);
        if (empty($masterData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_disposal')]));
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_asset_disposal_it_is_not_confirmed'));
        }

        $documentAutoId = $id;
        $documentSystemID = $masterData->documentSystemID;
        if($masterData->approvedYN == -1){
            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId, $documentSystemID);
            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                    return $this->sendError($validatePendingGlPost['message']);
                }
            }
        }

        $emailBody = '<p>' . $masterData->disposalDocumentCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->disposalDocumentCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confimedByEmpSystemID,
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

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confimedByEmpSystemID = null;
            $masterData->confimedByEmpID = null;
            $masterData->confirmedByEmpName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approvedYN = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_disposal_amend')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
}
