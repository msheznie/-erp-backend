<?php
/**
 * =============================================
 * -- File Name : JvDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  JvDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 25-September 2018
 * -- Description : This file contains the all CRUD for Jv Detail
 * -- REVISION HISTORY
 * -- Date: 25-September 2018 By: Nazir Description: Added new functions named as getJournalVoucherDetails()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvDetailAPIRequest;
use App\Http\Requests\API\UpdateJvDetailAPIRequest;
use App\Models\JvDetail;
use App\Repositories\JvDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class JvDetailController
 * @package App\Http\Controllers\API
 */

class JvDetailAPIController extends AppBaseController
{
    /** @var  JvDetailRepository */
    private $jvDetailRepository;

    public function __construct(JvDetailRepository $jvDetailRepo)
    {
        $this->jvDetailRepository = $jvDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetails",
     *      summary="Get a listing of the JvDetails.",
     *      tags={"JvDetail"},
     *      description="Get all JvDetails",
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
     *                  @SWG\Items(ref="#/definitions/JvDetail")
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
        $this->jvDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->jvDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jvDetails = $this->jvDetailRepository->all();

        return $this->sendResponse($jvDetails->toArray(), 'Jv Details retrieved successfully');
    }

    /**
     * @param CreateJvDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/jvDetails",
     *      summary="Store a newly created JvDetail in storage",
     *      tags={"JvDetail"},
     *      description="Store JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetail")
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
     *                  ref="#/definitions/JvDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateJvDetailAPIRequest $request)
    {
        $input = $request->all();

        $jvDetails = $this->jvDetailRepository->create($input);

        return $this->sendResponse($jvDetails->toArray(), 'Jv Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetails/{id}",
     *      summary="Display the specified JvDetail",
     *      tags={"JvDetail"},
     *      description="Get JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetail",
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
     *                  ref="#/definitions/JvDetail"
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
        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            return $this->sendError('Jv Detail not found');
        }

        return $this->sendResponse($jvDetail->toArray(), 'Jv Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateJvDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/jvDetails/{id}",
     *      summary="Update the specified JvDetail in storage",
     *      tags={"JvDetail"},
     *      description="Update JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetail")
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
     *                  ref="#/definitions/JvDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateJvDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            return $this->sendError('Jv Detail not found');
        }

        $jvDetail = $this->jvDetailRepository->update($input, $id);

        return $this->sendResponse($jvDetail->toArray(), 'JvDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jvDetails/{id}",
     *      summary="Remove the specified JvDetail from storage",
     *      tags={"JvDetail"},
     *      description="Delete JvDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetail",
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
        /** @var JvDetail $jvDetail */
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            return $this->sendError('Jv Detail not found');
        }

        $jvDetail->delete();

        return $this->sendResponse($id, 'Jv Detail deleted successfully');
    }

    public function getJournalVoucherDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['jvMasterAutoId'];

        $items = JvDetail::where('jvMasterAutoId', $id)
            ->with(['segment','currency_by'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Jv Detail retrieved successfully');
    }
}
