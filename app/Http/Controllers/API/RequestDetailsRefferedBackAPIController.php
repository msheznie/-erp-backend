<?php
/**
 * =============================================
 * -- File Name : RequestDetailsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Request Details Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 06-December 2018
 * -- Description : This file contains the all CRUD for Request Details Reffered Back
 * -- REVISION HISTORY
 * -- Date: 06-December 2018 By: Fayas Description: Added new functions named as getItemRequestDetailsReferBack()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRequestDetailsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateRequestDetailsRefferedBackAPIRequest;
use App\Models\RequestDetailsRefferedBack;
use App\Models\Unit;
use App\Repositories\RequestDetailsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class RequestDetailsRefferedBackController
 * @package App\Http\Controllers\API
 */

class RequestDetailsRefferedBackAPIController extends AppBaseController
{
    /** @var  RequestDetailsRefferedBackRepository */
    private $requestDetailsRefferedBackRepository;

    public function __construct(RequestDetailsRefferedBackRepository $requestDetailsRefferedBackRepo)
    {
        $this->requestDetailsRefferedBackRepository = $requestDetailsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/requestDetailsRefferedBacks",
     *      summary="Get a listing of the RequestDetailsRefferedBacks.",
     *      tags={"RequestDetailsRefferedBack"},
     *      description="Get all RequestDetailsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/RequestDetailsRefferedBack")
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
        $this->requestDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->requestDetailsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $requestDetailsRefferedBacks = $this->requestDetailsRefferedBackRepository->all();

        return $this->sendResponse($requestDetailsRefferedBacks->toArray(), trans('custom.request_details_reffered_backs_retrieved_successfu'));
    }

    /**
     * @param CreateRequestDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/requestDetailsRefferedBacks",
     *      summary="Store a newly created RequestDetailsRefferedBack in storage",
     *      tags={"RequestDetailsRefferedBack"},
     *      description="Store RequestDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RequestDetailsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RequestDetailsRefferedBack")
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
     *                  ref="#/definitions/RequestDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRequestDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $requestDetailsRefferedBacks = $this->requestDetailsRefferedBackRepository->create($input);

        return $this->sendResponse($requestDetailsRefferedBacks->toArray(), trans('custom.request_details_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/requestDetailsRefferedBacks/{id}",
     *      summary="Display the specified RequestDetailsRefferedBack",
     *      tags={"RequestDetailsRefferedBack"},
     *      description="Get RequestDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestDetailsRefferedBack",
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
     *                  ref="#/definitions/RequestDetailsRefferedBack"
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
        /** @var RequestDetailsRefferedBack $requestDetailsRefferedBack */
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            return $this->sendError(trans('custom.request_details_reffered_back_not_found'));
        }

        return $this->sendResponse($requestDetailsRefferedBack->toArray(), trans('custom.request_details_reffered_back_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateRequestDetailsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/requestDetailsRefferedBacks/{id}",
     *      summary="Update the specified RequestDetailsRefferedBack in storage",
     *      tags={"RequestDetailsRefferedBack"},
     *      description="Update RequestDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestDetailsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RequestDetailsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RequestDetailsRefferedBack")
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
     *                  ref="#/definitions/RequestDetailsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRequestDetailsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var RequestDetailsRefferedBack $requestDetailsRefferedBack */
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            return $this->sendError(trans('custom.request_details_reffered_back_not_found'));
        }

        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($requestDetailsRefferedBack->toArray(), trans('custom.requestdetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/requestDetailsRefferedBacks/{id}",
     *      summary="Remove the specified RequestDetailsRefferedBack from storage",
     *      tags={"RequestDetailsRefferedBack"},
     *      description="Delete RequestDetailsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of RequestDetailsRefferedBack",
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
        /** @var RequestDetailsRefferedBack $requestDetailsRefferedBack */
        $requestDetailsRefferedBack = $this->requestDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($requestDetailsRefferedBack)) {
            return $this->sendError(trans('custom.request_details_reffered_back_not_found'));
        }

        $requestDetailsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.request_details_reffered_back_deleted_successfully'));
    }


    public function getItemRequestDetailsReferBack(Request $request)
    {
        $input = $request->all();
        $rId = $input['RequestID'];
        $timesReferred = $input['timesReferred'];
        $items = RequestDetailsRefferedBack::where('RequestID', $rId)
            ->where('timesReferred', $timesReferred)
            ->with(['uom_default','uom_issuing','item_by'])
            ->get();

        foreach ($items as $item){

            $issueUnit = Unit::where('UnitID',$item['unitOfMeasure'])->with(['unitConversion.sub_unit'])->first();

            $issueUnits = array();
            foreach ($issueUnit->unitConversion as $unit){
                $temArray = array('value' => $unit->sub_unit->UnitID, 'label' => $unit->sub_unit->UnitShortCode);
                array_push($issueUnits,$temArray);
            }

            $item->issueUnits = $issueUnits;
        }

        return $this->sendResponse($items->toArray(), trans('custom.request_details_retrieved_successfully'));
    }

}
