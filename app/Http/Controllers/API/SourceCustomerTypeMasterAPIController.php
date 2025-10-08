<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSourceCustomerTypeMasterAPIRequest;
use App\Http\Requests\API\UpdateSourceCustomerTypeMasterAPIRequest;
use App\Models\SourceCustomerTypeMaster;
use App\Repositories\SourceCustomerTypeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SourceCustomerTypeMasterController
 * @package App\Http\Controllers\API
 */

class SourceCustomerTypeMasterAPIController extends AppBaseController
{
    /** @var  SourceCustomerTypeMasterRepository */
    private $sourceCustomerTypeMasterRepository;

    public function __construct(SourceCustomerTypeMasterRepository $sourceCustomerTypeMasterRepo)
    {
        $this->sourceCustomerTypeMasterRepository = $sourceCustomerTypeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/sourceCustomerTypeMasters",
     *      summary="Get a listing of the SourceCustomerTypeMasters.",
     *      tags={"SourceCustomerTypeMaster"},
     *      description="Get all SourceCustomerTypeMasters",
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
     *                  @SWG\Items(ref="#/definitions/SourceCustomerTypeMaster")
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
        $this->sourceCustomerTypeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->sourceCustomerTypeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sourceCustomerTypeMasters = $this->sourceCustomerTypeMasterRepository->all();

        return $this->sendResponse($sourceCustomerTypeMasters->toArray(), trans('custom.source_customer_type_masters_retrieved_successfull'));
    }

    /**
     * @param CreateSourceCustomerTypeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/sourceCustomerTypeMasters",
     *      summary="Store a newly created SourceCustomerTypeMaster in storage",
     *      tags={"SourceCustomerTypeMaster"},
     *      description="Store SourceCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SourceCustomerTypeMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SourceCustomerTypeMaster")
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
     *                  ref="#/definitions/SourceCustomerTypeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSourceCustomerTypeMasterAPIRequest $request)
    {
        $input = $request->all();

        $sourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepository->create($input);

        return $this->sendResponse($sourceCustomerTypeMaster->toArray(), trans('custom.source_customer_type_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/sourceCustomerTypeMasters/{id}",
     *      summary="Display the specified SourceCustomerTypeMaster",
     *      tags={"SourceCustomerTypeMaster"},
     *      description="Get SourceCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SourceCustomerTypeMaster",
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
     *                  ref="#/definitions/SourceCustomerTypeMaster"
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
        /** @var SourceCustomerTypeMaster $sourceCustomerTypeMaster */
        $sourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepository->findWithoutFail($id);

        if (empty($sourceCustomerTypeMaster)) {
            return $this->sendError(trans('custom.source_customer_type_master_not_found'));
        }

        return $this->sendResponse($sourceCustomerTypeMaster->toArray(), trans('custom.source_customer_type_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSourceCustomerTypeMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/sourceCustomerTypeMasters/{id}",
     *      summary="Update the specified SourceCustomerTypeMaster in storage",
     *      tags={"SourceCustomerTypeMaster"},
     *      description="Update SourceCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SourceCustomerTypeMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SourceCustomerTypeMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SourceCustomerTypeMaster")
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
     *                  ref="#/definitions/SourceCustomerTypeMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSourceCustomerTypeMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var SourceCustomerTypeMaster $sourceCustomerTypeMaster */
        $sourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepository->findWithoutFail($id);

        if (empty($sourceCustomerTypeMaster)) {
            return $this->sendError(trans('custom.source_customer_type_master_not_found'));
        }

        $sourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepository->update($input, $id);

        return $this->sendResponse($sourceCustomerTypeMaster->toArray(), trans('custom.sourcecustomertypemaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/sourceCustomerTypeMasters/{id}",
     *      summary="Remove the specified SourceCustomerTypeMaster from storage",
     *      tags={"SourceCustomerTypeMaster"},
     *      description="Delete SourceCustomerTypeMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SourceCustomerTypeMaster",
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
        /** @var SourceCustomerTypeMaster $sourceCustomerTypeMaster */
        $sourceCustomerTypeMaster = $this->sourceCustomerTypeMasterRepository->findWithoutFail($id);

        if (empty($sourceCustomerTypeMaster)) {
            return $this->sendError(trans('custom.source_customer_type_master_not_found'));
        }

        $sourceCustomerTypeMaster->delete();

        return $this->sendSuccess('Source Customer Type Master deleted successfully');
    }
}
