<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExternalLinkHashAPIRequest;
use App\Http\Requests\API\UpdateExternalLinkHashAPIRequest;
use App\Models\ExternalLinkHash;
use App\Repositories\ExternalLinkHashRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExternalLinkHashController
 * @package App\Http\Controllers\API
 */

class ExternalLinkHashAPIController extends AppBaseController
{
    /** @var  ExternalLinkHashRepository */
    private $externalLinkHashRepository;

    public function __construct(ExternalLinkHashRepository $externalLinkHashRepo)
    {
        $this->externalLinkHashRepository = $externalLinkHashRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/externalLinkHashes",
     *      summary="Get a listing of the ExternalLinkHashes.",
     *      tags={"ExternalLinkHash"},
     *      description="Get all ExternalLinkHashes",
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
     *                  @SWG\Items(ref="#/definitions/ExternalLinkHash")
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
        $this->externalLinkHashRepository->pushCriteria(new RequestCriteria($request));
        $this->externalLinkHashRepository->pushCriteria(new LimitOffsetCriteria($request));
        $externalLinkHashes = $this->externalLinkHashRepository->all();

        return $this->sendResponse($externalLinkHashes->toArray(), trans('custom.external_link_hashes_retrieved_successfully'));
    }

    /**
     * @param CreateExternalLinkHashAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/externalLinkHashes",
     *      summary="Store a newly created ExternalLinkHash in storage",
     *      tags={"ExternalLinkHash"},
     *      description="Store ExternalLinkHash",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExternalLinkHash that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExternalLinkHash")
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
     *                  ref="#/definitions/ExternalLinkHash"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExternalLinkHashAPIRequest $request)
    {
        $input = $request->all();

        $externalLinkHash = $this->externalLinkHashRepository->create($input);

        return $this->sendResponse($externalLinkHash->toArray(), trans('custom.external_link_hash_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/externalLinkHashes/{id}",
     *      summary="Display the specified ExternalLinkHash",
     *      tags={"ExternalLinkHash"},
     *      description="Get ExternalLinkHash",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExternalLinkHash",
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
     *                  ref="#/definitions/ExternalLinkHash"
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
        /** @var ExternalLinkHash $externalLinkHash */
        $externalLinkHash = $this->externalLinkHashRepository->findWithoutFail($id);

        if (empty($externalLinkHash)) {
            return $this->sendError(trans('custom.external_link_hash_not_found'));
        }

        return $this->sendResponse($externalLinkHash->toArray(), trans('custom.external_link_hash_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateExternalLinkHashAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/externalLinkHashes/{id}",
     *      summary="Update the specified ExternalLinkHash in storage",
     *      tags={"ExternalLinkHash"},
     *      description="Update ExternalLinkHash",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExternalLinkHash",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExternalLinkHash that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExternalLinkHash")
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
     *                  ref="#/definitions/ExternalLinkHash"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExternalLinkHashAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExternalLinkHash $externalLinkHash */
        $externalLinkHash = $this->externalLinkHashRepository->findWithoutFail($id);

        if (empty($externalLinkHash)) {
            return $this->sendError(trans('custom.external_link_hash_not_found'));
        }

        $externalLinkHash = $this->externalLinkHashRepository->update($input, $id);

        return $this->sendResponse($externalLinkHash->toArray(), trans('custom.externallinkhash_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/externalLinkHashes/{id}",
     *      summary="Remove the specified ExternalLinkHash from storage",
     *      tags={"ExternalLinkHash"},
     *      description="Delete ExternalLinkHash",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExternalLinkHash",
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
        /** @var ExternalLinkHash $externalLinkHash */
        $externalLinkHash = $this->externalLinkHashRepository->findWithoutFail($id);

        if (empty($externalLinkHash)) {
            return $this->sendError(trans('custom.external_link_hash_not_found'));
        }

        $externalLinkHash->delete();

        return $this->sendSuccess('External Link Hash deleted successfully');
    }
}
