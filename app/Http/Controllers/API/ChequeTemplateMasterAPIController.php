<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChequeTemplateMasterAPIRequest;
use App\Http\Requests\API\UpdateChequeTemplateMasterAPIRequest;
use App\Models\ChequeTemplateMaster;
use App\Repositories\ChequeTemplateMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\ChequeTemplateBank;
use App\Criteria\FilterActiveRecordsCriteria;
use App\Criteria\FilterNonAssignedTemplateCriteria;
/**
 * Class ChequeTemplateMasterController
 * @package App\Http\Controllers\API
 */

class ChequeTemplateMasterAPIController extends AppBaseController
{
    /** @var  ChequeTemplateMasterRepository */
    private $chequeTemplateMasterRepository;

    public function __construct(ChequeTemplateMasterRepository $chequeTemplateMasterRepo)
    {
        $this->chequeTemplateMasterRepository = $chequeTemplateMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeTemplateMasters",
     *      summary="Get a listing of the ChequeTemplateMasters.",
     *      tags={"ChequeTemplateMaster"},
     *      description="Get all ChequeTemplateMasters",
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
     *                  @SWG\Items(ref="#/definitions/ChequeTemplateMaster")
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
        $bank_id = $request->get('bank_id');
        $this->chequeTemplateMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->chequeTemplateMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->chequeTemplateMasterRepository->pushCriteria(new FilterActiveRecordsCriteria($request));
        $chequeTemplateMasters = $this->chequeTemplateMasterRepository->whereDoesntHave('templateBank',function($query)use($bank_id){
            $query->where('bank_id','=',$bank_id);
       })->get();


        return $this->sendResponse($chequeTemplateMasters, trans('custom.retrieve', ['attribute' => trans('custom.templates')]));
    }

    /**
     * @param CreateChequeTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chequeTemplateMasters",
     *      summary="Store a newly created ChequeTemplateMaster in storage",
     *      tags={"ChequeTemplateMaster"},
     * 
     *      description="Store ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeTemplateMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeTemplateMaster")
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
     *                  ref="#/definitions/ChequeTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChequeTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->create($input);

        return $this->sendResponse($chequeTemplateMaster->toArray(), trans('custom.cheque_template_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeTemplateMasters/{id}",
     *      summary="Display the specified ChequeTemplateMaster",
     *      tags={"ChequeTemplateMaster"},
     *      description="Get ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateMaster",
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
     *                  ref="#/definitions/ChequeTemplateMaster"
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
        /** @var ChequeTemplateMaster $chequeTemplateMaster */
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            return $this->sendError(trans('custom.cheque_template_master_not_found'));
        }

        return $this->sendResponse($chequeTemplateMaster->toArray(), trans('custom.cheque_template_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateChequeTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chequeTemplateMasters/{id}",
     *      summary="Update the specified ChequeTemplateMaster in storage",
     *      tags={"ChequeTemplateMaster"},
     *      description="Update ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeTemplateMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeTemplateMaster")
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
     *                  ref="#/definitions/ChequeTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChequeTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChequeTemplateMaster $chequeTemplateMaster */
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            return $this->sendError(trans('custom.cheque_template_master_not_found'));
        }

        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->update($input, $id);

        return $this->sendResponse($chequeTemplateMaster->toArray(), trans('custom.chequetemplatemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chequeTemplateMasters/{id}",
     *      summary="Remove the specified ChequeTemplateMaster from storage",
     *      tags={"ChequeTemplateMaster"},
     *      description="Delete ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateMaster",
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
        /** @var ChequeTemplateMaster $chequeTemplateMaster */
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            return $this->sendError(trans('custom.cheque_template_master_not_found'));
        }

        $chequeTemplateMaster->delete();

        return $this->sendSuccess('Cheque Template Master deleted successfully');
    }
}
