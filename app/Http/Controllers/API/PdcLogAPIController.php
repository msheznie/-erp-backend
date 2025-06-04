<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePdcLogAPIRequest;
use App\Http\Requests\API\UpdatePdcLogAPIRequest;
use App\Models\PdcLog;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\ChequeTemplateBank;
use App\Models\BankMaster;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\helper\Helper;
use App\Jobs\PdcDoubleEntry;
use App\Models\ChequeRegisterDetail;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PdcLogRepository;
use App\Services\PaymentVoucherServices;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\PaySupplierInvoiceMasterRepository;

/**
 * Class PdcLogController
 * @package App\Http\Controllers\API
 */

class PdcLogAPIController extends AppBaseController
{
    /** @var  PdcLogRepository */
    private $pdcLogRepository;
    private $paySupplierInvoiceMasterRepository;

    public function __construct(PdcLogRepository $pdcLogRepo, PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo)
    {
        $this->pdcLogRepository = $pdcLogRepo;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
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

        $data = PaymentVoucherServices::updatePDCCheque($id,$input);
        if ($data['status']) {
            return $this->sendResponse($data['data'], $data['message']);
        }
        else {
            return $this->sendError(
                $data['message'],
                $data['code'] ?? 404
            );
        }
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
        $companyId = (isset($input['companyId'])) ? $input['companyId'] : '';

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankmasterAutoID = $request['bank'];
        $bankmasterAutoID = (array)$bankmasterAutoID;
        $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

        $issuedCheques = PdcLog::where('documentSystemID',4)
                                ->whereHas('pay_supplier', function ($query) {
                                    $query->where('approved', -1);
                                })
                                ->when(!empty($input['fromDate']) && !empty($input['toDate']), function ($q) use ($input) {
                                    $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
                                    $toDate = Carbon::parse(trim($input['toDate'],'"'));
                                    return $q->whereBetween('chequeDate', [$fromDate,$toDate]);
                                })
                                ->when(!empty($input['bank']), function ($q) use ($bankmasterAutoID) {
                                    return $q->whereIn('paymentBankID', $bankmasterAutoID);
                                })
                                ->where('companySystemID',$companyId)
                                ->withCount('printed_history')
                                ->with(['currency','bank','pay_supplier', 'cheque_printed_by', 'printed_history' => function($query) {
                                    $query->with(['cheque_printed_by', 'changed_by', 'pay_supplier', 'currency']);
                                }]);

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
        $companyId = (isset($input['companyId'])) ? $input['companyId'] : '';

        
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankmasterAutoID = $request['bank'];
        $bankmasterAutoID = (array)$bankmasterAutoID;
        $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

        $receivedCheques = PdcLog::where('documentSystemID',21)
                                ->whereHas('customer_receive', function ($query){
                                    $query->where('approved', -1);
                                })
                                ->when(!empty($input['fromDate']) && !empty($input['toDate']), function ($q) use ($input) {
                                    $fromDate = Carbon::parse(trim($input['fromDate'],'"'));
                                    $toDate = Carbon::parse(trim($input['toDate'],'"'));
                                    return $q->whereBetween('chequeDate', [$fromDate,$toDate]);
                                })
                                ->when(!empty($request['bank']), function ($q) use ($bankmasterAutoID) {
                                    return $q->whereIn('paymentBankID', $bankmasterAutoID);
                                })
                                ->where('companySystemID',$companyId)
                                ->with(['currency','bank','customer_receive']);


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
        $input = $request->all();

        $bankIds =  PdcLog::whereNotNull('paymentBankID')->get()->pluck('paymentBankID')->unique();
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();
        $banks = BankMaster::whereIn('bankmasterAutoID', $bankIds)->get();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $policy = Helper::checkRestrictionByPolicy($input['companySystemID'],4);

        $data = [
            'banks' => $banks,
            'yesNoSelection' => $yesNoSelection,
            'chequeRePrintPolicy' => $policy,
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

        $checkBankLedger = BankLedger::where('pdcID', $input['id'])->where('trsClearedYN', -1)->first();

        if ($checkBankLedger && $input['newStatus'] == 2) {
            return $this->sendError("PDC cheque already cleared for treasury, cannot be returned", 500);
        }


        DB::beginTransaction();
        try {

            $empInfo = Helper::getEmployeeInfo();

            $masterData = ['documentSystemID' => $input['documentSystemID'], 'autoID' => $input['documentmasterAutoID'], 'companySystemID' => $input['companySystemID'], 'employeeSystemID' => $empInfo->employeeSystemID, 'pdcID' => $input['id']];

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

    public function getNextChequeNo(Request $request)
    {
        $input = $request->all();

         $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($input['PayMasterAutoId']);

        if (empty($paySupplierInvoiceMaster)) {
                return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $bankAccount = BankAccount::find($paySupplierInvoiceMaster->BPVAccount);

        $chequeRegisterAutoID = null;
        $nextChequeNo = 1;
        if ($paySupplierInvoiceMaster->BPVbankCurrency == $paySupplierInvoiceMaster->localCurrencyID && $paySupplierInvoiceMaster->supplierTransCurrencyID == $paySupplierInvoiceMaster->localCurrencyID) {
            $res =  $this->paySupplierInvoiceMasterRepository->getChequeNoForPDC($paySupplierInvoiceMaster->companySystemID, $bankAccount, $input['PayMasterAutoId'], $paySupplierInvoiceMaster->documentSystemID);

            if (!$res['status']) {
                return $this->sendError($res['message'], 500);
            }

            $chequeRegisterAutoID = $res['chequeRegisterAutoID'];
            $nextChequeNo = $res['nextChequeNo'];
        } else {
            $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoiceMaster->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoiceMaster->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
            if ($chkCheque) {
                $nextChequeNo = $chkCheque->BPVchequeNo + 1;
            } else {
                $nextChequeNo = 1;
            }
        }

        $pdcLogData = [
            'documentSystemID' => $paySupplierInvoiceMaster->documentSystemID,
            'documentmasterAutoID' => $input['PayMasterAutoId'],
            'paymentBankID' => $bankAccount->bankmasterAutoID,
            'companySystemID' => $paySupplierInvoiceMaster->companySystemID,
            'currencyID' => $paySupplierInvoiceMaster->supplierTransCurrencyID,
            'chequeRegisterAutoID' => $chequeRegisterAutoID,
            'chequeNo' => $nextChequeNo,
            'chequeStatus' => 0
        ];

        return $this->sendResponse($pdcLogData, "Cheque data retrived successfully");
    }

    public function reverseGeneratedChequeNo(Request $request)
    {
        $input = $request->all();

        if (!is_null($input['chequeRegisterAutoID'])) {
            $update_array = [
                'document_id' => null,
                'document_master_id' => null,
                'status' => 0,
            ];

            ChequeRegisterDetail::where('id', $input['chequeRegisterAutoID'])->update($update_array);
        }

        return $this->sendResponse([], "Generated Cheque reversed successfully");
    }

    public function issueNewCheque(Request $request)
    {
        $input = $request->all();

        $refereceID = $input['referenceChequeID'];

        $input['chequeDate'] = Carbon::parse($input['chequeDate']);

        if (isset($input['referenceChequeID'])) {
            unset($input['referenceChequeID']);
        }

        $createRes = PdcLog::create($input);

        if ($createRes) {
            PdcLog::where('id', $refereceID)->update(['referenceChequeID' => $createRes->id]);
        }

        return $this->sendResponse([], "Generated Cheque reversed successfully");
    }


    public function printPdcCheque(Request $request)
    {
        $input = $request->all();
        $htmlName = '';
        
        $employee = \Helper::getEmployeeInfo();
        $pvData = PaySupplierInvoiceMaster::where('PayMasterAutoId',$input['documentmasterAutoID'])->first();

        if (!$pvData) {
            return $this->sendError("Payment voucher not found");
        }


        if($input['type'] == 2 && $input['name'] != '') {
            $htmlName = $input['name'];
        } else if($input['type'] == 1) {   
            if(isset($input['bank_master_id']) && $input['bank_master_id'] > 0) {
            
                $bankTemplate = ChequeTemplateBank::where('bank_id',$input['bank_master_id'])->where('is_active', 1)->with('template')->get();
         
                if(count($bankTemplate) == 0) {
                    return $this->sendError(trans('custom.no_templates'),500);
                } else if(count($bankTemplate) == 1) {
                    $htmlName=$bankTemplate[0]['template']['view_name'];
                } else if(count($bankTemplate) > 1) {
                    $details['is_modal'] = true;
                    $details['data'] = $bankTemplate;
                    return $this->sendResponse($details, trans('custom.retrieved_successfully'));
                }
            } else {
                return $this->sendError(trans('custom.no_bank'),500);
            }
        }
        

        $selectedCompanyId = $input['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankAccount = null;
        if(isset($input['bankID']) && $input['bankID'] > 0 &&  isset($input['bankAccountID']) && $input['bankAccountID'] > 0){
            $bankAccount = BankAccount::where('bankmasterAutoID', $input['bankID'])
                ->where('bankAccountAutoID', $input['bankAccountID'])
                ->with(['currency'])
                ->first();
        }

        $bank_currency_id = 2;
        if ($bankAccount && $bankAccount->currency) {
            $bank_currency_id = $bankAccount->currency->currencyID;
        }
  
        $pdcData = PdcLog::where('id', $input['pdcLogID'])
                            ->with(['pay_supplier' => function ($query) {
                                $query->with(['bankcurrency', 'company', 'bankaccount', 'supplier']);
                            }, 'currency'])
                            ->whereHas('pay_supplier')
                            ->first();

        if (!$pdcData) {
            return $this->sendError(trans('custom.no_items_found_for_print'), 500);
        }

        $supplierTransCurrencyID = $pdcData->pay_supplier->supplierTransCurrencyID;
                
        DB::beginTransaction();
        try {
            $time = strtotime("now");
            $fileName = 'cheque_ahli' . $time . '.pdf';
            $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
            $totalAmount = 0;
            $temArray = array();
            $temArray['chequePrinted'] = 1;
            $temArray['chequePrintedDate'] = now();
            $temArray['chequePrintedBy'] = $employee->employeeSystemID;
            if(isset($input['isPrint']) && $input['isPrint']) {
                PdcLog::where('id', $input['pdcLogID'])->update($temArray);

                /*
                 * update cheque registry table print status if GCNFCR policy is on
                 * */
                $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $selectedCompanyId)
                    ->where('companyPolicyCategoryID', 35)
                    ->where('isYesNO', 1)
                    ->first();
                if (!empty($is_exist_policy_GCNFCR)) {
                    $check_registry = [
                        'isPrinted' => -1,
                        'cheque_printed_at' => now(),
                        'cheque_print_by' => $employee->employeeSystemID
                    ];
                    ChequeRegisterDetail::where('cheque_no', $pdcData->chequeNo)
                        ->where('company_id', $pdcData->companySystemID)
                        ->where('document_id', $pdcData->documentmasterAutoID)
                        ->update($check_registry);
                }
            }

            $item = PaySupplierInvoiceMaster::where('PayMasterAutoId', $pdcData->documentmasterAutoID)
                                             ->with(['bankcurrency', 'company', 'bankaccount', 'supplier' => function ($q3) use ($pdcData) {
                                                $q3->with(['supplierCurrency' => function ($q4) use ($pdcData) {
                                                    $q4->where('currencyID', $pdcData->pay_supplier->supplierTransCurrencyID)
                                                        ->where('isAssigned', -1)
                                                        ->with(['bankMemo_by']);
                                                }]);
                                            }, 'payee_memo' => function($q) use($subCompanies){
                                                $q->where('documentSystemID', 4)
                                                ->whereIn('companySystemID',$subCompanies);
                                            }])
                                            ->whereHas('bankcurrency')
                                            ->first();

            $item['decimalPlaces'] = 2;
            if ($item['bankcurrency']) {
                $item['decimalPlaces'] = $item['bankcurrency']['DecimalPlaces'];
            }

            $item->memos = isset($item['supplier']['supplierCurrency'][0]['bankMemo_by']) ? $item['supplier']['supplierCurrency'][0]['bankMemo_by'] : null;
            $temDetails = PaySupplierInvoiceMaster::where('PayMasterAutoId', $pdcData->documentmasterAutoID)
                                                  ->first();

            if (!empty($temDetails)) {
                if ($temDetails->invoiceType == 2) {
                    $item['details'] = $temDetails->supplierdetail;
                } else if ($temDetails->invoiceType == 3) {
                    $item['details'] = $temDetails->directdetail;
                } else if ($temDetails->invoiceType == 5) {
                    $item['details'] = $temDetails->advancedetail;
                } else {
                    $item['details'] = [];
                }
            } else {
                $item['details'] = [];
            }

            $totalAmount = $pdcData->amount;

            if($item){
                $entity = $item;
                $entity->totalAmount = $totalAmount;
                $entity->payAmountBank = $totalAmount;
                $entity->BPVdate = $pdcData->chequeDate;
                $totalAmount = round($totalAmount, $entity->decimalPlaces);
                $amountSplit = explode(".", $totalAmount);
                $intAmt = 0;
                $floatAmt = 00;

                if (count($amountSplit) == 1) {
                    $intAmt = $amountSplit[0];
                    $floatAmt = 00;
                } else if (count($amountSplit) == 2) {
                    $intAmt = $amountSplit[0];
                    $floatAmt = $amountSplit[1];
                }

                $entity->floatAmt = (string)$floatAmt;

                //add zeros to decimal point
                if($entity->floatAmt != 00){
                    $length = strlen($entity->floatAmt);
                    if($length<$entity->decimalPlaces){
                        $count = $entity->decimalPlaces-$length;
                        for ($i=0; $i<$count; $i++){
                            $entity->floatAmt .= '0';
                        }
                    }
                }

                // get supplier transaction currency
                $entity->instruction = '';
                $entity->supplierTransactionCurrencyDetails = [];
                if(isset($entity->supplier->supplierCurrency[0]->currencyMaster) && $entity->supplier->supplierCurrency[0]->currencyMaster){
                    $entity->supplierTransactionCurrencyDetails = $entity->supplier->supplierCurrency[0]->currencyMaster;
                    if($supplierTransCurrencyID != $bank_currency_id){
                        $entity->instruction = 'The exchange rate agreed with treasury department is '.$entity->supplierTransactionCurrencyDetails->CurrencyCode.' '.$entity->supplierTransCurrencyER.' = '.$entity->bankcurrency->CurrencyCode.' '.number_format($entity->companyRptCurrencyER,4);
                    }
                }else{
                    $entity->supplierTransactionCurrencyDetails = $entity->bankcurrency;
                }


                $entity->amount_word = ucfirst($f->format($intAmt));
                $entity->amount_word = str_replace('-', ' ', $entity->amount_word);
                $entity->chequePrintedByEmpName = $employee->empName;
                if($entity->supplier){
                    $entity->nameOnCheque = isset($entity->supplier->nameOnPaymentCheque)?$entity->supplier->nameOnPaymentCheque:'';
                }else{
                    $entity->nameOnCheque = $entity->directPaymentPayee;
                }

            }else{
                $entity = null;
            }
        
            $array = array('entity' => $entity, 'date' => $pdcData->chequeDate ,'type'=>$htmlName);
            if ($htmlName) {
                $html = view('print.' . $htmlName, $array)->render();
                DB::commit();
                if(isset($input['isPrint']) && $input['isPrint']) {
                    return $this->sendResponse($html, trans('custom.print_successfully'));
                }else{
                    $details['is_modal'] = false;
                    $details['data'] = $array;
                    return $this->sendResponse($details, trans('custom.retrieved_successfully'));
                }

            } else {
                return $this->sendError(trans('custom.error'), 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . trans('custom.error')];
        }
    }
}
