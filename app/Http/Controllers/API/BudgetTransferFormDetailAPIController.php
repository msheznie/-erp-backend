<?php
/**
 * =============================================
 * -- File Name : BudgetTransferFormDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Budget Transfer
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - August 2018
 * -- Description : This file contains the all CRUD for Budget Transfer Form Detail
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getDetailsByBudgetTransfer()
 */



namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTransferFormDetailAPIRequest;
use App\Http\Requests\API\UpdateBudgetTransferFormDetailAPIRequest;
use App\Models\BudgetTransferFormDetail;
use App\Repositories\BudgetTransferFormDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetTransferFormDetailController
 * @package App\Http\Controllers\API
 */

class BudgetTransferFormDetailAPIController extends AppBaseController
{
    /** @var  BudgetTransferFormDetailRepository */
    private $budgetTransferFormDetailRepository;

    public function __construct(BudgetTransferFormDetailRepository $budgetTransferFormDetailRepo)
    {
        $this->budgetTransferFormDetailRepository = $budgetTransferFormDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormDetails",
     *      summary="Get a listing of the BudgetTransferFormDetails.",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Get all BudgetTransferFormDetails",
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
     *                  @SWG\Items(ref="#/definitions/BudgetTransferFormDetail")
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
        $this->budgetTransferFormDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTransferFormDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->all();

        return $this->sendResponse($budgetTransferFormDetails->toArray(), 'Budget Transfer Form Details retrieved successfully');
    }

    /**
     * @param CreateBudgetTransferFormDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetTransferFormDetails",
     *      summary="Store a newly created BudgetTransferFormDetail in storage",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Store BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormDetail")
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
     *                  ref="#/definitions/BudgetTransferFormDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetTransferFormDetailAPIRequest $request)
    {
        $input = $request->all();

        $budgetTransferFormDetails = $this->budgetTransferFormDetailRepository->create($input);

        return $this->sendResponse($budgetTransferFormDetails->toArray(), 'Budget Transfer Form Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetTransferFormDetails/{id}",
     *      summary="Display the specified BudgetTransferFormDetail",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Get BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetail",
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
     *                  ref="#/definitions/BudgetTransferFormDetail"
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
        /** @var BudgetTransferFormDetail $budgetTransferFormDetail */
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            return $this->sendError('Budget Transfer Form Detail not found');
        }

        return $this->sendResponse($budgetTransferFormDetail->toArray(), 'Budget Transfer Form Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBudgetTransferFormDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetTransferFormDetails/{id}",
     *      summary="Update the specified BudgetTransferFormDetail in storage",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Update BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetTransferFormDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetTransferFormDetail")
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
     *                  ref="#/definitions/BudgetTransferFormDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetTransferFormDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetTransferFormDetail $budgetTransferFormDetail */
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            return $this->sendError('Budget Transfer Form Detail not found');
        }

        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->update($input, $id);

        return $this->sendResponse($budgetTransferFormDetail->toArray(), 'BudgetTransferFormDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetTransferFormDetails/{id}",
     *      summary="Remove the specified BudgetTransferFormDetail from storage",
     *      tags={"BudgetTransferFormDetail"},
     *      description="Delete BudgetTransferFormDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetTransferFormDetail",
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
        /** @var BudgetTransferFormDetail $budgetTransferFormDetail */
        $budgetTransferFormDetail = $this->budgetTransferFormDetailRepository->findWithoutFail($id);

        if (empty($budgetTransferFormDetail)) {
            return $this->sendError('Budget Transfer Form Detail not found');
        }

        $budgetTransferFormDetail->delete();

        return $this->sendResponse($id, 'Budget Transfer Form Detail deleted successfully');
    }

    public function getDetailsByBudgetTransfer(Request $request)
    {
        $input = $request->all();
        $id = $input['budgetTransferFormAutoID'];

        $items = BudgetTransferFormDetail::where('budgetTransferFormAutoID', $id)
            ->with(['from_segment','to_segment','from_template','to_template'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Budget Transfer Form Detail retrieved successfully');
    }


}
