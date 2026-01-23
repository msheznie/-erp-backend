<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateClientPerformaAppTypeAPIRequest;
use App\Http\Requests\API\UpdateClientPerformaAppTypeAPIRequest;
use App\Models\ClientPerformaAppType;
use App\Repositories\ClientPerformaAppTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ClientPerformaAppTypeController
 * @package App\Http\Controllers\API
 */

class ClientPerformaAppTypeAPIController extends AppBaseController
{
    /** @var  ClientPerformaAppTypeRepository */
    private $clientPerformaAppTypeRepository;

    public function __construct(ClientPerformaAppTypeRepository $clientPerformaAppTypeRepo)
    {
        $this->clientPerformaAppTypeRepository = $clientPerformaAppTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/clientPerformaAppTypes",
     *      summary="Get a listing of the ClientPerformaAppTypes.",
     *      tags={"ClientPerformaAppType"},
     *      description="Get all ClientPerformaAppTypes",
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
     *                  @SWG\Items(ref="#/definitions/ClientPerformaAppType")
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
        $this->clientPerformaAppTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->clientPerformaAppTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $clientPerformaAppTypes = $this->clientPerformaAppTypeRepository->all();

        return $this->sendResponse($clientPerformaAppTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.client_performa_app_types')]));
    }

    /**
     * @param CreateClientPerformaAppTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/clientPerformaAppTypes",
     *      summary="Store a newly created ClientPerformaAppType in storage",
     *      tags={"ClientPerformaAppType"},
     *      description="Store ClientPerformaAppType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ClientPerformaAppType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ClientPerformaAppType")
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
     *                  ref="#/definitions/ClientPerformaAppType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateClientPerformaAppTypeAPIRequest $request)
    {
        $input = $request->all();

        $clientPerformaAppType = $this->clientPerformaAppTypeRepository->create($input);

        return $this->sendResponse($clientPerformaAppType->toArray(), trans('custom.save', ['attribute' => trans('custom.client_performa_app_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/clientPerformaAppTypes/{id}",
     *      summary="Display the specified ClientPerformaAppType",
     *      tags={"ClientPerformaAppType"},
     *      description="Get ClientPerformaAppType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ClientPerformaAppType",
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
     *                  ref="#/definitions/ClientPerformaAppType"
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
        /** @var ClientPerformaAppType $clientPerformaAppType */
        $clientPerformaAppType = $this->clientPerformaAppTypeRepository->findWithoutFail($id);

        if (empty($clientPerformaAppType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.client_performa_app_types')]));
        }

        return $this->sendResponse($clientPerformaAppType->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.client_performa_app_types')]));
    }

    /**
     * @param int $id
     * @param UpdateClientPerformaAppTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/clientPerformaAppTypes/{id}",
     *      summary="Update the specified ClientPerformaAppType in storage",
     *      tags={"ClientPerformaAppType"},
     *      description="Update ClientPerformaAppType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ClientPerformaAppType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ClientPerformaAppType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ClientPerformaAppType")
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
     *                  ref="#/definitions/ClientPerformaAppType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateClientPerformaAppTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ClientPerformaAppType $clientPerformaAppType */
        $clientPerformaAppType = $this->clientPerformaAppTypeRepository->findWithoutFail($id);

        if (empty($clientPerformaAppType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.client_performa_app_types')]));
        }

        $clientPerformaAppType = $this->clientPerformaAppTypeRepository->update($input, $id);

        return $this->sendResponse($clientPerformaAppType->toArray(), trans('custom.update', ['attribute' => trans('custom.client_performa_app_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/clientPerformaAppTypes/{id}",
     *      summary="Remove the specified ClientPerformaAppType from storage",
     *      tags={"ClientPerformaAppType"},
     *      description="Delete ClientPerformaAppType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ClientPerformaAppType",
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
        /** @var ClientPerformaAppType $clientPerformaAppType */
        $clientPerformaAppType = $this->clientPerformaAppTypeRepository->findWithoutFail($id);

        if (empty($clientPerformaAppType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.client_performa_app_types')]));
        }

        $clientPerformaAppType->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.client_performa_app_types')]));
    }
}
