<?php
/**
 * =============================================
 * -- File Name : MonthlyAdditionDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Monthly Addition Detail
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - November 2018
 * -- Description : This file contains the all CRUD for Monthly Addition Detail
 * -- REVISION HISTORY
 * -- Date: 07-November 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMonthlyAdditionDetailAPIRequest;
use App\Http\Requests\API\UpdateMonthlyAdditionDetailAPIRequest;
use App\Models\MonthlyAdditionDetail;
use App\Repositories\MonthlyAdditionDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MonthlyAdditionDetailController
 * @package App\Http\Controllers\API
 */

class MonthlyAdditionDetailAPIController extends AppBaseController
{
    /** @var  MonthlyAdditionDetailRepository */
    private $monthlyAdditionDetailRepository;

    public function __construct(MonthlyAdditionDetailRepository $monthlyAdditionDetailRepo)
    {
        $this->monthlyAdditionDetailRepository = $monthlyAdditionDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyAdditionDetails",
     *      summary="Get a listing of the MonthlyAdditionDetails.",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Get all MonthlyAdditionDetails",
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
     *                  @SWG\Items(ref="#/definitions/MonthlyAdditionDetail")
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
        $this->monthlyAdditionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->monthlyAdditionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $monthlyAdditionDetails = $this->monthlyAdditionDetailRepository->all();

        return $this->sendResponse($monthlyAdditionDetails->toArray(), 'Monthly Addition Details retrieved successfully');
    }

    /**
     * @param CreateMonthlyAdditionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/monthlyAdditionDetails",
     *      summary="Store a newly created MonthlyAdditionDetail in storage",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Store MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyAdditionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyAdditionDetail")
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
     *                  ref="#/definitions/MonthlyAdditionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMonthlyAdditionDetailAPIRequest $request)
    {
        $input = $request->all();

        $monthlyAdditionDetails = $this->monthlyAdditionDetailRepository->create($input);

        return $this->sendResponse($monthlyAdditionDetails->toArray(), 'Monthly Addition Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/monthlyAdditionDetails/{id}",
     *      summary="Display the specified MonthlyAdditionDetail",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Get MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionDetail",
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
     *                  ref="#/definitions/MonthlyAdditionDetail"
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
        /** @var MonthlyAdditionDetail $monthlyAdditionDetail */
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            return $this->sendError('Monthly Addition Detail not found');
        }

        return $this->sendResponse($monthlyAdditionDetail->toArray(), 'Monthly Addition Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateMonthlyAdditionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/monthlyAdditionDetails/{id}",
     *      summary="Update the specified MonthlyAdditionDetail in storage",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Update MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MonthlyAdditionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MonthlyAdditionDetail")
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
     *                  ref="#/definitions/MonthlyAdditionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMonthlyAdditionDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var MonthlyAdditionDetail $monthlyAdditionDetail */
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            return $this->sendError('Monthly Addition Detail not found');
        }

        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->update($input, $id);

        return $this->sendResponse($monthlyAdditionDetail->toArray(), 'MonthlyAdditionDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/monthlyAdditionDetails/{id}",
     *      summary="Remove the specified MonthlyAdditionDetail from storage",
     *      tags={"MonthlyAdditionDetail"},
     *      description="Delete MonthlyAdditionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MonthlyAdditionDetail",
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
        /** @var MonthlyAdditionDetail $monthlyAdditionDetail */
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            return $this->sendError('Monthly Addition Detail not found');
        }

        $monthlyAdditionDetail->delete();

        return $this->sendResponse($id, 'Monthly Addition Detail deleted successfully');
    }
}
