<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMobileDetailAPIRequest;
use App\Http\Requests\API\UpdateMobileDetailAPIRequest;
use App\Models\MobileDetail;
use App\Repositories\MobileDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MobileDetailController
 * @package App\Http\Controllers\API
 */

class MobileDetailAPIController extends AppBaseController
{
    /** @var  MobileDetailRepository */
    private $mobileDetailRepository;

    public function __construct(MobileDetailRepository $mobileDetailRepo)
    {
        $this->mobileDetailRepository = $mobileDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileDetails",
     *      summary="Get a listing of the MobileDetails.",
     *      tags={"MobileDetail"},
     *      description="Get all MobileDetails",
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
     *                  @SWG\Items(ref="#/definitions/MobileDetail")
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
        $this->mobileDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileDetails = $this->mobileDetailRepository->all();

        return $this->sendResponse($mobileDetails->toArray(), 'Mobile Details retrieved successfully');
    }

    /**
     * @param CreateMobileDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileDetails",
     *      summary="Store a newly created MobileDetail in storage",
     *      tags={"MobileDetail"},
     *      description="Store MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileDetail")
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
     *                  ref="#/definitions/MobileDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileDetailAPIRequest $request)
    {
        $input = $request->all();

        $mobileDetail = $this->mobileDetailRepository->create($input);

        return $this->sendResponse($mobileDetail->toArray(), 'Mobile Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileDetails/{id}",
     *      summary="Display the specified MobileDetail",
     *      tags={"MobileDetail"},
     *      description="Get MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileDetail",
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
     *                  ref="#/definitions/MobileDetail"
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
        /** @var MobileDetail $mobileDetail */
        $mobileDetail = $this->mobileDetailRepository->findWithoutFail($id);

        if (empty($mobileDetail)) {
            return $this->sendError('Mobile Detail not found');
        }

        return $this->sendResponse($mobileDetail->toArray(), 'Mobile Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMobileDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileDetails/{id}",
     *      summary="Update the specified MobileDetail in storage",
     *      tags={"MobileDetail"},
     *      description="Update MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileDetail")
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
     *                  ref="#/definitions/MobileDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileDetail $mobileDetail */
        $mobileDetail = $this->mobileDetailRepository->findWithoutFail($id);

        if (empty($mobileDetail)) {
            return $this->sendError('Mobile Detail not found');
        }

        $mobileDetail = $this->mobileDetailRepository->update($input, $id);

        return $this->sendResponse($mobileDetail->toArray(), 'MobileDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileDetails/{id}",
     *      summary="Remove the specified MobileDetail from storage",
     *      tags={"MobileDetail"},
     *      description="Delete MobileDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileDetail",
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
        /** @var MobileDetail $mobileDetail */
        $mobileDetail = $this->mobileDetailRepository->findWithoutFail($id);

        if (empty($mobileDetail)) {
            return $this->sendError('Mobile Detail not found');
        }

        $mobileDetail->delete();

        return $this->sendSuccess('Mobile Detail deleted successfully');
    }
}
