<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseEmployeeAllocationAPIRequest;
use App\Http\Requests\API\UpdateExpenseEmployeeAllocationAPIRequest;
use App\Models\ExpenseEmployeeAllocation;
use App\Repositories\ExpenseEmployeeAllocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseEmployeeAllocationController
 * @package App\Http\Controllers\API
 */

class ExpenseEmployeeAllocationAPIController extends AppBaseController
{
    /** @var  ExpenseEmployeeAllocationRepository */
    private $expenseEmployeeAllocationRepository;

    public function __construct(ExpenseEmployeeAllocationRepository $expenseEmployeeAllocationRepo)
    {
        $this->expenseEmployeeAllocationRepository = $expenseEmployeeAllocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseEmployeeAllocations",
     *      summary="Get a listing of the ExpenseEmployeeAllocations.",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Get all ExpenseEmployeeAllocations",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseEmployeeAllocation")
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
        $this->expenseEmployeeAllocationRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseEmployeeAllocationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseEmployeeAllocations = $this->expenseEmployeeAllocationRepository->all();

        return $this->sendResponse($expenseEmployeeAllocations->toArray(), 'Expense Employee Allocations retrieved successfully');
    }

    /**
     * @param CreateExpenseEmployeeAllocationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseEmployeeAllocations",
     *      summary="Store a newly created ExpenseEmployeeAllocation in storage",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Store ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseEmployeeAllocation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseEmployeeAllocation")
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
     *                  ref="#/definitions/ExpenseEmployeeAllocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseEmployeeAllocationAPIRequest $request)
    {
        $input = $request->all();

        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->create($input);

        return $this->sendResponse($expenseEmployeeAllocation->toArray(), 'Expense Employee Allocation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseEmployeeAllocations/{id}",
     *      summary="Display the specified ExpenseEmployeeAllocation",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Get ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseEmployeeAllocation",
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
     *                  ref="#/definitions/ExpenseEmployeeAllocation"
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
        /** @var ExpenseEmployeeAllocation $expenseEmployeeAllocation */
        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->findWithoutFail($id);

        if (empty($expenseEmployeeAllocation)) {
            return $this->sendError('Expense Employee Allocation not found');
        }

        return $this->sendResponse($expenseEmployeeAllocation->toArray(), 'Expense Employee Allocation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseEmployeeAllocationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseEmployeeAllocations/{id}",
     *      summary="Update the specified ExpenseEmployeeAllocation in storage",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Update ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseEmployeeAllocation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseEmployeeAllocation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseEmployeeAllocation")
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
     *                  ref="#/definitions/ExpenseEmployeeAllocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseEmployeeAllocationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseEmployeeAllocation $expenseEmployeeAllocation */
        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->findWithoutFail($id);

        if (empty($expenseEmployeeAllocation)) {
            return $this->sendError('Expense Employee Allocation not found');
        }

        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->update($input, $id);

        return $this->sendResponse($expenseEmployeeAllocation->toArray(), 'ExpenseEmployeeAllocation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseEmployeeAllocations/{id}",
     *      summary="Remove the specified ExpenseEmployeeAllocation from storage",
     *      tags={"ExpenseEmployeeAllocation"},
     *      description="Delete ExpenseEmployeeAllocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseEmployeeAllocation",
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
        /** @var ExpenseEmployeeAllocation $expenseEmployeeAllocation */
        $expenseEmployeeAllocation = $this->expenseEmployeeAllocationRepository->findWithoutFail($id);

        if (empty($expenseEmployeeAllocation)) {
            return $this->sendError('Expense Employee Allocation not found');
        }

        $expenseEmployeeAllocation->delete();

        return $this->sendSuccess('Expense Employee Allocation deleted successfully');
    }
}
