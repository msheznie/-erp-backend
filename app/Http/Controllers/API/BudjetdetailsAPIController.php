<?php
/**
 * =============================================
 * -- File Name : BudjetdetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Mohamed Fayas
 * -- Create date : 16 - October 2018
 * -- Description : This file contains the all CRUD for Budget details
 * -- REVISION HISTORY
 * -- Date: 25 -October 2018 By: Fayas Description: Added new function getDetailsByBudget(),getDetailsByBudget
 * -- Date: 26 -October 2018 By: Fayas Description: Added new function bulkUpdateBudgetDetails(),getBudgetDetailTotalSummary(),
 * removeBudgetDetails()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudjetdetailsAPIRequest;
use App\Http\Requests\API\UpdateBudjetdetailsAPIRequest;
use App\Models\Budjetdetails;
use App\Models\TemplatesDetails;
use App\Models\BudgetDetailHistory;
use App\Models\BudgetAdjustment;
use App\Models\Company;
use App\Models\BudgetMaster;
use App\Models\ReportTemplateLinks;
use App\Models\TemplatesGLCode;
use App\Models\Months;
use App\Models\CompanyFinanceYear;
use App\Models\ReportTemplateDetails;
use App\Repositories\BudgetMasterRepository;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\AddBudgetDetails;
use Carbon\CarbonPeriod;

/**
 * Class BudjetdetailsController
 * @package App\Http\Controllers\API
 */
class BudjetdetailsAPIController extends AppBaseController
{
    /** @var  BudjetdetailsRepository */
    private $budjetdetailsRepository;
    private $budgetMasterRepository;

    public function __construct(BudjetdetailsRepository $budjetdetailsRepo, BudgetMasterRepository $budgetMasterRepo)
    {
        $this->budjetdetailsRepository = $budjetdetailsRepo;
        $this->budgetMasterRepository = $budgetMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budjetdetails",
     *      summary="Get a listing of the Budjetdetails.",
     *      tags={"Budjetdetails"},
     *      description="Get all Budjetdetails",
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
     *                  @SWG\Items(ref="#/definitions/Budjetdetails")
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
        $this->budjetdetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->budjetdetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budjetdetails = $this->budjetdetailsRepository->all();

        return $this->sendResponse($budjetdetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budjet_details')]));
    }

    /**
     * @param CreateBudjetdetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budjetdetails",
     *      summary="Store a newly created Budjetdetails in storage",
     *      tags={"Budjetdetails"},
     *      description="Store Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Budjetdetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Budjetdetails")
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
     *                  ref="#/definitions/Budjetdetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudjetdetailsAPIRequest $request)
    {
        $input = $request->all();

        $budjetdetails = $this->budjetdetailsRepository->create($input);

        return $this->sendResponse($budjetdetails->toArray(), trans('custom.save', ['attribute' => trans('custom.budjet_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budjetdetails/{id}",
     *      summary="Display the specified Budjetdetails",
     *      tags={"Budjetdetails"},
     *      description="Get Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Budjetdetails",
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
     *                  ref="#/definitions/Budjetdetails"
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
        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budjet_details')]));
        }

        return $this->sendResponse($budjetdetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budjet_details')]));
    }

    /**
     * @param int $id
     * @param UpdateBudjetdetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budjetdetails/{id}",
     *      summary="Update the specified Budjetdetails in storage",
     *      tags={"Budjetdetails"},
     *      description="Update Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Budjetdetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Budjetdetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Budjetdetails")
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
     *                  ref="#/definitions/Budjetdetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudjetdetailsAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'budjetAmtRpt' => 'numeric'
        ]);

        if ($validator->fails()) {
            $input['budjetAmtRpt'] = floatval($input['budjetAmtRpt']);
        }

        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budjet_details')]));
        }

        if(!$input['budjetAmtRpt']){
            $input['budjetAmtRpt'] = 0;
        }

        $companyData = Company::find($budjetdetails->companySystemID);

        $reportingCurrencyID = ($companyData) ? $companyData->reportingCurrency : 2;

        $currencyConvection = \Helper::currencyConversion($budjetdetails->companySystemID, $reportingCurrencyID, $reportingCurrencyID, $input['budjetAmtRpt']);

        $input['budjetAmtLocal'] = \Helper::roundValue($currencyConvection['localAmount']);
        if ($input['budjetAmtRpt'] < 0) {
            $input['budjetAmtLocal'] = abs($input['budjetAmtLocal']) * -1;
        }

        $budjetdetails = $this->budjetdetailsRepository->update(array_only($input, ['budjetAmtRpt', 'budjetAmtLocal']), $id);

        return $this->sendResponse($budjetdetails->toArray(), trans('custom.update', ['attribute' => trans('custom.budjet_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budjetdetails/{id}",
     *      summary="Remove the specified Budjetdetails from storage",
     *      tags={"Budjetdetails"},
     *      description="Delete Budjetdetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Budjetdetails",
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
        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budjet_details')]));
        }

        $budjetdetails->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.budjet_details')]));
    }

    public function getDetailsByBudget(Request $request)
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_master')]));
        }



        $companyFinanceYear = CompanyFinanceYear::find($budgetMaster->companyFinanceYearID);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));
            $temp['monthName'] = (Months::find(floatval($dt->format("m")))) ? Months::find(floatval($dt->format("m")))->monthDes : "";

            $monthArray[] = $temp;
        }

        $finalArray = ReportTemplateDetails::selectRaw('*,0 as expanded')
                                         ->with(['subcategory' => function ($q) use ($budgetMaster){
                                            $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster){
                                                        $q->with('subcategory')
                                                          ->orderBy('sortOrder', 'asc')
                                                          ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                          }])
                                                          ->with(['items' => function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                          }])
                                                          ->whereHas('items', function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                          });
                                                    }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                        $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                            $q->with('subcategory')
                                                              ->orderBy('sortOrder', 'asc')
                                                              ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                              }])
                                                              ->with(['items' => function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                              }])
                                                              ->whereHas('items', function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                              });
                                                        }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                            $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                                $q->with('subcategory')
                                                                  ->orderBy('sortOrder', 'asc')
                                                                  ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                  }])
                                                                  ->with(['items' => function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                  }])
                                                                  ->whereHas('items', function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                  });
                                                            }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                                $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                                    $q->with('subcategory')
                                                                      ->orderBy('sortOrder', 'asc')
                                                                      ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                      }])
                                                                      ->with(['items' => function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                      }])
                                                                      ->whereHas('items', function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                      });
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
                                        }])->OfMaster($budgetMaster->templateMasterID)->whereNull('masterID')->orderBy('sortOrder')->get();


        return $this->sendResponse(['budgetDetails' => $finalArray, 'months' => $monthArray], trans('custom.retrieve', ['attribute' => trans('custom.budjet_details')]));
    }


    public function getDetailsByBudgetNew(Request $request)
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_master')]));
        }


        $companyFinanceYear = CompanyFinanceYear::find($budgetMaster->companyFinanceYearID);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));
            $temp['monthName'] = (Months::find(floatval($dt->format("m")))) ? Months::find(floatval($dt->format("m")))->monthDes : "";

            $monthArray[] = $temp;
        }

        $finalArray = ReportTemplateDetails::with(['subcategory' => function ($q) {
            $q->select('detID', 'masterID', 'description', 'companyReportTemplateID', 'isFinalLevel','itemType','hideHeader','bgColor')
                ->with(['subcategory' => function ($subcategory) {
                    $subcategory->select('detID', 'masterID', 'description', 'companyReportTemplateID', 'isFinalLevel','itemType','hideHeader','bgColor')
                        ->with(['subcategory' => function ($subcategory1) {
                            $subcategory1->select('detID', 'masterID', 'description', 'companyReportTemplateID', 'isFinalLevel','itemType','hideHeader','bgColor')
                                ->with(['subcategory' => function ($subcategory2) {
                                    $subcategory2->select('detID', 'masterID', 'description', 'companyReportTemplateID', 'isFinalLevel','itemType','hideHeader','bgColor')
                                        ->with('subcategory')
                                        ->orderBy('sortOrder', 'asc');
                                }])
                                ->orderBy('sortOrder', 'asc');
                        }])
                        ->orderBy('sortOrder', 'asc');
                }])
                ->orderBy('sortOrder', 'asc');
        }])
            ->select('detID', 'masterID', 'description', 'companyReportTemplateID', 'isFinalLevel','itemType','hideHeader','bgColor')
            ->OfMaster($budgetMaster->templateMasterID)
            ->whereNull('masterID')
            ->orderBy('sortOrder')
            ->get();

        return $this->sendResponse(['budgetDetails' => $finalArray, 'months' => $monthArray], trans('custom.retrieve', ['attribute' => trans('custom.budjet_details')]));
    }

    public function  getGLCodesByBudgetCategory(Request  $request)
    {
        $reportTemplateDetailsID = $request->input('id');

        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($request->input('budgetmasterID'));

        $glCodes = ReportTemplateDetails::find($reportTemplateDetailsID)
            ->gl_codes()
            ->with(['items' => function($query) use ($budgetMaster) {

                $query->where('companySystemID', $budgetMaster->companySystemID)
                    ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                    ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID)
                    ->orderBy('month');
            }])
            ->whereHas('items', function($query) use ($budgetMaster) {
                $query->where('companySystemID', $budgetMaster->companySystemID)
                    ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                    ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
            })
            ->get();


        foreach ($glCodes as $glCode) {
            if ($glCode->items->isEmpty()) {
                $defaultItems = Collection::times(12, function ($month) {
                    return [
                        'month' => $month,
                        'budjetAmtRpt' => 0, // Default value
                    ];
                });
                $glCode->setRelation('items', $defaultItems);
            }
            $budjetAmtRptSum = $glCode->items->sum('budjetAmtRpt');
            $glCode->items->push(['budjetAmtRpt' => $budjetAmtRptSum,'isText' => true]);
        }

        return $this->sendResponse(['glData' => $glCodes], trans('custom.data_retrieved_successfully'));
    }

    public function exportReport(Request $request)
    {
        $input = $request->all();
        $id = $request->id;
        $budgetMaster = $this->budgetMasterRepository->with(['confirmed_by','segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }


        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_master')]));
        }

        $companyFinanceYear = CompanyFinanceYear::find($budgetMaster->companyFinanceYearID);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));
            $temp['monthName'] = (Months::find(floatval($dt->format("m")))) ? Months::find(floatval($dt->format("m")))->monthDes : "";

            $monthArray[] = $temp;
        }

        $finalArray = ReportTemplateDetails::selectRaw('*,0 as expanded')
            ->with(['subcategory' => function ($q) use ($budgetMaster){
                $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster){
                    $q->with('subcategory')
                        ->orderBy('sortOrder', 'asc')
                        ->withCount(['items' => function($query) use ($budgetMaster) {
                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                        }])
                        ->with(['items' => function($query) use ($budgetMaster) {
                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                        }])
                        ->whereHas('items', function($query) use ($budgetMaster) {
                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                        });
                }, 'subcategory' => function ($q) use ($budgetMaster) {
                    $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                        $q->with('subcategory')
                            ->orderBy('sortOrder', 'asc')
                            ->withCount(['items' => function($query) use ($budgetMaster) {
                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                    ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                    ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                            }])
                            ->with(['items' => function($query) use ($budgetMaster) {
                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                    ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                    ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                            }])
                            ->whereHas('items', function($query) use ($budgetMaster) {
                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                    ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                    ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                            });
                    }, 'subcategory' => function ($q) use ($budgetMaster) {
                        $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                            $q->with('subcategory')
                                ->orderBy('sortOrder', 'asc')
                                ->withCount(['items' => function($query) use ($budgetMaster) {
                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                        ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                        ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                }])
                                ->with(['items' => function($query) use ($budgetMaster) {
                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                        ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                        ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                }])
                                ->whereHas('items', function($query) use ($budgetMaster) {
                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                        ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                        ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                });
                        }, 'subcategory' => function ($q) use ($budgetMaster) {
                            $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                $q->with('subcategory')
                                    ->orderBy('sortOrder', 'asc')
                                    ->withCount(['items' => function($query) use ($budgetMaster) {
                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                            ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                            ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                    }])
                                    ->with(['items' => function($query) use ($budgetMaster) {
                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                            ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                            ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                    }])
                                    ->whereHas('items', function($query) use ($budgetMaster) {
                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                            ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                            ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                    });
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
            }])->OfMaster($budgetMaster->templateMasterID)->whereNull('masterID')->orderBy('sortOrder')->get();

        $currencyData = \Helper::companyCurrency($budgetMaster->companySystemID);

        $x = 0;

        $templateName = "export_report.budget_details";

        $reportData = ['budgetDetails' => $finalArray,'months' => $monthArray, 'entity' => $budgetMaster, 'currency' => $currencyData];



        \Excel::create('finance', function ($excel) use ($reportData, $templateName) {
            $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName) {
                $sheet->loadView($templateName, $reportData);
                
                // Set right-to-left for Arabic locale
                if (app()->getLocale() == 'ar') {
                    $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->setRightToLeft(true);
                }
            });
        })->download('csv');

//        return $this->sendResponse(array(), trans('custom.success_export'));
       return $this->sendResponse(['budgetDetails' => $finalArray, 'months' => $monthArray], trans('custom.retrieve', ['attribute' => trans('custom.budjet_details')]));

    }

    public function bulkUpdateBudgetDetails(Request $request)
    {
        $input = $request->all();


        $items = collect($input['items'])->forget(12);

        foreach ($items as $item) {
            /** @var Budjetdetails $budgetDetail */
            $budgetDetail = $this->budjetdetailsRepository->findWithoutFail($item['budjetDetailsID'] ?? null);

            if (empty($budgetDetail)) {
                return $this->sendError(trans('custom.budget_details_not_found'));
            }
            if(!$item['budjetAmtRpt']){
                $item['budjetAmtRpt'] = 0;
            }

            $companyData = Company::find($budgetDetail->companySystemID);

            $reportingCurrencyID = ($companyData) ? $companyData->reportingCurrency : 2;

            $currencyConvection = \Helper::currencyConversion($item['companySystemID'], $reportingCurrencyID, $reportingCurrencyID, $item['budjetAmtRpt']);

            $item['budjetAmtLocal'] = \Helper::roundValue($currencyConvection['localAmount']);
            if ($item['budjetAmtRpt'] < 0) {
                $item['budjetAmtLocal'] = abs($item['budjetAmtLocal']) * -1;
            }
            $this->budjetdetailsRepository->update(array_only($item, ['budjetAmtRpt', 'budjetAmtLocal']), $item['budjetDetailsID']);
        }

        return $this->sendResponse([], trans('custom.update', ['attribute' => trans('custom.budjet_details')]));
    }

    public function removeBudgetDetails(Request $request)
    {
        $input = $request->all();

        $items = collect($input['items'])->forget(12);

        foreach ($items as $item) {
            /** @var Budjetdetails $budgetDetail */
            $budgetDetail = $this->budjetdetailsRepository->findWithoutFail($item['budjetDetailsID']);
            if (!empty($budgetDetail)) {
                $budgetDetail->delete();
            }
        }

        return $this->sendResponse([], trans('custom.budjetdetails_deleted_successfully'));
    }


    public function getBudgetDetailTotalSummary(Request $request)
    {
        $input = $request->all();

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

        if (empty($budgetMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_master')]));
        }
        $total = array();
        return $this->sendResponse($total, trans('custom.update', ['attribute' => trans('custom.budjet_details_summery')]));
    }

    public function budgetDetailsUpload(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['budgetExcelUpload'];
            $input = array_except($request->all(), 'budgetExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];
            $extension = $excelUpload[0]['filetype'];
            $size = $excelUpload[0]['size'];

            $budgetMaster = BudgetMaster::where('budgetmasterID', $input['budgetMasterID'])
                                               ->first();


            if (empty($budgetMaster)) {
                return $this->sendError(trans('custom.budget_master_not_found'), 500);
            }

            $companyFinanceYear = CompanyFinanceYear::find($budgetMaster->companyFinanceYearID);
            if (empty($companyFinanceYear)) {
                return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
            }

            $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
            $monthArray = [];
            foreach ($result as $dt) {
                $temp['year'] = $dt->format("Y");
                $temp['monthID'] = floatval($dt->format("m"));
                $temp['monthName'] = (Months::find(floatval($dt->format("m")))) ? Months::find(floatval($dt->format("m")))->monthDes : "";

                $monthArray[] = $temp;
            }


            $allowedExtensions = ['xlsx','xls'];

            if (!in_array($extension, $allowedExtensions))
            {
                return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
            }

            if ($size > 20000000) {
                return $this->sendError('The maximum size allow to upload is 20 MB',500);
            }

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $finalData = [];
            $formatChk = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->get()->toArray();

            $uniqueData = array_filter(collect($formatChk)->toArray());

            $validateExcel = false;

            foreach ($uniqueData as $key => $value) {
               if (isset($value['account_code']) && isset($value['account_description']) && isset($value['main_category']) && isset($value['sub_category'])) {
                   $validateExcel = true;
               }
            }

            if (!$validateExcel) {
                return $this->sendError('Excel is not valid, template deafult fields are modified', 500);
            }

            $selectArray = [];
            foreach ($monthArray as $key => $month) {
                $monthName = strtolower($month['monthName']).'_'.$month['year'];

                $selectArray[] = $monthName;
            }

            $selectArray[] = 'account_code';
            $selectArray[] = 'account_description';
            $selectArray[] = 'main_category';
            $selectArray[] = 'sub_category';

            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select($selectArray)->get()->toArray();

            $filteredRecords = array_filter(collect($record)->toArray());
            $cdcd = '-xs';

            foreach ($filteredRecords as $key => $value) {
                if (isset($value['sub_category'])) {
                     $templateDetail = ReportTemplateDetails::where('description', $value['sub_category'])
                                                       ->where('companyReportTemplateID', $budgetMaster->templateMasterID)
                                                       ->whereHas('gllink', function($query) use ($value) {
                                                            $query->where('glCode', $value['account_code']);
                                                       })
                                                       ->first();

                     if ($templateDetail) {
                        foreach ($monthArray as $key1 => $month) {
                            $monthName = strtolower($month['monthName']).'_'.$month['year'];
                            if (isset($value[$monthName]) && !is_null($value[$monthName]) && (abs(floatval($value[$monthName])) > 0)) {
                                $amounts = $this->setBudgetRptAndLocalAmount($value[$monthName], $budgetMaster->companySystemID);
                                $updateRes = Budjetdetails::where('templateDetailID', $templateDetail->detID)
                                                          ->where('budgetmasterID', $input['budgetMasterID'])
                                                          ->where('glCode', $value['account_code'])
                                                          ->where('month', $month['monthID'])
                                                          ->where('Year', $month['year'])
                                                          ->update($amounts);
                            }
                        }
                    }
                }
            }

            Storage::disk($disk)->delete('app/' . $originalFileName);
            DB::commit();
            return $this->sendResponse([], trans('custom.budget_details_uploaded_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function setBudgetRptAndLocalAmount($budjetAmtRpt, $companySystemID)
    {
        $input['budjetAmtRpt']  = $budjetAmtRpt;

        $companyData = Company::find($companySystemID);

        $reportingCurrencyID = ($companyData) ? $companyData->reportingCurrency : 2;

        $currencyConvection = \Helper::currencyConversion($companySystemID, $reportingCurrencyID, $reportingCurrencyID, $budjetAmtRpt);
        $input['budjetAmtLocal'] = \Helper::roundValue($currencyConvection['localAmount']);

        if ($budjetAmtRpt < 0) {
            $input['budjetAmtLocal'] = abs($input['budjetAmtLocal']) * -1;
        }

        return $input;
    }


    public function syncGlBudget(Request $request)
    {
        $input = $request->all();

        $budgetMaster = BudgetMaster::find($input['budgetMasterID']);
        if (!$budgetMaster) {
            return $this->sendError(trans('custom.budget_master_not_found'), 500);
        }

        $companyFinanceYear = CompanyFinanceYear::find($budgetMaster->companyFinanceYearID);
        if (empty($companyFinanceYear)) {
            return $this->sendError(trans('custom.selected_financial_year_is_not_found'), 500);
        }

        $result = CarbonPeriod::create($companyFinanceYear->bigginingDate, '1 month', $companyFinanceYear->endingDate);
        $monthArray = [];
        foreach ($result as $dt) {
            $temp['year'] = $dt->format("Y");
            $temp['monthID'] = floatval($dt->format("m"));

            $monthArray[] = $temp;
        }

        $glData = ReportTemplateLinks::where('templateMasterID', $budgetMaster->templateMasterID)
                                    ->whereNotNull('glAutoID')
                                    ->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                                        $q->where('companySystemID', $budgetMaster->companySystemID)
                                            ->where('isActive', 1)
                                            ->where('isAssigned', -1);
                                    })
                                    ->whereHas('template_category', function ($q) use ($input) {
                                        $q->where('itemType', '!=',4);
                                    })
                                    ->with(['chart_of_account' => function ($q) use ($budgetMaster) {
                                        $q->where('companySystemID', $budgetMaster->companySystemID)
                                            ->where('isActive', 1)
                                            ->where('isAssigned', -1);
                                    }])
                                    ->whereDoesntHave('items', function($query) use ($input) {
                                        $query->where('budgetmasterID', $input['budgetMasterID']);
                                    })
                                    ->get();

        if (count($glData)) {
            AddBudgetDetails::dispatch($budgetMaster,$glData, $monthArray);
        }

        return $this->sendResponse($budgetMaster->toArray(), trans('custom.budget_details_synced_successfully'));
    }

    public function getBudgetDetailHistory(Request $request)
    {
        $input = $request->all();
        $budgetHistoryData  = [];
        $budgetMaster = BudgetMaster::find($input['budgetMasterID']);

        if (!$budgetMaster) {
            return $this->sendError(trans('custom.budget_master_not_found'));
        }

        $budgetHistoryData['initialBudget'] = BudgetDetailHistory::where('budgetmasterID', $input['budgetMasterID'])
                                                                 ->where('chartOfAccountID', $input['glAutoID'])
                                                                 ->where('templateDetailID', $input['templateDetailID'])
                                                                 ->sum('budjetAmtRpt');


        $budgetHistoryData['currentBudget'] = Budjetdetails::where('budgetmasterID', $input['budgetMasterID'])
                                                                 ->where('chartOfAccountID', $input['glAutoID'])
                                                                 ->where('templateDetailID', $input['templateDetailID'])
                                                                 ->sum('budjetAmtRpt');



        $budgetHistoryData['budgetOutgoingTransfer'] = BudgetAdjustment::with(['to_account'])
                                                                     ->where('companySystemID', $budgetMaster->companySystemID)
                                                                     ->where('fromGLCodeSystemID', $input['glAutoID'])
                                                                     ->where('budgetMasterID', $input['budgetMasterID'])
                                                                     ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID)
                                                                     ->whereNotNull('fromGLCodeSystemID')
                                                                     ->whereNotNull('toGLCodeSystemID')
                                                                     ->where('adjustmentRptAmount', '<', 0)
                                                                     ->get();


        $budgetHistoryData['budgetIncomingTransfer'] = BudgetAdjustment::with(['from_account'])
                                                                     ->where('companySystemID', $budgetMaster->companySystemID)
                                                                     ->where('toGLCodeSystemID', $input['glAutoID'])
                                                                     ->where('budgetMasterID', $input['budgetMasterID'])
                                                                     ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID)
                                                                     ->whereNotNull('fromGLCodeSystemID')
                                                                     ->whereNotNull('toGLCodeSystemID')
                                                                     ->where('adjustmentRptAmount', '>', 0)
                                                                     ->get();

        $budgetHistoryData['budgetAddition'] = BudgetAdjustment::where('companySystemID', $budgetMaster->companySystemID)
                                                                     ->where('toGLCodeSystemID', $input['glAutoID'])
                                                                     ->where('budgetMasterID', $input['budgetMasterID'])
                                                                     ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID)
                                                                     ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                     ->whereNull('fromGLCodeSystemID')
                                                                     ->whereNotNull('toGLCodeSystemID')
                                                                     // ->where('adjustmentRptAmount', '>', 0)
                                                                     ->get();



        return $this->sendResponse($budgetHistoryData, trans('custom.budget_history_retrived_successfully'));
    }

}
