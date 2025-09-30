<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateGeneralLedgerAPIRequest;
use App\Http\Requests\API\UpdateGeneralLedgerAPIRequest;
use App\Jobs\ApprovePendingSegments;
use App\Jobs\GeneralLedgerInsert;
use App\Jobs\ProccessMissedAccumalatedDepreciation;
use App\Jobs\UnbilledGRVInsert;
use App\Models\AccountsPayableLedger;
use App\Models\AccountsReceivableLedger;
use App\Models\AssetDisposalMaster;
use App\Models\BankLedger;
use App\Models\BookInvSuppMaster;
use App\Models\Route;
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
use App\helper\CommonJobService;
use App\Models\GeneralLedger;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnMaster;
use App\Models\JvMaster;
use App\Models\EmployeeLedger;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\POSGLEntries;
use App\Models\PurchaseReturn;
use App\Models\CurrencyMaster;
use App\Models\SalesReturn;
use App\Models\CustomerMaster;
use App\Models\StockAdjustment;
use App\Models\StockCount;
use App\Models\StockReceive;
use App\Models\StockTransfer;
use App\Models\SupplierMaster;
use App\Models\UnbilledGrvGroupBy;
use App\Models\Year;
use App\Models\SegmentMaster;
use App\Repositories\GeneralLedgerRepository;
use App\Services\DocumentAutoApproveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\ChartOfAccount;
use App\helper\CreateExcel;
use App\Services\GeneralLedger\GrvGlService;
use App\Services\GeneralLedger\MaterialIssueGlService;
use App\Services\GeneralLedger\MaterialReturnGlService;
use App\Services\GeneralLedger\StockTransferGlService;
use App\Services\GeneralLedger\StockRecieveGlService;
use App\Services\GeneralLedger\InventoryReclassificationGlService;
use App\Services\GeneralLedger\PurchaseReturnGlService;
use App\Services\GeneralLedger\CustomerInvoiceGlService;
use App\Services\GeneralLedger\StockAdjustmentGlService;
use App\Services\GeneralLedger\SupplierInvoiceGlService;
use App\Services\GeneralLedger\DebitNoteGlService;
use App\Services\GeneralLedger\CreditNoteGlService;
use App\Services\GeneralLedger\PaymentVoucherGlService;
use App\Services\GeneralLedger\CustomerReceivePaymentGlService;
use App\Services\GeneralLedger\JournalVoucherGlService;
use App\Services\GeneralLedger\FixedAssetMasterGlService;
use App\Services\GeneralLedger\FixedAssetDipreciationGlService;
use App\Services\GeneralLedger\FixedAssetDisposalGlService;
use App\Services\GeneralLedger\DeliveryOrderGlService;
use App\Services\GeneralLedger\SalesReturnGlService;
use App\Services\GeneralLedger\StockCountGlService;
use App\Services\GeneralLedger\GPOSSalesGlService;
use App\Services\GeneralLedger\RPOSSalesGlService;
use App\Services\GeneralLedger\GeneralLedgerPostingService;
use Illuminate\Support\Facades\Log;

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

    public function updateNotPostedGLEntries(Request $request)
    {
        $input = $request->all();

        Log::useFiles(storage_path() . '/logs/update_missing_docs.log');

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return  "tenant list is empty";
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            Log::info('checking the db : '.$tenantDb);
            CommonJobService::db_switch($tenantDb);

            $data = DB::select("SELECT da.companyID, da.companySystemID, da.documentSystemID,da.employeeSystemID, da.documentID, da.documentSystemCode, da.documentCode, da.TIMESTAMP, da.documentApprovedID FROM erp_documentapproved da WHERE da.approvedYN != 0 AND da.documentSystemID NOT IN ( 1, 2, 56, 66, 59, 58, 50, 57, 101, 51, 107, 96, 62, 67, 68, 9, 65, 64, 100, 102, 103, 46, 99 ) AND da.documentCode NOT IN ( SELECT documentCode FROM erp_generalledger WHERE documentCode IS NOT NULL GROUP BY documentCode) AND da.rollLevelOrder = (SELECT max(da_new.rollLevelOrder) FROM erp_documentapproved as da_new WHERE da_new.documentSystemID = da.documentSystemID AND da_new.documentSystemCode = da.documentSystemCode) AND da.`timeStamp` > '2024-01-01'");

            foreach ($data as $dt){
                Log::info($dt->documentCode);
                $masterData = ['documentSystemID' => $dt->documentSystemID,
                               'autoID' => $dt->documentSystemCode,
                               'companySystemID' => $dt->companySystemID,
                               'documentDateOveride' => $dt->TIMESTAMP,
                               'employeeSystemID' => $dt->employeeSystemID];
                $jobGL = GeneralLedgerInsert::dispatch($masterData, $tenantDb);
            }
        }

        return $this->sendResponse([], 'General Ledger updated successfully');
    }


    public function updateNotPostedPVGLEntries(Request $request)
    {
        $input = $request->all();

        Log::useFiles(storage_path() . '/logs/update_missing_docs.log');

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return  "tenant list is empty";
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            Log::info('checking the db : '.$tenantDb);
            CommonJobService::db_switch($tenantDb);

            $data = DB::table('erp_paysupplierinvoicemaster')
                ->where('invoiceType', 2)
                ->leftJoin('erp_generalledger', function ($join) {
                    $join->on('erp_paysupplierinvoicemaster.PayMasterAutoId', '=', 'erp_generalledger.documentSystemCode')
                        ->where('erp_generalledger.documentSystemID', 4);
                })
                ->select('erp_paysupplierinvoicemaster.*')
                ->where('erp_paysupplierinvoicemaster.approved', -1)
                ->whereNull('erp_generalledger.documentSystemCode')
                ->where('erp_paysupplierinvoicemaster.approvedDate', '>', '2024-07-17')
                ->get();

            foreach ($data as $dt){
                Log::info($dt->PayMasterAutoId);
                $masterData = ['documentSystemID' => $dt->documentSystemID,
                    'autoID' => $dt->PayMasterAutoId,
                    'companySystemID' => $dt->companySystemID,
                    'documentDateOveride' => $dt->postedDate,
                    'employeeSystemID' => $dt->approvedByUserSystemID,
                    'otherLedgers' => false];
                $jobGL = GeneralLedgerInsert::dispatch($masterData, $tenantDb);
            }
        }

        return $this->sendResponse([], 'General Ledger updated successfully');
    }

    public function updateNotPostedRVGLEntries(Request $request)
    {
        $input = $request->all();

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return  "tenant list is empty";
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            CommonJobService::db_switch($tenantDb);

            $data = DB::table('erp_customerreceivepayment')
                ->leftJoin('erp_generalledger', function ($join) {
                    $join->on('erp_customerreceivepayment.custReceivePaymentAutoID', '=', 'erp_generalledger.documentSystemCode')
                        ->where('erp_generalledger.documentSystemID', 21);
                })
                ->select('erp_customerreceivepayment.*')
                ->where('erp_customerreceivepayment.approved', -1)
                ->where('erp_customerreceivepayment.documentSystemID', 21)
                ->whereNull('erp_generalledger.documentSystemCode')
                ->where('erp_customerreceivepayment.timestamp', '>', '2024-01-01')
                ->get();

            foreach ($data as $dt){
                $masterData = ['documentSystemID' => $dt->documentSystemID,
                    'autoID' => $dt->custReceivePaymentAutoID,
                    'companySystemID' => $dt->companySystemID,
                    'documentDateOveride' => $dt->postedDate,
                    'employeeSystemID' => $dt->approvedByUserSystemID,
                    'otherLedgers' => false];
                $jobGL = GeneralLedgerInsert::dispatch($masterData, $tenantDb);


                DB::table('migratedDocs')->insert([
                    'documentSystemID' => $dt->documentSystemID,
                    'documentSystemCode' => $dt->custReceivePaymentAutoID,
                    'documentCode' => $dt->custPaymentReceiveCode,
                    'comment' => "Update General Ledger",
                    'created_at' => Carbon::now()
                ]);
            }
        }

        return $this->sendResponse([], 'General Ledger updated successfully');
    }

    public function updateNotPostedFAGLEntries(Request $request)
    {
        $input = $request->all();

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return  "tenant list is empty";
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            CommonJobService::db_switch($tenantDb);

            $data = DB::table('erp_fa_asset_master')
                ->leftJoin('erp_generalledger', function ($join) {
                    $join->on('erp_fa_asset_master.faID', '=', 'erp_generalledger.documentSystemCode')
                        ->where('erp_generalledger.documentSystemID', 22);
                })
                ->select('erp_fa_asset_master.*')
                ->where('erp_fa_asset_master.approved', -1)
                ->where('erp_fa_asset_master.documentSystemID', 22)
                ->where('erp_fa_asset_master.assetType', 1)
                ->whereNull('erp_generalledger.documentSystemCode')
                ->whereNotNull('erp_fa_asset_master.assetCostingUploadID')
                ->get();

            foreach ($data as $dt){
                $masterData = ['documentSystemID' => $dt->documentSystemID,
                    'autoID' => $dt->faID,
                    'companySystemID' => $dt->companySystemID,
                    'employeeSystemID' => $dt->approvedByUserSystemID
                ];
                $jobGL = GeneralLedgerInsert::dispatch($masterData, $tenantDb);

                DB::table('migratedDocs')->insert([
                    'documentSystemID' => $dt->documentSystemID,
                    'documentSystemCode' => $dt->faID,
                    'documentCode' => $dt->faCode,
                    'comment' => "Update General Ledger",
                    'created_at' => Carbon::now()
                ]);
            }
        }

        return $this->sendResponse([], 'General Ledger updated successfully');
    }

     public function updateNotPostedFADepGLEntries(Request $request)
    {
        $input = $request->all();

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return  "tenant list is empty";
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            CommonJobService::db_switch($tenantDb);

            $data = DB::table('erp_fa_depmaster')
                ->leftJoin('erp_generalledger', function ($join) {
                    $join->on('erp_fa_depmaster.depMasterAutoID', '=', 'erp_generalledger.documentSystemCode')
                        ->where('erp_generalledger.documentSystemID', 23);
                })
                ->select('erp_fa_depmaster.*')
                ->where('erp_fa_depmaster.approved', -1)
                ->where('erp_fa_depmaster.documentSystemID', 23)
                ->where('erp_fa_depmaster.is_acc_dep', 1)
                ->whereNull('erp_generalledger.documentSystemCode')
                ->get();

            foreach ($data as $dt){
                $masterData = ['documentSystemID' => $dt->documentSystemID,
                    'autoID' => $dt->depMasterAutoID,
                    'companySystemID' => $dt->companySystemID,
                    'employeeSystemID' => $dt->approvedByUserSystemID,
                    'otherLedgers' => false];
                $jobGL = GeneralLedgerInsert::dispatch($masterData, $tenantDb);


                DB::table('migratedDocs')->insert([
                    'documentSystemID' => $dt->documentSystemID,
                    'documentSystemCode' => $dt->depMasterAutoID,
                    'documentCode' => $dt->depCode,
                    'comment' => "Update General Ledger",
                    'created_at' => Carbon::now()
                ]);
            }
        }

        return $this->sendResponse([], 'General Ledger updated successfully');
    }

    public function updateNotPostedBSIGLEntries(Request $request)
    {

        $showInvoices = $request->showInvoices;

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return  "tenant list is empty";
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            CommonJobService::db_switch($tenantDb);

            $data = BookInvSuppMaster::whereDoesntHave('generalLedger')
                ->where('approved',-1)
                ->select(['bookingSuppMasInvAutoID','documentSystemID','companySystemID','approvedByUserSystemID','bookingInvCode'])
                ->get();

            if($showInvoices == "true")
            {
                print_r($data);
            }

            foreach ($data as $dt){
                $masterData = ['documentSystemID' => $dt->documentSystemID,
                    'autoID' => $dt->bookingSuppMasInvAutoID,
                    'companySystemID' => $dt->companySystemID,
                    'employeeSystemID' => $dt->approvedByUserSystemID
                ];
                $jobGL = GeneralLedgerInsert::dispatch($masterData, $tenantDb);

                DB::table('migratedDocs')->insert([
                    'documentSystemID' => $dt->documentSystemID,
                    'documentSystemCode' => $dt->bookingSuppMasInvAutoID,
                    'documentCode' => $dt->bookingInvCode,
                    'comment' => "Update General Ledger",
                    'created_at' => Carbon::now()
                ]);
            }
        }

        return $this->sendResponse([], 'General Ledger updated successfully');

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


        $bankLedgerData = BankLedger::with(['local_currency','reporting_currency','bank_account','bank_currency_by'])
                                    ->where('documentSystemID', $request->documentSystemID)
                                    ->where('documentSystemCode', $request->autoID)
                                    ->where('companySystemID', $request->companySystemID)
                                    ->get();


        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
        $generalLedger = [
                'outputData' => (!empty($generalLedger->toArray())) ? $generalLedger->toArray() : $this->getNotApprovedGlData($request->documentSystemID, $request->autoID, $request->companySystemID), 
                'companyCurrency' => $companyCurrency,
                'accountPaybaleLedgerData' => $accountPaybaleLedgerData,
                'accountReceviableLedgerData' => $accountReceviableLedgerData,
                'itemLedgerData' => $itemLedgerData,
                'employeeLedgerData' => $employeeLedgerData,
                'unbilledLedgerData' => $unbilledLedgerData,
                'bankLedgerData' => $bankLedgerData
            ];

        return $this->sendResponse($generalLedger, 'General Ledger retrieved successfully');
    }

    public function getNotApprovedGlData($documentSystemID, $autoID, $companySystemID)
    {
        $company = Company::where('companySystemID', $companySystemID)->first();
        $masterModel = [
            'employeeSystemID' => \Helper::getEmployeeSystemID(),
            'autoID' => $autoID,
            'documentSystemID' => $documentSystemID,
            'companySystemID' => $companySystemID,
            'companyID' => $company->CompanyID
        ];

        $result = [];
        switch ($documentSystemID) {
            case 3: // GRV
                $grvMaster = GRVMaster::find($autoID);
                if($grvMaster && $grvMaster->approved != -1){
                    $result = GrvGlService::processEntry($masterModel);
                }
                else{
                    $result = [];
                }
                break;
            case 8: // MI - Material issue
                $materialIssueMaster = ItemIssueMaster::find($autoID);
                if($materialIssueMaster && $materialIssueMaster->approved != -1){
                    $result = MaterialIssueGlService::processEntry($masterModel);
                }
                else{
                    $result = [];
                }
                break;
            case 12: // SR - Material Return
                $materialReturnMaster = ItemReturnMaster::find($autoID);
                if($materialReturnMaster && $materialReturnMaster->approved != -1){
                    $result = MaterialReturnGlService::processEntry($masterModel);
                }
                else{
                    $result = [];
                }
                break;
            case 13: // ST - Stock Transfer
                $stockTransferMaster = StockTransfer::find($autoID);
                if($stockTransferMaster && $stockTransferMaster->approved != -1){
                    $result = StockTransferGlService::processEntry($masterModel);
                }
                else{
                   $result = [];
                }
                break;
            case 10: // RS - Stock Receive
                $stockRecieveMaster = StockReceive::find($autoID);
                if($stockRecieveMaster && $stockRecieveMaster->approved != -1) {
                    $result = StockRecieveGlService::processEntry($masterModel);
                }
                else{
                    $result = [];
                }
                break;
            case 61: // INRC - Inventory Reclassififcation
                $inventoryRecMaster = InventoryReclassification::find($autoID);
                if($inventoryRecMaster && $inventoryRecMaster->approved != -1) {
                    $result = InventoryReclassificationGlService::processEntry($masterModel);
                }
                else{
                    $result = [];
                }
                break;
            case 24: // PRN - Purchase Return
                $purchaseReturnMaster = PurchaseReturn::find($autoID);
                if($purchaseReturnMaster && $purchaseReturnMaster->approved != -1){
                    $result = PurchaseReturnGlService::processEntry($masterModel);
                }
                else{
                    $result = [];
                }
                break;
            case 20:
                $custInvMaster = CustomerInvoiceDirect::find($autoID);
                if($custInvMaster && $custInvMaster->approved != -1) {
                    $result = CustomerInvoiceGlService::processEntry($masterModel);
                }else{
                    $result = [];
                }
                break;
            case 7: // SA - Stock Adjustment
                $stockAdjusmentMaster = StockAdjustment::find($autoID);
                if($stockAdjusmentMaster && $stockAdjusmentMaster->approved != -1){
                    $result = StockAdjustmentGlService::processEntry($masterModel);
                }else{
                    $result = [];
                }
                break;
            case 11: // SI - Supplier Invoice
                $supplierInvMaster = BookInvSuppMaster::find($autoID);
                if($supplierInvMaster && $supplierInvMaster->approved != -1){
                    $result = SupplierInvoiceGlService::processEntry($masterModel);
                }else{
                    $result = [];
                }
                break;
            case 15: // DN - Debit Note
                $debitNoteMaster = DebitNote::find($autoID);
                if($debitNoteMaster && $debitNoteMaster->approved != -1) {
                    $result = DebitNoteGlService::processEntry($masterModel);
                }else{
                    $result = [];
                }
                break;
            case 19: // CN - Credit Note
                $creditNoteMaster = CreditNote::find($autoID);
                if($creditNoteMaster && $creditNoteMaster->approved != -1) {
                    $result = CreditNoteGlService::processEntry($masterModel);
                }else{
                    $result = [];
                }
                break;
            case 4: // PV - Payment Voucher
                $pvMaster = PaySupplierInvoiceMaster::find($autoID);
                if($pvMaster && $pvMaster->approved != -1) {
                    $result = PaymentVoucherGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 21: // BRV - Customer Receive Payment
                $custRecPayMaster = CustomerReceivePayment::find($autoID);
                if($custRecPayMaster && $custRecPayMaster->approved != -1){
                    $result = CustomerReceivePaymentGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 17: // JV - Journal Voucher
                $jvMaster = JvMaster::find($autoID);
                if($jvMaster && $jvMaster->approved != -1){
                    $result = JournalVoucherGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 22: // FA - Fixed Asset Master
                $faMaster = FixedAssetMaster::find($autoID);
                if($faMaster && $faMaster->approved != -1){
                    $result = FixedAssetMasterGlService::processEntry($masterModel);
                } else{
                    $result = [];
                }
                break;
            case 23: // FAD - Fixed Asset Depreciation
                $fadMaster = FixedAssetDepreciationMaster::find($autoID);
                if($fadMaster && $fadMaster->approved != -1){
                    $result = FixedAssetDipreciationGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 41: // FADS - Fixed Asset Disposal
                $fadsMaster = AssetDisposalMaster::find($autoID);
                if($fadsMaster && $fadsMaster->approvedYN != -1){
                    $result = FixedAssetDisposalGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 71:
                $deoMaster = DeliveryOrder::find($autoID);
                if($deoMaster && $deoMaster->approvedYN != -1){
                    $result = DeliveryOrderGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 87: // sales return
                $srMaster = SalesReturn::find($autoID);
                if($srMaster && $srMaster->approvedYN != -1){
                    $result = SalesReturnGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 97: // SA - Stock Count
                $saMaster = StockCount::find($autoID);
                if($saMaster && $saMaster->approved != -1){
                    $result = StockCountGlService::processEntry($masterModel);
                } else {
                    $result = [];
                }
                break;
            case 110: // GPOS Sales
                $result = GPOSSalesGlService::processEntry($masterModel);
                break;
            case 111: // RPOS Sales
                $result = RPOSSalesGlService::processEntry($masterModel);
                break;
            default:
                $result = ['status' => false, 'message' => "Document ID not found"];
        }

        $resData  = ((isset($result['status']) && $result['status']) && (isset($result['data']['finalData']) && $result['data']['finalData'])) ? $result['data']['finalData'] : [];

        $glData = [];
        foreach ($resData as $key => $value) {
              $value['supplier'] = SupplierMaster::find($value['supplierCodeSystem']);
              $value['customer'] = CustomerMaster::find($value['supplierCodeSystem']);
              $value['charofaccount'] = ChartOfAccount::find($value['chartOfAccountSystemID']);
              $value['localcurrency'] = CurrencyMaster::find($value['documentLocalCurrencyID']);
              $value['transcurrency'] = CurrencyMaster::find($value['documentTransCurrencyID']);
              $value['rptcurrency'] = CurrencyMaster::find($value['documentRptCurrencyID']);
              $value['documentDate'] = Carbon::parse($value['documentDate'])->format('Y-m-d');

              $glData[] = $value;
        } 

        return $glData;
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
        $dataBase = (isset($input['db'])) ? $input['db'] : "";
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
                $generalLedger = GeneralLedgerInsert::dispatch($masterData, $dataBase);
            }


            if ($input['documentSystemID'] == 3 && $unbilledCount == 0) {
                $grvData = GRVMaster::find($input['documentSystemCode']);

                $masterData = ['documentSystemID' => $input['documentSystemID'],
                               'autoID' => $input['documentSystemCode'],
                               'supplierID' => ($grvData) ? $grvData->supplierID : 0,
                               'companySystemID' => $input['companySystemID'],
                               'employeeSystemID' => $empInfo->employeeSystemID];
                $unbilledGRVInsert = UnbilledGRVInsert::dispatch($masterData, $dataBase);

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

        $fromDate = ((new Carbon($request->fromDate))->format('Y-m-d'));
        $type = $request->currency;
        $company = $request->company;
        $details = $this->generateGLReport($fromDate,$toDate,$type,$company);

        return $this->sendResponse($details,'Posting date changed successfully');

        
    }

    public function generateSegmentGlReportExcel(Request $request)
    {

        $input = $request->all();

        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');
        $fromDate = ((new Carbon($request->fromDate))->format('Y-m-d'));
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
        $companyCode = isset($checkIsGroup->CompanyID) ? $checkIsGroup->CompanyID: null;
        $reportData['companyCode'] = $companyCode;

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

        $segment_data = SegmentMaster::where('companySystemID',$company)->pluck('ServiceLineDes');

        $segment_data->push('Total');

        $segments = SegmentMaster::where('companySystemID',$company)->get();

        $checkIsGroup = Company::find($company);

        $char_ac = ChartOfAccount::where('controlAccountsSystemID',2)->pluck('chartOfAccountSystemID');
        $seg_info = SegmentMaster::where('companySystemID',$company)->pluck('serviceLineSystemID');

        $companyCurrency = \Helper::companyCurrency($company);
        if($companyCurrency) {
            $requestCurrencyLocal = $companyCurrency->localcurrency;
            $requestCurrencyRpt = $companyCurrency->reportingcurrency;
        }

        $collection =  DB::table('erp_generalledger')
        ->whereIn('serviceLineSystemID',$seg_info)
        ->whereIn('chartOfAccountSystemID',$char_ac)
        ->whereDate('documentDate','>=', $fromDate)
        ->whereDate('documentDate','<=', $toDate)
        ->groupBy(['serviceLineSystemID','chartOfAccountSystemID'])
         ->get();

        foreach($entries as $entry)
        {

           


                $data[$i]['glAccountId'] = $entry->AccountCode.' | '.$entry->AccountDescription;
                $data[$i]['AccountCode'] = $entry->AccountCode;
                $data[$i]['AccountDescription'] = $entry->AccountDescription;
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
                                ->whereDate('documentDate','>=', $fromDate)
                                ->whereDate('documentDate','<=', $toDate)
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

    public function updateNotApprovedSegments(Request $request)
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        
        $input = $request->all();

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0) {
            return  "tenant list is empty";
        }

        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            CommonJobService::db_switch($tenantDb);

            ApprovePendingSegments::dispatch($tenantDb);
        }

        return $this->sendResponse([], 'Segments fully approved successfully');
    }

    public function updateNotPostedAssetEntries(Request $request)
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);

        $input = $request->all();

//        $tenants = CommonJobService::tenant_list();
        $tenants = ["database" => "gears_erp_gutech"];
        if(count($tenants) == 0) {
            return  "tenant list is empty";
        }

        foreach ($tenants as $tenant){
//            $tenantDb = $tenant->database;

            $tenantDb = "gears_erp_gutech";
            CommonJobService::db_switch($tenantDb);

            ProccessMissedAccumalatedDepreciation::dispatch($tenantDb);
        }

        return $this->sendResponse([], 'Asset Accumulated Depreciation running!');
    }
}
