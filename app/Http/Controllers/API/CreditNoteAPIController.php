<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteAPIRequest;
use App\Models\CreditNote;
use App\Repositories\CreditNoteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CreditNoteController
 * @package App\Http\Controllers\API
 */
class CreditNoteAPIController extends AppBaseController
{
    /** @var  CreditNoteRepository */
    private $creditNoteRepository;

    public function __construct(CreditNoteRepository $creditNoteRepo)
    {
        $this->creditNoteRepository = $creditNoteRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNotes",
     *      summary="Get a listing of the CreditNotes.",
     *      tags={"CreditNote"},
     *      description="Get all CreditNotes",
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
     *                  @SWG\Items(ref="#/definitions/CreditNote")
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
        $this->creditNoteRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNotes = $this->creditNoteRepository->all();

        return $this->sendResponse($creditNotes->toArray(), 'Credit Notes retrieved successfully');
    }

    /**
     * @param CreateCreditNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNotes",
     *      summary="Store a newly created CreditNote in storage",
     *      tags={"CreditNote"},
     *      description="Store CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNote that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNote")
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
     *                  ref="#/definitions/CreditNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteAPIRequest $request)
    {
        $input = $request->all();

        $creditNotes = $this->creditNoteRepository->create($input);

        return $this->sendResponse($creditNotes->toArray(), 'Credit Note saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNotes/{id}",
     *      summary="Display the specified CreditNote",
     *      tags={"CreditNote"},
     *      description="Get CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
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
     *                  ref="#/definitions/CreditNote"
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
        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError('Credit Note not found');
        }

        return $this->sendResponse($creditNote->toArray(), 'Credit Note retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNotes/{id}",
     *      summary="Update the specified CreditNote in storage",
     *      tags={"CreditNote"},
     *      description="Update CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNote that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNote")
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
     *                  ref="#/definitions/CreditNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteAPIRequest $request)
    {
        $input = $request->all();

        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError('Credit Note not found');
        }

        $creditNote = $this->creditNoteRepository->update($input, $id);

        return $this->sendResponse($creditNote->toArray(), 'CreditNote updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNotes/{id}",
     *      summary="Remove the specified CreditNote from storage",
     *      tags={"CreditNote"},
     *      description="Delete CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
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
        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError('Credit Note not found');
        }

        $creditNote->delete();

        return $this->sendResponse($id, 'Credit Note deleted successfully');
    }

    public function getCreditNoteMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = CreditNote::where('creditNoteAutoID', $input['creditNoteAutoID'])->with(['details'=> function ($query) {
            $query->with('segment');
        },'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 19);
        },'company','currency','customer','confirmed_by','createduser'])->first();



        return $this->sendResponse($output, 'Data retrieved successfully');

    }
}
