<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDebitNoteDetailsAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteDetailsAPIRequest;
use App\Models\DebitNoteDetails;
use App\Repositories\DebitNoteDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DebitNoteDetailsController
 * @package App\Http\Controllers\API
 */

class DebitNoteDetailsAPIController extends AppBaseController
{
    /** @var  DebitNoteDetailsRepository */
    private $debitNoteDetailsRepository;

    public function __construct(DebitNoteDetailsRepository $debitNoteDetailsRepo)
    {
        $this->debitNoteDetailsRepository = $debitNoteDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteDetails",
     *      summary="Get a listing of the DebitNoteDetails.",
     *      tags={"DebitNoteDetails"},
     *      description="Get all DebitNoteDetails",
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
     *                  @SWG\Items(ref="#/definitions/DebitNoteDetails")
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
        $this->debitNoteDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNoteDetails = $this->debitNoteDetailsRepository->all();

        return $this->sendResponse($debitNoteDetails->toArray(), 'Debit Note Details retrieved successfully');
    }

    /**
     * @param CreateDebitNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNoteDetails",
     *      summary="Store a newly created DebitNoteDetails in storage",
     *      tags={"DebitNoteDetails"},
     *      description="Store DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteDetails")
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
     *                  ref="#/definitions/DebitNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteDetailsAPIRequest $request)
    {
        $input = $request->all();

        $debitNoteDetails = $this->debitNoteDetailsRepository->create($input);

        return $this->sendResponse($debitNoteDetails->toArray(), 'Debit Note Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNoteDetails/{id}",
     *      summary="Display the specified DebitNoteDetails",
     *      tags={"DebitNoteDetails"},
     *      description="Get DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetails",
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
     *                  ref="#/definitions/DebitNoteDetails"
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
        /** @var DebitNoteDetails $debitNoteDetails */
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            return $this->sendError('Debit Note Details not found');
        }

        return $this->sendResponse($debitNoteDetails->toArray(), 'Debit Note Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNoteDetails/{id}",
     *      summary="Update the specified DebitNoteDetails in storage",
     *      tags={"DebitNoteDetails"},
     *      description="Update DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNoteDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNoteDetails")
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
     *                  ref="#/definitions/DebitNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DebitNoteDetails $debitNoteDetails */
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            return $this->sendError('Debit Note Details not found');
        }

        $debitNoteDetails = $this->debitNoteDetailsRepository->update($input, $id);

        return $this->sendResponse($debitNoteDetails->toArray(), 'DebitNoteDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNoteDetails/{id}",
     *      summary="Remove the specified DebitNoteDetails from storage",
     *      tags={"DebitNoteDetails"},
     *      description="Delete DebitNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNoteDetails",
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
        /** @var DebitNoteDetails $debitNoteDetails */
        $debitNoteDetails = $this->debitNoteDetailsRepository->findWithoutFail($id);

        if (empty($debitNoteDetails)) {
            return $this->sendError('Debit Note Details not found');
        }

        $debitNoteDetails->delete();

        return $this->sendResponse($id, 'Debit Note Details deleted successfully');
    }
}
