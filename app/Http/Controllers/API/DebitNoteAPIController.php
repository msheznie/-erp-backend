<?php
/**
 * =============================================
 * -- File Name : DebitNoteAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DebitNote
 * -- Author : Mohamed Nazir
 * -- Create date : 16 - August 2018
 * -- Description : This file contains the all CRUD for Debit Note
 * -- REVISION HISTORY
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getDebitNoteMasterRecord(),
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDebitNoteAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteAPIRequest;
use App\Models\DebitNote;
use App\Repositories\DebitNoteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DebitNoteController
 * @package App\Http\Controllers\API
 */

class DebitNoteAPIController extends AppBaseController
{
    /** @var  DebitNoteRepository */
    private $debitNoteRepository;

    public function __construct(DebitNoteRepository $debitNoteRepo)
    {
        $this->debitNoteRepository = $debitNoteRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNotes",
     *      summary="Get a listing of the DebitNotes.",
     *      tags={"DebitNote"},
     *      description="Get all DebitNotes",
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
     *                  @SWG\Items(ref="#/definitions/DebitNote")
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
        $this->debitNoteRepository->pushCriteria(new RequestCriteria($request));
        $this->debitNoteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $debitNotes = $this->debitNoteRepository->all();

        return $this->sendResponse($debitNotes->toArray(), 'Debit Notes retrieved successfully');
    }

    /**
     * @param CreateDebitNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/debitNotes",
     *      summary="Store a newly created DebitNote in storage",
     *      tags={"DebitNote"},
     *      description="Store DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNote that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNote")
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
     *                  ref="#/definitions/DebitNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDebitNoteAPIRequest $request)
    {
        $input = $request->all();

        $debitNotes = $this->debitNoteRepository->create($input);

        return $this->sendResponse($debitNotes->toArray(), 'Debit Note saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/debitNotes/{id}",
     *      summary="Display the specified DebitNote",
     *      tags={"DebitNote"},
     *      description="Get DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
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
     *                  ref="#/definitions/DebitNote"
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
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError('Debit Note not found');
        }

        return $this->sendResponse($debitNote->toArray(), 'Debit Note retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDebitNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/debitNotes/{id}",
     *      summary="Update the specified DebitNote in storage",
     *      tags={"DebitNote"},
     *      description="Update DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DebitNote that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DebitNote")
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
     *                  ref="#/definitions/DebitNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDebitNoteAPIRequest $request)
    {
        $input = $request->all();

        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError('Debit Note not found');
        }

        $debitNote = $this->debitNoteRepository->update($input, $id);

        return $this->sendResponse($debitNote->toArray(), 'DebitNote updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/debitNotes/{id}",
     *      summary="Remove the specified DebitNote from storage",
     *      tags={"DebitNote"},
     *      description="Delete DebitNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DebitNote",
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
        /** @var DebitNote $debitNote */
        $debitNote = $this->debitNoteRepository->findWithoutFail($id);

        if (empty($debitNote)) {
            return $this->sendError('Debit Note not found');
        }

        $debitNote->delete();

        return $this->sendResponse($id, 'Debit Note deleted successfully');
    }


    public function getDebitNoteMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = DebitNote::where('debitNoteAutoID', $input['debitNoteAutoID'])->with(['detail' => function ($query) {
            $query->with('segment');
        },'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 15);
        }, 'company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier','confirmed_by'])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');
    }
}
