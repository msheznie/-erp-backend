<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectReceiptDetailAPIRequest;
use App\Http\Requests\API\UpdateDirectReceiptDetailAPIRequest;
use App\Models\DirectReceiptDetail;
use App\Repositories\DirectReceiptDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectReceiptDetailController
 * @package App\Http\Controllers\API
 */

class DirectReceiptDetailAPIController extends AppBaseController
{
    /** @var  DirectReceiptDetailRepository */
    private $directReceiptDetailRepository;

    public function __construct(DirectReceiptDetailRepository $directReceiptDetailRepo)
    {
        $this->directReceiptDetailRepository = $directReceiptDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directReceiptDetails",
     *      summary="Get a listing of the DirectReceiptDetails.",
     *      tags={"DirectReceiptDetail"},
     *      description="Get all DirectReceiptDetails",
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
     *                  @SWG\Items(ref="#/definitions/DirectReceiptDetail")
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
        $this->directReceiptDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->directReceiptDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directReceiptDetails = $this->directReceiptDetailRepository->all();

        return $this->sendResponse($directReceiptDetails->toArray(), 'Direct Receipt Details retrieved successfully');
    }

    /**
     * @param CreateDirectReceiptDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directReceiptDetails",
     *      summary="Store a newly created DirectReceiptDetail in storage",
     *      tags={"DirectReceiptDetail"},
     *      description="Store DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectReceiptDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectReceiptDetail")
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
     *                  ref="#/definitions/DirectReceiptDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectReceiptDetailAPIRequest $request)
    {
        $input = $request->all();

        $directReceiptDetails = $this->directReceiptDetailRepository->create($input);

        return $this->sendResponse($directReceiptDetails->toArray(), 'Direct Receipt Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directReceiptDetails/{id}",
     *      summary="Display the specified DirectReceiptDetail",
     *      tags={"DirectReceiptDetail"},
     *      description="Get DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetail",
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
     *                  ref="#/definitions/DirectReceiptDetail"
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
        /** @var DirectReceiptDetail $directReceiptDetail */
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            return $this->sendError('Direct Receipt Detail not found');
        }

        return $this->sendResponse($directReceiptDetail->toArray(), 'Direct Receipt Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectReceiptDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directReceiptDetails/{id}",
     *      summary="Update the specified DirectReceiptDetail in storage",
     *      tags={"DirectReceiptDetail"},
     *      description="Update DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectReceiptDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectReceiptDetail")
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
     *                  ref="#/definitions/DirectReceiptDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectReceiptDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectReceiptDetail $directReceiptDetail */
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            return $this->sendError('Direct Receipt Detail not found');
        }

        $directReceiptDetail = $this->directReceiptDetailRepository->update($input, $id);

        return $this->sendResponse($directReceiptDetail->toArray(), 'DirectReceiptDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directReceiptDetails/{id}",
     *      summary="Remove the specified DirectReceiptDetail from storage",
     *      tags={"DirectReceiptDetail"},
     *      description="Delete DirectReceiptDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetail",
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
        /** @var DirectReceiptDetail $directReceiptDetail */
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            return $this->sendError('Direct Receipt Detail not found');
        }

        $directReceiptDetail->delete();

        return $this->sendResponse($id, 'Direct Receipt Detail deleted successfully');
    }
}
