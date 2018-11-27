<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteReferredbackAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteReferredbackAPIRequest;
use App\Models\CreditNoteReferredback;
use App\Repositories\CreditNoteReferredbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CreditNoteReferredbackController
 * @package App\Http\Controllers\API
 */

class CreditNoteReferredbackAPIController extends AppBaseController
{
    /** @var  CreditNoteReferredbackRepository */
    private $creditNoteReferredbackRepository;

    public function __construct(CreditNoteReferredbackRepository $creditNoteReferredbackRepo)
    {
        $this->creditNoteReferredbackRepository = $creditNoteReferredbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteReferredbacks",
     *      summary="Get a listing of the CreditNoteReferredbacks.",
     *      tags={"CreditNoteReferredback"},
     *      description="Get all CreditNoteReferredbacks",
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
     *                  @SWG\Items(ref="#/definitions/CreditNoteReferredback")
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
        $this->creditNoteReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteReferredbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNoteReferredbacks = $this->creditNoteReferredbackRepository->all();

        return $this->sendResponse($creditNoteReferredbacks->toArray(), 'Credit Note Referredbacks retrieved successfully');
    }

    /**
     * @param CreateCreditNoteReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNoteReferredbacks",
     *      summary="Store a newly created CreditNoteReferredback in storage",
     *      tags={"CreditNoteReferredback"},
     *      description="Store CreditNoteReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteReferredback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteReferredback")
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
     *                  ref="#/definitions/CreditNoteReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteReferredbackAPIRequest $request)
    {
        $input = $request->all();

        $creditNoteReferredbacks = $this->creditNoteReferredbackRepository->create($input);

        return $this->sendResponse($creditNoteReferredbacks->toArray(), 'Credit Note Referredback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteReferredbacks/{id}",
     *      summary="Display the specified CreditNoteReferredback",
     *      tags={"CreditNoteReferredback"},
     *      description="Get CreditNoteReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteReferredback",
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
     *                  ref="#/definitions/CreditNoteReferredback"
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
        /** @var CreditNoteReferredback $creditNoteReferredback */
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            return $this->sendError('Credit Note Referredback not found');
        }

        return $this->sendResponse($creditNoteReferredback->toArray(), 'Credit Note Referredback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteReferredbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNoteReferredbacks/{id}",
     *      summary="Update the specified CreditNoteReferredback in storage",
     *      tags={"CreditNoteReferredback"},
     *      description="Update CreditNoteReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteReferredback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteReferredback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteReferredback")
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
     *                  ref="#/definitions/CreditNoteReferredback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteReferredbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var CreditNoteReferredback $creditNoteReferredback */
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            return $this->sendError('Credit Note Referredback not found');
        }

        $creditNoteReferredback = $this->creditNoteReferredbackRepository->update($input, $id);

        return $this->sendResponse($creditNoteReferredback->toArray(), 'CreditNoteReferredback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNoteReferredbacks/{id}",
     *      summary="Remove the specified CreditNoteReferredback from storage",
     *      tags={"CreditNoteReferredback"},
     *      description="Delete CreditNoteReferredback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteReferredback",
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
        /** @var CreditNoteReferredback $creditNoteReferredback */
        $creditNoteReferredback = $this->creditNoteReferredbackRepository->findWithoutFail($id);

        if (empty($creditNoteReferredback)) {
            return $this->sendError('Credit Note Referredback not found');
        }

        $creditNoteReferredback->delete();

        return $this->sendResponse($id, 'Credit Note Referredback deleted successfully');
    }


    public function getCreditNoteAmendHistory(Request $request)
    {
        $input = $request->all();

        $supplierInvoiceHistory = CreditNoteReferredback::where('creditNoteAutoID', $input['creditNoteAutoID'])
            ->with(['created_by','confirmed_by','modified_by','customer','approved_by', 'currency'])
            ->get();

        return $this->sendResponse($supplierInvoiceHistory, 'Invoice detail retrieved successfully');
    }
}
