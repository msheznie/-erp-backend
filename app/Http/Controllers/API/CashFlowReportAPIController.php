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
use App\Models\Company;


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
                if ($dt->logicType == 4) {
                    foreach ($dt->gllink as $gl){
                        $glLinkAutoID = $gl->glAutoID;
                    if ($reportMasterData) {
                        if ($gl->categoryType == 2) {

                            $plGlTot = GeneralLedger::where('chartOfAccountSystemID', $glLinkAutoID)->where('documentDate', "<=", $reportMasterData->finance_year_by->bigginingDate)->where('companySystemID', $input['companySystemID'])->sum('documentLocalAmount');
                            $balGlTotRpt = GeneralLedger::where('chartOfAccountSystemID', $glLinkAutoID)->where('documentDate', "<=", $reportMasterData->finance_year_by->bigginingDate)->where('companySystemID', $input['companySystemID'])->sum('documentRptAmount');

                            $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                            $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                            $dataCashFlow['subCategoryID'] = $dt->id;
                            $dataCashFlow['localAmount'] = $plGlTot;
                            $dataCashFlow['rptAmount'] = $balGlTotRpt;
                            CashFlowSubCategoryGLCode::create($dataCashFlow);
                        }
                    }
                    }
                }

                if ($dt->logicType == 5) {
                    foreach ($dt->gllink as $gl){
                        $glLinkAutoID = $gl->glAutoID;
                    if ($reportMasterData) {

                        $plGlTot = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $reportMasterData->finance_year_by->endingDate)->where('companySystemID', $input['companySystemID'])->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentLocalAmount');
                        $balGlTotRpt = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $reportMasterData->finance_year_by->endingDate)->where('companySystemID', $input['companySystemID'])->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentRptAmount');
                        $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;

                        $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                        $dataCashFlow['subCategoryID'] = $dt->id;
                        $dataCashFlow['localAmount'] = $plGlTot;
                        $dataCashFlow['rptAmount'] = $balGlTotRpt;
                        CashFlowSubCategoryGLCode::create($dataCashFlow);
                    }
                }
                }

                if ($dt->logicType == 1) {
                    foreach ($dt->gllink as $gl){
                        $glLinkAutoID = $gl->glAutoID;
                        // if ($gl->categoryType == 1) {
                        //     $balGlTot = GeneralLedger::where('documentDate', "<=", $cashFlowReport->date)->where('companySystemID',$input['companySystemID'])->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentLocalAmount');
                        //     $balGlTotRpt = GeneralLedger::where('documentDate', "<=", $cashFlowReport->date)->where('companySystemID',$input['companySystemID'])->where('chartOfAccountSystemID',$glLinkAutoID)->sum('documentRptAmount');

                        //     $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                        //     $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                        //     $dataCashFlow['subCategoryID'] = $dt->id;
                        //     $dataCashFlow['localAmount'] = $balGlTot;
                        //     $dataCashFlow['rptAmount'] = $balGlTotRpt;
                        //     CashFlowSubCategoryGLCode::create($dataCashFlow);
                        // }
                        //if ($gl->categoryType == 2) {
                            if ($reportMasterData) {
                                $plGlTot = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $cashFlowReport->date)->where('companySystemID',$input['companySystemID'])->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentLocalAmount');
                                $balGlTotRpt = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $cashFlowReport->date)->where('companySystemID',$input['companySystemID'])->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentRptAmount');

                                $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                                $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                                $dataCashFlow['subCategoryID'] = $dt->id;
                                $dataCashFlow['localAmount'] = $plGlTot;
                                $dataCashFlow['rptAmount'] = $balGlTotRpt;
                                CashFlowSubCategoryGLCode::create($dataCashFlow);

                            }
                        //}


                    }
                }
                foreach ($dt->subcategory as $da) {
                        $plGlTot = 0;
                        $balGlTotRpt = 0;
                        if($dt->description == 'Operating cash flows before working capital changes:' && $dt->logicType == 1 )
                        {
                            foreach ($da->gllink as $gl){
                                $glLinkAutoID = $gl->glAutoID;
                                 if ($reportMasterData) {
                                    $plGlTotLocal = GeneralLedger::selectRaw("
                                                    SUM(IF(documentLocalAmount >= 0, documentLocalAmount, 0)) AS documentLocalAmountDebit,
                                                    SUM(IF(documentLocalAmount < 0, -documentLocalAmount, 0)) AS documentLocalAmountCredit
                                                ")
                                                ->where('documentDate', '>=', $reportMasterData->finance_year_by->bigginingDate)
                                                ->where('documentDate', '<=', $cashFlowReport->date)
                                                ->where('chartOfAccountSystemID', $glLinkAutoID)
                                                ->first();

                                    $plGlTotRpt = GeneralLedger::selectRaw("
                                                    SUM(IF(documentRptAmount >= 0, documentRptAmount, 0)) AS documentLocalAmountDebit,
                                                    SUM(IF(documentRptAmount < 0, -documentRptAmount, 0)) AS documentLocalAmountCredit
                                                ")
                                                ->where('documentDate', '>=', $reportMasterData->finance_year_by->bigginingDate)
                                                ->where('documentDate', '<=', $cashFlowReport->date)
                                                ->where('chartOfAccountSystemID', $glLinkAutoID)
                                                ->first();

                                    $plGlTot = $plGlTotLocal ? $plGlTotLocal->documentLocalAmountCredit - $plGlTotLocal->documentLocalAmountDebit : 0;
                                    $balGlTotRpt = $plGlTotRpt ? $plGlTotRpt->documentLocalAmountCredit - $plGlTotRpt->documentLocalAmountDebit : 0;

                                    $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                                    $dataCashFlow['chartOfAccountID'] = $glLinkAutoID;
                                    $dataCashFlow['subCategoryID'] = $da->id;
                                    $dataCashFlow['localAmount'] = $plGlTot;
                                    $dataCashFlow['rptAmount'] = $balGlTotRpt;
                                    CashFlowSubCategoryGLCode::create($dataCashFlow);
                                 }
                            }
                        }
                        else if ($dt->logicType == 1 || $dt->logicType == 3 || $dt->logicType == 4) {
                            if ($dt->logicType == 1) {
                                foreach ($da->gllink as $gl){
                                $glLinkAutoID = $gl->glAutoID;
                                    if ($reportMasterData) {
                                        $plGlTot = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $cashFlowReport->date)->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentLocalAmount');
                                        $balGlTotRpt = GeneralLedger::where('documentDate', ">=", $reportMasterData->finance_year_by->bigginingDate)->where('documentDate', "<=", $cashFlowReport->date)->where('chartOfAccountSystemID', $glLinkAutoID)->sum('documentRptAmount');

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
                }
            }
        }

            $this->updateGroupTotalOfCashFlowTemplate($reportTemplateDetails, $cashFlowReportID);

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
        CashFlowSubCategoryGLCode::where('cashFlowReportID',$id)->delete();

        CashFlowReportDetail::where('cashFlowReportID',$id)->delete();


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

            $companyCurrency = \Helper::companyCurrency($reportMasterData->companySystemID);

            $companyCurrencyCode = isset($companyCurrency->localcurrency->CurrencyCode) ? $companyCurrency->localcurrency->CurrencyCode : '';

            $companyCurrencyDecimal = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces : 3;

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
                if ($data->logicType == 2) {
                    $amount = CashFlowSubCategoryGLCode::where('subCategoryID',$data->id)->where('cashFlowReportID',$input['id'])->sum('localAmount');
                    if($amount){
                        $data->amount = $amount;
                    }
                    else{
                        $isExist = CashFlowSubCategoryGLCode::where('subCategoryID',$data->id)->where('cashFlowReportID',$input['id'])->first();
                        if($isExist){
                            $data->amount = 0;
                        }
                    }
                }
                foreach ($data->subcategory as $dt) {
                    if ($dt->logicType == 1 || $dt->logicType == 2 || $dt->logicType == 3 || $dt->logicType == 6|| $dt->logicType == 4  || $dt->logicType == 5) {
                        $amount = CashFlowSubCategoryGLCode::where('subCategoryID',$dt->id)->where('cashFlowReportID',$input['id'])->sum('localAmount');
                        if($amount){
                            $dt->amount = $amount;
                        }
                        else{
                            $isExist = CashFlowSubCategoryGLCode::where('subCategoryID',$dt->id)->where('cashFlowReportID',$input['id'])->first();
                            if($isExist){
                                $dt->amount = 0;
                            }
                        }

                    }
                    if ($dt->logicType == 1) {
                        foreach ($dt->subcategory as $da) {
                            $amount = CashFlowSubCategoryGLCode::where('subCategoryID',$da->id)->where('cashFlowReportID',$input['id'])->sum('localAmount');
                            if($amount){
                                $da->amount = $amount;
                            }
                            else{
                                $isExist = CashFlowSubCategoryGLCode::where('subCategoryID',$da->id)->where('cashFlowReportID',$input['id'])->first();
                                if($isExist){
                                    $da->amount = 0;
                                }
                            }
                        }
                    }

                }
            }

            $output = ['template' => $reportMasterData->toArray(), 'details' => $reportTemplateDetails->toArray(), 'currency' => $companyCurrencyCode, 'currencyDecimal' => $companyCurrencyDecimal];


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

        $subCategoryID = collect($dataCashFlow)->pluck('subCategoryID');
        $subCategoryID = isset($subCategoryID[0]) ? $subCategoryID[0] : $subCategoryID;

        $cashFlowReportID = collect($dataCashFlow)->pluck('cashFlowReportID');
        $cashFlowReportID = isset($cashFlowReportID[0]) ? $cashFlowReportID[0] : $cashFlowReportID;


        $details = DB::select('SELECT * FROM (SELECT
	erp_grvmaster.grvPrimaryCode AS grvPrimaryCode,
	erp_grvmaster.grvAutoID AS grvAutoID,
    erp_bookinvsuppmaster.bookingSuppMasInvAutoID as bookingSuppMasInvAutoID,
    SUM(erp_grvmaster.grvTotalLocalCurrency) as grvAmount,
    erp_bookinvsuppmaster.bookingInvCode as bookingInvCode,
    SUM(erp_bookinvsupp_item_det.totLocalAmount) as bsiAmountLocal,
    SUM(ABS(erp_generalledger.documentLocalAmount)) as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode,
    erp_paysupplierinvoicemaster.PayMasterAutoID as pvID,
    erp_generalledger.GeneralLedgerID as pvDetailID,
    erp_generalledger.chartOfAccountSystemID as glAutoID,
    erp_generalledger.glCode as glCode
	FROM 
    erp_generalledger
    LEFT JOIN erp_grvmaster ON erp_generalledger.documentSystemCode = erp_grvmaster.grvAutoID
    LEFT JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
    LEFT JOIN erp_bookinvsupp_item_det ON erp_grvdetails.grvDetailsID = erp_bookinvsupp_item_det.grvDetailsID
    LEFT JOIN erp_bookinvsuppmaster ON erp_bookinvsupp_item_det.bookingSuppMasInvAutoID = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
    LEFT JOIN erp_paysupplierinvoicedetail ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_paysupplierinvoicedetail.bookingInvSystemCode
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.payMasterAutoId = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_generalledger.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_generalledger.documentSystemID = 3 AND
    erp_grvmaster.companySystemID = '.$companySystemID.' AND
    erp_grvmaster.grvPrimaryCode IS NOT NULL AND
    erp_paysupplierinvoicemaster.approved = -1 AND
    erp_paysupplierinvoicemaster.BPVcode IS NOT NULL GROUP BY glAutoID, grvAutoID, bookingSuppMasInvAutoID, pvID
    )AS t1
    UNION ALL
    SELECT
      * FROM
      (SELECT
	"-" AS grvPrimaryCode,
	NULL AS grvAutoID,
    erp_bookinvsuppmaster.bookingSuppMasInvAutoID as bookingSuppMasInvAutoID,
    NULL as grvAmount,
    erp_bookinvsuppmaster.bookingInvCode as bookingInvCode,
    SUM(erp_bookinvsuppmaster.bookingAmountLocal) as bsiAmountLocal,
    SUM(ABS(erp_generalledger.documentLocalAmount)) as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode,
    erp_paysupplierinvoicemaster.PayMasterAutoID as pvID,
    erp_generalledger.GeneralLedgerID as pvDetailID,
    erp_generalledger.chartOfAccountSystemID as glAutoID,
    erp_generalledger.glCode as glCode
	FROM 
    erp_generalledger
    LEFT JOIN erp_bookinvsuppmaster ON erp_generalledger.documentSystemCode = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
    LEFT JOIN erp_paysupplierinvoicedetail ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_paysupplierinvoicedetail.bookingInvSystemCode
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.payMasterAutoId = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_generalledger.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_generalledger.documentSystemID = 11 AND
    erp_paysupplierinvoicemaster.companySystemID = '.$companySystemID.' AND
    erp_bookinvsuppmaster.bookingInvCode IS NOT NULL AND
    erp_paysupplierinvoicemaster.approved = -1 AND
    erp_paysupplierinvoicemaster.BPVcode IS NOT NULL GROUP BY glAutoID, grvAutoID, bookingSuppMasInvAutoID, pvID
    )AS t2
    UNION ALL
    SELECT
      * FROM
      (SELECT
    "-" AS grvPrimaryCode,
    NULL as grvAmount,
    "-" as bookingInvCode,
    NULL as bsiAmountLocal,
    NULL AS grvAutoID,
    NULL as bookingSuppMasInvAutoID,
    SUM(ABS(erp_generalledger.documentLocalAmount)) as payAmountLocal,
    erp_paysupplierinvoicemaster.BPVcode as payCode,
    erp_paysupplierinvoicemaster.PayMasterAutoID as pvID,
    erp_generalledger.GeneralLedgerID as pvDetailID,
    erp_generalledger.chartOfAccountSystemID as glAutoID,
    erp_generalledger.glCode as glCode
	FROM 
    erp_generalledger
    LEFT JOIN erp_paysupplierinvoicemaster ON erp_generalledger.documentSystemCode = erp_paysupplierinvoicemaster.payMasterAutoId
    WHERE
    erp_generalledger.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
    erp_generalledger.documentSystemID = 4 AND
    erp_paysupplierinvoicemaster.approved = -1 AND
    erp_paysupplierinvoicemaster.companySystemID = '.$companySystemID.' GROUP BY glAutoID, grvAutoID, bookingSuppMasInvAutoID, pvID
    ) AS t3');

        foreach($details as $detail)
        {
            $pv = CashFlowSubCategoryGLCode::where('chartOfAccountID',$detail->glAutoID)->where('pvID', $detail->pvID)->where('grvID',$detail->grvAutoID)->where('invID',$detail->bookingSuppMasInvAutoID)->where('cashFlowReportID',$cashFlowReportID)->where('subCategoryID',$subCategoryID)->first();
            $detail->cashFlowAmount = null;
            if($pv){
                $companyCurrency = \Helper::companyCurrency($companySystemID);

                $companyCurrencyDecimal = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces : 3;
                $detail->cashFlowAmount = number_format($pv->localAmount,$companyCurrencyDecimal,'.','');
            }

        }
        $confimedYN = isset($confimedYN[0]) ? $confimedYN[0] : $confimedYN;
    if($confimedYN == 1){
        foreach ($details as $key => $detail) {
            if($detail->cashFlowAmount == null){
                unset($details[$key]);
            }
        }
        $details = array_values($details);
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
        }, 'gllink'])->OfMaster($cashFlowReportID)->whereNull('masterID')->orderBy('sortOrder')->get();

        $this->updateGroupTotalOfCashFlowTemplate($reportTemplateDetails, $cashFlowReportID);


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

        $subCategoryID = collect($dataCashFlow)->pluck('subCategoryID');
        $subCategoryID = isset($subCategoryID[0]) ? $subCategoryID[0] : $subCategoryID;

        $cashFlowReportID = collect($dataCashFlow)->pluck('cashFlowReportID');
        $cashFlowReportID = isset($cashFlowReportID[0]) ? $cashFlowReportID[0] : $cashFlowReportID;

        $details = DB::select('SELECT * FROM (SELECT
            "" AS deliveryOrderCode,
            "" AS deliveryOrderID,
            "" as custInvoiceDirectAutoID,
            "" as deliveryAmount,
            "" as bookingInvCode,
            "" as custAmountLocal,
            SUM(ABS(erp_generalledger.documentLocalAmount)) as receiveAmountLocal,
            erp_customerreceivepayment.custPaymentReceiveCode as receiveCode,
            erp_customerreceivepayment.custReceivePaymentAutoID as brvID,
            erp_generalledger.GeneralLedgerID as brvDetailID,
            erp_generalledger.chartOfAccountSystemID as glAutoID,
            erp_generalledger.glCode as glCode
            FROM 
            erp_generalledger
            LEFT JOIN erp_customerreceivepayment ON erp_generalledger.documentSystemCode = erp_customerreceivepayment.custReceivePaymentAutoID
            WHERE
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
            erp_generalledger.documentSystemID = 21 AND
            erp_customerreceivepayment.companySystemID = '.$companySystemID.' AND
            erp_customerreceivepayment.approved = -1 AND
            erp_customerreceivepayment.custPaymentReceiveCode IS NOT NULL GROUP BY glAutoID, deliveryOrderID, custInvoiceDirectAutoID, brvID
            )AS t1
            UNION ALL
            SELECT * FROM (SELECT
            "" AS deliveryOrderCode,
            "" AS deliveryOrderID,
            erp_custinvoicedirect.custInvoiceDirectAutoID as custInvoiceDirectAutoID,
            "" as deliveryAmount,
            erp_custinvoicedirect.bookingInvCode as bookingInvCode,
            erp_custinvoicedirect.bookingAmountLocal as custAmountLocal,
            SUM(ABS(erp_generalledger.documentLocalAmount)) as receiveAmountLocal,
            erp_customerreceivepayment.custPaymentReceiveCode as receiveCode,
            erp_customerreceivepayment.custReceivePaymentAutoID as brvID,
            erp_generalledger.GeneralLedgerID as brvDetailID,
            erp_generalledger.chartOfAccountSystemID as glAutoID,
            erp_generalledger.glCode as glCode
            FROM 
            erp_generalledger
            LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID
            LEFT JOIN erp_custreceivepaymentdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custreceivepaymentdet.bookingInvCodeSystem
            LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
            WHERE
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', json_decode($glAutoID)) . ') AND
            erp_generalledger.documentSystemID = 20 AND
            erp_customerreceivepayment.companySystemID = '.$companySystemID.' AND
            erp_customerreceivepayment.approved = -1 AND
            erp_customerreceivepayment.custPaymentReceiveCode IS NOT NULL GROUP BY glAutoID, deliveryOrderID, custInvoiceDirectAutoID, brvID
            )AS t1
            ');

        foreach($details as $detail)
        {
            $brv = CashFlowSubCategoryGLCode::where('chartOfAccountID',$detail->glAutoID)->where('brvID', $detail->brvID)->where('deoID', $detail->deliveryOrderID)->where('custInvID',$detail->custInvoiceDirectAutoID)->where('cashFlowReportID',$cashFlowReportID)->where('subCategoryID',$subCategoryID)->first();
            $detail->cashFlowAmount = null;
            if($brv){
                $companyCurrency = \Helper::companyCurrency($companySystemID);

                $companyCurrencyDecimal = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces : 3;
                $detail->cashFlowAmount = number_format($brv->localAmount,$companyCurrencyDecimal,'.','');
            }
        }
        $confimedYN = isset($confimedYN[0]) ? $confimedYN[0] : $confimedYN;
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


        $cashFlowData = CashFlowReport::find($request->cashFlowReportID);
        if($cashFlowData){

        $data = array();
        foreach($details as $detail){

            $applicableAmount = 0;

            $minArray=array();
            if($detail['grvAmount'] != null){
                array_push($minArray,$detail['grvAmount']);
            }
            if($detail['bsiAmountLocal'] != null){
                array_push($minArray,$detail['bsiAmountLocal']);
            }
            if($detail['payAmountLocal'] != null) {
                array_push($minArray,$detail['payAmountLocal']);
            }


            if(isset($detail['cashFlowAmount'])) {
                $companyCurrency = \Helper::companyCurrency($cashFlowData->companySystemID);

                $companyCurrencyDecimal = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces : 3;
                $applicableAmount = min($minArray);
                $applicableAmount = round($applicableAmount,$companyCurrencyDecimal);
                if ($detail['cashFlowAmount'] > $applicableAmount) {
                    return $this->sendError('Cash Flow Amount is greater than applicable amount', 500);
                }
                $data['localAmount'] = $detail['cashFlowAmount'];
                $data['subCategoryID'] = $subCategoryID;
                $data['chartOfAccountID'] = $detail['glAutoID'];
                $data['pvID'] = $detail['pvID'];
                $data['grvID'] = $detail['grvAutoID'];
                $data['invID'] = $detail['bookingSuppMasInvAutoID'];
                $data['pvDetailID'] = $detail['pvDetailID'];
                $data['cashFlowReportID'] = $cashFlowReportID;
                $data['rptAmount'] = 0;

                $pvData = CashFlowSubCategoryGLCode::where('chartOfAccountID', $data['chartOfAccountID'])->where('pvID', $data['pvID'])->where('grvID', $data['grvID'])->where('invID', $data['invID'])->where('cashFlowReportID', $data['cashFlowReportID'])->where('subCategoryID',$data['subCategoryID'])->first();
                if ($pvData) {
                    CashFlowSubCategoryGLCode::where('chartOfAccountID', $data['chartOfAccountID'])->where('pvID', $data['pvID'])->where('grvID', $data['grvID'])->where('subCategoryID',$data['subCategoryID'])->where('invID', $data['invID'])->where('cashFlowReportID', $data['cashFlowReportID'])->update($data);

                } else {
                    CashFlowSubCategoryGLCode::create($data);
                }
              }
            }
            $reportMasterData = CashFlowReport::find($cashFlowReportID);
            if($reportMasterData) {
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
                $this->updateGroupTotalOfCashFlowTemplate($reportTemplateDetails, $cashFlowReportID);
            }
        }
        return $this->sendResponse($details, 'Report Template Details retrieved successfully');
    }

    public function postCashFlowPulledItemsForProceeds(Request $request){

        $details = $request->data;
        $subCategoryID = $request->subCategoryID;
        $cashFlowReportID = $request->cashFlowReportID;
        $cashFlowData = CashFlowReport::find($request->cashFlowReportID);
        if($cashFlowData){


            $data = array();
            foreach($details as $detail){
                $applicableAmount = 0;
                $minArray=array();
                if($detail['deliveryAmount'] != null){
                    array_push($minArray,$detail['deliveryAmount']);
                }
                if($detail['custAmountLocal'] != null){
                    array_push($minArray,$detail['custAmountLocal']);
                }
                if($detail['receiveAmountLocal'] != null) {
                    array_push($minArray,$detail['receiveAmountLocal']);
                }
                if(isset($detail['cashFlowAmount'])) {
                    $companyCurrency = \Helper::companyCurrency($cashFlowData->companySystemID);

                    $companyCurrencyDecimal = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces : 3;
                    $applicableAmount = min($minArray);
                    $applicableAmount = round($applicableAmount,$companyCurrencyDecimal);
                    if ($detail['cashFlowAmount'] > $applicableAmount) {
                        return $this->sendError('Cash Flow Amount is greater than applicable amount', 500);
                    }
                    $data['localAmount'] = $detail['cashFlowAmount'];
                    $data['subCategoryID'] = $subCategoryID;
                    $data['brvID'] = $detail['brvID'];
                    $data['custInvID'] = $detail['custInvoiceDirectAutoID'];
                    $data['deoID'] = $detail['deliveryOrderID'];
                    $data['brvDetailID'] = $detail['brvDetailID'];
                    $data['chartOfAccountID'] = $detail['glAutoID'];
                    $data['cashFlowReportID'] = $cashFlowReportID;
                    $data['rptAmount'] = 0;

                    $brvData = CashFlowSubCategoryGLCode::where('chartOfAccountID',$data['chartOfAccountID'])->where('brvID', $data['brvID'])->where('deoID', $data['deoID'])->where('custInvID',$data['custInvID'])->where('subCategoryID',$data['subCategoryID'])->where('cashFlowReportID', $data['cashFlowReportID'])->first();
                    if($brvData){
                        CashFlowSubCategoryGLCode::where('chartOfAccountID',$data['chartOfAccountID'])->where('brvID', $data['brvID'])->where('deoID', $data['deoID'])->where('custInvID',$data['custInvID'])->where('subCategoryID',$data['subCategoryID'])->where('cashFlowReportID', $data['cashFlowReportID'])->update($data);
                    }
                    else{
                        CashFlowSubCategoryGLCode::create($data);
                    }
                }
            }
            $reportMasterData = CashFlowReport::find($cashFlowReportID);
    if($reportMasterData) {
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
        $this->updateGroupTotalOfCashFlowTemplate($reportTemplateDetails, $cashFlowReportID);
    }

        }

        return $this->sendResponse($reportTemplateDetails, 'Report Template Details retrieved successfully');

    }

    public function cashFlowConfirmation(Request $request){
        $input = $request->reportData;
        $input = array_except($input, ['finance_year_by','template','confirmed_by']);
        $input['confirmed_by'] = \Helper::getEmployeeSystemID();
        $input['confirmed_date'] = now();
        $cashFlowReport = $this->cashFlowReportRepository->update($input, $input['id']);


        return $this->sendResponse($cashFlowReport, 'Report Template Details retrieved successfully');
    }

    public function updateGroupTotalOfCashFlowTemplate($templateDetails, $cashFlowReportID)
    {
        $cashFlowReport = CashFlowReport::find($cashFlowReportID);

        if ($cashFlowReport) {
            $companyData = Company::find($cashFlowReport->companySystemID);

            if ($companyData) {
                foreach ($templateDetails as $key => $value) {

                    foreach ($value->subcategory as $key1 => $value1) {
                        if ($value1->logicType == 2 && $value1->type == 3) {
                            $dataCashFlow = [];
                            $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                            $dataCashFlow['subCategoryID'] = $value1->id;
                            $dataCashFlow['localAmount'] = $this->getLinkedGrouptotal($value1->subcategorytot, $cashFlowReportID);

                            $convertedAmount = \Helper::currencyConversion($cashFlowReport->companySystemID, $companyData->localCurrencyID, $companyData->localCurrencyID, $dataCashFlow['localAmount']);

                            $dataCashFlow['rptAmount'] = $convertedAmount['reportingAmount'];

                            $isExists = CashFlowSubCategoryGLCode::where('subCategoryID',$dataCashFlow['subCategoryID'])->where('cashFlowReportID', $dataCashFlow['cashFlowReportID'])->first();
                            if($isExists){
                                CashFlowSubCategoryGLCode::where('subCategoryID',$dataCashFlow['subCategoryID'])->where('cashFlowReportID',$dataCashFlow['cashFlowReportID'])->update($dataCashFlow);

                            }
                            else{
                                CashFlowSubCategoryGLCode::create($dataCashFlow);
                            }
                        }
                    }

                    if ($value->logicType == 2 && $value->type == 3) {
                        $dataCashFlow = [];
                        $dataCashFlow['cashFlowReportID'] = $cashFlowReportID;
                        $dataCashFlow['subCategoryID'] = $value->id;
                        $dataCashFlow['localAmount'] = $this->getLinkedGrouptotal($value->subcategorytot, $cashFlowReportID);

                        $convertedAmount = \Helper::currencyConversion($cashFlowReport->companySystemID, $companyData->localCurrencyID, $companyData->localCurrencyID, $dataCashFlow['localAmount']);

                        $dataCashFlow['rptAmount'] = $convertedAmount['reportingAmount'];
                        $isExists = CashFlowSubCategoryGLCode::where('subCategoryID',$dataCashFlow['subCategoryID'])->where('cashFlowReportID', $dataCashFlow['cashFlowReportID'])->first();
                        if($isExists){
                            CashFlowSubCategoryGLCode::where('subCategoryID',$dataCashFlow['subCategoryID'])->where('cashFlowReportID',$dataCashFlow['cashFlowReportID'])->update($dataCashFlow);
                        }
                        else{
                            CashFlowSubCategoryGLCode::create($dataCashFlow);
                        }
                    }
                }
            }
        }
    }

    public function getLinkedGrouptotal($subCategories, $cashFlowReportID)
    {
        $totalAmount = 0;

        foreach ($subCategories as $key3 => $value3) {
            $totalAmount += CashFlowSubCategoryGLCode::where('subCategoryID',$value3->subCategory)->where('cashFlowReportID',$cashFlowReportID)->sum('localAmount');
        }

        return $totalAmount;
    }
}
