<?php
/**
 * =============================================
 * -- File Name : CreditNoteDetailsRefferdbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Credit Note Details Refferdback
 * -- Author : Mohamed Nazir
 * -- Create date : 26 - November 2018
 * -- Description : This file contains the all CRUD for Credit Note Details Refferdback
 * -- REVISION HISTORY
 * -- Date: 26-November 2018 By: Nazir Description: Added new function getCNDetailAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteDetailsRefferdbackAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteDetailsRefferdbackAPIRequest;
use App\Models\CreditNoteDetailsRefferdback;
use App\Repositories\CreditNoteDetailsRefferdbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CreditNoteDetailsRefferdbackController
 * @package App\Http\Controllers\API
 */
class CreditNoteDetailsRefferdbackAPIController extends AppBaseController
{
    /** @var  CreditNoteDetailsRefferdbackRepository */
    private $creditNoteDetailsRefferdbackRepository;

    public function __construct(CreditNoteDetailsRefferdbackRepository $creditNoteDetailsRefferdbackRepo)
    {
        $this->creditNoteDetailsRefferdbackRepository = $creditNoteDetailsRefferdbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteDetailsRefferdbacks",
     *      summary="Get a listing of the CreditNoteDetailsRefferdbacks.",
     *      tags={"CreditNoteDetailsRefferdback"},
     *      description="Get all CreditNoteDetailsRefferdbacks",
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
     *                  @SWG\Items(ref="#/definitions/CreditNoteDetailsRefferdback")
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
        $this->creditNoteDetailsRefferdbackRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteDetailsRefferdbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNoteDetailsRefferdbacks = $this->creditNoteDetailsRefferdbackRepository->all();

        return $this->sendResponse($creditNoteDetailsRefferdbacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
    }

    /**
     * @param CreateCreditNoteDetailsRefferdbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNoteDetailsRefferdbacks",
     *      summary="Store a newly created CreditNoteDetailsRefferdback in storage",
     *      tags={"CreditNoteDetailsRefferdback"},
     *      description="Store CreditNoteDetailsRefferdback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteDetailsRefferdback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteDetailsRefferdback")
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
     *                  ref="#/definitions/CreditNoteDetailsRefferdback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteDetailsRefferdbackAPIRequest $request)
    {
        $input = $request->all();

        $creditNoteDetailsRefferdbacks = $this->creditNoteDetailsRefferdbackRepository->create($input);

        return $this->sendResponse($creditNoteDetailsRefferdbacks->toArray(), trans('custom.save', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNoteDetailsRefferdbacks/{id}",
     *      summary="Display the specified CreditNoteDetailsRefferdback",
     *      tags={"CreditNoteDetailsRefferdback"},
     *      description="Get CreditNoteDetailsRefferdback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetailsRefferdback",
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
     *                  ref="#/definitions/CreditNoteDetailsRefferdback"
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
        /** @var CreditNoteDetailsRefferdback $creditNoteDetailsRefferdback */
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
        }

        return $this->sendResponse($creditNoteDetailsRefferdback->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteDetailsRefferdbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNoteDetailsRefferdbacks/{id}",
     *      summary="Update the specified CreditNoteDetailsRefferdback in storage",
     *      tags={"CreditNoteDetailsRefferdback"},
     *      description="Update CreditNoteDetailsRefferdback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetailsRefferdback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNoteDetailsRefferdback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNoteDetailsRefferdback")
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
     *                  ref="#/definitions/CreditNoteDetailsRefferdback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteDetailsRefferdbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var CreditNoteDetailsRefferdback $creditNoteDetailsRefferdback */
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
        }

        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->update($input, $id);

        return $this->sendResponse($creditNoteDetailsRefferdback->toArray(), trans('custom.update', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNoteDetailsRefferdbacks/{id}",
     *      summary="Remove the specified CreditNoteDetailsRefferdback from storage",
     *      tags={"CreditNoteDetailsRefferdback"},
     *      description="Delete CreditNoteDetailsRefferdback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNoteDetailsRefferdback",
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
        /** @var CreditNoteDetailsRefferdback $creditNoteDetailsRefferdback */
        $creditNoteDetailsRefferdback = $this->creditNoteDetailsRefferdbackRepository->findWithoutFail($id);

        if (empty($creditNoteDetailsRefferdback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
        }

        $creditNoteDetailsRefferdback->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.credit_note_details_refferdbacks')]));
    }

    public function getCNDetailAmendHistory(Request $request)
    {
        $input = $request->all();
        $creditNoteAutoID = $input['creditNoteAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = CreditNoteDetailsRefferdback::where('creditNoteAutoID', $creditNoteAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['segment'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.credit_note_details_history')]));
    }
}
