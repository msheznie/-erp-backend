<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePdcLogAPIRequest;
use App\Http\Requests\API\UpdatePdcLogAPIRequest;
use App\Models\PdcLog;
use App\helper\Helper;
use App\Jobs\PdcDoubleEntry;
use App\Models\ChequeRegisterDetail;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PdcLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class PdcLogController
 * @package App\Http\Controllers\API
 */

class PdcLogAPIController extends AppBaseController
{
    /** @var  PdcLogRepository */
    private $pdcLogRepository;

    public function __construct(PdcLogRepository $pdcLogRepo)
    {
        $this->pdcLogRepository = $pdcLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pdcLogs",
     *      summary="Get a listing of the PdcLogs.",
     *      tags={"PdcLog"},
     *      description="Get all PdcLogs",
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
     *                  @SWG\Items(ref="#/definitions/PdcLog")
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
        $this->pdcLogRepository->pushCriteria(new RequestCriteria($request));
        $this->pdcLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pdcLogs = $this->pdcLogRepository->with('currency')->all();

        return $this->sendResponse($pdcLogs->toArray(), 'Pdc Logs retrieved successfully');
    }

    /**
     * @param CreatePdcLogAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pdcLogs",
     *      summary="Store a newly created PdcLog in storage",
     *      tags={"PdcLog"},
     *      description="Store PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PdcLog that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PdcLog")
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
     *                  ref="#/definitions/PdcLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePdcLogAPIRequest $request)
    {
        $input = $request->all();

        $pdcLog = $this->pdcLogRepository->create($input);

        return $this->sendResponse($pdcLog->toArray(), 'Pdc Log saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pdcLogs/{id}",
     *      summary="Display the specified PdcLog",
     *      tags={"PdcLog"},
     *      description="Get PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PdcLog",
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
     *                  ref="#/definitions/PdcLog"
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
        /** @var PdcLog $pdcLog */
        $pdcLog = $this->pdcLogRepository->findWithoutFail($id);

        if (empty($pdcLog)) {
            return $this->sendError('Pdc Log not found');
        }

        return $this->sendResponse($pdcLog->toArray(), 'Pdc Log retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePdcLogAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pdcLogs/{id}",
     *      summary="Update the specified PdcLog in storage",
     *      tags={"PdcLog"},
     *      description="Update PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PdcLog",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PdcLog that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PdcLog")
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
     *                  ref="#/definitions/PdcLog"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePdcLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var PdcLog $pdcLog */
        $pdcLog = $this->pdcLogRepository->findWithoutFail($id);

        if (empty($pdcLog)) {
            return $this->sendError('Pdc Log not found');
        }

        if (isset($input['chequeDate'])) {
            $input['chequeDate'] = Carbon::parse($input['chequeDate']);
        }

        $pdcLog = $this->pdcLogRepository->update($input, $id);

        return $this->sendResponse($pdcLog->toArray(), 'PdcLog updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pdcLogs/{id}",
     *      summary="Remove the specified PdcLog from storage",
     *      tags={"PdcLog"},
     *      description="Delete PdcLog",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PdcLog",
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
        /** @var PdcLog $pdcLog */
        $pdcLog = $this->pdcLogRepository->findWithoutFail($id);

        if (empty($pdcLog)) {
            return $this->sendError('Pdc Log not found');
        }

        if (!is_null($pdcLog->chequeRegisterAutoID)) {
            $update_array = [
                'document_id' => null,
                'document_master_id' => null,
                'status' => 0,
            ];

            ChequeRegisterDetail::where('id', $pdcLog->chequeRegisterAutoID)->update($update_array);
        }

        $pdcLog->delete();

        return $this->sendResponse([], 'Pdc Log deleted successfully');
    }

    public function getPdcCheques(Request $request)
    {
        $input = $request->all();

        $cheques = PdcLog::where('documentSystemID', $input['documentSystemID'])
                         ->where('documentmasterAutoID', $input['documentAutoID'])
                         ->get();

        return $this->sendResponse($cheques, 'Pdc cheques retrieved successfully');
    }

    public function deleteAllPDC(Request $request)
    {
        $input = $request->all();

        $cheques = PdcLog::where('documentSystemID', $input['documentSystemID'])
                         ->where('documentmasterAutoID', $input['documentAutoID'])
                         ->get();

        if (count($cheques) == 0) {
             return $this->sendError('Pdc cheques not found', 500);
        }

        $chequeRegisterAutoIDs = collect($cheques)->pluck('chequeRegisterAutoID')->toArray();


        if (count($chequeRegisterAutoIDs) > 0) {
            $update_array = [
                'document_id' => null,
                'document_master_id' => null,
                'status' => 0,
            ];

            ChequeRegisterDetail::whereIn('id', $chequeRegisterAutoIDs)->update($update_array);
        }

        $chequesDelete = PdcLog::where('documentSystemID', $input['documentSystemID'])
                         ->where('documentmasterAutoID', $input['documentAutoID'])
                         ->delete();

        return $this->sendResponse([], 'Pdc cheques deleted successfully');
    }

    public function getIssuedCheques(Request $request) {


        $input = $request;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $issuedCheques = PdcLog::where('documentSystemID',4)
                                ->whereHas('pay_supplier', function ($query) {
                                    $query->where('approved', -1);
                                })
                                ->when(!empty($input['fromDate']) && !empty($input['toDate']), function ($q) use ($input) {
                                    $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
                                    $toDate = Carbon::parse(trim($input['toDate'],'"'));
                                    return $q->whereBetween('chequeDate', [$fromDate,$toDate]);
                                })
                                ->when(!empty($input['bank']), function ($q) use ($input) {
                                    return $q->where('paymentBankID', $input['bank']);
                                })
                                ->with(['currency','bank']);

        return \DataTables::eloquent($issuedCheques)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getAllReceivedCheques(Request $request) {
        $input = $request->all();

        
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $receivedCheques = PdcLog::where('documentSystemID',21)
                                ->whereHas('customer_receive', function ($query){
                                    $query->where('approved', -1);
                                })
                                ->when(!empty($input['fromDate']) && !empty($input['toDate']), function ($q) use ($input) {
                                    $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
                                    $toDate = Carbon::parse(trim($input['toDate'],'"'));
                                    return $q->whereBetween('chequeDate', [$fromDate,$toDate]);
                                })
                                ->when(!empty($input['bank']), function ($q) use ($input) {
                                    return $q->where('paymentBankID', $input['bank']);
                                })
                                ->with(['currency','bank']);


        return \DataTables::eloquent($receivedCheques)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getAllBanks(Request $request) {
        $banks =  PdcLog::all()->pluck('bank')->unique();

        return $this->sendResponse($banks->toArray(), 'Banks received successfully');
    }

    public function getFormData(Request $request) {
        $banks =  PdcLog::all()->pluck('bank')->unique();
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $data = [
            'banks' => $banks,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus
        ];

        return $this->sendResponse($data, 'FromData received successfully');

    }

    public function changePdcChequeStatus(Request $request)
    {
        $input = $request->all();

        if (!isset($input['documentSystemID']) || (isset($input['documentSystemID']) && is_null($input['documentSystemID']))) {
            return $this->sendError("Document ID not found", 500);
        }

        DB::beginTransaction();
        try {

            $empInfo = Helper::getEmployeeInfo();

            $masterData = ['documentSystemID' => $input['documentSystemID'], 'autoID' => $input['documentmasterAutoID'], 'companySystemID' => $input['companySystemID'], 'employeeSystemID' => $empInfo->employeeSystemID];

            if ($input['newStatus'] == 1 || $input['newStatus'] == 2) {
                $jobGL = PdcDoubleEntry::dispatch($masterData, $input);
            }

            $updateChequeStatus = PdcLog::where('id', $input['id'])->update(['chequeStatus' => $input['newStatus']]);

            DB::commit();
            return $this->sendResponse([], "Cheque status changed successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError("Error occured", 500);            
        }
    }
}
