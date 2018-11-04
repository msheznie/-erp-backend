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

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails retrieved successfully');
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

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails saved successfully');
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
            return $this->sendError('Budjetdetails not found');
        }

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails retrieved successfully');
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

        /** @var Budjetdetails $budjetdetails */
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            return $this->sendError('Budjetdetails not found');
        }

        if(!$input['budjetAmtRpt']){
            $input['budjetAmtRpt'] = 0;
        }

        $currencyConvection = \Helper::currencyConversion($budjetdetails->companySystemID, 2, 2, $input['budjetAmtRpt']);

        $input['budjetAmtLocal'] = round($currencyConvection['localAmount'], 3);
        $budjetdetails = $this->budjetdetailsRepository->update(array_only($input, ['budjetAmtRpt', 'budjetAmtLocal']), $id);

        return $this->sendResponse($budjetdetails->toArray(), 'Budjetdetails updated successfully');
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
            return $this->sendError('Budjetdetails not found');
        }

        $budjetdetails->delete();

        return $this->sendResponse($id, 'Budjetdetails deleted successfully');
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
            return $this->sendError('Budget Master not found');
        }

        /* $glData = TemplatesGLCode::where('templateMasterID', $budgetMaster->templateMasterID)
             ->whereNotNull('chartOfAccountSystemID')
             ->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                 $q->where('companySystemID', $budgetMaster->companySystemID)
                     ->where('isActive', 1)
                     ->where('isAssigned', -1);
             })
             ->with(['template_detail'])
             //->limit(20)
             ->get();*/

        /*$search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $glData = $glData->where(function ($query) use ($search) {
                $query->where('glCode', 'LIKE', "%{$search}%")
                      ->orWhere('glDescription', 'LIKE', "%{$search}%")
                     ->orWhereHas('template_detail',function ($q) use($search){
                            $q->where('templateDetailDescription', 'LIKE', "%{$search}%");
                     });
            });
        }*/

        /*return \DataTables::eloquent($glData)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                       // $query->orderBy('itemIssueAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('items', function ($data) use($budgetMaster) {
                $details =  $this->budjetdetailsRepository->findWhere(['companySystemID' => $budgetMaster->companySystemID,
                    'serviceLineSystemID' => $budgetMaster->serviceLineSystemID,
                    'Year' => $budgetMaster->Year,
                    'chartOfAccountID' => $data->chartOfAccountSystemID,
                    'templateDetailID' => $data->templatesDetailsAutoID
                ]);

                return $details->toArray();
            })
            ->make(true);*/

        /*foreach ($glData as $key1 => $data) {
            $data['items'] = $this->budjetdetailsRepository->findWhere(['companySystemID' => $budgetMaster->companySystemID,
                'serviceLineSystemID' => $budgetMaster->serviceLineSystemID,
                'Year' => $budgetMaster->Year,
                'chartOfAccountID' => $data->chartOfAccountSystemID,
                'templateDetailID' => $data->templatesDetailsAutoID
            ]);
            //->orderBy('month','asc');
        }*/

        // Incomes
        $income = TemplatesDetails::where('templatesMasterAutoID', $budgetMaster->templateMasterID)
            ->where('controlAccountSystemID', 1)
            ->whereHas('gl_codes', function ($q) use ($budgetMaster) {
                $q->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                    $q->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                });
            })
            ->with(['gl_codes' => function ($q) use ($budgetMaster) {
                $q->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                    $q->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                });
            }])
            ->orderBy('sortOrder', 'asc')
            ->get();


        foreach ($income as $key1 => $data) {


            foreach ($data['gl_codes'] as $key => $gl_code) {

                $items = $this->budjetdetailsRepository->findWhere(['companySystemID' => $budgetMaster->companySystemID,
                    'serviceLineSystemID' => $budgetMaster->serviceLineSystemID,
                    'Year' => $budgetMaster->Year,
                    'chartOfAccountID' => $gl_code['chartOfAccountSystemID'],
                    'templateDetailID' => $gl_code['templatesDetailsAutoID']
                ]);

                $gl_code['items_count'] = count($items);
                $gl_code['items'] = $items;
            }
        }

        //Eexpense
        $expense = TemplatesDetails::where('templatesMasterAutoID', $budgetMaster->templateMasterID)
            ->where('controlAccountSystemID', 2)
            ->whereHas('gl_codes', function ($q) use ($budgetMaster) {
                $q->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                    $q->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                });
            })
            ->with(['gl_codes' => function ($q) use ($budgetMaster) {
                $q->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                    $q->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                });
            }])
            ->orderBy('sortOrder', 'asc')
            ->get();

        foreach ($expense as $data) {

            foreach ($data['gl_codes'] as $gl_code) {

                $gl_code['items'] = $this->budjetdetailsRepository->findWhere(['companySystemID' => $budgetMaster->companySystemID,
                    'serviceLineSystemID' => $budgetMaster->serviceLineSystemID,
                    'Year' => $budgetMaster->Year,
                    'chartOfAccountID' => $gl_code['chartOfAccountSystemID'],
                    'templateDetailID' => $gl_code['templatesDetailsAutoID']
                ]);
                //->orderBy('month','asc');
            }

        }


        $finalIncomeArray = array();
        $finalExpenseArray = array();

        foreach ($income as  $data) {
            $temGlCodes = array();
            foreach ($data['gl_codes'] as $gl_code)
            {
                if(count($gl_code['items']) > 0){
                    array_push($temGlCodes,$gl_code);
                }
            }
            $data['gl_codes_data'] = $temGlCodes;
            if(count($data['gl_codes_data']) > 0){
                array_push($finalIncomeArray,$data);
            }
        }

        foreach ($expense as  $data) {
            $temGlCodes = array();
            foreach ($data['gl_codes'] as $gl_code)
            {
                if(count($gl_code['items']) > 0){
                    array_push($temGlCodes,$gl_code);
                }
            }
            $data['gl_codes_data'] = $temGlCodes;
            if(count($data['gl_codes_data']) > 0){
                array_push($finalExpenseArray,$data);
            }
        }

        $finalArray = array('income' => $finalIncomeArray, 'expense' => $finalExpenseArray);

        return $this->sendResponse($finalArray, 'Budget details retrieved successfully');
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

        return $this->sendResponse([], 'Budjetdetails updated successfully');
    }

    public function removeBudgetDetails(Request $request)
    {
        $input = $request->all();


        foreach ($input['items'] as $item) {
            /** @var Budjetdetails $budgetDetail */
            $budgetDetail = $this->budjetdetailsRepository->findWithoutFail($item['budjetDetailsID']);
            if (!empty($budgetDetail)) {
                //return $this->sendError('Budget details not found');
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
            return $this->sendError('Budget Master not found');
        }

        //$incomesTotal
        /*$income = TemplatesDetails::where('templatesMasterAutoID', $budgetMaster->templateMasterID)
            ->where('controlAccountSystemID', 1)
            ->whereHas('gl_codes', function ($q) use ($budgetMaster) {
                $q->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                    $q->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                });
            })
            ->with(['gl_codes' => function ($q) use ($budgetMaster) {
                $q->whereHas('chart_of_account', function ($q) use ($budgetMaster) {
                    $q->where('companySystemID', $budgetMaster->companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                });
            }])
            ->orderBy('sortOrder', 'asc')
            ->get();

        foreach ($income as $data) {

            foreach ($data['gl_codes'] as $gl_code) {

                $gl_code['items'] = $this->budjetdetailsRepository->findWhere(['companySystemID' => $budgetMaster->companySystemID,
                    'serviceLineSystemID' => $budgetMaster->serviceLineSystemID,
                    'Year' => $budgetMaster->Year,
                    'chartOfAccountID' => $gl_code['chartOfAccountSystemID'],
                    'templateDetailID' => $gl_code['templatesDetailsAutoID']
                ]);
                //->orderBy('month','asc');
            }
        }*/

        $incomesTotal = 0;
        $total = array();


        return $this->sendResponse($total, 'Budjet details summary updated successfully');
    }


}
