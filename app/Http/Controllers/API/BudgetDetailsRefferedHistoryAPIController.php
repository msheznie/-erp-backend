<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetDetailsRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateBudgetDetailsRefferedHistoryAPIRequest;
use App\Models\BudgetDetailsRefferedHistory;
use App\Repositories\BudgetDetailsRefferedHistoryRepository;
use App\Repositories\BudgetMasterRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Budjetdetails;
use App\Models\TemplatesDetails;
use App\Models\Company;
use App\Models\BudgetMaster;
use App\Models\ReportTemplateLinks;
use App\Models\TemplatesGLCode;
use App\Models\Months;
use App\Models\CompanyFinanceYear;
use App\Models\ReportTemplateDetails;
use Carbon\CarbonPeriod;

/**
 * Class BudgetDetailsRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class BudgetDetailsRefferedHistoryAPIController extends AppBaseController
{
    /** @var  BudgetDetailsRefferedHistoryRepository */
    private $budgetDetailsRefferedHistoryRepository;
    private $budgetMasterRefferedHistoryRepository;
    public function __construct(BudgetDetailsRefferedHistoryRepository $budgetDetailsRefferedHistoryRepo, BudgetMasterRefferedHistoryRepository $budgetMasterRefferedHistoryRepo)
    {
        $this->budgetDetailsRefferedHistoryRepository = $budgetDetailsRefferedHistoryRepo;
        $this->budgetMasterRefferedHistoryRepository = $budgetMasterRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetDetailsRefferedHistories",
     *      summary="Get a listing of the BudgetDetailsRefferedHistories.",
     *      tags={"BudgetDetailsRefferedHistory"},
     *      description="Get all BudgetDetailsRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/BudgetDetailsRefferedHistory")
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
        $this->budgetDetailsRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetDetailsRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetDetailsRefferedHistories = $this->budgetDetailsRefferedHistoryRepository->all();

        return $this->sendResponse($budgetDetailsRefferedHistories->toArray(), trans('custom.budget_details_reffered_histories_retrieved_succes'));
    }

    /**
     * @param CreateBudgetDetailsRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetDetailsRefferedHistories",
     *      summary="Store a newly created BudgetDetailsRefferedHistory in storage",
     *      tags={"BudgetDetailsRefferedHistory"},
     *      description="Store BudgetDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetDetailsRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetDetailsRefferedHistory")
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
     *                  ref="#/definitions/BudgetDetailsRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetDetailsRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $budgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepository->create($input);

        return $this->sendResponse($budgetDetailsRefferedHistory->toArray(), trans('custom.budget_details_reffered_history_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetDetailsRefferedHistories/{id}",
     *      summary="Display the specified BudgetDetailsRefferedHistory",
     *      tags={"BudgetDetailsRefferedHistory"},
     *      description="Get BudgetDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailsRefferedHistory",
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
     *                  ref="#/definitions/BudgetDetailsRefferedHistory"
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
        /** @var BudgetDetailsRefferedHistory $budgetDetailsRefferedHistory */
        $budgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($budgetDetailsRefferedHistory)) {
            return $this->sendError(trans('custom.budget_details_reffered_history_not_found'));
        }

        return $this->sendResponse($budgetDetailsRefferedHistory->toArray(), trans('custom.budget_details_reffered_history_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateBudgetDetailsRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetDetailsRefferedHistories/{id}",
     *      summary="Update the specified BudgetDetailsRefferedHistory in storage",
     *      tags={"BudgetDetailsRefferedHistory"},
     *      description="Update BudgetDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailsRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetDetailsRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetDetailsRefferedHistory")
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
     *                  ref="#/definitions/BudgetDetailsRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetDetailsRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetDetailsRefferedHistory $budgetDetailsRefferedHistory */
        $budgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($budgetDetailsRefferedHistory)) {
            return $this->sendError(trans('custom.budget_details_reffered_history_not_found'));
        }

        $budgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($budgetDetailsRefferedHistory->toArray(), trans('custom.budgetdetailsrefferedhistory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetDetailsRefferedHistories/{id}",
     *      summary="Remove the specified BudgetDetailsRefferedHistory from storage",
     *      tags={"BudgetDetailsRefferedHistory"},
     *      description="Delete BudgetDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetDetailsRefferedHistory",
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
        /** @var BudgetDetailsRefferedHistory $budgetDetailsRefferedHistory */
        $budgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($budgetDetailsRefferedHistory)) {
            return $this->sendError(trans('custom.budget_details_reffered_history_not_found'));
        }

        $budgetDetailsRefferedHistory->delete();

        return $this->sendSuccess('Budget Details Reffered History deleted successfully');
    }

    public function getDetailsByBudgetRefereback(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRefferedHistoryRepository->with(['segment_by', 'template_master', 'finance_year_by'])->findWithoutFail($input['id']);

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
                                                          ->withCount(['items_refferd' => function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                      ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                          }])
                                                          ->with(['items_refferd' => function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                       ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                      ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                          }])
                                                          ->whereHas('items_refferd', function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                       ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                          });
                                                    }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                        $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                            $q->with('subcategory')
                                                              ->orderBy('sortOrder', 'asc')
                                                              ->withCount(['items_refferd' => function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                           ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                          ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                              }])
                                                              ->with(['items_refferd' => function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                     ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                              }])
                                                              ->whereHas('items_refferd', function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                     ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                              });
                                                        }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                            $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                                $q->with('subcategory')
                                                                  ->orderBy('sortOrder', 'asc')
                                                                  ->withCount(['items_refferd' => function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                         ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                  }])
                                                                  ->with(['items_refferd' => function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                         ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                  }])
                                                                  ->whereHas('items_refferd', function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                         ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                  });
                                                            }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                                $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                                    $q->with('subcategory')
                                                                      ->orderBy('sortOrder', 'asc')
                                                                      ->withCount(['items_refferd' => function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                             ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                      }])
                                                                      ->with(['items_refferd' => function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                             ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('companyFinanceYearID', $budgetMaster->companyFinanceYearID);
                                                                      }])
                                                                      ->whereHas('items_refferd', function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                             ->where('timesReferred', $budgetMaster->timesReferred)
                                                                      ->where('budgetmasterID', $budgetMaster->budgetmasterID)
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
}
