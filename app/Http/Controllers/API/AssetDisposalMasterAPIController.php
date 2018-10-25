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

use App\Http\Requests\API\CreateAssetDisposalMasterAPIRequest;
use App\Http\Requests\API\UpdateAssetDisposalMasterAPIRequest;
use App\Models\AssetDisposalMaster;
use App\Models\AssetDisposalType;
use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\DocumentMaster;
use App\Models\FixedAssetMaster;
use App\Models\Months;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\AssetDisposalMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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

        return $this->sendResponse($assetDisposalMasters->toArray(), 'Asset Disposal Masters retrieved successfully');
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

        if ($validator->fails()) {//echo 'in';exit;
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

        $input['documentDate'] = new Carbon($input['documentDate']);

        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($input['documentDate'] >= $monthBegin) && ($input['documentDate'] <= $monthEnd)) {
        } else {
            return $this->sendError('Disposal date is not within financial period!', 500);
        }

        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
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
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $assetDisposalMasters = $this->assetDisposalMasterRepository->create($input);

        return $this->sendResponse($assetDisposalMasters->toArray(), 'Asset Disposal Master saved successfully');
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
        $assetDisposalMaster = $this->assetDisposalMasterRepository->with(['confirmed_by', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError('Asset Disposal Master not found');
        }

        return $this->sendResponse($assetDisposalMaster->toArray(), 'Asset Disposal Master retrieved successfully');
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
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var AssetDisposalMaster $assetDisposalMaster */
        $assetDisposalMaster = $this->assetDisposalMasterRepository->findWithoutFail($id);

        if (empty($assetDisposalMaster)) {
            return $this->sendError('Asset Disposal Master not found');
        }

        $assetDisposalMaster = $this->assetDisposalMasterRepository->update($input, $id);

        return $this->sendResponse($assetDisposalMaster->toArray(), 'AssetDisposalMaster updated successfully');
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
            return $this->sendError('Asset Disposal Master not found');
        }

        $assetDisposalMaster->delete();

        return $this->sendResponse($id, 'Asset Disposal Master deleted successfully');
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

        $assetCositng = AssetDisposalMaster::with('disposal_type')->ofCompany($subCompanies);

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
        $companyFinanceYear = \Helper::companyFinanceYear($companyId);
        $disposalType = AssetDisposalType::all();
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
        return $this->sendResponse($output, 'Record retrieved successfully');
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
            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,faID');
            $query->where('companySystemID', $input['companySystemID']);
            $query->groupBy('faID');
        }])->isDisposed()->ofCompany([$input['companySystemID']])->isSelectedForDisposal();

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

}
