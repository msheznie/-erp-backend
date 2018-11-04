<?php
/**
 * =============================================
 * -- File Name : FixedAssetDepreciationMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Asset depreciation
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetDepreciationMasterAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetDepreciationMasterAPIRequest;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\EmployeesDepartment;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetDepreciationMasterController
 * @package App\Http\Controllers\API
 */
class FixedAssetDepreciationMasterAPIController extends AppBaseController
{
    /** @var  FixedAssetDepreciationMasterRepository */
    private $fixedAssetDepreciationMasterRepository;

    public function __construct(FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo)
    {
        $this->fixedAssetDepreciationMasterRepository = $fixedAssetDepreciationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetDepreciationMasters",
     *      summary="Get a listing of the FixedAssetDepreciationMasters.",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Get all FixedAssetDepreciationMasters",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetDepreciationMaster")
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
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetDepreciationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->all();

        return $this->sendResponse($fixedAssetDepreciationMasters->toArray(), 'Fixed Asset Depreciation Masters retrieved successfully');
    }

    /**
     * @param CreateFixedAssetDepreciationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetDepreciationMasters",
     *      summary="Store a newly created FixedAssetDepreciationMaster in storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Store FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationMaster")
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetDepreciationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'companyFinanceYearID' => 'required',
                'companyFinancePeriodID' => 'required',
            ]);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            $alreadyExist = $this->fixedAssetDepreciationMasterRepository->findWhere(['companySystemID' => $input['companySystemID'], 'companyFinanceYearID' => $input['companyFinanceYearID'], 'companyFinancePeriodID' => $input['companyFinancePeriodID']]);

            if (count($alreadyExist) > 0) {
                return $this->sendError('Depreciation already processed for the selected month', 500);
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

            $subMonth = new Carbon($input['FYPeriodDateFrom']);
            $subMonthStart = $subMonth->subMonth()->startOfMonth()->format('Y-m-d');
            $subMonthStartCarbon = new Carbon($subMonthStart);
            $subMonthEnd = $subMonthStartCarbon->endOfMonth()->format('Y-m-d');

            $lastMonthRun = FixedAssetDepreciationMaster::where('companySystemID', $input['companySystemID'])->where('companyFinanceYearID', $input['companyFinanceYearID'])->where('FYPeriodDateFrom', $subMonthStart)->where('FYPeriodDateTo', $subMonthEnd)->first();

            if (!empty($lastMonthRun)) {
                if ($lastMonthRun->approved == 0) {
                    return $this->sendError('Last month depreciation is not approved. Please approve it before you run for this month', 500);
                }
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $documentMaster = DocumentMaster::find($input['documentSystemID']);
            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }

            if ($companyFinanceYear["message"]) {
                $startYear = $companyFinanceYear["message"]['bigginingDate'];
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            } else {
                $finYear = date("Y");
            }

            $lastSerial = FixedAssetDepreciationMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                ->orderBy('depMasterAutoID', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['depCode'] = $documentCode;
            $input['serialNo'] = $lastSerialNumber;
            $depDate = Carbon::parse($input['FYPeriodDateTo']);
            $input['depDate'] = $input['FYPeriodDateTo'];
            $input['depMonthYear'] = $depDate->month . '/' . $depDate->year;
            $input['depLocalCur'] = $company->localCurrencyID;
            $input['depRptCur'] = $company->reportingCurrency;
            $input['createdPCID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->create($input);
            $depMasterAutoID = $fixedAssetDepreciationMasters['depMasterAutoID'];

            $faMaster = FixedAssetMaster::with(['depperiod_by' => function ($query) {
                $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
                $query->groupBy('faID');
            }])->isDisposed()->ofCompany([$input['companySystemID']])->orderBy('faID', 'desc')->get();
            $depAmountRptTotal = 0;
            $depAmountLocalTotal = 0;
            if ($faMaster) {
                foreach ($faMaster as $val) {
                    $depAmountRpt = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]->depAmountRpt : 0;
                    $depAmountLocal = count($val->depperiod_by) > 0 ? $val->depperiod_by[0]->depAmountLocal : 0;
                    $nbvLocal = $val->COSTUNIT - $depAmountLocal;
                    $nbvRpt = $val->costUnitRpt - $depAmountRpt;
                    $monthlyLocal = ($val->COSTUNIT * ($val->DEPpercentage / 100)) / 12;
                    $monthlyRpt = ($val->costUnitRpt * ($val->DEPpercentage / 100)) / 12;

                    if ($nbvLocal != 0 || $nbvRpt != 0) {
                        $data['depMasterAutoID'] = $fixedAssetDepreciationMasters['depMasterAutoID'];
                        $data['companySystemID'] = $input['companySystemID'];
                        $data['companyID'] = $company->CompanyID;
                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                        $data['serviceLineCode'] = $val->serviceLineCode;
                        $data['faFinanceCatID'] = $val->AUDITCATOGARY;
                        $data['faMainCategory'] = $val->faCatID;
                        $data['faSubCategory'] = $val->faSubCatID;
                        $data['faID'] = $val->faID;
                        $data['faCode'] = $val->faCode;
                        $data['assetDescription'] = $val->assetDescription;
                        $data['depPercent'] = $val->DEPpercentage;
                        $data['COSTUNIT'] = $val->COSTUNIT;
                        $data['costUnitRpt'] = $val->costUnitRpt;
                        $data['depDoneYN'] = -1;
                        $data['createdPCid'] = gethostname();
                        $data['createdBy'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $data['depMonthYear'] = $input['depMonthYear'];
                        $data['depMonth'] = $val->depMonth;
                        $data['depAmountLocalCurr'] = $input['depLocalCur'];
                        $data['depAmountRptCurr'] = $input['depRptCur'];

                        if ($nbvLocal < $monthlyLocal) {
                            $data['depAmountLocal'] = $nbvLocal;
                        } else {
                            $data['depAmountLocal'] = $monthlyLocal;
                        }

                        if ($nbvRpt < $monthlyRpt) {
                            $data['depAmountRpt'] = $nbvRpt;
                        } else {
                            $data['depAmountRpt'] = $monthlyRpt;
                        }

                        if ($depAmountRpt == 0 && $depAmountLocal == 0) {
                            $dateDEP = Carbon::parse($val->dateDEP);
                            if ($dateDEP->lessThanOrEqualTo($depDate)) {
                                $differentMonths = CarbonPeriod::create($dateDEP->format('Y-m-d'), '1 month', $depDate->format('Y-m-d'));
                                if ($differentMonths) {
                                    foreach ($differentMonths as $dt) {
                                        $companyFinanceYearID = CompanyFinanceYear::ofCompany($input['companySystemID'])->where('bigginingDate', '<=', $dt)->where('endingDate', '>=', $dt->format('Y-m-d'))->first();
                                        if ($companyFinanceYearID) {
                                            $data['FYID'] = $companyFinanceYearID->companyFinanceYearID;
                                            $data['depForFYStartDate'] = $companyFinanceYearID->bigginingDate;
                                            $data['depForFYEndDate'] = $companyFinanceYearID->endingDate;
                                            $companyFinancePeriodID = CompanyFinancePeriod::ofCompany($input['companySystemID'])->ofDepartment(9)->where('dateFrom', '<=', $dt)->where('dateTo', '>=', $dt->format('Y-m-d'))->first();
                                            $data['FYperiodID'] = $companyFinancePeriodID->companyFinancePeriodID;
                                            $data['depForFYperiodStartDate'] = $companyFinancePeriodID->dateFrom;
                                            $data['depForFYperiodEndDate'] = $companyFinancePeriodID->dateTo;
                                            $assetDepPeriod = FixedAssetDepreciationPeriod::create($data);
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($nbvRpt != 0 && $nbvLocal != 0) {
                                $data['FYID'] = $input['companyFinanceYearID'];
                                $data['depForFYStartDate'] = $input['FYBiggin'];
                                $data['depForFYEndDate'] = $input['FYEnd'];
                                $data['FYperiodID'] = $input['companyFinancePeriodID'];
                                $data['depForFYperiodStartDate'] = $input['FYPeriodDateFrom'];
                                $data['depForFYperiodEndDate'] = $input['FYPeriodDateTo'];
                                $assetDepPeriod = FixedAssetDepreciationPeriod::create($data);
                            }
                        }
                    }
                }
            }

            $depDetail = FixedAssetDepreciationPeriod::selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt')->OfDepreciation($depMasterAutoID)->first();

            $fixedAssetDepreciationMasters = $this->fixedAssetDepreciationMasterRepository->update(['depAmountLocal' => $depDetail->depAmountLocal, 'depAmountRpt' => $depDetail->depAmountRpt], $depMasterAutoID);

            DB::commit();
            return $this->sendResponse($fixedAssetDepreciationMasters->toArray(), 'Fixed Asset Depreciation Master saved successfully');
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
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Display the specified FixedAssetDepreciationMaster",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Get FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
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
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'Fixed Asset Depreciation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetDepreciationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Update the specified FixedAssetDepreciationMaster in storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Update FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetDepreciationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetDepreciationMaster")
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
     *                  ref="#/definitions/FixedAssetDepreciationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetDepreciationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        if ($fixedAssetDepreciationMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {
            $params = array('autoID' => $id, 'company' => $fixedAssetDepreciationMaster->companySystemID, 'document' => $fixedAssetDepreciationMaster->documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
            }
        }

        /*$input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input["timestamp"] = date('Y-m-d H:i:s');*/

        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->update($input, $id);

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'FixedAssetDepreciationMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetDepreciationMasters/{id}",
     *      summary="Remove the specified FixedAssetDepreciationMaster from storage",
     *      tags={"FixedAssetDepreciationMaster"},
     *      description="Delete FixedAssetDepreciationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetDepreciationMaster",
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
        /** @var FixedAssetDepreciationMaster $fixedAssetDepreciationMaster */
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);

        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        $fixedAssetDepreciationMaster->delete();

        return $this->sendResponse($id, 'Fixed Asset Depreciation Master deleted successfully');
    }

    public function getAllDepreciationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved'));

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

        $assetCositng = FixedAssetDepreciationMaster::with(['depperiod_by' => function ($query) use ($input) {
            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,depMasterAutoID');
            $query->groupBy('depMasterAutoID');
        }])->ofCompany($subCompanies);

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

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getDepreciationFormData(Request $request)
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

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $companyCurrency = \Helper::companyCurrency($companyId);

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $output = array(
            'financialYears' => $financialYears,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'companyCurrency' => $companyCurrency,
            'companyFinanceYear' => $companyFinanceYear,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function assetDepreciationByID($id)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->with(['confirmed_by'])->findWithoutFail($id);
        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        $detail = FixedAssetDepreciationPeriod::with(['maincategory_by', 'financecategory_by', 'serviceline_by'])->ofDepreciation($id)->get();

        $output = ['master' => $fixedAssetDepreciationMaster, 'detail' => $detail];

        return $this->sendResponse($output, 'Fixed Asset Master retrieved successfully');
    }

    public function assetDepreciationMaster(Request $request)
    {
        $fixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepository->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 23);
        }, 'confirmed_by', 'created_by'])->findWithoutFail($request['depMasterAutoID']);
        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError('Fixed Asset Depreciation Master not found');
        }

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), 'Fixed Asset Master retrieved successfully');
    }


    function assetDepreciationReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['faID'];
            $fixedAssetDep = $this->fixedAssetDepreciationMasterRepository->findWithoutFail($id);
            $emails = array();
            if (empty($fixedAssetDep)) {
                return $this->sendError('Fixed Asset Master not found');
            }


            if ($fixedAssetDep->approved == -1) {
                return $this->sendError('You cannot reopen this Asset Depreciation it is already fully approved');
            }

            if ($fixedAssetDep->RollLevForApp_curr > 1) {
                return $this->sendError('You cannot reopen this Asset Depreciation it is already partially approved');
            }

            if ($fixedAssetDep->confirmedYN == 0) {
                return $this->sendError('You cannot reopen this Asset Depreciation, it is not confirmed');
            }

            $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
                'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->fixedAssetDepreciationMasterRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $fixedAssetDep->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $fixedAssetDep->depCode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $fixedAssetDep->depCode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $fixedAssetDep->companySystemID)
                ->where('documentSystemCode', $fixedAssetDep->depMasterAutoID)
                ->where('documentSystemID', $fixedAssetDep->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $fixedAssetDep->companySystemID)
                        ->where('documentSystemID', $fixedAssetDep->documentSystemID)
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

            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $fixedAssetDep->companySystemID)
                ->where('documentSystemID', $fixedAssetDep->documentSystemID)
                ->delete();

            DB::commit();
            return $this->sendResponse($fixedAssetDep->toArray(), 'Asset depreciation reopened successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function getAssetDepApprovalByUser(Request $request)
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
                'erp_fa_depmaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [23])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_fa_depmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'depMasterAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_fa_depmaster.companySystemID', $companyId)
                    ->where('erp_fa_depmaster.approved', 0)
                    ->where('erp_fa_depmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [23])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getAssetDepApprovedByUser(Request $request)
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
                'erp_fa_depmaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_fa_depmaster', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'depMasterAutoID')
                    ->where('erp_fa_depmaster.companySystemID', $companyId)
                    ->where('erp_fa_depmaster.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [23])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCost = $assetCost->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($assetCost)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
