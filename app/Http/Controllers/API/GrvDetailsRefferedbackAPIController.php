<?php
/**
 * =============================================
 * -- File Name : GRVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name : GrvDetailsRefferedback
 * -- Author : Mohamed Nazir
 * -- Create date : 14-November 2018
 * -- Description : This file contains the all CRUD for Grv Details Reffered back
 * -- REVISION HISTORY
 * -- Date: 14-November 2018 By: Nazir Description: Added new functions named as getGRVDetailsAmendHistory()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGrvDetailsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateGrvDetailsRefferedbackAPIRequest;
use App\Models\GrvDetailsRefferedback;
use App\Repositories\GrvDetailsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GrvDetailsRefferedbackController
 * @package App\Http\Controllers\API
 */

class GrvDetailsRefferedbackAPIController extends AppBaseController
{
    /** @var  GrvDetailsRefferedbackRepository */
    private $grvDetailsRefferedbackRepository;

    public function __construct(GrvDetailsRefferedbackRepository $grvDetailsRefferedbackRepo)
    {
        $this->grvDetailsRefferedbackRepository = $grvDetailsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/grvDetailsRefferedbacks",
     *      summary="Get a listing of the GrvDetailsRefferedbacks.",
     *      tags={"GrvDetailsRefferedback"},
     *      description="Get all GrvDetailsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/GrvDetailsRefferedback")
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
        $this->grvDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->grvDetailsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $grvDetailsRefferedbacks = $this->grvDetailsRefferedbackRepository->all();

        return $this->sendResponse($grvDetailsRefferedbacks->toArray(), trans('custom.grv_details_refferedbacks_retrieved_successfully'));
    }

    /**
     * @param CreateGrvDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/grvDetailsRefferedbacks",
     *      summary="Store a newly created GrvDetailsRefferedback in storage",
     *      tags={"GrvDetailsRefferedback"},
     *      description="Store GrvDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GrvDetailsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GrvDetailsRefferedback")
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
     *                  ref="#/definitions/GrvDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGrvDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $grvDetailsRefferedbacks = $this->grvDetailsRefferedbackRepository->create($input);

        return $this->sendResponse($grvDetailsRefferedbacks->toArray(), trans('custom.grv_details_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/grvDetailsRefferedbacks/{id}",
     *      summary="Display the specified GrvDetailsRefferedback",
     *      tags={"GrvDetailsRefferedback"},
     *      description="Get GrvDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvDetailsRefferedback",
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
     *                  ref="#/definitions/GrvDetailsRefferedback"
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
        /** @var GrvDetailsRefferedback $grvDetailsRefferedback */
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            return $this->sendError(trans('custom.grv_details_refferedback_not_found'));
        }

        return $this->sendResponse($grvDetailsRefferedback->toArray(), trans('custom.grv_details_refferedback_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGrvDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/grvDetailsRefferedbacks/{id}",
     *      summary="Update the specified GrvDetailsRefferedback in storage",
     *      tags={"GrvDetailsRefferedback"},
     *      description="Update GrvDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvDetailsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GrvDetailsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GrvDetailsRefferedback")
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
     *                  ref="#/definitions/GrvDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGrvDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var GrvDetailsRefferedback $grvDetailsRefferedback */
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            return $this->sendError(trans('custom.grv_details_refferedback_not_found'));
        }

        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($grvDetailsRefferedback->toArray(), trans('custom.grvdetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/grvDetailsRefferedbacks/{id}",
     *      summary="Remove the specified GrvDetailsRefferedback from storage",
     *      tags={"GrvDetailsRefferedback"},
     *      description="Delete GrvDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvDetailsRefferedback",
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
        /** @var GrvDetailsRefferedback $grvDetailsRefferedback */
        $grvDetailsRefferedback = $this->grvDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($grvDetailsRefferedback)) {
            return $this->sendError(trans('custom.grv_details_refferedback_not_found'));
        }

        $grvDetailsRefferedback->delete();

        return $this->sendResponse($id, trans('custom.grv_details_refferedback_deleted_successfully'));
    }

    public function getGRVDetailsAmendHistory(Request $request)
    {
        $input = $request->all();
        $grvAutoID = $input['grvAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = GrvDetailsRefferedback::where('grvAutoID', $grvAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['unit','po_master'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.grv_details_refferedback_retrieved_successfully'));
    }

    public function getGRVDetailsReversalHistory(Request $request)
    {
        $input = $request->all();
        $grvAutoID = $input['grvAutoID'];
        $timesReferred = $input['timesReferred'];
        $grvRefferedBackID = $input['grvRefferedBackID'];

        $items = GrvDetailsRefferedback::where('grvAutoID', $grvAutoID)
            ->where('timesReferred', $timesReferred)
            ->where('grvRefferedBackID', $grvRefferedBackID)
            ->with(['unit','po_master'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.grv_details_refferedback_retrieved_successfully'));
    }
}
