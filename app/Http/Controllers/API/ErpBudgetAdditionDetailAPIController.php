<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateErpBudgetAdditionDetailAPIRequest;
use App\Http\Requests\API\UpdateErpBudgetAdditionDetailAPIRequest;
use App\Models\BudgetMaster;
use App\Models\Budjetdetails;
use App\Models\Company;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyFinanceYearperiodMaster;
use App\Models\ErpBudgetAdditionDetail;
use App\Models\SegmentMaster;
use App\Repositories\ErpBudgetAdditionDetailRepository;
use App\Repositories\ErpBudgetAdditionRepository;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpBudgetAdditionDetailController
 *
 * @package App\Http\Controllers\API
 */
class ErpBudgetAdditionDetailAPIController extends AppBaseController
{
    /** @var  ErpBudgetAdditionDetailRepository */
    private $erpBudgetAdditionDetailRepository;

    private $erpBudgetAdditionRepository;

    private $budgetDetailsRepository;

    public function __construct(ErpBudgetAdditionDetailRepository $erpBudgetAdditionDetailRepo, ErpBudgetAdditionRepository $erpBudgetAdditionRepository, BudjetdetailsRepository $budgetDetailsRepository)
    {
        $this->erpBudgetAdditionDetailRepository = $erpBudgetAdditionDetailRepo;
        $this->erpBudgetAdditionRepository = $erpBudgetAdditionRepository;
        $this->budgetDetailsRepository = $budgetDetailsRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditionDetails",
     *      summary="Get a listing of the ErpBudgetAdditionDetails.",
     *      tags={"ErpBudgetAdditionDetail"},
     *      description="Get all ErpBudgetAdditionDetails",
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
     *                  @SWG\Items(ref="#/definitions/ErpBudgetAdditionDetail")
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
        $this->erpBudgetAdditionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->erpBudgetAdditionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpBudgetAdditionDetails = $this->erpBudgetAdditionDetailRepository->all();

        return $this->sendResponse($erpBudgetAdditionDetails->toArray(), trans('custom.erp_budget_addition_details_retrieved_successfully'));
    }

    /**
     * @param CreateErpBudgetAdditionDetailAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpBudgetAdditionDetails",
     *      summary="Store a newly created ErpBudgetAdditionDetail in storage",
     *      tags={"ErpBudgetAdditionDetail"},
     *      description="Store ErpBudgetAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAdditionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAdditionDetail")
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
     *                  ref="#/definitions/ErpBudgetAdditionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpBudgetAdditionDetailAPIRequest $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $budgetAdditionMaster = $this->erpBudgetAdditionRepository->find($input['budgetAdditionFormAutoID']);

        if (empty($budgetAdditionMaster)) {
            return $this->sendError(trans('custom.budget_addition_not_found'));
        }


        /*Department*/
        $department = SegmentMaster::where('companySystemID', $budgetAdditionMaster->companySystemID)
            ->where('serviceLineSystemID', $input['serviceLineSystemID'])
            ->first();

        if (empty($department)) {
            return $this->sendError(trans('custom.department_not_found'));
        }

        if ($department->isActive == 0) {
            return $this->sendError('Please select an active to department', 500);
        }

        $companyData = Company::find($budgetAdditionMaster->companySystemID);

        if (empty($companyData)) {
            return $this->sendError(trans('custom.company_not_found'));
        }


        $input['serviceLineCode'] = $department->ServiceLineCode;

        /*GL Code*/
        $chartOfAccount = ChartOfAccountsAssigned::where('companySystemID', $budgetAdditionMaster->companySystemID)
            ->where('chartOfAccountSystemID', $input['chartOfAccountSystemID'])
            ->first();

        if (empty($chartOfAccount)) {
            return $this->sendError(trans('custom.account_not_found'), 500);
        }

        $input['gLCode'] = $chartOfAccount->AccountCode;
        $input['gLCodeDescription'] = $chartOfAccount->AccountDescription;

        /*Reporting Amount*/
        $input['adjustmentAmountRpt'] = floatval($input['adjustmentAmountRpt']);

        /*Local Amount*/
        $currency = \Helper::currencyConversion($budgetAdditionMaster->companySystemID, $companyData->reportingCurrency, $companyData->reportingCurrency, $input['adjustmentAmountRpt']);

        if($input['adjustmentAmountRpt'] < 0) {
            $input['adjustmentAmountLocal'] = -$currency['localAmount'];
        }
        else {
            $input['adjustmentAmountLocal'] = $currency['localAmount'];
        }

        /*Budget details id*/
        $budgetMaster = BudgetMaster::where([
            'companySystemID' => $budgetAdditionMaster->companySystemID,
            'Year' => $budgetAdditionMaster['year'],
            'templateMasterID'=> $budgetAdditionMaster->templatesMasterAutoID,
            'serviceLineSystemID'=> $input['serviceLineSystemID'],
        ])->first();

        if (!$budgetMaster) {
            return $this->sendError(trans('custom.budget_is_not_created_for_selected_segment_and_fin'));
        }

        $budgetDetails = Budjetdetails::where([
            'budgetmasterID' => $budgetMaster['budgetmasterID'],
            'companySystemID' => $budgetAdditionMaster->companySystemID,
            'templateDetailID' => $input['templateDetailID'],
            'gLCode' => $input['gLCode'],
            'Year' => $budgetAdditionMaster['year'],
        ])->first();


        if (!$budgetDetails) {
            $companyFinanceMonths = CompanyFinanceYearperiodMaster::where('companyFinanceYearID',$budgetMaster['companyFinanceYearID'])->get();
            foreach ($companyFinanceMonths as $companyFinanceMonth) {
                $month = \Carbon\Carbon::parse($companyFinanceMonth->dateFrom);
                $newBudgetDetails = array();
                $newBudgetDetails['budgetmasterID'] = $budgetMaster['budgetmasterID'];
                $newBudgetDetails['companySystemID'] = $budgetAdditionMaster->companySystemID;
                $newBudgetDetails['companyId'] = $budgetAdditionMaster->companyID;
                $newBudgetDetails['companyFinanceYearID'] = $budgetMaster['companyFinanceYearID'];
                $newBudgetDetails['serviceLineSystemID'] = $input['serviceLineSystemID'];
                $newBudgetDetails['serviceLine'] = $input['serviceLineCode'];
                $newBudgetDetails['templateDetailID'] = $input['templateDetailID'];
                $newBudgetDetails['chartOfAccountID'] = $input['chartOfAccountSystemID'];
                $newBudgetDetails['glCode'] = $chartOfAccount->AccountCode;
                $newBudgetDetails['glCodeType'] = $chartOfAccount->controlAccounts;
                $newBudgetDetails['Year'] = $budgetAdditionMaster['year'];
                $newBudgetDetails['month'] = $month->format('m');
                $newBudgetDetails['createdByUserSystemID'] = $budgetAdditionMaster['createdUserSystemID'];
                $newBudgetDetails['createdByUserID'] = $budgetAdditionMaster['createdUserID'];
                $budgetDetails = $this->budgetDetailsRepository->create($newBudgetDetails);
            }
        }


        $input['budjetDetailsID'] = $budgetDetails['budjetDetailsID'];

        $budgetAdditionDetails = $this->erpBudgetAdditionDetailRepository->create($input);

        return $this->sendResponse($budgetAdditionDetails->toArray(), trans('custom.budget_addition_form_detail_saved_successfully'));
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpBudgetAdditionDetails/{id}",
     *      summary="Display the specified ErpBudgetAdditionDetail",
     *      tags={"ErpBudgetAdditionDetail"},
     *      description="Get ErpBudgetAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAdditionDetail",
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
     *                  ref="#/definitions/ErpBudgetAdditionDetail"
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
        /** @var ErpBudgetAdditionDetail $erpBudgetAdditionDetail */
        $erpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepository->findWithoutFail($id);

        if (empty($erpBudgetAdditionDetail)) {
            return $this->sendError(trans('custom.erp_budget_addition_detail_not_found'));
        }

        return $this->sendResponse($erpBudgetAdditionDetail->toArray(), trans('custom.erp_budget_addition_detail_retrieved_successfully'));
    }

    /**
     * @param int                                     $id
     * @param UpdateErpBudgetAdditionDetailAPIRequest $request
     *
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpBudgetAdditionDetails/{id}",
     *      summary="Update the specified ErpBudgetAdditionDetail in storage",
     *      tags={"ErpBudgetAdditionDetail"},
     *      description="Update ErpBudgetAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAdditionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpBudgetAdditionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpBudgetAdditionDetail")
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
     *                  ref="#/definitions/ErpBudgetAdditionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpBudgetAdditionDetailAPIRequest $request)
    {

        $input = $request->all();

        /** @var ErpBudgetAdditionDetail $erpBudgetAdditionDetail */
        $erpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepository->findWithoutFail($id);

        if (empty($erpBudgetAdditionDetail)) {
            return $this->sendError(trans('custom.erp_budget_addition_detail_not_found'));
        }

        $erpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepository->update($input, $id);

        return $this->sendResponse($erpBudgetAdditionDetail->toArray(), trans('custom.erpbudgetadditiondetail_updated_successfully'));
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpBudgetAdditionDetails/{id}",
     *      summary="Remove the specified ErpBudgetAdditionDetail from storage",
     *      tags={"ErpBudgetAdditionDetail"},
     *      description="Delete ErpBudgetAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpBudgetAdditionDetail",
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
        /** @var ErpBudgetAdditionDetail $erpBudgetAdditionDetail */
        $erpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepository->findWithoutFail($id);

        if (empty($erpBudgetAdditionDetail)) {
            return $this->sendError(trans('custom.erp_budget_addition_detail_not_found'));
        }

        $erpBudgetAdditionDetail->delete();

        return $this->sendResponse($id, trans('custom.erp_budget_addition_detail_deleted_successfully'));
    }

    public function getDetailsByBudgetAddition(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $items = ErpBudgetAdditionDetail::where('budgetAdditionFormAutoID', $id)
            ->with(['segment', 'template'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.budget_addition_form_detail_retrieved_successfully'));
    }

}
