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
 * -- Date: 08-August 2018 By: Nazir Description: Added new function getDebitNoteMasterRecord()
 * -- Date: 04-September 2018 By: Nazir Description: Added new function getAllDebitNotes(),getDebitNoteFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDebitNoteAPIRequest;
use App\Http\Requests\API\UpdateDebitNoteAPIRequest;
use App\Models\DebitNote;
use App\Models\Months;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\DebitNoteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
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

    public function getAllDebitNotes(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'isProforma'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $debitNotes = DebitNote::whereIn('companySystemID', $subCompanies)
                                ->with('created_by','transactioncurrency','supplier')
                               ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $debitNotes = $debitNotes->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $debitNotes = $debitNotes->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $debitNotes = $debitNotes->whereMonth('debitNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $debitNotes = $debitNotes->whereYear('debitNoteDate', '=', $input['year']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $debitNotes = $debitNotes->where(function ($query) use ($search) {
                  $query->where('debitNoteCode', 'LIKE', "%{$search}%")
                        ->orWhereHas('supplier', function ($query) use($search) {
                            $query->where('supplierName', 'like', "%{$search}%");
                        });
            });
        }

        return \DataTables::of($debitNotes)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('debitNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getDebitNoteFormData(Request $request)
    {

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = DebitNote::select(DB::raw("YEAR(createdDateAndTime) as year"))
            ->whereNotNull('createdDateAndTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();


        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,

        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
