<?php
/**
 * =============================================
 * -- File Name : BudgetMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget
 * -- Author : Mohamed Nazir
 * -- Create date : 16 - October 2018
 * -- Description : This file contains the all CRUD for Budget Master
 * -- REVISION HISTORY
 * -- Date: 16 -October 2018 By: Fayas Description: Added new function getBudgetsByCompany()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetMasterAPIRequest;
use App\Http\Requests\API\UpdateBudgetMasterAPIRequest;
use App\Models\BudgetMaster;
use App\Repositories\BudgetMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetMasterController
 * @package App\Http\Controllers\API
 */
class BudgetMasterAPIController extends AppBaseController
{
    /** @var  BudgetMasterRepository */
    private $budgetMasterRepository;

    public function __construct(BudgetMasterRepository $budgetMasterRepo)
    {
        $this->budgetMasterRepository = $budgetMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetMasters",
     *      summary="Get a listing of the BudgetMasters.",
     *      tags={"BudgetMaster"},
     *      description="Get all BudgetMasters",
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
     *                  @SWG\Items(ref="#/definitions/BudgetMaster")
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
        $this->budgetMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetMasters = $this->budgetMasterRepository->all();

        return $this->sendResponse($budgetMasters->toArray(), 'Budget Masters retrieved successfully');
    }

    /**
     * @param CreateBudgetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetMasters",
     *      summary="Store a newly created BudgetMaster in storage",
     *      tags={"BudgetMaster"},
     *      description="Store BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetMaster")
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
     *                  ref="#/definitions/BudgetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetMasterAPIRequest $request)
    {
        $input = $request->all();

        $budgetMasters = $this->budgetMasterRepository->create($input);

        return $this->sendResponse($budgetMasters->toArray(), 'Budget Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetMasters/{id}",
     *      summary="Display the specified BudgetMaster",
     *      tags={"BudgetMaster"},
     *      description="Get BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMaster",
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
     *                  ref="#/definitions/BudgetMaster"
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
        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError('Budget Master not found');
        }

        return $this->sendResponse($budgetMaster->toArray(), 'Budget Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBudgetMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetMasters/{id}",
     *      summary="Update the specified BudgetMaster in storage",
     *      tags={"BudgetMaster"},
     *      description="Update BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetMaster")
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
     *                  ref="#/definitions/BudgetMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError('Budget Master not found');
        }

        $budgetMaster = $this->budgetMasterRepository->update($input, $id);

        return $this->sendResponse($budgetMaster->toArray(), 'BudgetMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetMasters/{id}",
     *      summary="Remove the specified BudgetMaster from storage",
     *      tags={"BudgetMaster"},
     *      description="Delete BudgetMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetMaster",
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
        /** @var BudgetMaster $budgetMaster */
        $budgetMaster = $this->budgetMasterRepository->findWithoutFail($id);

        if (empty($budgetMaster)) {
            return $this->sendError('Budget Master not found');
        }

        $budgetMaster->delete();

        return $this->sendResponse($id, 'Budget Master deleted successfully');
    }

    public function getBudgetsByCompany(Request $request)
    {

        $input = $request->all();

        //$input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year'));

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

        $budgets = BudgetMaster::whereIn('companySystemID', $subCompanies)
                                ->with('segment_by', 'template_master');

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('Year', 'like', "%{$search}%")
                    ->orWhereHas('segment_by', function ($q1) use ($search) {
                         $q1->where('ServiceLineDes', 'like', "%{$search}%");
                    })->orWhereHas('template_master', function ($q2) use ($search) {
                         $q2->where('templateDescription', 'like', "%{$search}%");
                    });
            });
        }

        $budgets = $budgets->groupBy(['Year','serviceLineSystemID']);

        return \DataTables::of($budgets)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('Year', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
