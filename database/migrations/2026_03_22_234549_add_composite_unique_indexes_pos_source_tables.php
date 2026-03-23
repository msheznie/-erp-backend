<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration 2 of 2 — Add composite unique indexes to all pos_source_* tables.
 *
 * PREREQUISITE: Run 2026_03_22_234548_add_pos_type_to_pos_source_tables.php first,
 * then backfill pos_type on all existing rows to remove duplicates before running this.
 *
 * Backfill SQL (run manually after Migration 1):
 *
 *   -- shiftdetails: sync posType → pos_type if posType column exists
 *   UPDATE pos_source_shiftdetails SET pos_type = posType WHERE posType IN (1, 2);
 *
 *   -- GPOS-only tables: already defaulted to 1, no action needed
 *   -- RPOS-only tables: already defaulted to 2, no action needed
 *
 *   -- Shared reference tables: determine the correct type per record and update.
 *   -- Rows with pos_type = 0 must be resolved before this migration can succeed.
 */
class AddCompositeUniqueIndexesPosSourceTables extends Migration
{
    public function up(): void
    {
        // --- Group 1: Shift ---
        if (Schema::hasTable('pos_source_shiftdetails')) {
            Schema::table('pos_source_shiftdetails', function (Blueprint $table) {
                $table->unique(['shiftID', 'pos_type', 'companyID'], 'uq_shift_type_company');
            });
        }

        // --- Group 2: GPOS Invoice ---
        if (Schema::hasTable('pos_source_invoice')) {
            Schema::table('pos_source_invoice', function (Blueprint $table) {
                $table->unique(['invoiceID', 'pos_type', 'companyID'], 'uq_invoice_type_company');
            });
        }

        if (Schema::hasTable('pos_source_invoicepayments')) {
            Schema::table('pos_source_invoicepayments', function (Blueprint $table) {
                $table->unique(['PaymentID', 'pos_type'], 'uq_inv_payment_type');
            });
        }

        // --- Group 3: Sales Returns ---
        if (Schema::hasTable('pos_source_salesreturn')) {
            Schema::table('pos_source_salesreturn', function (Blueprint $table) {
                $table->unique(['salesReturnID', 'pos_type', 'companyID'], 'uq_return_type_company');
            });
        }

        // --- Group 4: RPOS Menu Sales ---
        if (Schema::hasTable('pos_source_menusalesmaster')) {
            Schema::table('pos_source_menusalesmaster', function (Blueprint $table) {
                $table->unique(['menuSalesID', 'companyID'], 'uq_menusales_company');
            });
        }

        if (Schema::hasTable('pos_source_menusalesitems')) {
            Schema::table('pos_source_menusalesitems', function (Blueprint $table) {
                $table->unique(['menuSalesItemID', 'menuSalesID'], 'uq_menusales_item');
            });
        }

        if (Schema::hasTable('pos_source_menusalespayments')) {
            Schema::table('pos_source_menusalespayments', function (Blueprint $table) {
                $table->unique(['menuSalesPaymentID', 'menuSalesID'], 'uq_menusales_payment');
            });
        }

        if (Schema::hasTable('pos_source_menusalestaxes')) {
            Schema::table('pos_source_menusalestaxes', function (Blueprint $table) {
                $table->unique(['menuSalesTaxID', 'menuSalesID'], 'uq_menusales_tax');
            });
        }

        if (Schema::hasTable('pos_source_menusalesoutlettaxes')) {
            Schema::table('pos_source_menusalesoutlettaxes', function (Blueprint $table) {
                $table->unique(['menuSalesOutletTaxID', 'menuSalesID'], 'uq_menusales_outlet_tax');
            });
        }

        if (Schema::hasTable('pos_source_menusalesservicecharge')) {
            Schema::table('pos_source_menusalesservicecharge', function (Blueprint $table) {
                $table->unique(['menusalesServiceChargeID', 'menuSalesID'], 'uq_menusales_svc');
            });
        }

        // --- Group 5: Shared Reference Tables ---
        if (Schema::hasTable('pos_source_customermaster')) {
            Schema::table('pos_source_customermaster', function (Blueprint $table) {
                $table->unique(['customerSystemCode', 'companyID', 'pos_type'], 'uq_customer_type_company');
            });
        }

        if (Schema::hasTable('pos_source_taxmaster')) {
            Schema::table('pos_source_taxmaster', function (Blueprint $table) {
                $table->unique(['taxShortCode', 'companyID', 'pos_type'], 'uq_tax_type_company');
            });
        }

        if (Schema::hasTable('pos_source_taxledger')) {
            Schema::table('pos_source_taxledger', function (Blueprint $table) {
                $table->unique(['taxLedgerAutoID', 'pos_type', 'companyID'], 'uq_taxledger_type_company');
            });
        }

        if (Schema::hasTable('pos_source_paymentglconfigmaster')) {
            Schema::table('pos_source_paymentglconfigmaster', function (Blueprint $table) {
                $table->unique(['autoID', 'pos_type', 'companyID'], 'uq_payconfig_type_company');
            });
        }

        if (Schema::hasTable('pos_source_paymentglconfigdetail')) {
            Schema::table('pos_source_paymentglconfigdetail', function (Blueprint $table) {
                $table->unique(['ID', 'paymentConfigMasterID', 'pos_type'], 'uq_payconfig_detail_type');
            });
        }
    }

    public function down(): void
    {
        $drops = [
            'pos_source_shiftdetails' => 'uq_shift_type_company',
            'pos_source_invoice' => 'uq_invoice_type_company',
            'pos_source_invoicepayments' => 'uq_inv_payment_type',
            'pos_source_salesreturn' => 'uq_return_type_company',
            'pos_source_menusalesmaster' => 'uq_menusales_company',
            'pos_source_menusalesitems' => 'uq_menusales_item',
            'pos_source_menusalespayments' => 'uq_menusales_payment',
            'pos_source_menusalestaxes' => 'uq_menusales_tax',
            'pos_source_menusalesoutlettaxes' => 'uq_menusales_outlet_tax',
            'pos_source_menusalesservicecharge' => 'uq_menusales_svc',
            'pos_source_customermaster' => 'uq_customer_type_company',
            'pos_source_taxmaster' => 'uq_tax_type_company',
            'pos_source_taxledger' => 'uq_taxledger_type_company',
            'pos_source_paymentglconfigmaster' => 'uq_payconfig_type_company',
            'pos_source_paymentglconfigdetail' => 'uq_payconfig_detail_type',
        ];

        foreach ($drops as $table => $index) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($index) {
                    $t->dropUnique($index);
                });
            }
        }
    }
}
