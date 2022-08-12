<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateGeneralLedgerAPIRequest;
use App\Http\Requests\API\UpdateGeneralLedgerAPIRequest;
use App\Jobs\GeneralLedgerInsert;
use App\Jobs\UnbilledGRVInsert;
use App\Models\AccountsPayableLedger;
use App\Models\AccountsReceivableLedger;
use App\Models\BankLedger;
use App\Models\BookInvSuppMaster;
use App\Models\Company;
use App\Models\CreditNote;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\DeliveryOrder;
use App\Models\DocumentMaster;
use App\Models\ErpItemLedger;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetMaster;
use App\Models\GeneralLedger;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnMaster;
use App\Models\JvMaster;
use App\Models\EmployeeLedger;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PurchaseReturn;
use App\Models\SalesReturn;
use App\Models\StockAdjustment;
use App\Models\StockReceive;
use App\Models\StockTransfer;
use App\Models\UnbilledGrvGroupBy;
use App\Models\Year;
use App\Models\SegmentMaster;
use App\Repositories\GeneralLedgerRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\ChartOfAccount;
use App\helper\CreateExcel;
/**
 * Class GeneralLedgerController
 * @package App\Http\Controllers\API
 */

class GeneralLedgerAPIController extends AppBaseController
{
    /** @var  GeneralLedgerRepository */
    private $generalLedgerRepository;

    public function __construct(GeneralLedgerRepository $generalLedgerRepo)
    {
        $this->generalLedgerRepository = $generalLedgerRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/generalLedgers",
     *      summary="Get a listing of the GeneralLedgers.",
     *      tags={"GeneralLedger"},
     *      description="Get all GeneralLedgers",
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
     *                  @SWG\Items(ref="#/definitions/GeneralLedger")
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
        $this->generalLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->generalLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $generalLedgers = $this->generalLedgerRepository->all();

        return $this->sendResponse($generalLedgers->toArray(), 'General Ledgers retrieved successfully');
    }

    /**
     * @param CreateGeneralLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/generalLedgers",
     *      summary="Store a newly created GeneralLedger in storage",
     *      tags={"GeneralLedger"},
     *      description="Store GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GeneralLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GeneralLedger")
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
     *                  ref="#/definitions/GeneralLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGeneralLedgerAPIRequest $request)
    {
        $input = $request->all();

        $generalLedgers = $this->generalLedgerRepository->create($input);

        return $this->sendResponse($generalLedgers->toArray(), 'General Ledger saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/generalLedgers/{id}",
     *      summary="Display the specified GeneralLedger",
     *      tags={"GeneralLedger"},
     *      description="Get GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GeneralLedger",
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
     *                  ref="#/definitions/GeneralLedger"
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
        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        return $this->sendResponse($generalLedger->toArray(), 'General Ledger retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateGeneralLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/generalLedgers/{id}",
     *      summary="Update the specified GeneralLedger in storage",
     *      tags={"GeneralLedger"},
     *      description="Update GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GeneralLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GeneralLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GeneralLedger")
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
     *                  ref="#/definitions/GeneralLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGeneralLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->findWithoutFail($id);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        $generalLedger = $this->generalLedgerRepository->update($input, $id);

        return $this->sendResponse($generalLedger->toArray(), 'GeneralLedger updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/generalLedgers/{id}",
     *      summary="Remove the specified GeneralLedger from storage",
     *      tags={"GeneralLedger"},
     *      description="Delete GeneralLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GeneralLedger",
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
        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->findWhere($id);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        $generalLedger->delete();

        return $this->sendResponse($id, 'General Ledger deleted successfully');
    }


    public function getGeneralLedgerReview(Request $request)
    {
        /** @var GeneralLedger $generalLedger */
        $generalLedger = $this->generalLedgerRepository->with(['supplier','customer','charofaccount','localcurrency','transcurrency','rptcurrency'])->findWhere(['companySystemID' => $request->companySystemID,'documentSystemID' => $request->documentSystemID,'documentSystemCode' => $request->autoID]);

        if (empty($generalLedger)) {
            return $this->sendError('General Ledger not found');
        }

        $accountPaybaleLedgerData = AccountsPayableLedger::with(['supplier','local_currency', 'transaction_currency', 'reporting_currency'])
                                                         ->where('documentSystemID', $request->documentSystemID)
                                                         ->where('documentSystemCode', $request->autoID)
                                                         ->where('companySystemID', $request->companySystemID)
                                                         ->get();


        $accountReceviableLedgerData = AccountsReceivableLedger::with(['customer', 'local_currency', 'transaction_currency', 'reporting_currency'])
                                                         ->where('documentSystemID', $request->documentSystemID)
                                                         ->where('documentCodeSystem', $request->autoID)
                                                         ->where('companySystemID', $request->companySystemID)
                                                         ->get();

        $itemLedgerData = ErpItemLedger::with(['service_line', 'warehouse', 'uom', 'local_currency', 'reporting_currency'])
                                       ->where('documentSystemID', $request->documentSystemID)
                                       ->where('documentSystemCode', $request->autoID)
                                       ->where('companySystemID', $request->companySystemID)
                                       ->get();

        $unbilledLedgerData = [];
        if (in_array($request->documentSystemID, [3, 24])) {
            $unbilledLedgerData = UnbilledGrvGroupBy::with(['supplier', 'pomaster', 'grvmaster', 'local_currency', 'transaction_currency', 'reporting_currency'])
                                                    ->where('companySystemID', $request->companySystemID);

            if ($request->documentSystemID == 3) {
                $unbilledLedgerData = $unbilledLedgerData->where('grvAutoID', $request->autoID)
                                                         ->where(function($query) {
                                                            $query->where('purhaseReturnAutoID', 0)
                                                                  ->orWhereNull('purhaseReturnAutoID');
                                                         });
            } else {
                $unbilledLedgerData = $unbilledLedgerData->where('purhaseReturnAutoID', $request->autoID);
            }
            
            $unbilledLedgerData = $unbilledLedgerData->get();
        }

        $employeeLedgerData = EmployeeLedger::with(['employee','local_currency', 'transaction_currency', 'reporting_currency'])
                                             ->where('documentSystemID', $request->documentSystemID)
                                             ->where('documentSystemCode', $request->autoID)
                                             ->where('companySystemID', $request->companySystemID)
                                             ->get();



        $companyCurrency = \Helper::companyCurrency($request->companySystemID);

        $generalLedger = [
                'outputData' => $generalLedger->toArray(), 
                'companyCurrency' => $companyCurrency,
                'accountPaybaleLedgerData' => $accountPaybaleLedgerData,
                'accountReceviableLedgerData' => $accountReceviableLedgerData,
                'itemLedgerData' => $itemLedgerData,
                'employeeLedgerData' => $employeeLedgerData,
                'unbilledLedgerData' => $unbilledLedgerData
            ];

        return $this->sendResponse($generalLedger, 'General Ledger retrieved successfully');
    }

    /*
     * year
     * document
     * company
     * */
    public function getDocumentAmendFormData(Request $request){

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }
        $years = Year::orderBy('year','DESC')->get();
        $companies = Company::whereIn("companySystemID", $subCompanies)->get();
        $documents = DocumentMaster::whereIn('documentSystemID',[3,4,7,8,10,11,12,13,15,17,19,20,21,22,23,24,41,61,71,87])->get();

        $output = [
            'years'=>$years,
            'companies'=>$companies,
            'documents'=>$documents,
        ];

        return $this->sendResponse($output, 'Document Amend Form Data retrieved successfully');
    }

    public function getDocumentAmendFromGL(Request $request){
        $input = $request->all();
        $messages = [
            'companySystemID.required' => 'Company is required.',
            'documentSystemID.required' => 'Document is required.',
            'yearID.required' => 'Year is required.',

        ];
        $validator = \Validator::make($input, [
            'companySystemID' => 'required|numeric|min:1',
            'documentSystemID' => 'required|numeric|min:1',
            'yearID' => 'required|numeric|min:1',

        ], $messages);

        if($validator->fails()) {
          //  return $this->sendError($validator->messages(), 422);
        }




        $input['companySystemID'] = isset($input['companySystemID'])?$input['companySystemID']:0;
        $input['documentSystemID'] = isset($input['documentSystemID'])?$input['documentSystemID']:0;
        $input['yearID'] = isset($input['yearID'])?$input['yearID']:0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $glDocuments = GeneralLedger::where('companySystemID',$input['companySystemID'])
            ->where('documentSystemID',$input['documentSystemID'])
            ->where('documentYear',$input['yearID'])
            ->with(['confirm_by','final_approved_by']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $glDocuments = $glDocuments->where(function ($query) use ($search) {
                $query->where('documentNarration', 'LIKE', "%{$search}%")
                    ->orWhere('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('glCode', 'LIKE', "%{$search}%");
            });
        }
        $glDocuments = $glDocuments->groupBy('documentSystemCode', 'documentSystemID');

        return \DataTables::eloquent($glDocuments)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('GeneralLedgerID', $input['order'][0]['dir']);

                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function changePostingDate(Request $request){

        $input = $request->all();
        $messages = [
            'GeneralLedgerID.required' => 'ID is required',
            'documentDate.required' => 'Posting Date is required.'
        ];
        $validator = \Validator::make($input, [
            'GeneralLedgerID' => 'required|numeric|min:1',
            'documentDate' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $id = $input['GeneralLedgerID'];
        $time = Carbon::now()->format('H:i:s');
        $documentDate = Carbon::parse($input['documentDate'])->format('y-m-d').' '.$time;
        $documentDate = Carbon::parse($documentDate)->format('Y-m-d H:i:s');

        $gl = GeneralLedger::find($id);
        if(count((array)$gl)==0){
            return $this->sendError('GL Entries Not Found');
        }

        $companySystemID = $gl->companySystemID;
        $documentSystemID = $gl->documentSystemID;
        $documentSystemCode = $gl->documentSystemCode;
        $documentYear = $gl->documentYear;

        DB::beginTransaction();
        try{

            $isGlUpdated = GeneralLedger::where('companySystemID',$companySystemID)
                ->where('documentSystemID',$documentSystemID)
                ->where('documentSystemCode',$documentSystemCode)
                ->update(['documentDate' => $documentDate]);

            switch ($documentSystemID) {

                case 3:
                    /*GRV
                     * erp_itemledger
                     * erp_unbilledgrvgroupby
                     * erp_generalledger
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                    UnbilledGrvGroupBy::where('companySystemID',$companySystemID)
                        ->where('grvAutoID',$documentSystemCode)
                        ->update(['grvDate' => $documentDate]);

                case 4:
                    /*
                     * PV
                     * erp_bankledger
                     * erp_generalledger
                     * master table - erp_paysupplierinvoicemaster - postedDate
                     * */

                    BankLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['postedDate' => $documentDate]);

                    PaySupplierInvoiceMaster::where('PayMasterAutoId',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 7:
                    /*
                     * SA
                     * erp_itemledger
                     * erp_generalledger
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                case 8:
                    /*
                     * MI
                     * erp_itemledger
                     * erp_generalledger
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                case 10:
                    /*
                     * RS - Receive Stock
                     * erp_itemledger
                     * erp_generalledger
                     * master table - erp_stockreceive - postedDate
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                    StockReceive::where('stockReceiveAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 11:
                    /*
                     * SI
                     * erp_accountspayableledger
                     * erp_generalledger
                     * master table - erp_bookinvsuppmaster - postedDate
                     * */

                    AccountsPayableLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['documentDate' => $documentDate]);

                    BookInvSuppMaster::where('bookingSuppMasInvAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 12:
                    /*
                     * SR (Materiral return)
                     * erp_itemledger
                     * erp_generalledger
                     * master table -erp_itemreturnmaster - postedDate
                     * */

                ErpItemLedger::where('companySystemID',$companySystemID)
                    ->where('documentSystemID',$documentSystemID)
                    ->where('documentSystemCode',$documentSystemCode)
                    ->update(['transactionDate' => $documentDate]);

                ItemReturnMaster::where('itemReturnAutoID',$documentSystemCode)
                    ->where('companySystemID',$companySystemID)
                    ->where('documentSystemID',$documentSystemID)
                    ->update(['postedDate' => $documentDate]);

                case 13:
                    /*
                     * ST
                     * erp_itemledger
                     * erp_generalledger
                     * master table - erp_stocktransfer - postedDate
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                    StockTransfer::where('stockTransferAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 15:
                    /*
                     * DN
                     * erp_accountspayableledger
                     * erp_generalledger
                     * master table - erp_debitnote - postedDate
                     * */
                    AccountsPayableLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['documentDate' => $documentDate]);

                    DebitNote::where('debitNoteAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 17:
                    /*
                     * JV
                     * erp_generalledger
                     * master table - erp_jvmaster - postedDate
                     * */
                    JvMaster::where('jvMasterAutoId',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 19:
                    /*
                     * CN
                     * erp_accountsreceivableledger
                     * erp_generalledger
                     * master table - erp_creditnote - postedDate
                     * */
                    AccountsReceivableLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentCodeSystem',$documentSystemCode)
                        ->update(['documentDate' => $documentDate]);

                    CreditNote::where('creditNoteAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemiD',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 20:
                    /*
                     * INV
                     * erp_accountsreceivableledger
                     * erp_generalledger
                     * master table - erp_custinvoicedirect - postedDate
                     * */
                    AccountsReceivableLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentCodeSystem',$documentSystemCode)
                        ->update(['documentDate' => $documentDate]);

                    CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 21:
                    /*
                     * BRV
                     * erp_bankledger
                     * erp_generalledger
                     * master table - erp_customerreceivepayment - postedDate
                     * */
                    BankLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['postedDate' => $documentDate]);

                    CustomerReceivePayment::where('custReceivePaymentAutoID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 22:
                    /*
                     * FA - asset costing
                     * erp_generalledger
                     * master table - erp_fa_asset_master - postedDate
                     * */

                    FixedAssetMaster::where('faID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 23:
                    /*
                     * FAD - Fixed Asset Depreciation
                     * erp_generalledger
                     * master table - erp_fa_depmaster -depDate ??????// TODO
                     * */

//                    FixedAssetDepreciationMaster::where('depMasterAutoID',$documentSystemCode)
//                        ->where('companySystemID',$companySystemID)
//                        ->where('documentSystemID',$documentSystemID)
//                        ->update(['depDate' => $documentDate]);

                case 24:
                    /*
                     * PRN
                     * erp_itemledger
                     * erp_generalledger
                     * */

                ErpItemLedger::where('companySystemID',$companySystemID)
                    ->where('documentSystemID',$documentSystemID)
                    ->where('documentSystemCode',$documentSystemCode)
                    ->update(['transactionDate' => $documentDate]);

                AccountsPayableLedger::where('companySystemID',$companySystemID)
                    ->where('documentSystemID',$documentSystemID)
                    ->where('documentSystemCode',$documentSystemCode)
                    ->update(['documentDate' => $documentDate]);

                     UnbilledGrvGroupBy::where('companySystemID',$companySystemID)
                        ->where('purhaseReturnAutoID',$documentSystemCode)
                        ->update(['grvDate' => $documentDate]);

                case 41:
                    /*
                     * FADS - Fixed Asset Depreciation
                     * erp_generalledger
                     * master table - erp_fa_asset_disposalmaster -disposalDocumentDate ??????// TODO
                     * */

//                    FixedAssetDepreciationMaster::where('depMasterAutoID',$documentSystemCode)
//                        ->where('companySystemID',$companySystemID)
//                        ->where('documentSystemID',$documentSystemID)
//                        ->update(['disposalDocumentDate' => $documentDate]);

                case 61:
                    /*
                     * INRC
                     * erp_itemledger
                     * erp_generalledger
                     * ???? master table - erp_inventoryreclassification - postedDate
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                    InventoryReclassification::where('inventoryreclassificationID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                case 71:
                    /*
                     * INRC
                     * erp_itemledger
                     * erp_generalledger
                     * ???? master table - erp_delivery_order - postedDate
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                    DeliveryOrder::where('deliveryOrderID',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);


                    case 87:
                    /*
                     * INRC
                     * erp_itemledger
                     * erp_generalledger
                     * ???? master table - salesreturn - postedDate
                     * */

                    ErpItemLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentSystemCode',$documentSystemCode)
                        ->update(['transactionDate' => $documentDate]);

                    SalesReturn::where('id',$documentSystemCode)
                        ->where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->update(['postedDate' => $documentDate]);

                    AccountsReceivableLedger::where('companySystemID',$companySystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->where('documentCodeSystem',$documentSystemCode)
                        ->update(['documentDate' => $documentDate]);



            }





            DB::commit();
            return $this->sendResponse([],'Posting date changed successfully');
        }catch (\Exception $e){
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }

    public function updateGLEntries(Request $request){

        $input = $request->all();
        $messages = [
            'documentSystemID.required' => 'Document system ID is required',
            'documentSystemCode.required' => 'Document system code is required.',
            'companySystemID.required' => 'Company system ID is required.'
        ];
        $validator = \Validator::make($input, [
            'documentSystemID' => 'required',
            'documentSystemCode' => 'required',
            'companySystemID' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        DB::beginTransaction();
        try {

            $empInfo = Helper::getEmployeeInfo();
            $count = GeneralLedger::where('documentSystemID', $input['documentSystemID'])
                ->where('documentSystemCode', $input['documentSystemCode'])
                ->where('companySystemID', $input['companySystemID'])
                ->count();


            $unbilledCount = 0;
            if ($input['documentSystemID'] == 3) {
                $unbilledCount = UnbilledGrvGroupBy::where('grvAutoID', $input['documentSystemCode'])
                                        ->where('companySystemID', $input['companySystemID'])
                                        ->count();

                if ($count > 0 && $unbilledCount > 0) {
                    return $this->sendError('GL entries and unbilled ledger entries are already passed for this document',500);
                }

            } else {
                if($count > 0){
                    return $this->sendError('GL entries are already passed for this document',500);
                }
            }


            if ($count == 0) {
                $masterData = ['documentSystemID' => $input['documentSystemID'],
                               'autoID' => $input['documentSystemCode'],
                               'companySystemID' => $input['companySystemID'],
                               'employeeSystemID' => $empInfo->employeeSystemID];
                $generalLedger = GeneralLedgerInsert::dispatch($masterData);
            }


            if ($input['documentSystemID'] == 3 && $unbilledCount == 0) {
                $grvData = GRVMaster::find($input['documentSystemCode']);

                $masterData = ['documentSystemID' => $input['documentSystemID'],
                               'autoID' => $input['documentSystemCode'],
                               'supplierID' => ($grvData) ? $grvData->supplierID : 0,
                               'companySystemID' => $input['companySystemID'],
                               'employeeSystemID' => $empInfo->employeeSystemID];
                $unbilledGRVInsert = UnbilledGRVInsert::dispatch($masterData);

                DB::commit();
                if ($count == 0) {
                    return $this->sendResponse([],'GL and unbilled ledger entries posted successfully');
                } else {
                    return $this->sendResponse([],'Unbilled ledger entries posted successfully');
                }
            }


            DB::commit();
            return $this->sendResponse([],'GL posted successfully');
        }catch (\Exception $e){
            DB::rollback();
            return $this->sendError($e->getMessage());
        }
    }


    public function generateSegmentGlReport(Request $request)
    {

        $input = $request->all();

        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');
        $fromDate = ((new Carbon($request->fromDate))->addDays(1)->format('Y-m-d'));
        $type = $request->currency;
        $company = $request->company;
        $details = $this->generateGLReport($fromDate,$toDate,$type,$company);

        return $this->sendResponse($details,'Posting date changed successfully');

        
    }

    public function generateSegmentGlReportExcel(Request $request)
    {

        $input = $request->all();

        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');
        $fromDate = ((new Carbon($request->fromDate))->addDays(1)->format('Y-m-d'));
        $type = $request->currency;
        $file_type = $request->type;
        $company = $request->company;

        $checkIsGroup = Company::find($company);
        
        $companyCurrency = \Helper::companyCurrency($company);
 

        $reportData = $this->generateGLReport($fromDate,$toDate,$type,$company);
        $deb_cred = array("Debit","Credit","Balance");
        $reportData['deb_cred'] = $deb_cred;
        $reportData['length'] = ($reportData['j']*3)+3;
        $reportData['fromDate'] = $fromDate;
        $reportData['toDate'] = $toDate;
        $reportData['currency'] = ($type[0] == 1) ? $reportData['localcurrency']['CurrencyCode'] : $reportData['reportingcurrency']['CurrencyCode'];
        $reportData['company'] = $checkIsGroup->CompanyName;
        $reportData['Title'] = 'Segment Wise GL Report';
   
        
        $templateName = "export_report.segment-wise-gL-report";
        $fileName = 'gl_segment_report';
        $path = 'general-ledger/report/segment-wise-gL-report/excel/';
        $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }

    }

    public function generateGLReport($fromDate,$toDate,$type,$company)
    {


        if(is_array($type))
        {
            $type = $type[0];
        }

        if($type == 1)
        {
            $cur = 'documentLocalCurrencyID';
            $amount = 'documentLocalAmount';
        }
        else
        {   
            $amount = 'documentRptAmount';
            $cur = 'documentRptCurrencyID';
        }
        $entries = ChartOfAccount::where('controlAccountsSystemID',2)->orderBy('AccountCode')->get();

        $data = [];
        $i = 0;
        $segment_data = [];

        $segment_data = SegmentMaster::pluck('ServiceLineDes');

        $segment_data->push('Total');

        $segments = SegmentMaster::get();

        $checkIsGroup = Company::find($company);

        $char_ac = ChartOfAccount::where('controlAccountsSystemID',2)->pluck('chartOfAccountSystemID');
        $seg_info = SegmentMaster::pluck('serviceLineSystemID');

        $companyCurrency = \Helper::companyCurrency($company);
        if($companyCurrency) {
            $requestCurrencyLocal = $companyCurrency->localcurrency;
            $requestCurrencyRpt = $companyCurrency->reportingcurrency;
        }

        $collection =  DB::table('erp_generalledger')
        ->whereIn('serviceLineSystemID',$seg_info)
        ->whereIn('chartOfAccountSystemID',$char_ac)
        ->whereBetween('documentDate', [$fromDate, $toDate])
        ->groupBy(['serviceLineSystemID','chartOfAccountSystemID'])
         ->get();

        foreach($entries as $entry)
        {

           


                $data[$i]['glAccountId'] = $entry->AccountCode.' | '.$entry->AccountDescription;
                $j = 0;
                $tot_credit = 0;
                $tot_debit = 0;
                $tot_total = 0;
                foreach($segments as $segment)
                {
                    
                  
                    $segment_id = $segment->serviceLineSystemID;
                    $segment_name = $segment->ServiceLineDes;
                   
                    $data[$i][$j]['segement_id'] =  $segment_name;


                    if($collection->contains('serviceLineSystemID',$segment_id) && $collection->contains('chartOfAccountSystemID',$entry->chartOfAccountSystemID))
                        {

                            $general_ledger = DB::table('erp_generalledger')
                            ->join('currencymaster', $cur, '=', 'currencyID')
                            ->where('serviceLineSystemID',$segment_id)
                            ->where('chartOfAccountSystemID',$entry->chartOfAccountSystemID)
                            ->whereBetween('documentDate', [$fromDate, $toDate])
                            ->selectRaw("sum(case when $amount<0 then $amount else 0 end) as credit,
                            sum(case when $amount>0 then $amount else 0 end) as debit, DecimalPlaces")
                             ->first();

                             $credit = round($general_ledger->credit,$general_ledger->DecimalPlaces)*-1;
                             $debit = round($general_ledger->debit,$general_ledger->DecimalPlaces);
                             $total = round(($general_ledger->debit - ($general_ledger->credit*-1)),$general_ledger->DecimalPlaces);
                             $decimal = ($general_ledger->DecimalPlaces);


                        }
                    else
                        {
                            $credit = 0;
                            $debit = 0;
                            $total = 0;
                            $decimal = 0;
                        }


                        $data[$i][$j]['credit'] =  $credit;
                        $data[$i][$j]['debit'] =  $debit;
                        $data[$i][$j]['total'] =  $total;
                        $data[$i][$j]['decimal'] =  $decimal;
                        $tot_credit += $credit;
                        $tot_debit += $debit;
                        $tot_total += ($debit - $credit);
                        $j++;   

                }
                
                $data[$i][$j]['segement_id'] =  'Total';
                $data[$i][$j]['credit'] =  round($tot_credit,2);
                $data[$i][$j]['debit'] =  round($tot_debit,2);
                $data[$i][$j]['total'] =  round(($tot_total),2);

                $i++;

        }
        
        
        $details['data'] = $data;
        $details['segment'] = $segment_data;
        $details['company'] = $checkIsGroup->CompanyName;
        $details['localcurrency'] = $requestCurrencyLocal;
        $details['reportingcurrency'] = $requestCurrencyRpt;
        $details['j'] = $j;

        return $details;
    }

    public static function getToal($data,$index,$column){
        $sum = 0;

        
        for ($i = 0; $i < count($data); $i++) {
          $sum += $data[$i][$index][$column];
        }
        return $sum;
    }


}
