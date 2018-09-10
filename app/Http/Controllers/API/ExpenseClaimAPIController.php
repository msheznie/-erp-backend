<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Mohamed Nazir
 * -- Create date : 10 - September 2018
 * -- Description : This file contains the all CRUD for Expense Claim
 * -- REVISION HISTORY
 * -- Date: 10- September 2018 By: Fayas Description: Added new function getExpenseClaimByCompany(),getExpenseClaimFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimAPIRequest;
use App\Models\ExpenseClaim;
use App\Models\ExpenseClaimType;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ExpenseClaimRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimAPIController extends AppBaseController
{
    /** @var  ExpenseClaimRepository */
    private $expenseClaimRepository;

    public function __construct(ExpenseClaimRepository $expenseClaimRepo)
    {
        $this->expenseClaimRepository = $expenseClaimRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaims",
     *      summary="Get a listing of the ExpenseClaims.",
     *      tags={"ExpenseClaim"},
     *      description="Get all ExpenseClaims",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaim")
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
        $this->expenseClaimRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaims = $this->expenseClaimRepository->all();

        return $this->sendResponse($expenseClaims->toArray(), 'Expense Claims retrieved successfully');
    }

    /**
     * @param CreateExpenseClaimAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaims",
     *      summary="Store a newly created ExpenseClaim in storage",
     *      tags={"ExpenseClaim"},
     *      description="Store ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaim that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaim")
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
     *                  ref="#/definitions/ExpenseClaim"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaims = $this->expenseClaimRepository->create($input);

        return $this->sendResponse($expenseClaims->toArray(), 'Expense Claim saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaims/{id}",
     *      summary="Display the specified ExpenseClaim",
     *      tags={"ExpenseClaim"},
     *      description="Get ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaim",
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
     *                  ref="#/definitions/ExpenseClaim"
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
        /** @var ExpenseClaim $expenseClaim */
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaims/{id}",
     *      summary="Update the specified ExpenseClaim in storage",
     *      tags={"ExpenseClaim"},
     *      description="Update ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaim",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaim that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaim")
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
     *                  ref="#/definitions/ExpenseClaim"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaim $expenseClaim */
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim = $this->expenseClaimRepository->update($input, $id);

        return $this->sendResponse($expenseClaim->toArray(), 'ExpenseClaim updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaims/{id}",
     *      summary="Remove the specified ExpenseClaim from storage",
     *      tags={"ExpenseClaim"},
     *      description="Delete ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaim",
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
        /** @var ExpenseClaim $expenseClaim */
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim->delete();

        return $this->sendResponse($id, 'Expense Claim deleted successfully');
    }

    public function getExpenseClaimByCompany(Request $request){

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'glCodeAssignedYN', 'approved', 'year'));

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

        $expenseClaims = ExpenseClaim::whereIn('companySystemID', $subCompanies)
            ->with('created_by')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $expenseClaims = $expenseClaims->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $expenseClaims = $expenseClaims->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('glCodeAssignedYN', $input)) {
            if (($input['glCodeAssignedYN'] == 0 || $input['glCodeAssignedYN'] == -1) && !is_null($input['glCodeAssignedYN'])) {
                $expenseClaims = $expenseClaims->where('glCodeAssignedYN', '=', $input['glCodeAssignedYN']);
            }
        }
        
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $expenseClaims = $expenseClaims->where(function ($query) use ($search) {
                $query->where('expenseClaimCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($expenseClaims)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('expenseClaimMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getExpenseClaimFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $types = ExpenseClaimType::all();

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->where('isActive', 1)->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'types' => $types,
            'segments' => $segments
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
