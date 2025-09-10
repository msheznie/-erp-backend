<?php
/**
 * =============================================
 * -- File Name : AssetCapitalizationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 09 - September 2018
 * -- Description : This file contains the all CRUD for Asset Capitalization
 * -- REVISION HISTORY
 * -- Date: 03-September 2018 By:Mubashir Description: Added new functions named as getAllCapitalizationByCompany(),getAllCapitalizationByCompany()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetCapitalizationAPIRequest;
use App\Http\Requests\API\UpdateAssetCapitalizationAPIRequest;
use App\Models\AssetCapitalizatioDetReferred;
use App\Models\AssetCapitalization;
use App\Models\AssetCapitalizationDetail;
use App\Models\AssetCapitalizationReferred;
use App\Models\AssetDisposalDetail;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\Months;
use App\Models\SupplierAssigned;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\AssetCapitalizationRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetCapitalizationController
 * @package App\Http\Controllers\API
 */
class AssetCapitalizationAPIController extends AppBaseController
{
    /** @var  AssetCapitalizationRepository */
    private $assetCapitalizationRepository;

    public function __construct(AssetCapitalizationRepository $assetCapitalizationRepo)
    {
        $this->assetCapitalizationRepository = $assetCapitalizationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetCapitalizations",
     *      summary="Get a listing of the AssetCapitalizations.",
     *      tags={"AssetCapitalization"},
     *      description="Get all AssetCapitalizations",
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
     *                  @SWG\Items(ref="#/definitions/AssetCapitalization")
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
        $this->assetCapitalizationRepository->pushCriteria(new RequestCriteria($request));
        $this->assetCapitalizationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetCapitalizations = $this->assetCapitalizationRepository->all();

        return $this->sendResponse($assetCapitalizations->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalization')]));
    }

    /**
     * @param CreateAssetCapitalizationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetCapitalizations",
     *      summary="Store a newly created AssetCapitalization in storage",
     *      tags={"AssetCapitalization"},
     *      description="Store AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalization that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalization")
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
     *                  ref="#/definitions/AssetCapitalization"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetCapitalizationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'allocationTypeID' => 'required',
                'narration' => 'required',
                'documentDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $assetCapitalizations = AssetCapitalization::ofAsset($input['faID'])->first();

            if ($assetCapitalizations) {
                return $this->sendError(trans('custom.selected_asset_is_already_added_for_capitalization'), 500);
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

            $input['documentDate'] = new Carbon($input['documentDate']);

            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];

            if (($input['documentDate'] >= $monthBegin) && ($input['documentDate'] <= $monthEnd)) {
            } else {
                return $this->sendError(trans('custom.capitalization_date_is_not_within_financial_period'), 500);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $contraAccount = ChartOfAccount::find($input['contraAccountSystemID']);
            if ($contraAccount) {
                $input['contraAccountGLCode'] = $contraAccount->AccountCode;
            }

            $documentMaster = DocumentMaster::find($input['documentSystemID']);
            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }

            $lastSerial = AssetCapitalization::where('companySystemID', $input['companySystemID'])
                ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                ->orderBy('capitalizationID', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            if ($companyFinanceYear["message"]) {
                $startYear = $companyFinanceYear["message"]['bigginingDate'];
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            } else {
                $finYear = date("Y");
            }
            if ($documentMaster) {
                $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $input['capitalizationCode'] = $documentCode;
            }
            $input['serialNo'] = $lastSerialNumber;

            if ($input['allocationTypeID'] == 1) {
                $assets = FixedAssetMaster::find($input['faID']);
                $depreciationLocal = FixedAssetDepreciationPeriod::OfCompany([$input['companySystemID']])->OfAsset($input['faID'])->sum('depAmountLocal');
                $depreciationRpt = FixedAssetDepreciationPeriod::OfCompany([$input['companySystemID']])->OfAsset($input['faID'])->sum('depAmountRpt');

                $nbvRpt = $assets->costUnitRpt - $depreciationRpt;
                $nbvLocal = $assets->COSTUNIT - $depreciationLocal;

                $input['assetNBVLocal'] = $nbvLocal;
                $input['assetNBVRpt'] = $nbvRpt;
            }
            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

            $assetCapitalizations = $this->assetCapitalizationRepository->create($input);
            DB::commit();
            return $this->sendResponse($assetCapitalizations->toArray(), trans('custom.save', ['attribute' => trans('custom.asset_capitalization')]));
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
     *      path="/assetCapitalizations/{id}",
     *      summary="Display the specified AssetCapitalization",
     *      tags={"AssetCapitalization"},
     *      description="Get AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalization",
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
     *                  ref="#/definitions/AssetCapitalization"
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
        /** @var AssetCapitalization $assetCapitalization */
        $assetCapitalization = $this->assetCapitalizationRepository->with(['confirmed_by', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'contra_account', 'asset_by' => function ($query) {
            $query->selectRaw("CONCAT(faCode,' - ',assetDescription) as assetName,faID");
        }])->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization')]));
        }

        return $this->sendResponse($assetCapitalization->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.asset_capitalization')]));
    }

    /**
     * @param int $id
     * @param UpdateAssetCapitalizationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetCapitalizations/{id}",
     *      summary="Update the specified AssetCapitalization in storage",
     *      tags={"AssetCapitalization"},
     *      description="Update AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalization",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetCapitalization that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetCapitalization")
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
     *                  ref="#/definitions/AssetCapitalization"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetCapitalizationAPIRequest $request)
    {
        /** @var AssetCapitalization $assetCapitalization */
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);

            $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

            if (empty($assetCapitalization)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization')]));
            }

            $assetCapitalizations = AssetCapitalization::ofAsset($input['faID'])->where('capitalizationID', '<>', $id)->first();

            if ($assetCapitalizations) {
                return $this->sendError(trans('custom.selected_asset_is_already_added_for_capitalization'), 500);
            }

            $companySystemID = $assetCapitalization->companySystemID;
            $documentSystemID = $assetCapitalization->documentSystemID;

            $input['documentDate'] = new Carbon($input['documentDate']);

            $contraAccount = ChartOfAccount::find($input['contraAccountSystemID']);
            if ($contraAccount) {
                $input['contraAccountGLCode'] = $contraAccount->AccountCode;
            }

            if ($assetCapitalization->confirmedYN == 0 && $input['confirmedYN'] == 1) {
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

                if (($input['documentDate'] >= $monthBegin) && ($input['documentDate'] <= $monthEnd)) {
                } else {
                    return $this->sendError(trans('custom.capitalization_date_is_not_within_financial_period'), 500, ['type' => 'confirm']);
                }

                $acDetailExist = AssetCapitalizationDetail::where('capitalizationID', $id)->get();

                if (empty($acDetailExist)) {
                    return $this->sendError(trans('custom.asset_capitalization_document_cannot_confirm_without_details'), 500, ['type' => 'confirm']);
                }

                foreach ($acDetailExist as $val) {
                    if ($val->allocatedAmountRpt == 0) {
                        return $this->sendError(trans('custom.asset_capitalization_document_cannot_confirm_with_zero_allocated_amount'), 500, ['type' => 'confirm']);
                    }
                }

                $params = array('autoID' => $id, 'company' => $companySystemID, 'document' => $documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
                }
            }

            if ($input['allocationTypeID'] == 1) {
                $assets = FixedAssetMaster::find($input['faID']);
                $depreciationLocal = FixedAssetDepreciationPeriod::OfCompany([$companySystemID])->OfAsset($input['faID'])->sum('depAmountLocal');
                $depreciationRpt = FixedAssetDepreciationPeriod::OfCompany([$companySystemID])->OfAsset($input['faID'])->sum('depAmountRpt');

                $nbvRpt = $assets->costUnitRpt - $depreciationRpt;
                $nbvLocal = $assets->COSTUNIT - $depreciationLocal;

                $input['assetNBVLocal'] = $nbvLocal;
                $input['assetNBVRpt'] = $nbvRpt;
            }
            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

            $assetCapitalization = $this->assetCapitalizationRepository->update($input, $id);
            DB::commit();
            return $this->sendReponseWithDetails($assetCapitalization->toArray(), trans('custom.update', ['attribute' => trans('custom.asset_capitalization')]),1,$confirm['data'] ?? null);
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
     *      path="/assetCapitalizations/{id}",
     *      summary="Remove the specified AssetCapitalization from storage",
     *      tags={"AssetCapitalization"},
     *      description="Delete AssetCapitalization",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetCapitalization",
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
        /** @var AssetCapitalization $assetCapitalization */
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);

        if (empty($assetCapitalization)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.assetCapitalization')]));
        }

        if ($assetCapitalization->confirmedYN == 1) {
            return $this->sendError(trans('custom.you_cannot_delete_confirmed_document'));
        }

        $assetCapitalization->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.assetCapitalization')]));
    }

    public function getCapitalizationFormData(Request $request)
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

        $companyFinanceYear = \Helper::companyFinanceYear($companyId,1);
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = AssetCapitalization::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $assetCategoryType = FixedAssetCategory::OfCompany($subCompanies)->get();

        $output = array(
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'assetCategoryType' => $assetCategoryType,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }


    public function getAllCapitalizationByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'cancelYN', 'confirmedYN', 'approved', 'allocationTypeID'));

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

        $assetCapitalization = AssetCapitalization::with(['created_by'])->whereIN('companySystemID', $subCompanies);

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $assetCapitalization->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $assetCapitalization->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $assetCapitalization->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $assetCapitalization->whereMonth('documentDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $assetCapitalization->whereYear('documentDate', '=', $input['year']);
            }
        }

        if (array_key_exists('allocationTypeID', $input)) {
            if ($input['allocationTypeID'] && !is_null($input['allocationTypeID'])) {
                $assetCapitalization->where('allocationTypeID', $input['allocationTypeID']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCapitalization = $assetCapitalization->where(function ($query) use ($search) {
                $query->where('capitalizationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCapitalization)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('capitalizationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    function getAssetByCategory(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $assets = FixedAssetMaster::OfCompany($subCompanies)->isDisposed()->OfCategory($request['faCatID'])->isApproved()->get();
        return $this->sendResponse($assets, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    function getAssetNBV(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $assets = FixedAssetMaster::find($request['faID']);
        $depreciation = FixedAssetDepreciationPeriod::OfCompany($subCompanies)->OfAsset($request['faID'])->sum('depAmountRpt');

        $nbv = $assets->costUnitRpt - $depreciation;

        return $this->sendResponse(['nbv' => $nbv], trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    function getCapitalizationFixedAsset(Request $request)
    {
        $input = $request->all();
        $companyID = $input['companyID'];

        $items = FixedAssetMaster::OfCompany([$companyID])->isDisposed()->isApproved();

        if (array_key_exists('search', $input)) {
            $search = str_replace("\\", "\\\\", $input['search']);
            $items = $items->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%")
                    ->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        $items = $items->take(20)->get();
        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.data')]));
    }

    function capitalizationReopen(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['capitalizationID'];
            $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);
            $emails = array();
            if (empty($assetCapitalization)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_reclassification')]));
            }

            if ($assetCapitalization->approved == -1) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_asset_reclassification_it_is_already_fully_approved'));
            }

            if ($assetCapitalization->RollLevForApp_curr > 1) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_asset_reclassification_it_is_already_partially_approved'));
            }

            if ($assetCapitalization->confirmedYN == 0) {
                return $this->sendError(trans('custom.you_cannot_reopen_this_asset_reclassification_it_is_not_confirmed'));
            }

            $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
                'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1];

            $this->assetCapitalizationRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $assetCapitalization->documentSystemID)->first();

            $cancelDocNameBody = $document->document_description_translated . ' <b>' . $assetCapitalization->BPVcode . '</b>';
            $cancelDocNameSubject = $document->document_description_translated . ' ' . $assetCapitalization->BPVcode;

            $subject = $cancelDocNameSubject . ' ' . trans('email.is_reopened');

            $body = '<p>' . $cancelDocNameBody . ' ' . trans('email.is_reopened_by', ['empID' => $employee->empID, 'empName' => $employee->empFullName]) . '</p><p>' . trans('email.comment') . ' : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $assetCapitalization->companySystemID)
                ->where('documentSystemCode', $assetCapitalization->PayMasterAutoId)
                ->where('documentSystemID', $assetCapitalization->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $assetCapitalization->companySystemID)
                        ->where('documentSystemID', $assetCapitalization->documentSystemID)
                        ->first();

                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => trans('custom.policy_not_found_for_this_document')];
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
                ->where('companySystemID', $assetCapitalization->companySystemID)
                ->where('documentSystemID', $assetCapitalization->documentSystemID)
                ->delete();

            /*Audit entry*/
            AuditTrial::createAuditTrial($assetCapitalization->documentSystemID,$id,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($assetCapitalization->toArray(), trans('custom.reopened', ['attribute' => trans('custom.asset_capitalization')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getAssetCapitalizationMaster(Request $request)
    {
        $input = $request->all();

        $output = $this->assetCapitalizationRepository
            ->with(['confirmed_by', 'detail' => function ($query) {
                $query->with('segment');
            }, 'approved_by' => function ($query) {
                $query->with('employee');
                $query->where('documentSystemID', 63);
            }, 'created_by', 'modified_by','audit_trial.modified_by'])->findWithoutFail($input['capitalizationID']);

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.data')]));

    }

    public function getCapitalizationApprovalByUser(Request $request)
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
                'erp_fa_assetcapitalization.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $query->whereIn('employeesdepartments.documentSystemID', [63])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_fa_assetcapitalization', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'capitalizationID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_fa_assetcapitalization.companySystemID', $companyId)
                    ->where('erp_fa_assetcapitalization.approved', 0)
                    ->where('erp_fa_assetcapitalization.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [63])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $capitalization = $capitalization->where(function ($query) use ($search) {
                $query->where('capitalizationCode', 'LIKE', "%{$search}%")
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
                        $query->orderBy('capitalizationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getCapitalizationApprovedByUser(Request $request)
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

        $capitalization = DB::table('erp_documentapproved')
            ->select(
                'erp_fa_assetcapitalization.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_fa_assetcapitalization', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'capitalizationID')
                    ->where('erp_fa_assetcapitalization.companySystemID', $companyId)
                    ->where('erp_fa_assetcapitalization.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [63])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $capitalization = $capitalization->where(function ($query) use ($search) {
                $query->where('capitalizationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($capitalization)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('capitalizationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getCapitalizationLinkedDocument(Request $request)
    {
        $id = $request['capitalizationID'];
        $assetCapitalization = $this->assetCapitalizationRepository->findWithoutFail($id);
        if (empty($assetCapitalization)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization')]));
        }

        $assetDisposalDetail = AssetDisposalDetail::with('master_by')->OfAsset($assetCapitalization->faID)->first();

        $fixedAsset = FixedAssetMaster::OfCompany([$request->companySystemID])->where('docOriginDocumentSystemID', $assetCapitalization->documentSystemID)->where('docOriginSystemCode', $id)->get();

        $output = ['disposal' => $assetDisposalDetail, 'assets' => $fixedAsset];
        return $this->sendResponse($output, trans('custom.record_successfully'));
    }

    function referBackCapitalization(Request $request){
        DB::beginTransaction();
        try {
            $input = $request->all();
            $capitalizationID = $input['capitalizationID'];

            $capitalization = $this->assetCapitalizationRepository->findWithoutFail($capitalizationID);
            if (empty($capitalization)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.asset_capitalization')]));
            }

            if ($capitalization->refferedBackYN != -1) {
                return $this->sendError(trans('custom.you_cannot_amend_this_document'));
            }

            $capitalizationArray = $capitalization->toArray();

            AssetCapitalizationReferred::create($capitalizationArray);

            $fetchCADetails = AssetCapitalizationDetail::OfCapitalization($capitalizationID)
                ->get();

            if (!empty($fetchCADetails)) {
                foreach ($fetchCADetails as $caDetail) {
                    $caDetail['timesReferred'] = $capitalization->timesReferred;
                }
            }

            $caDetailArray = $fetchCADetails->toArray();

            AssetCapitalizatioDetReferred::insert($caDetailArray);


            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $capitalizationID)
                ->where('companySystemID', $capitalization->companySystemID)
                ->where('documentSystemID', $capitalization->documentSystemID)
                ->get();

            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $capitalization->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();

            DocumentReferedHistory::insert($DocumentApprovedArray);

            $deleteApproval = DocumentApproved::where('documentSystemCode', $capitalizationID)
                ->where('companySystemID', $capitalization->companySystemID)
                ->where('documentSystemID', $capitalization->documentSystemID)
                ->delete();

            if ($deleteApproval) {
                $capitalization->refferedBackYN = 0;
                $capitalization->confirmedYN = 0;
                $capitalization->confirmedByEmpSystemID = null;
                $capitalization->confirmedByEmpID = null;
                $capitalization->confirmedDate = null;
                $capitalization->RollLevForApp_curr = 1;
                $capitalization->save();
            }

            DB::commit();
            return $this->sendResponse($capitalization->toArray(), trans('custom.asset_capitalization_amended_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}
