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
use App\Models\TemplatesGLCode;
use App\Models\ReportTemplateDetails;
use App\Repositories\BudgetMasterRepository;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

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

        $currencyConvection = \Helper::currencyConversion($budjetdetails->companySystemID, 2, 2, $input['budjetAmtRpt']);

        $input['budjetAmtLocal'] = round($currencyConvection['localAmount'], 3);
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

        $finalArray = ReportTemplateDetails::selectRaw('*,0 as expanded')
                                         ->with(['subcategory' => function ($q) use ($budgetMaster){
                                            $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster){
                                                        $q->with('subcategory')
                                                          ->orderBy('sortOrder', 'asc')
                                                          ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('Year', $budgetMaster->Year);
                                                          }])
                                                          ->with(['items' => function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('Year', $budgetMaster->Year);
                                                          }])
                                                          ->whereHas('items', function($query) use ($budgetMaster) {
                                                                $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                      ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                      ->where('Year', $budgetMaster->Year);
                                                          });
                                                    }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                        $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                            $q->with('subcategory')
                                                              ->orderBy('sortOrder', 'asc')
                                                              ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('Year', $budgetMaster->Year);
                                                              }])
                                                              ->with(['items' => function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('Year', $budgetMaster->Year);
                                                              }])
                                                              ->whereHas('items', function($query) use ($budgetMaster) {
                                                                    $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                          ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                          ->where('Year', $budgetMaster->Year);
                                                              });
                                                        }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                            $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                                $q->with('subcategory')
                                                                  ->orderBy('sortOrder', 'asc')
                                                                  ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('Year', $budgetMaster->Year);
                                                                  }])
                                                                  ->with(['items' => function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('Year', $budgetMaster->Year);
                                                                  }])
                                                                  ->whereHas('items', function($query) use ($budgetMaster) {
                                                                        $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                              ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                              ->where('Year', $budgetMaster->Year);
                                                                  });
                                                            }, 'subcategory' => function ($q) use ($budgetMaster) {
                                                                $q->with(['gllink','gl_codes' => function ($q) use ($budgetMaster) {
                                                                    $q->with('subcategory')
                                                                      ->orderBy('sortOrder', 'asc')
                                                                      ->withCount(['items' => function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('Year', $budgetMaster->Year);
                                                                      }])
                                                                      ->with(['items' => function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('Year', $budgetMaster->Year);
                                                                      }])
                                                                      ->whereHas('items', function($query) use ($budgetMaster) {
                                                                            $query->where('companySystemID', $budgetMaster->companySystemID)
                                                                                  ->where('serviceLineSystemID', $budgetMaster->serviceLineSystemID)
                                                                                  ->where('Year', $budgetMaster->Year);
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


        return $this->sendResponse($finalArray, trans('custom.retrieve', ['attribute' => trans('custom.budjet_details')]));
    }

    public function bulkUpdateBudgetDetails(Request $request)
    {
        $input = $request->all();


        foreach ($input['items'] as $item) {
            /** @var Budjetdetails $budgetDetail */
            $budgetDetail = $this->budjetdetailsRepository->findWithoutFail($item['budjetDetailsID']);

            if (empty($budgetDetail)) {
                return $this->sendError('Budget details not found');
            }
            if(!$item['budjetAmtRpt']){
                $item['budjetAmtRpt'] = 0;
            }
            $currencyConvection = \Helper::currencyConversion($item['companySystemID'], 2, 2, $item['budjetAmtRpt']);

            $item['budjetAmtLocal'] = round($currencyConvection['localAmount'], 3);
            $this->budjetdetailsRepository->update(array_only($item, ['budjetAmtRpt', 'budjetAmtLocal']), $item['budjetDetailsID']);
        }

        return $this->sendResponse([], trans('custom.update', ['attribute' => trans('custom.budjet_details')]));
    }

    public function removeBudgetDetails(Request $request)
    {
        $input = $request->all();


        foreach ($input['items'] as $item) {
            /** @var Budjetdetails $budgetDetail */
            $budgetDetail = $this->budjetdetailsRepository->findWithoutFail($item['budjetDetailsID']);
            if (!empty($budgetDetail)) {
                $budgetDetail->delete();
            }
        }

        return $this->sendResponse([], 'Budjetdetails deleted successfully');
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


}
