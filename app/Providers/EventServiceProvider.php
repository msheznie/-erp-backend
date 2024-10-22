<?php

namespace App\Providers;
use App\Events\DocumentCreated;
use App\Events\POServiceLineCheck;
use App\Events\UnverifiedEmailEvent;
use App\Listeners\AfterDocumentCreated;
use App\Listeners\POUpdated;
use App\Listeners\UnverifiedEmailListener;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnMaster;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseReturn;
use App\Models\StockAdjustment;
use App\Models\StockReceive;
use App\Models\StockTransfer;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
        'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\RevokeOldTokens',
        ],
        'Laravel\Passport\Events\RefreshTokenCreated' => [
            'App\Listeners\PruneOldTokens',
        ],
        DocumentCreated::class => [
            AfterDocumentCreated::class
        ],
        POServiceLineCheck::class => [
            POUpdated::class
        ],
        UnverifiedEmailEvent::class => [
            UnverifiedEmailListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //Inventory Documents
        GRVMaster::created(function (GRVMaster $document) {
            event(new DocumentCreated($document));
        });
        StockAdjustment::created(function (StockAdjustment $document) {
            event(new DocumentCreated($document));
        });
        ItemIssueMaster::created(function (ItemIssueMaster $document) {
            event(new DocumentCreated($document));
        });
        StockReceive::created(function (StockReceive $document) {
            event(new DocumentCreated($document));
        });
        ItemReturnMaster::created(function (ItemReturnMaster $document) {
            event(new DocumentCreated($document));
        });
        StockTransfer::created(function (StockTransfer $document) {
            event(new DocumentCreated($document));
        });
        PurchaseReturn::created(function (PurchaseReturn $document) {
            event(new DocumentCreated($document));
        });
        InventoryReclassification::created(function (InventoryReclassification $document) {
            event(new DocumentCreated($document));
        });

        // Account Payable Documents
        DebitNote::created(function (DebitNote $document) {
            event(new DocumentCreated($document));
        });
        BookInvSuppMaster::created(function (BookInvSuppMaster $document) {
            event(new DocumentCreated($document));
        });
        PaySupplierInvoiceMaster::created(function (PaySupplierInvoiceMaster $document) {
            event(new DocumentCreated($document));
        });

        // Account Receivable Documents
        CreditNote::created(function (CreditNote $document) {
            $document->documentSystemID = $document->documentSystemiD;
            event(new DocumentCreated($document));
        });
        CustomerInvoiceDirect::created(function (CustomerInvoiceDirect $document) {
            $document->documentSystemID = $document->documentSystemiD;
            event(new DocumentCreated($document));
        });
        CustomerReceivePayment::created(function (CustomerReceivePayment $document) {
            event(new DocumentCreated($document));
        });

        // General Ledger
        JvMaster::created(function (JvMaster $document) {
            event(new DocumentCreated($document));
        });

        // PO service line check
        ProcumentOrder::updating(function (ProcumentOrder $order){
            event(new POServiceLineCheck($order));
        });

    }
}
