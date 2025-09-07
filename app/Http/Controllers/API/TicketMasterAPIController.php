<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTicketMasterAPIRequest;
use App\Http\Requests\API\UpdateTicketMasterAPIRequest;
use App\Models\TicketMaster;
use App\Repositories\TicketMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TicketMasterController
 * @package App\Http\Controllers\API
 */

class TicketMasterAPIController extends AppBaseController
{
    /** @var  TicketMasterRepository */
    private $ticketMasterRepository;

    public function __construct(TicketMasterRepository $ticketMasterRepo)
    {
        $this->ticketMasterRepository = $ticketMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketMasters",
     *      summary="Get a listing of the TicketMasters.",
     *      tags={"TicketMaster"},
     *      description="Get all TicketMasters",
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
     *                  @SWG\Items(ref="#/definitions/TicketMaster")
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
        $this->ticketMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->ticketMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $ticketMasters = $this->ticketMasterRepository->all();

        return $this->sendResponse($ticketMasters->toArray(), trans('custom.ticket_masters_retrieved_successfully'));
    }

    /**
     * @param CreateTicketMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/ticketMasters",
     *      summary="Store a newly created TicketMaster in storage",
     *      tags={"TicketMaster"},
     *      description="Store TicketMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketMaster")
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
     *                  ref="#/definitions/TicketMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTicketMasterAPIRequest $request)
    {
        $input = $request->all();

        $ticketMasters = $this->ticketMasterRepository->create($input);

        return $this->sendResponse($ticketMasters->toArray(), trans('custom.ticket_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/ticketMasters/{id}",
     *      summary="Display the specified TicketMaster",
     *      tags={"TicketMaster"},
     *      description="Get TicketMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketMaster",
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
     *                  ref="#/definitions/TicketMaster"
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
        /** @var TicketMaster $ticketMaster */
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            return $this->sendError(trans('custom.ticket_master_not_found'));
        }

        return $this->sendResponse($ticketMaster->toArray(), trans('custom.ticket_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTicketMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/ticketMasters/{id}",
     *      summary="Update the specified TicketMaster in storage",
     *      tags={"TicketMaster"},
     *      description="Update TicketMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TicketMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TicketMaster")
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
     *                  ref="#/definitions/TicketMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTicketMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TicketMaster $ticketMaster */
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            return $this->sendError(trans('custom.ticket_master_not_found'));
        }

        $ticketMaster = $this->ticketMasterRepository->update($input, $id);

        return $this->sendResponse($ticketMaster->toArray(), trans('custom.ticketmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/ticketMasters/{id}",
     *      summary="Remove the specified TicketMaster from storage",
     *      tags={"TicketMaster"},
     *      description="Delete TicketMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TicketMaster",
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
        /** @var TicketMaster $ticketMaster */
        $ticketMaster = $this->ticketMasterRepository->findWithoutFail($id);

        if (empty($ticketMaster)) {
            return $this->sendError(trans('custom.ticket_master_not_found'));
        }

        $ticketMaster->delete();

        return $this->sendResponse($id, trans('custom.ticket_master_deleted_successfully'));
    }
}
