<?php
/**
 * =============================================
 * -- File Name : PoAddonsRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PoAddonsRefferedBack
 * -- Author : Nazir
 * -- Create date : 25 - July 2018
 * -- Description : This file contains the all CRUD for Po Addons Reffered Back APIController
 * -- REVISION HISTORY
 * -- Date: 25-July 2018 By: Nazir Description: Added new function getPoAddonsForAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoAddonsRefferedBackAPIRequest;
use App\Http\Requests\API\UpdatePoAddonsRefferedBackAPIRequest;
use App\Models\PoAddonsRefferedBack;
use App\Repositories\PoAddonsRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PoAddonsRefferedBackController
 * @package App\Http\Controllers\API
 */

class PoAddonsRefferedBackAPIController extends AppBaseController
{
    /** @var  PoAddonsRefferedBackRepository */
    private $poAddonsRefferedBackRepository;

    public function __construct(PoAddonsRefferedBackRepository $poAddonsRefferedBackRepo)
    {
        $this->poAddonsRefferedBackRepository = $poAddonsRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/poAddonsRefferedBacks",
     *      summary="Get a listing of the PoAddonsRefferedBacks.",
     *      tags={"PoAddonsRefferedBack"},
     *      description="Get all PoAddonsRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/PoAddonsRefferedBack")
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
        $this->poAddonsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->poAddonsRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poAddonsRefferedBacks = $this->poAddonsRefferedBackRepository->all();

        return $this->sendResponse($poAddonsRefferedBacks->toArray(), trans('custom.po_addons_reffered_backs_retrieved_successfully'));
    }

    /**
     * @param CreatePoAddonsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/poAddonsRefferedBacks",
     *      summary="Store a newly created PoAddonsRefferedBack in storage",
     *      tags={"PoAddonsRefferedBack"},
     *      description="Store PoAddonsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoAddonsRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoAddonsRefferedBack")
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
     *                  ref="#/definitions/PoAddonsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoAddonsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $poAddonsRefferedBacks = $this->poAddonsRefferedBackRepository->create($input);

        return $this->sendResponse($poAddonsRefferedBacks->toArray(), trans('custom.po_addons_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/poAddonsRefferedBacks/{id}",
     *      summary="Display the specified PoAddonsRefferedBack",
     *      tags={"PoAddonsRefferedBack"},
     *      description="Get PoAddonsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoAddonsRefferedBack",
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
     *                  ref="#/definitions/PoAddonsRefferedBack"
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
        /** @var PoAddonsRefferedBack $poAddonsRefferedBack */
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            return $this->sendError(trans('custom.po_addons_reffered_back_not_found'));
        }

        return $this->sendResponse($poAddonsRefferedBack->toArray(), trans('custom.po_addons_reffered_back_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePoAddonsRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/poAddonsRefferedBacks/{id}",
     *      summary="Update the specified PoAddonsRefferedBack in storage",
     *      tags={"PoAddonsRefferedBack"},
     *      description="Update PoAddonsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoAddonsRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoAddonsRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoAddonsRefferedBack")
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
     *                  ref="#/definitions/PoAddonsRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoAddonsRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoAddonsRefferedBack $poAddonsRefferedBack */
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            return $this->sendError(trans('custom.po_addons_reffered_back_not_found'));
        }

        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->update($input, $id);

        return $this->sendResponse($poAddonsRefferedBack->toArray(), trans('custom.poaddonsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/poAddonsRefferedBacks/{id}",
     *      summary="Remove the specified PoAddonsRefferedBack from storage",
     *      tags={"PoAddonsRefferedBack"},
     *      description="Delete PoAddonsRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoAddonsRefferedBack",
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
        /** @var PoAddonsRefferedBack $poAddonsRefferedBack */
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            return $this->sendError(trans('custom.po_addons_reffered_back_not_found'));
        }

        $poAddonsRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.po_addons_reffered_back_deleted_successfully'));
    }


    public function getPoAddonsForAmendHistory(Request $request)
    {
        $input = $request->all();
        $timesReferred = $input['timesReferred'];

        $orderAddons = PoAddonsRefferedBack::where('poId', $input['purchaseOrderID'])
            ->where('timesReferred', $timesReferred)
            ->get();

        return $this->sendResponse($orderAddons->toArray(), trans('custom.data_retrieved_successfully'));
    }
}
