<?php
/**
 * =============================================
 * -- File Name : JvDetailsReferredbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Jv Details Referred back
 * -- Author : Mohamed Nazir
 * -- Create date : 05 - December 2018
 * -- Description : This file contains the all CRUD for Jv Details Referred back
 * -- REVISION HISTORY
 * -- Date: 05-December 2018 By: Nazir Description: Added new function getJVDetailAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateJvDetailsReferredbackAPIRequest;
use App\Http\Requests\API\UpdateJvDetailsReferredbackAPIRequest;
use App\Models\JvDetailsReferredback;
use App\Repositories\JvDetailsReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class JvDetailsReferredbackController
 * @package App\Http\Controllers\API
 */

class JvDetailsReferredbackAPIController extends AppBaseController
{
    /** @var  JvDetailsReferredbackRepository */
    private $jvDetailsReferredbackRepository;

    public function __construct(JvDetailsReferredbackRepository $jvDetailsReferredbackRepo)
    {
        $this->jvDetailsReferredbackRepository = $jvDetailsReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetailsReferredbacks",
     *      summary="Get a listing of the JvDetailsReferredbacks.",
     *      tags={"JvDetailsReferredback"},
     *      description="Get all JvDetailsReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/JvDetailsReferredback")
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
        $this->jvDetailsReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->jvDetailsReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $jvDetailsReferredbacks = $this->jvDetailsReferredbackRepository->all();

        return $this->sendResponse($jvDetailsReferredbacks->toArray(), trans('custom.jv_details_referredbacks_retrieved_successfully'));
    }

    /**
     * @param CreateJvDetailsReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/jvDetailsReferredbacks",
     *      summary="Store a newly created JvDetailsReferredback in storage",
     *      tags={"JvDetailsReferredback"},
     *      description="Store JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetailsReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetailsReferredback")
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
     *                  ref="#/definitions/JvDetailsReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateJvDetailsReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $jvDetailsReferredbacks = $this->jvDetailsReferredbackRepository->create($input);

        return $this->sendResponse($jvDetailsReferredbacks->toArray(), trans('custom.jv_details_referredback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/jvDetailsReferredbacks/{id}",
     *      summary="Display the specified JvDetailsReferredback",
     *      tags={"JvDetailsReferredback"},
     *      description="Get JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetailsReferredback",
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
     *                  ref="#/definitions/JvDetailsReferredback"
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
        /** @var JvDetailsReferredback $jvDetailsReferredback */
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            return $this->sendError(trans('custom.jv_details_referredback_not_found'));
        }

        return $this->sendResponse($jvDetailsReferredback->toArray(), trans('custom.jv_details_referredback_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateJvDetailsReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/jvDetailsReferredbacks/{id}",
     *      summary="Update the specified JvDetailsReferredback in storage",
     *      tags={"JvDetailsReferredback"},
     *      description="Update JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetailsReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="JvDetailsReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/JvDetailsReferredback")
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
     *                  ref="#/definitions/JvDetailsReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateJvDetailsReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var JvDetailsReferredback $jvDetailsReferredback */
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            return $this->sendError(trans('custom.jv_details_referredback_not_found'));
        }

        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->update($input, $id);

        return $this->sendResponse($jvDetailsReferredback->toArray(), trans('custom.jvdetailsreferredback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/jvDetailsReferredbacks/{id}",
     *      summary="Remove the specified JvDetailsReferredback from storage",
     *      tags={"JvDetailsReferredback"},
     *      description="Delete JvDetailsReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of JvDetailsReferredback",
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
        /** @var JvDetailsReferredback $jvDetailsReferredback */
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            return $this->sendError(trans('custom.jv_details_referredback_not_found'));
        }

        $jvDetailsReferredback->delete();

        return $this->sendResponse($id, trans('custom.jv_details_referredback_deleted_successfully'));
    }


    public function getJVDetailAmendHistory(Request $request)
    {
        $input = $request->all();
        $jvMasterAutoId = $input['jvMasterAutoId'];
        $timesReferred = $input['timesReferred'];

        $items = JvDetailsReferredback::where('jvMasterAutoId', $jvMasterAutoId)
            ->where('timesReferred', $timesReferred)
            ->with(['segment', 'chartofaccount'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.journal_voucher_detail_history_retrieved_successfu'));
    }

}
