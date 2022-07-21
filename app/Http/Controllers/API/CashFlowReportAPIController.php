<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowReportAPIRequest;
use App\Http\Requests\API\UpdateCashFlowReportAPIRequest;
use App\Models\CashFlowReport;
use App\Models\CashFlowReportDetail;
use App\Models\CashFlowSubCategoryGLCode;
use App\Models\CashFlowTemplateDetail;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CashFlowTemplate;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\CashFlowReportRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class CashFlowReportController
 * @package App\Http\Controllers\API
 */

class CashFlowReportAPIController extends AppBaseController
{
    /** @var  CashFlowReportRepository */
    private $cashFlowReportRepository;

    public function __construct(CashFlowReportRepository $cashFlowReportRepo)
    {
        $this->cashFlowReportRepository = $cashFlowReportRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowReports",
     *      summary="Get a listing of the CashFlowReports.",
     *      tags={"CashFlowReport"},
     *      description="Get all CashFlowReports",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowReport")
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
        $this->cashFlowReportRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowReportRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowReports = $this->cashFlowReportRepository->all();

        return $this->sendResponse($cashFlowReports->toArray(), 'Cash Flow Reports retrieved successfully');
    }

    /**
     * @param CreateCashFlowReportAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowReports",
     *      summary="Store a newly created CashFlowReport in storage",
     *      tags={"CashFlowReport"},
     *      description="Store CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowReport that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowReport")
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
     *                  ref="#/definitions/CashFlowReport"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowReportAPIRequest $request)
    {
        $input = $request->all();
        $input['createdPCID'] = gethostname();
        $input['date'] = Carbon::parse($input['date']);
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $cashFlowReport = $this->cashFlowReportRepository->create($input);

        $cashFlowReportID = $cashFlowReport->id;

        $cashFlowTemplateData = CashFlowTemplateDetail::where('cashFlowTemplateID', $input['cashFlowTemplateID'])->get();



        foreach ($cashFlowTemplateData as $key => $value) {
            $data['cashFlowTemplateDetailID'] = $value->id;
            $data['cashFlowReportID'] = $cashFlowReportID;

            //logic need to caluate using seperate function with switch case
            $data['amount'] = 0;

            CashFlowReportDetail::create($data);
        }

        $reportTemplateDetails = CashFlowTemplateDetail::selectRaw('*,0 as expanded')->with(['subcategory' => function ($q) {
            $q->with(['gllink' => function ($q) {
                $q->with('subcategory');
                $q->orderBy('sortOrder', 'asc');
            }, 'subcategory' => function ($q) {
                $q->with(['gllink' => function ($q) {
                    $q->with('subcategory');
                    $q->orderBy('sortOrder', 'asc');
                }, 'subcategory' => function ($q) {
                    $q->with(['gllink' => function ($q) {
                        $q->with('subcategory');
                        $q->orderBy('sortOrder', 'asc');
                    }, 'subcategory' => function ($q) {
                        $q->with(['gllink' => function ($q) {
                            $q->with('subcategory');
                            $q->orderBy('sortOrder', 'asc');
                        }]);
                        $q->orderBy('sortOrder', 'asc');
                    }]);
                    $q->orderBy('sortOrder', 'asc');
                }]);
                $q->orderBy('sortOrder', 'asc');
            }]);
            $q->orderBy('sortOrder', 'asc');
        }, 'subcategorytot' => function ($q) {
            $q->with('subcategory');
        }, 'gllink'])->OfMaster($input['cashFlowTemplateID'])->whereNull('masterID')->orderBy('sortOrder')->get();

        $reportMasterData = CashFlowReport::with(['finance_year_by'])->find($cashFlowReportID);


        $dataCashFlow = array();

        foreach ($reportTemplateDetails as $data) {
            foreach ($data->subcategory as $dt) {
                    foreach ($dt->subcategory as $da) {
                        if ($dt->logicType == 1 || $dt->logicType == 3 || $dt->logicType == 4) {
                            if ($dt->logicType == 1) {
                            if ($da->gllink != "[]") {
                                $glLinkAutoID = $da->gllink[0]->glAutoID;
                                if ($da->gllink[0]->categoryType == 1) {
                                    $balGlTot = GeneralLedger::where('documentDate', "<=", $cashFlowReport->date)->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentLocalAmount');
                                    $balGlTotRpt = GeneralLedger::where('documentDate', "<=", $cashFlowReport->date)->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentRptAmount');

                                    $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                                    $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                                    $dataCashFlow['subCategoryID'] = $da->id;
                                    $dataCashFlow['localAmount'] = $balGlTot;
                                    $dataCashFlow['rptAmount'] = $balGlTotRpt;
                                    CashFlowSubCategoryGLCode::create($dataCashFlow);
                                }
                                if ($da->gllink[0]->categoryType == 2) {
                                    if ($reportMasterData) {
                                        $plGlTot = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $cashFlowReport->date)->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentLocalAmount');
                                        $balGlTotRpt = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $cashFlowReport->date)->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentRptAmount');

                                        $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                                        $dataCashFlow['chartOfAccountID'] = $da->gllink[0]->glAutoID;
                                        $dataCashFlow['subCategoryID'] = $da->id;
                                        $dataCashFlow['localAmount'] = $plGlTot;
                                        $dataCashFlow['rptAmount'] = $balGlTotRpt;
                                        CashFlowSubCategoryGLCode::create($dataCashFlow);

                                    }
                                }
                            }
                        }

                        if ($dt->logicType == 4) {
                            if ($reportMasterData) {
                                if ($da->gllink != "[]") {
                                    $glLinkAutoID = $da->gllink[0]->glAutoID;
                                    if ($da->gllink[0]->categoryType == 1) {

                                        $plGlTot = GeneralLedger::where('chartOfAccountSystemID', $glLinkAutoID)->where('documentDate', "<=", $reportMasterData->finance_year_by->bigginingDate)->sum('documentLocalAmount');
                                        $balGlTotRpt = GeneralLedger::where('chartOfAccountSystemID', $glLinkAutoID)->where('documentDate', "<=", $reportMasterData->finance_year_by->bigginingDate)->sum('documentRptAmount');

                                        $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                                        $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                                        $dataCashFlow['subCategoryID'] = $da->id;
                                        $dataCashFlow['localAmount'] = $plGlTot;
                                        $dataCashFlow['rptAmount'] = $balGlTotRpt;
                                        CashFlowSubCategoryGLCode::create($dataCashFlow);
                                    }
                                }
                            }
                        }

                        if ($dt->logicType == 5) {
                            if ($reportMasterData) {
                                if ($da->gllink != "[]") {
                                    $glLinkAutoID = $da->gllink[0]->glAutoID;

                                    $plGlTot = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $reportMasterData->finance_year_by->endingDate)->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentLocalAmount');
                                    $balGlTotRpt = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $reportMasterData->finance_year_by->endingDate)->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentRptAmount');
                                        $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;

                                    $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                                    $dataCashFlow['subCategoryID'] = $da->id;
                                    $dataCashFlow['localAmount'] = $plGlTot;
                                    $dataCashFlow['rptAmount'] = $balGlTotRpt;
                                    CashFlowSubCategoryGLCode::create($dataCashFlow);
                                }
                            }
                        }
                    }
                }
            }
        }

            return $this->sendResponse([$cashFlowReport->toArray(), $reportTemplateDetails], 'Cash Flow Report saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowReports/{id}",
     *      summary="Display the specified CashFlowReport",
     *      tags={"CashFlowReport"},
     *      description="Get CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReport",
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
     *                  ref="#/definitions/CashFlowReport"
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
        /** @var CashFlowReport $cashFlowReport */
        $cashFlowReport = $this->cashFlowReportRepository->findWithoutFail($id);

        if (empty($cashFlowReport)) {
            return $this->sendError('Cash Flow Report not found');
        }

        return $this->sendResponse($cashFlowReport->toArray(), 'Cash Flow Report retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCashFlowReportAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowReports/{id}",
     *      summary="Update the specified CashFlowReport in storage",
     *      tags={"CashFlowReport"},
     *      description="Update CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReport",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowReport that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowReport")
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
     *                  ref="#/definitions/CashFlowReport"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowReportAPIRequest $request)
    {
        $input = $request->all();

        /** @var CashFlowReport $cashFlowReport */
        $cashFlowReport = $this->cashFlowReportRepository->findWithoutFail($id);

        if (empty($cashFlowReport)) {
            return $this->sendError('Cash Flow Report not found');
        }

        $cashFlowReport = $this->cashFlowReportRepository->update($input, $id);

        return $this->sendResponse($cashFlowReport->toArray(), 'CashFlowReport updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowReports/{id}",
     *      summary="Remove the specified CashFlowReport from storage",
     *      tags={"CashFlowReport"},
     *      description="Delete CashFlowReport",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReport",
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
        /** @var CashFlowReport $cashFlowReport */
        $cashFlowReport = $this->cashFlowReportRepository->findWithoutFail($id);

        if (empty($cashFlowReport)) {
            return $this->sendError('Cash Flow Report not found');
        }
        $cashFlowReport->delete();
        $cashFlowGL = CashFlowSubCategoryGLCode::where('cashFlowReportID',$id)->first();
        if($cashFlowGL){
            $cashFlowGL->delete();
        }

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.cash_flow_report')]));
    }

    public function getCashFlowFormData(Request $request)
    {
        $input = $request->all();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
                                                ->where('companySystemID', $input['companySystemID'])
                                                ->get();

        $templates = CashFlowTemplate::OfCompany($input['companySystemID'])
                                     ->get();

        $yesNoSelection = YesNoSelection::all();


        $data = [
            'companyFinanceYear' => $companyFinanceYear,
            'templates' => $templates,
            'yesNoSelection'=> $yesNoSelection
        ];

        return $this->sendResponse($data, "Form data retrived successfully");
    }


    public function getCashFlowReports(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $results = CashFlowReport::with(['finance_year_by'])
                                 ->whereIn('companySystemID', $subCompanies);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $results = $results->where(function ($query) use ($search) {
                $query->where('description', 'like', "%{$search}%");
            });
        }

        return \DataTables::of($results)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCashFlowReportData(Request $request)
    {
        $input = $request->all();

        $reportMasterData = CashFlowReport::with(['finance_year_by','template','confirmed_by'])->find($input['id']);

        $reportDetails = [];
        if ($reportMasterData) {
            $reportTemplateDetails = CashFlowTemplateDetail::selectRaw('*,0 as expanded')->with(['subcategory' => function ($q) {
                                                            $q->with(['gllink' => function ($q) {
                                                                $q->with('subcategory');
                                                                $q->orderBy('sortOrder', 'asc');
                                                            }, 'subcategory' => function ($q) {
                                                                $q->with(['gllink' => function ($q) {
                                                                    $q->with('subcategory');
                                                                    $q->orderBy('sortOrder', 'asc');
                                                                }, 'subcategory' => function ($q) {
                                                                    $q->with(['gllink' => function ($q) {
                                                                        $q->with('subcategory');
                                                                        $q->orderBy('sortOrder', 'asc');
                                                                    }, 'subcategory' => function ($q) {
                                                                        $q->with(['gllink' => function ($q) {
                                                                            $q->with('subcategory');
                                                                            $q->orderBy('sortOrder', 'asc');
                                                                        }]);
                                                                        $q->orderBy('sortOrder', 'asc');
                                                                    }]);
                                                                    $q->orderBy('sortOrder', 'asc');
                                                                }]);
                                                                $q->orderBy('sortOrder', 'asc');
                                                            }]);
                                                            $q->orderBy('sortOrder', 'asc');
                                                        }, 'subcategorytot' => function ($q) {
                                                            $q->with('subcategory');
                                                        }, 'gllink'])->OfMaster($reportMasterData->cashFlowTemplateID)->whereNull('masterID')->orderBy('sortOrder')->get();
            foreach ($reportTemplateDetails as $data) {
                foreach ($data->subcategory as $dt) {
                    if ($dt->logicType == 2 || $dt->logicType == 3 || $dt->logicType == 6) {
                        $amount = CashFlowSubCategoryGLCode::where('subCategoryID',$dt->id)->where('cashFlowReportID',$input['id'])->sum('localAmount');
                        if($amount){
                            $dt->amount = $amount;
                        }
                    }
                    if ($dt->logicType == 1 || $dt->logicType == 4  || $dt->logicType == 5) {
                        foreach ($dt->subcategory as $da) {
                            $amount = CashFlowSubCategoryGLCode::where('subCategoryID',$da->id)->where('cashFlowReportID',$input['id'])->sum('localAmount');
                            if($amount){
                                $da->amount = $amount;
                            }
                        }
                    }
                }
            }

            $output = ['template' => $reportMasterData->toArray(), 'details' => $reportTemplateDetails->toArray()];


        }
        else{
            $output = [];
        }



        return $this->sendResponse($output, 'Report Template Details retrieved successfully');   
    }

    public function getCashFlowPullingItems(Request $request){

        $dataCashFlow = $request->dataCashFlow;
        $dataCashFlow = (array)$dataCashFlow;
        $glAutoID = collect($dataCashFlow)->pluck('glAutoID');
        $companySystemID = collect($dataCashFlow)->pluck('companySystemID');
        $companySystemID = isset($companySystemID[0]) ? $companySystemID[0] : $companySystemID;

        $confimedYN = collect($dataCashFlow)->pluck('confimedYN');
        $confimedYN = isset($confimedYN[0]) ? $confimedYN[0] : $confimedYN;

        $cashFlowReportID = collect($dataCashFlow)->pluck('cashFlowReportID');
        $cashFlowReportID = isset($cashFlowReportID[0]) ? $cashFlowReportID[0] : $cashFlowReportID;


        $details = DB::select('SELECT * FROM (SELECT
	erp_grvmaster.grvPrimaryCode AS grvPrimaryCode,
    erp_grvdetails.netAmount as grvAmount,
    erp_bookinvsuppmaster.bookingInvCode as bookingInvCode,
    erp_bookinvsupp_item_det.totLocalAmount as bsiAmountLocal,
    erp_paysupplierinvoicedetail.localAmount as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode,
    erp_paysupplierinvoicemaster.PayMasterAutoID as pvID,
    erp_paysupplierinvoicedetail.payDetailAutoID as pvDetailID,
    erp_grvdetails.financeGLcodePLSystemID as glAutoID
	FROM 
    erp_grvdetails
    LEFT JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID
    LEFT JOIN erp_bookinvsupp_item_det ON erp_grvdetails.grvDetailsID = erp_bookinvsupp_item_det.grvDetailsID
    LEFT JOIN erp_bookinvsuppmaster ON erp_bookinvsupp_item_det.bookingSuppMasInvAutoID = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
    LEFT JOIN erp_paysupplierinvoicedetail ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_paysupplierinvoicedetail.bookingInvSystemCode
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.payMasterAutoId = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_grvdetails.financeGLcodePLSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_grvdetails.companySystemID = '.$companySystemID.' AND
    erp_bookinvsuppmaster.bookingInvCode IS NOT NULL AND
    erp_paysupplierinvoicemaster.approved = -1 AND
    erp_paysupplierinvoicemaster.BPVcode IS NOT NULL
    )AS t1
    UNION ALL
      SELECT
      * FROM
      (SELECT
    "-" AS grvPrimaryCode,
    NULL as grvAmount,
    erp_bookinvsuppmaster.bookingInvCode as bookingInvCode,
    erp_directinvoicedetails.netAmountLocal as bsiAmountLocal,
    erp_paysupplierinvoicedetail.localAmount as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode,
    erp_paysupplierinvoicemaster.PayMasterAutoID as pvID,
    erp_paysupplierinvoicedetail.payDetailAutoID as pvDetailID,
    erp_directinvoicedetails.chartOfAccountSystemID as glAutoID
	FROM 
    erp_directinvoicedetails
    LEFT JOIN erp_bookinvsuppmaster ON erp_directinvoicedetails.directInvoiceAutoID = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
    LEFT JOIN erp_paysupplierinvoicedetail ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_paysupplierinvoicedetail.bookingInvSystemCode
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.payMasterAutoId = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_directinvoicedetails.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_directinvoicedetails.companySystemID = '.$companySystemID.' AND
    erp_paysupplierinvoicemaster.approved = -1 AND
    erp_paysupplierinvoicemaster.BPVcode IS NOT NULL
) AS t2
    UNION ALL
    SELECT
      * FROM
      (SELECT
    "-" AS grvPrimaryCode,
    NULL as grvAmount,
    "-" as bookingInvCode,
    NULL as bsiAmountLocal,
    erp_directpaymentdetails.netAmountLocal as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode,
    erp_paysupplierinvoicemaster.PayMasterAutoID as pvID,
    erp_directpaymentdetails.directPaymentDetailsID as pvDetailID,
    erp_directpaymentdetails.chartOfAccountSystemID as glAutoID
	FROM 
    erp_directpaymentdetails
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_directpaymentdetails.directPaymentAutoID = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_directpaymentdetails.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_paysupplierinvoicemaster.approved = -1 AND
    erp_directpaymentdetails.companySystemID = '.$companySystemID.'
    ) AS t3');

        foreach($details as $detail)
        {
            $pv = CashFlowSubCategoryGLCode::where('pvID', $detail->pvID)->where('pvDetailID', $detail->pvDetailID)->where('cashFlowReportID',$cashFlowReportID)->first();
            $detail->cashFlowAmount = null;
            if($pv){
                $detail->cashFlowAmount = number_format($pv->localAmount,3);;
            }

        }
        $confimedYN = isset($confimedYN[0]) ? $confimedYN[0] : 0;
    if($confimedYN == 1){
        foreach ($details as $key => $detail) {
            if($detail->cashFlowAmount == null){
                unset($details[$key]);
            }
        }
        $details = array_values($details);
    }



        return $this->sendResponse($details, 'Report Template Details retrieved successfully');

    }


    public function getCashFlowPullingItemsForProceeds(Request $request){

        $dataCashFlow = $request->dataCashFlow;
        $dataCashFlow = (array)$dataCashFlow;
        $glAutoID = collect($dataCashFlow)->pluck('glAutoID');
        $companySystemID = collect($dataCashFlow)->pluck('companySystemID');
        $companySystemID = isset($companySystemID[0]) ? $companySystemID[0] : $companySystemID;

        $confimedYN = collect($dataCashFlow)->pluck('confimedYN');
        $confimedYN = isset($confimedYN[0]) ? $confimedYN[0] : $confimedYN;

        $cashFlowReportID = collect($dataCashFlow)->pluck('cashFlowReportID');
        $cashFlowReportID = isset($cashFlowReportID[0]) ? $cashFlowReportID[0] : $cashFlowReportID;

        $details = DB::select('SELECT * FROM (SELECT
	erp_delivery_order.deliveryOrderCode AS deliveryOrderCode,
    erp_delivery_order_detail.companyLocalAmount as deliveryAmount,
    erp_custinvoicedirect.bookingInvCode as bookingInvCode,
    erp_customerinvoiceitemdetails.issueCostLocalTotal as custAmountLocal,
    erp_custreceivepaymentdet.bookingAmountLocal as receiveAmountLocal,
    erp_customerreceivepayment.custPaymentReceiveCode as receiveCode,
    erp_customerreceivepayment.custReceivePaymentAutoID as brvID,
    erp_custreceivepaymentdet.custRecivePayDetAutoID as brvDetailID,
    erp_delivery_order_detail.financeGLcodePLSystemID as glAutoID
	FROM 
    erp_delivery_order_detail
    LEFT JOIN erp_delivery_order ON erp_delivery_order_detail.deliveryOrderID = erp_delivery_order.deliveryOrderID
    LEFT JOIN erp_customerinvoiceitemdetails ON erp_delivery_order_detail.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
    LEFT JOIN erp_custinvoicedirect ON erp_customerinvoiceitemdetails.custInvoiceDirectAutoID = erp_custinvoicedirect.custInvoiceDirectAutoID
    LEFT JOIN erp_custreceivepaymentdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custreceivepaymentdet.bookingInvCodeSystem
    LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
    WHERE
    erp_delivery_order_detail.financeGLcodePLSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_custinvoicedirect.bookingInvCode IS NOT NULL AND
    erp_delivery_order_detail.companySystemID = '.$companySystemID.' AND
    erp_customerreceivepayment.approved = -1 AND
    erp_customerreceivepayment.custPaymentReceiveCode IS NOT NULL
    )AS t1
    UNION ALL
      SELECT
      * FROM
      (SELECT
	"-" AS deliveryOrderCode,
    NULL as deliveryAmount,
    erp_custinvoicedirect.bookingInvCode as bookingInvCode,
    erp_customerinvoiceitemdetails.issueCostLocalTotal as custAmountLocal,
    erp_custreceivepaymentdet.bookingAmountLocal as receiveAmountLocal,
    erp_customerreceivepayment.custPaymentReceiveCode as receiveCode,
    erp_customerreceivepayment.custReceivePaymentAutoID as brvID,
    erp_custreceivepaymentdet.custRecivePayDetAutoID as brvDetailID,
    erp_customerinvoiceitemdetails.financeGLcodePLSystemID as glAutoID
	FROM 
    erp_customerinvoiceitemdetails
    LEFT JOIN erp_custinvoicedirect ON erp_customerinvoiceitemdetails.custInvoiceDirectAutoID = erp_custinvoicedirect.custInvoiceDirectAutoID
    LEFT JOIN erp_custreceivepaymentdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custreceivepaymentdet.bookingInvCodeSystem
    LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
    WHERE
    erp_customerinvoiceitemdetails.financeGLcodePLSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_custinvoicedirect.companySystemID = '.$companySystemID.' AND
    erp_customerreceivepayment.approved = -1 AND
    erp_customerreceivepayment.custPaymentReceiveCode IS NOT NULL
) AS t2
  UNION ALL
    SELECT
      * FROM
      (SELECT
	"-" AS deliveryOrderCode,
    NULL as deliveryAmount,
    "-" as bookingInvCode,
    NULL as custAmountLocal,
    erp_directreceiptdetails.localAmount as receiveAmountLocal,
    erp_customerreceivepayment.custPaymentReceiveCode as receiveCode,
    erp_customerreceivepayment.custReceivePaymentAutoID as brvID,
    erp_directreceiptdetails.directReceiptAutoID as brvDetailID,
    erp_directreceiptdetails.chartOfAccountSystemID as glAutoID
	FROM 
    erp_directreceiptdetails
    LEFT JOIN erp_customerreceivepayment ON erp_directreceiptdetails.directReceiptAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
    WHERE
    erp_directreceiptdetails.companySystemID = '.$companySystemID.' AND
    erp_customerreceivepayment.approved = -1 AND
    erp_directreceiptdetails.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') 
) AS t3');

        foreach($details as $detail)
        {
            $brv = CashFlowSubCategoryGLCode::where('brvID', $detail->brvID)->where('brvDetailID', $detail->brvDetailID)->where('cashFlowReportID',$cashFlowReportID)->first();
            if($brv){
                $detail->cashFlowAmount = number_format($brv->localAmount,3);
            }
        }
        $confimedYN = isset($confimedYN[0]) ? $confimedYN[0] : 0;
        if($confimedYN == 1){
            foreach ($details as $key => $detail) {
                if($detail->cashFlowAmount == null){
                    unset($details[$key]);
                }
            }
            $details = array_values($details);
        }

        return $this->sendResponse($details, 'Report Template Details retrieved successfully');

    }

    public function postCashFlowPulledItems(Request $request){

        $details = $request->data;
        $subCategoryID = $request->subCategoryID;
        $cashFlowReportID = $request->cashFlowReportID;

        $data = array();
        foreach($details as $detail){
            $applicableAmount = 0;
            if($detail['grvAmount'] != null){
                if($detail['payAmountLocal'] <  $detail['bsiAmountLocal'] && $detail['payAmountLocal'] <  $detail['grvAmount']){
                    $applicableAmount = $detail['payAmountLocal'];
                }
                if($detail['bsiAmountLocal'] <  $detail['payAmountLocal'] && $detail['bsiAmountLocal'] <  $detail['grvAmount']){
                    $applicableAmount = $detail['bsiAmountLocal'];
                }
                if($detail['grvAmount'] <  $detail['payAmountLocal'] && $detail['grvAmount'] <  $detail['bsiAmountLocal']){
                    $applicableAmount = $detail['grvAmount'];
                }
                if($detail['grvAmount'] ==  $detail['payAmountLocal'] && $detail['grvAmount'] ==  $detail['bsiAmountLocal']){
                    $applicableAmount = $detail['grvAmount'];
                }
            }
            if($detail['grvAmount'] == null){
                if($detail['payAmountLocal'] <  $detail['bsiAmountLocal']){
                    $applicableAmount = $detail['payAmountLocal'];
                }
                if($detail['bsiAmountLocal'] <  $detail['payAmountLocal']){
                    $applicableAmount = $detail['bsiAmountLocal'];
                }
                if($detail['bsiAmountLocal'] ==  $detail['payAmountLocal']){
                    $applicableAmount = $detail['bsiAmountLocal'];
                }
            }
            if($detail['grvAmount'] == null && $detail['bsiAmountLocal'] == null) {
                $applicableAmount = $detail['payAmountLocal'];
            }
            if(isset($detail['cashFlowAmount'])) {
                if($detail['cashFlowAmount'] > $applicableAmount){
                    return $this->sendError('Cash Flow Amount is greater than applicable amount', 500);
                }
                $data['localAmount'] = $detail['cashFlowAmount'];
                $data['subCategoryID'] = $subCategoryID;
                $data['chartOfAccountID'] = $detail['glAutoID'];
                $data['pvID'] = $detail['pvID'];
                $data['pvDetailID'] = $detail['pvDetailID'];
                $data['cashFlowReportID'] = $cashFlowReportID;
                $data['rptAmount'] = 0;

                $pvID = CashFlowSubCategoryGLCode::where('pvID', $detail['pvID'])->where('pvDetailID', $detail['pvDetailID'])->first();
                if($pvID){
                    CashFlowSubCategoryGLCode::where('pvID', $detail['pvID'])->where('pvDetailID', $detail['pvDetailID'])->update($data);
                }
                else{
                    CashFlowSubCategoryGLCode::create($data);
                }
            }
        }
        return $this->sendResponse($details, 'Report Template Details retrieved successfully');
    }

    public function postCashFlowPulledItemsForProceeds(Request $request){

        $details = $request->data;
        $subCategoryID = $request->subCategoryID;
        $cashFlowReportID = $request->cashFlowReportID;

        $data = array();
        foreach($details as $detail){
            $applicableAmount = 0;
            if($detail['deliveryAmount'] != null){
                if($detail['receiveAmountLocal'] <  $detail['custAmountLocal'] && $detail['receiveAmountLocal'] <  $detail['deliveryAmount']){
                    $applicableAmount = $detail['payAmountLocal'];
                }
                if($detail['custAmountLocal'] <  $detail['receiveAmountLocal'] && $detail['custAmountLocal'] <  $detail['deliveryAmount']){
                    $applicableAmount = $detail['custAmountLocal'];
                }
                if($detail['deliveryAmount'] <  $detail['receiveAmountLocal'] && $detail['deliveryAmount'] <  $detail['custAmountLocal']){
                    $applicableAmount = $detail['deliveryAmount'];
                }
                if($detail['deliveryAmount'] ==  $detail['receiveAmountLocal'] && $detail['deliveryAmount'] ==  $detail['custAmountLocal']){
                    $applicableAmount = $detail['deliveryAmount'];
                }
            }
            if($detail['deliveryAmount'] == null){
                if($detail['receiveAmountLocal'] <  $detail['custAmountLocal']){
                    $applicableAmount = $detail['payAmountLocal'];
                }
                if($detail['custAmountLocal'] <  $detail['receiveAmountLocal']){
                    $applicableAmount = $detail['custAmountLocal'];
                }
                if($detail['custAmountLocal'] ==  $detail['receiveAmountLocal']){
                    $applicableAmount = $detail['custAmountLocal'];
                }
            }
            if($detail['deliveryAmount'] == null && $detail['custAmountLocal'] == null) {
                $applicableAmount = $detail['receiveAmountLocal'];
            }
            if(isset($detail['cashFlowAmount'])) {
                    $applicableAmount = number_format($applicableAmount,3);
                if ($detail['cashFlowAmount'] > $applicableAmount) {
                    return $this->sendError('Cash Flow Amount is greater than applicable amount', 500);
                }
                $data['localAmount'] = $detail['cashFlowAmount'];
                $data['subCategoryID'] = $subCategoryID;
                $data['brvID'] = $detail['brvID'];
                $data['brvDetailID'] = $detail['brvDetailID'];
                $data['chartOfAccountID'] = $detail['glAutoID'];
                $data['cashFlowReportID'] = $cashFlowReportID;
                $data['rptAmount'] = 0;

                $brvID = CashFlowSubCategoryGLCode::where('brvID', $detail['brvID'])->where('brvDetailID', $detail['brvDetailID'])->first();
                if($brvID){
                    CashFlowSubCategoryGLCode::where('brvID', $detail['brvID'])->where('brvDetailID', $detail['brvDetailID'])->update($data);
                }
                else{
                    CashFlowSubCategoryGLCode::create($data);
                }
            }
        }
        return $this->sendResponse($details, 'Report Template Details retrieved successfully');
    }

    public function cashFlowConfirmation(Request $request){
        $input = $request->reportData;
        $input = array_except($input, ['finance_year_by','template','confirmed_by']);
        $input['confirmed_by'] = \Helper::getEmployeeSystemID();
        $input['confirmed_date'] = now();
        $cashFlowReport = $this->cashFlowReportRepository->update($input, $input['id']);


        return $this->sendResponse($cashFlowReport, 'Report Template Details retrieved successfully');
    }
}
