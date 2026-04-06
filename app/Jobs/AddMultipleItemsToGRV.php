<?php

namespace App\Jobs;

use App\helper\Helper;
use App\helper\TaxService;
use App\Models\ItemCategoryTypeMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\FinanceItemCategorySub;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\ItemMaster;
use App\Services\CompanyDocumentAttachmentService;
use App\Services\Procurement\CategoryValidationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AddMultipleItemsToGRV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $dispatch_db;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $input)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                 self::onConnection('database_main');
            }else{
                 self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->data = $input;
        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $input = $this->data;
        $grvAutoID = $input['grvAutoID'] ?? null;
        $companySystemID = $input['companySystemID'] ?? null;

        try {
            DB::beginTransaction();

            $grvMaster = GRVMaster::where('grvAutoID', $grvAutoID)->first();

            $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($input) {
                                return $query->where('companySystemID',$input['companySystemID'])->where('isAssigned', -1);
                            })
                            ->where('isActive', 1)
                            ->where('itemApprovedYN', 1)
                            ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function ($query) use ($input) {
                                $query->where('financeCategoryMaster', $input['financeCategoryMaster']);
                            })
                            ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function ($query) use ($input) {
                                $query->where('financeCategorySub', $input['financeCategorySub']);
                            })
                            ->whereHas('item_category_type', function ($query) {
                                $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::allItemTypes());
                            })
                            ->whereDoesntHave('grv_details', function ($query) use ($input) {
                                $query->where('grvAutoID', $input['grvAutoID']);
                            })
                            ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])
                            ->get();

            if (CategoryValidationService::shouldEnforceSingleCategory($grvMaster->companySystemID, (int) $grvMaster->documentSystemID)) {
                $existingRow = GRVDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('grvAutoID', $grvAutoID)
                    ->first();

                $allowedCategory = $existingRow ? $existingRow->itemFinanceCategoryID : null;
                if ($allowedCategory === null && $itemMasters->isNotEmpty()) {
                    $allowedCategory = $itemMasters->first()->financeCategoryMaster;
                }

                if ($allowedCategory !== null) {
                    $itemMasters = $itemMasters->where('financeCategoryMaster', $allowedCategory);
                }
            }

            foreach ($itemMasters as $itemMaster) {
                $itemAssigned = ItemAssigned::where('itemCodeSystem', $itemMaster->itemCodeSystem)
                    ->where('companySystemID', $input['companySystemID'])
                    ->where('isAssigned', -1)
                    ->where('isActive', 1)
                    ->with(['item_master'])
                    ->first();

                if (empty($itemAssigned)) {
                    continue;
                }

                $detailPayload = $this->buildGrvDetailPayload(
                    $grvMaster,
                    $itemAssigned,
                    $input['empID'] ?? null,
                    $input['employeeSystemID'] ?? null
                );

                if (!empty($detailPayload)) {
                    GRVDetails::create($detailPayload);
                }
            }

            GRVMaster::where('grvAutoID', $input['grvAutoID'])->update(['isBulkItemJobRun' => 0]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('AddMultipleItemsToGRV failed', [
                'grvAutoID' => $grvAutoID,
                'companySystemID' => $companySystemID,
                'db' => $db,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        } finally {
            $this->resetBulkJobFlag($grvAutoID, $companySystemID, $db);
        }
    }

    private function resetBulkJobFlag($grvAutoID, $companySystemID, $db)
    {
        if (empty($grvAutoID)) {
            return;
        }

        try {
            GRVMaster::where('grvAutoID', $grvAutoID)->update(['isBulkItemJobRun' => 0]);
        } catch (\Throwable $exception) {
            Log::error('AddMultipleItemsToGRV failed to reset bulk job flag', [
                'grvAutoID' => $grvAutoID,
                'companySystemID' => $companySystemID,
                'db' => $db,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }
    }

    private function buildGrvDetailPayload($grvMaster, $itemAssigned, $empID, $employeeSystemID)
    {
        $financeCategorySub = FinanceItemCategorySub::find($itemAssigned->financeCategorySub);
        if (empty($financeCategorySub)) {
            return [];
        }

        $unitCost = 0;
        $noQty = 1;
        $currency = Helper::convertAmountToLocalRpt($grvMaster->documentSystemID, $grvMaster->grvAutoID, $unitCost);

        $payload = [];
        $payload['grvAutoID'] = $grvMaster->grvAutoID;
        $payload['companySystemID'] = $grvMaster->companySystemID;
        $payload['companyID'] = $grvMaster->companyID;
        $payload['serviceLineCode'] = $grvMaster->serviceLineCode;
        $payload['purchaseOrderMastertID'] = 0;
        $payload['purchaseOrderDetailsID'] = 0;
        $payload['itemCode'] = $itemAssigned->itemCodeSystem;
        $payload['trackingType'] = optional($itemAssigned->item_master)->trackingType;
        $payload['itemPrimaryCode'] = $itemAssigned->itemPrimaryCode;
        $payload['itemDescription'] = $itemAssigned->itemDescription;
        $payload['itemFinanceCategoryID'] = $itemAssigned->financeCategoryMaster;
        $payload['itemFinanceCategorySubID'] = $itemAssigned->financeCategorySub;
        $payload['financeGLcodebBSSystemID'] = $financeCategorySub->financeGLcodebBSSystemID;
        $payload['financeGLcodebBS'] = $financeCategorySub->financeGLcodebBS;
        $payload['financeGLcodePLSystemID'] = $financeCategorySub->financeGLcodePLSystemID;
        $payload['financeGLcodePL'] = $financeCategorySub->financeGLcodePL;
        $payload['includePLForGRVYN'] = $financeCategorySub->includePLForGRVYN;
        $payload['supplierPartNumber'] = $itemAssigned->secondaryItemCode;
        $payload['unitOfMeasure'] = $itemAssigned->itemUnitOfMeasure;
        $payload['wasteQty'] = 0;
        $payload['noQty'] = $noQty;
        $payload['prvRecievedQty'] = 0;
        $payload['poQty'] = 0;
        $payload['unitCost'] = $unitCost;
        $payload['discountPercentage'] = 0;
        $payload['discountAmount'] = 0;
        $payload['netAmount'] = $unitCost * $noQty;
        $payload['comment'] = null;
        $payload['supplierDefaultCurrencyID'] = $grvMaster->supplierDefaultCurrencyID;
        $payload['supplierDefaultER'] = $grvMaster->supplierDefaultER;
        $payload['supplierItemCurrencyID'] = $grvMaster->supplierTransactionCurrencyID;
        $payload['foreignToLocalER'] = $grvMaster->supplierTransactionER;
        $payload['companyReportingCurrencyID'] = $grvMaster->companyReportingCurrencyID;
        $payload['companyReportingER'] = $grvMaster->companyReportingER;
        $payload['localCurrencyID'] = $grvMaster->localCurrencyID;
        $payload['localCurrencyER'] = $grvMaster->localCurrencyER;
        $payload['addonDistCost'] = 0;
        $payload['GRVcostPerUnitLocalCur'] = Helper::roundValue($currency['localAmount']);
        $payload['GRVcostPerUnitSupDefaultCur'] = Helper::roundValue($currency['defaultAmount']);
        $payload['GRVcostPerUnitSupTransCur'] = Helper::roundValue($unitCost);
        $payload['GRVcostPerUnitComRptCur'] = Helper::roundValue($currency['reportingAmount']);
        $payload['landingCost_LocalCur'] = Helper::roundValue($currency['localAmount']);
        $payload['landingCost_TransCur'] = Helper::roundValue($unitCost);
        $payload['landingCost_RptCur'] = Helper::roundValue($currency['reportingAmount']);
        $payload['vatRegisteredYN'] = 0;
        $payload['supplierVATEligible'] = 0;
        $payload['VATPercentage'] = 0;
        $payload['VATAmount'] = 0;
        $payload['VATAmountLocal'] = 0;
        $payload['VATAmountRpt'] = 0;
        $payload['logisticsAvailable'] = 0;
        $payload['createdPcID'] = gethostname();
        $payload['createdUserID'] = $empID;
        $payload['createdUserSystemID'] = $employeeSystemID;

        if ($grvMaster->vatRegisteredYN) {
            $vatDetails = TaxService::getVATDetailsByItem($grvMaster->companySystemID, $payload['itemCode'], $grvMaster->supplierID);
            $payload['VATPercentage'] = $vatDetails['percentage'];
            $payload['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $payload['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $payload['VATAmount'] = 0;
            $payload['VATAmountLocal'] = 0;
            $payload['VATAmountRpt'] = 0;
        }

        return $payload;
    }
}
