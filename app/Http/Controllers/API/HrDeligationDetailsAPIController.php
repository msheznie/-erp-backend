<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrDeligationDetailsAPIRequest;
use App\Http\Requests\API\UpdateHrDeligationDetailsAPIRequest;
use App\Models\HrDeligationDetails;
use App\Repositories\HrDeligationDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrDeligationDetailsController
 * @package App\Http\Controllers\API
 */

class HrDeligationDetailsAPIController extends AppBaseController
{
    /** @var  HrDeligationDetailsRepository */
    private $hrDeligationDetailsRepository;

    public function __construct(HrDeligationDetailsRepository $hrDeligationDetailsRepo)
    {
        $this->hrDeligationDetailsRepository = $hrDeligationDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hrDeligationDetails",
     *      summary="getHrDeligationDetailsList",
     *      tags={"HrDeligationDetails"},
     *      description="Get all HrDeligationDetails",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/HrDeligationDetails")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->hrDeligationDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->hrDeligationDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrDeligationDetails = $this->hrDeligationDetailsRepository->all();

        return $this->sendResponse($hrDeligationDetails->toArray(), trans('custom.hr_deligation_details_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hrDeligationDetails",
     *      summary="createHrDeligationDetails",
     *      tags={"HrDeligationDetails"},
     *      description="Create HrDeligationDetails",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HrDeligationDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrDeligationDetailsAPIRequest $request)
    {
        $input = $request->all();

        $hrDeligationDetails = $this->hrDeligationDetailsRepository->create($input);

        return $this->sendResponse($hrDeligationDetails->toArray(), trans('custom.hr_deligation_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hrDeligationDetails/{id}",
     *      summary="getHrDeligationDetailsItem",
     *      tags={"HrDeligationDetails"},
     *      description="Get HrDeligationDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrDeligationDetails",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HrDeligationDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var HrDeligationDetails $hrDeligationDetails */
        $hrDeligationDetails = $this->hrDeligationDetailsRepository->findWithoutFail($id);

        if (empty($hrDeligationDetails)) {
            return $this->sendError(trans('custom.hr_deligation_details_not_found'));
        }

        return $this->sendResponse($hrDeligationDetails->toArray(), trans('custom.hr_deligation_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hrDeligationDetails/{id}",
     *      summary="updateHrDeligationDetails",
     *      tags={"HrDeligationDetails"},
     *      description="Update HrDeligationDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrDeligationDetails",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/HrDeligationDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrDeligationDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrDeligationDetails $hrDeligationDetails */
        $hrDeligationDetails = $this->hrDeligationDetailsRepository->findWithoutFail($id);

        if (empty($hrDeligationDetails)) {
            return $this->sendError(trans('custom.hr_deligation_details_not_found'));
        }

        $hrDeligationDetails = $this->hrDeligationDetailsRepository->update($input, $id);

        return $this->sendResponse($hrDeligationDetails->toArray(), trans('custom.hrdeligationdetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hrDeligationDetails/{id}",
     *      summary="deleteHrDeligationDetails",
     *      tags={"HrDeligationDetails"},
     *      description="Delete HrDeligationDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HrDeligationDetails",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var HrDeligationDetails $hrDeligationDetails */
        $hrDeligationDetails = $this->hrDeligationDetailsRepository->findWithoutFail($id);

        if (empty($hrDeligationDetails)) {
            return $this->sendError(trans('custom.hr_deligation_details_not_found'));
        }

        $hrDeligationDetails->delete();

        return $this->sendSuccess('Hr Deligation Details deleted successfully');
    }
}
