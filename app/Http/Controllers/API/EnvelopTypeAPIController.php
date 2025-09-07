<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEnvelopTypeAPIRequest;
use App\Http\Requests\API\UpdateEnvelopTypeAPIRequest;
use App\Models\EnvelopType;
use App\Repositories\EnvelopTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EnvelopTypeController
 * @package App\Http\Controllers\API
 */

class EnvelopTypeAPIController extends AppBaseController
{
    /** @var  EnvelopTypeRepository */
    private $envelopTypeRepository;

    public function __construct(EnvelopTypeRepository $envelopTypeRepo)
    {
        $this->envelopTypeRepository = $envelopTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/envelopTypes",
     *      summary="Get a listing of the EnvelopTypes.",
     *      tags={"EnvelopType"},
     *      description="Get all EnvelopTypes",
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
     *                  @SWG\Items(ref="#/definitions/EnvelopType")
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
        $this->envelopTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->envelopTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $envelopTypes = $this->envelopTypeRepository->all();

        return $this->sendResponse($envelopTypes->toArray(), trans('custom.envelop_types_retrieved_successfully'));
    }

    /**
     * @param CreateEnvelopTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/envelopTypes",
     *      summary="Store a newly created EnvelopType in storage",
     *      tags={"EnvelopType"},
     *      description="Store EnvelopType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EnvelopType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EnvelopType")
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
     *                  ref="#/definitions/EnvelopType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateEnvelopTypeAPIRequest $request)
    {
        $input = $request->all();

        $envelopType = $this->envelopTypeRepository->create($input);

        return $this->sendResponse($envelopType->toArray(), trans('custom.envelop_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/envelopTypes/{id}",
     *      summary="Display the specified EnvelopType",
     *      tags={"EnvelopType"},
     *      description="Get EnvelopType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EnvelopType",
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
     *                  ref="#/definitions/EnvelopType"
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
        /** @var EnvelopType $envelopType */
        $envelopType = $this->envelopTypeRepository->findWithoutFail($id);

        if (empty($envelopType)) {
            return $this->sendError(trans('custom.envelop_type_not_found'));
        }

        return $this->sendResponse($envelopType->toArray(), trans('custom.envelop_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateEnvelopTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/envelopTypes/{id}",
     *      summary="Update the specified EnvelopType in storage",
     *      tags={"EnvelopType"},
     *      description="Update EnvelopType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EnvelopType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="EnvelopType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/EnvelopType")
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
     *                  ref="#/definitions/EnvelopType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateEnvelopTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var EnvelopType $envelopType */
        $envelopType = $this->envelopTypeRepository->findWithoutFail($id);

        if (empty($envelopType)) {
            return $this->sendError(trans('custom.envelop_type_not_found'));
        }

        $envelopType = $this->envelopTypeRepository->update($input, $id);

        return $this->sendResponse($envelopType->toArray(), trans('custom.enveloptype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/envelopTypes/{id}",
     *      summary="Remove the specified EnvelopType from storage",
     *      tags={"EnvelopType"},
     *      description="Delete EnvelopType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of EnvelopType",
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
        /** @var EnvelopType $envelopType */
        $envelopType = $this->envelopTypeRepository->findWithoutFail($id);

        if (empty($envelopType)) {
            return $this->sendError(trans('custom.envelop_type_not_found'));
        }

        $envelopType->delete();

        return $this->sendSuccess('Envelop Type deleted successfully');
    }
}
