<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateErpBudgetAdditionDetailAPIRequest;
use App\Http\Requests\API\UpdateErpBudgetAdditionDetailAPIRequest;
use App\Models\ErpBudgetAdditionDetail;
use App\Repositories\ErpBudgetAdditionDetailRepository;
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

    public function __construct(ErpBudgetAdditionDetailRepository $erpBudgetAdditionDetailRepo)
    {
        $this->erpBudgetAdditionDetailRepository = $erpBudgetAdditionDetailRepo;
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

        return $this->sendResponse($erpBudgetAdditionDetails->toArray(), 'Erp Budget Addition Details retrieved successfully');
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

        $budgetAdditionDetails = $this->erpBudgetAdditionDetailRepository->create($input);

        return $this->sendResponse($budgetAdditionDetails->toArray(), 'Budget Transfer Form Detail saved successfully');
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
            return $this->sendError('Erp Budget Addition Detail not found');
        }

        return $this->sendResponse($erpBudgetAdditionDetail->toArray(), 'Erp Budget Addition Detail retrieved successfully');
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
            return $this->sendError('Erp Budget Addition Detail not found');
        }

        $erpBudgetAdditionDetail = $this->erpBudgetAdditionDetailRepository->update($input, $id);

        return $this->sendResponse($erpBudgetAdditionDetail->toArray(), 'ErpBudgetAdditionDetail updated successfully');
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
            return $this->sendError('Erp Budget Addition Detail not found');
        }

        $erpBudgetAdditionDetail->delete();

        return $this->sendResponse($id, 'Erp Budget Addition Detail deleted successfully');
    }

    public function getDetailsByBudgetAddition(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        $items = ErpBudgetAdditionDetail::where('budgetAdditionFormAutoID', $id)
            ->with(['segment', 'template'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Budget Transfer Form Detail retrieved successfully');
    }

}
