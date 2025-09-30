<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDebitNoteDetailsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteDetailsRefferedbackAPIRequest;
use App\Models\DebitNoteDetailsRefferedback;
use App\Repositories\DebitNoteDetailsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DebitNoteDetailsRefferedbackController
 * @package App\Http\Controllers\API
 */

class DebitNoteDetailsRefferedbackAPIController extends AppBaseController
{
    /** @var  DebitNoteDetailsRefferedbackRepository */
    private $debitNoteDetailsRefferedbackRepository;

    public function __construct(DebitNoteDetailsRefferedbackRepository $debitNoteDetailsRefferedbackRepo)
    {
        $this->debitNoteDetailsRefferedbackRepository = $debitNoteDetailsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteDetailsRefferedbacks",
     *      summary="Get a listing of the DebitNoteDetailsRefferedbacks.",
     *      tags={"DebitNoteDetailsRefferedback"},
     *      description="Get all DebitNoteDetailsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/DebitNoteDetailsRefferedback")
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
        $this->debitNoteDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteDetailsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNoteDetailsRefferedbacks = $this->debitNoteDetailsRefferedbackRepository->all();

        return $this->sendResponse($debitNoteDetailsRefferedbacks->toArray(), trans('custom.debit_note_details_refferedbacks_retrieved_success'));
    }

    /**
     * @param CreateDebitNoteDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNoteDetailsRefferedbacks",
     *      summary="Store a newly created DebitNoteDetailsRefferedback in storage",
     *      tags={"DebitNoteDetailsRefferedback"},
     *      description="Store DebitNoteDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteDetailsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteDetailsRefferedback")
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
     *                  ref="#/definitions/DebitNoteDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $debitNoteDetailsRefferedbacks = $this->debitNoteDetailsRefferedbackRepository->create($input);

        return $this->sendResponse($debitNoteDetailsRefferedbacks->toArray(), trans('custom.debit_note_details_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteDetailsRefferedbacks/{id}",
     *      summary="Display the specified DebitNoteDetailsRefferedback",
     *      tags={"DebitNoteDetailsRefferedback"},
     *      description="Get DebitNoteDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetailsRefferedback",
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
     *                  ref="#/definitions/DebitNoteDetailsRefferedback"
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
        /** @var DebitNoteDetailsRefferedback $debitNoteDetailsRefferedback */
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            return $this->sendError(trans('custom.debit_note_details_refferedback_not_found'));
        }

        return $this->sendResponse($debitNoteDetailsRefferedback->toArray(), trans('custom.debit_note_details_refferedback_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNoteDetailsRefferedbacks/{id}",
     *      summary="Update the specified DebitNoteDetailsRefferedback in storage",
     *      tags={"DebitNoteDetailsRefferedback"},
     *      description="Update DebitNoteDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetailsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteDetailsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteDetailsRefferedback")
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
     *                  ref="#/definitions/DebitNoteDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DebitNoteDetailsRefferedback $debitNoteDetailsRefferedback */
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            return $this->sendError(trans('custom.debit_note_details_refferedback_not_found'));
        }

        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($debitNoteDetailsRefferedback->toArray(), trans('custom.debitnotedetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNoteDetailsRefferedbacks/{id}",
     *      summary="Remove the specified DebitNoteDetailsRefferedback from storage",
     *      tags={"DebitNoteDetailsRefferedback"},
     *      description="Delete DebitNoteDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetailsRefferedback",
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
        /** @var DebitNoteDetailsRefferedback $debitNoteDetailsRefferedback */
        $debitNoteDetailsRefferedback = $this->debitNoteDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($debitNoteDetailsRefferedback)) {
            return $this->sendError(trans('custom.debit_note_details_refferedback_not_found'));
        }

        $debitNoteDetailsRefferedback->delete();

        return $this->sendResponse($id, trans('custom.debit_note_details_refferedback_deleted_successful'));
    }


    public function getDNDetailAmendHistory(Request $request)
    {
        $input = $request->all();
        $debitNoteAutoID = $input['debitNoteAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = DebitNoteDetailsRefferedback::where('debitNoteAutoID', $debitNoteAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['segment'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.debit_note_details_history_retrieved_successfully'));
    }
}
