<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteDetailsAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteDetailsAPIRequest;
use App\Models\CreditNoteDetails;
use App\Repositories\CreditNoteDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CreditNoteDetailsController
 * @package App\Http\Controllers\API
 */

class CreditNoteDetailsAPIController extends AppBaseController
{
    /** @var  CreditNoteDetailsRepository */
    private $creditNoteDetailsRepository;

    public function __construct(CreditNoteDetailsRepository $creditNoteDetailsRepo)
    {
        $this->creditNoteDetailsRepository = $creditNoteDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteDetails",
     *      summary="Get a listing of the CreditNoteDetails.",
     *      tags={"CreditNoteDetails"},
     *      description="Get all CreditNoteDetails",
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
     *                  @SWG\Items(ref="#/definitions/CreditNoteDetails")
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
        $this->creditNoteDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNoteDetails = $this->creditNoteDetailsRepository->all();

        return $this->sendResponse($creditNoteDetails->toArray(), 'Credit Note Details retrieved successfully');
    }

    /**
     * @param CreateCreditNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNoteDetails",
     *      summary="Store a newly created CreditNoteDetails in storage",
     *      tags={"CreditNoteDetails"},
     *      description="Store CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteDetails")
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
     *                  ref="#/definitions/CreditNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteDetailsAPIRequest $request)
    {
        $input = $request->all();

        $creditNoteDetails = $this->creditNoteDetailsRepository->create($input);

        return $this->sendResponse($creditNoteDetails->toArray(), 'Credit Note Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteDetails/{id}",
     *      summary="Display the specified CreditNoteDetails",
     *      tags={"CreditNoteDetails"},
     *      description="Get CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetails",
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
     *                  ref="#/definitions/CreditNoteDetails"
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
        /** @var CreditNoteDetails $creditNoteDetails */
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            return $this->sendError('Credit Note Details not found');
        }

        return $this->sendResponse($creditNoteDetails->toArray(), 'Credit Note Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNoteDetails/{id}",
     *      summary="Update the specified CreditNoteDetails in storage",
     *      tags={"CreditNoteDetails"},
     *      description="Update CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteDetails")
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
     *                  ref="#/definitions/CreditNoteDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CreditNoteDetails $creditNoteDetails */
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            return $this->sendError('Credit Note Details not found');
        }

        $creditNoteDetails = $this->creditNoteDetailsRepository->update($input, $id);

        return $this->sendResponse($creditNoteDetails->toArray(), 'CreditNoteDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNoteDetails/{id}",
     *      summary="Remove the specified CreditNoteDetails from storage",
     *      tags={"CreditNoteDetails"},
     *      description="Delete CreditNoteDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetails",
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
        /** @var CreditNoteDetails $creditNoteDetails */
        $creditNoteDetails = $this->creditNoteDetailsRepository->findWithoutFail($id);

        if (empty($creditNoteDetails)) {
            return $this->sendError('Credit Note Details not found');
        }

        $creditNoteDetails->delete();

        return $this->sendResponse($id, 'Credit Note Details deleted successfully');
    }
}
