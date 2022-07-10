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

                                    $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                                    $dataCashFlow['subCategoryID'] = $da->id;
                                    $dataCashFlow['localAmount'] = $balGlTot;
                                    $dataCashFlow['rptAmount'] = 0;
                                    CashFlowSubCategoryGLCode::create($dataCashFlow);
                                }
                                if ($da->gllink[0]->categoryType == 2) {
                                    $companyFiancePeriod = CompanyFinanceYear::where('bigginingDate', "<=", $cashFlowReport->date)->where('endingDate', ">=", $cashFlowReport->date)->first();
                                    if ($companyFiancePeriod) {
                                        $plGlTot = GeneralLedger::where('documentDate', ">=", $companyFiancePeriod->bigginingDate)->where('documentDate', "<=", $companyFiancePeriod->endingDate)->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentLocalAmount');

                                        $dataCashFlow['chartOfAccountID'] = $da->gllink[0]->glAutoID;
                                        $dataCashFlow['subCategoryID'] = $da->id;
                                        $dataCashFlow['localAmount'] = $plGlTot;
                                        $dataCashFlow['rptAmount'] = 0;
                                        CashFlowSubCategoryGLCode::create($dataCashFlow);

                                    }
                                }
                            }
                        }

                        if ($dt->logicType == 4) {
                            $companyFiancePeriod = CompanyFinancePeriod::where('dateFrom', ">=", $cashFlowReport->date)->where('dateTo', "<=", $cashFlowReport->date)->first();
                            if ($companyFiancePeriod) {
                                if ($da->gllink != "[]") {
                                    $glLinkAutoID = $da->gllink[0]->glAutoID;
                                    $plGlTot = GeneralLedger::where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentLocalAmount');
                                    $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                                    $dataCashFlow['subCategoryID'] = $da->id;
                                    $dataCashFlow['localAmount'] = $plGlTot;
                                    $dataCashFlow['rptAmount'] = 0;
                                    CashFlowSubCategoryGLCode::create($dataCashFlow);
                                }
                            }
                        }

                        if ($dt->logicType == 5) {
                            $companyFiancePeriod = CompanyFinancePeriod::where('dateFrom', ">=", $cashFlowReport->date)->where('dateTo', "<=", $cashFlowReport->date)->first();
                            if ($companyFiancePeriod) {
                                if ($da->gllink != "[]") {
                                    $glLinkAutoID = $da->gllink[0]->glAutoID;

                                    $plGlTot = GeneralLedger::where('documentDate', "<=", $companyFiancePeriod->bigginingDate)->where('documentDate', "<", $companyFiancePeriod->endingDate)->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentLocalAmount');
                                        $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;


                                    $dataCashFlow['subCategoryID'] = $da->id;
                                    $dataCashFlow['localAmount'] = $plGlTot;
                                    $dataCashFlow['rptAmount'] = 0;
                                    CashFlowSubCategoryGLCode::create($dataCashFlow);
                                }
                            }
                        }


                        if($dt->logicType == 3){
                            $payments = PaySupplierInvoiceMaster::where('invoiceType', 3)->with(['directdetail'])->get();
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

        return $this->sendSuccess('Cash Flow Report deleted successfully');
    }

    public function getCashFlowFormData(Request $request)
    {
        $input = $request->all();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear"))
                                                ->where('companySystemID', $input['companySystemID'])
                                                ->get();

        $templates = CashFlowTemplate::OfCompany($input['companySystemID'])
                                     ->get();

        $data = [
            'companyFinanceYear' => $companyFinanceYear,
            'templates' => $templates
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

        $reportMasterData = CashFlowReport::with(['finance_year_by', 'template'])->find($input['id']);

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



        }

        foreach ($reportTemplateDetails as $data) {
            foreach ($data->subcategory as $dt) {
                if ($dt->logicType == 1 || $dt->logicType == 4  || $dt->logicType == 5) {
                    foreach ($dt->subcategory as $da) {
                        $amount = CashFlowSubCategoryGLCode::where('subCategoryID',$da->id)->first();
                        if($amount){
                            $da->amount = $amount->localAmount;

                        }
                    }
                }
            }
        }
     
        $output = ['template' => $reportMasterData->toArray(), 'details' => $reportTemplateDetails->toArray()];

        return $this->sendResponse($output, 'Report Template Details retrieved successfully');   
    }

    public function getCashFlowPullingItems(Request $request){

        $dataCashFlow = $request->dataCashFlow;
        $dataCashFlow = (array)$dataCashFlow;
        $dataCashFlow = collect($dataCashFlow)->pluck('glAutoID');

        $details = DB::select('SELECT * FROM (SELECT
	erp_grvmaster.grvPrimaryCode AS grvPrimaryCode,
    erp_grvdetails.netAmount as grvAmount,
    erp_bookinvsuppmaster.bookingInvCode as bookingInvCode,
    erp_bookinvsupp_item_det.totLocalAmount as bsiAmountLocal,
    erp_paysupplierinvoicedetail.localAmount as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode
	FROM 
    erp_grvdetails
    LEFT JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID
    LEFT JOIN erp_bookinvsupp_item_det ON erp_grvdetails.grvDetailsID = erp_bookinvsupp_item_det.grvDetailsID
    LEFT JOIN erp_bookinvsuppmaster ON erp_bookinvsupp_item_det.bookingSuppMasInvAutoID = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
    LEFT JOIN erp_paysupplierinvoicedetail ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_paysupplierinvoicedetail.bookingInvSystemCode
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.payMasterAutoId = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_grvdetails.financeGLcodePLSystemID IN (' . join(',', json_decode($dataCashFlow)) . ')
    )AS t1');


        return $this->sendResponse($details, 'Report Template Details retrieved successfully');

    }
}
