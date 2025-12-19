<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpPrintTemplateMasterAPIRequest;
use App\Http\Requests\API\UpdateErpPrintTemplateMasterAPIRequest;
use App\Models\ErpPrintTemplateMaster;
use App\Repositories\ErpPrintTemplateMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpPrintTemplateMasterController
 * @package App\Http\Controllers\API
 */

class ErpPrintTemplateMasterAPIController extends AppBaseController
{
    /** @var  ErpPrintTemplateMasterRepository */
    private $erpPrintTemplateMasterRepository;

    public function __construct(ErpPrintTemplateMasterRepository $erpPrintTemplateMasterRepo)
    {
        $this->erpPrintTemplateMasterRepository = $erpPrintTemplateMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpPrintTemplateMasters",
     *      summary="Get a listing of the ErpPrintTemplateMasters.",
     *      tags={"ErpPrintTemplateMaster"},
     *      description="Get all ErpPrintTemplateMasters",
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
     *                  @SWG\Items(ref="#/definitions/ErpPrintTemplateMaster")
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
        $this->erpPrintTemplateMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->erpPrintTemplateMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpPrintTemplateMasters = $this->erpPrintTemplateMasterRepository->all();

        return $this->sendResponse($erpPrintTemplateMasters->toArray(), trans('custom.erp_print_template_masters_retrieved_successfully'));
    }

    /**
     * @param CreateErpPrintTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/erpPrintTemplateMasters",
     *      summary="Store a newly created ErpPrintTemplateMaster in storage",
     *      tags={"ErpPrintTemplateMaster"},
     *      description="Store ErpPrintTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpPrintTemplateMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpPrintTemplateMaster")
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
     *                  ref="#/definitions/ErpPrintTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateErpPrintTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->create($input);

        return $this->sendResponse($erpPrintTemplateMaster->toArray(), trans('custom.erp_print_template_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/erpPrintTemplateMasters/{id}",
     *      summary="Display the specified ErpPrintTemplateMaster",
     *      tags={"ErpPrintTemplateMaster"},
     *      description="Get ErpPrintTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpPrintTemplateMaster",
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
     *                  ref="#/definitions/ErpPrintTemplateMaster"
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
        /** @var ErpPrintTemplateMaster $erpPrintTemplateMaster */
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            return $this->sendError(trans('custom.erp_print_template_master_not_found'));
        }

        return $this->sendResponse($erpPrintTemplateMaster->toArray(), trans('custom.erp_print_template_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateErpPrintTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/erpPrintTemplateMasters/{id}",
     *      summary="Update the specified ErpPrintTemplateMaster in storage",
     *      tags={"ErpPrintTemplateMaster"},
     *      description="Update ErpPrintTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpPrintTemplateMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ErpPrintTemplateMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ErpPrintTemplateMaster")
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
     *                  ref="#/definitions/ErpPrintTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateErpPrintTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpPrintTemplateMaster $erpPrintTemplateMaster */
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            return $this->sendError(trans('custom.erp_print_template_master_not_found'));
        }

        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->update($input, $id);

        return $this->sendResponse($erpPrintTemplateMaster->toArray(), trans('custom.erpprinttemplatemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/erpPrintTemplateMasters/{id}",
     *      summary="Remove the specified ErpPrintTemplateMaster from storage",
     *      tags={"ErpPrintTemplateMaster"},
     *      description="Delete ErpPrintTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ErpPrintTemplateMaster",
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
        /** @var ErpPrintTemplateMaster $erpPrintTemplateMaster */
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            return $this->sendError(trans('custom.erp_print_template_master_not_found'));
        }

        $erpPrintTemplateMaster->delete();

        return $this->sendResponse($id, trans('custom.erp_print_template_master_deleted_successfully'));
    }
}
