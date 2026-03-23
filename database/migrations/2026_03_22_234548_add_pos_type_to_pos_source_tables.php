<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration 1 of 2 — Add pos_type discriminator column to all pos_source_* tables.
 *
 * This column distinguishes GPOS (1) from RPOS (2) records.
 * Defaults are set per table based on which POS type populates that table.
 * Shared reference tables (customer, tax, payment) default to 0 (unknown) and
 * must be backfilled before Migration 2 (unique indexes) is run.
 *
 * Run order:
 *   1. Run this migration
 *   2. Backfill pos_type on existing rows
 *   3. Run 2026_03_22_234549_add_composite_unique_indexes_pos_source_tables.php
 */
class AddPosTypeToPosSourceTables extends Migration
{
    public function up(): void
    {
        // --- GPOS-only tables (default 1) ---

        $gposTables = [
            'pos_source_shiftdetails',
            'pos_source_invoice',
            'pos_source_invoicedetail',
            'pos_source_invoicepayments',
            'pos_source_salesreturn',
            'pos_source_salesreturndetails',
        ];

        foreach ($gposTables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'pos_type')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedTinyInteger('pos_type')
                        ->default(1)
                        ->after('companyID')
                        ->comment('1=GPOS 2=RPOS');
                });
            }
        }

        // --- RPOS-only tables (default 2) ---

        $rposTables = [
            'pos_source_menusalesmaster',
            'pos_source_menusalesitems',
            'pos_source_menusalesitemdetails',
            'pos_source_menusalespayments',
            'pos_source_menusalestaxes',
            'pos_source_menusalesoutlettaxes',
            'pos_source_menusalesservicecharge',
        ];

        foreach ($rposTables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'pos_type')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedTinyInteger('pos_type')
                        ->default(2)
                        ->after('companyID')
                        ->comment('1=GPOS 2=RPOS');
                });
            }
        }

        // --- Shared reference tables (default 0 — requires backfill before indexing) ---

        $sharedTables = [
            'pos_source_customermaster',
            'pos_source_taxmaster',
            'pos_source_taxledger',
            'pos_source_paymentglconfigmaster',
            'pos_source_paymentglconfigdetail',
        ];

        foreach ($sharedTables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'pos_type')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedTinyInteger('pos_type')
                        ->default(0)
                        ->after('companyID')
                        ->comment('0=unknown 1=GPOS 2=RPOS — backfill before adding unique index');
                });
            }
        }
    }

    public function down(): void
    {
        $allTables = [
            'pos_source_shiftdetails',
            'pos_source_invoice',
            'pos_source_invoicedetail',
            'pos_source_invoicepayments',
            'pos_source_salesreturn',
            'pos_source_salesreturndetails',
            'pos_source_menusalesmaster',
            'pos_source_menusalesitems',
            'pos_source_menusalesitemdetails',
            'pos_source_menusalespayments',
            'pos_source_menusalestaxes',
            'pos_source_menusalesoutlettaxes',
            'pos_source_menusalesservicecharge',
            'pos_source_customermaster',
            'pos_source_taxmaster',
            'pos_source_taxledger',
            'pos_source_paymentglconfigmaster',
            'pos_source_paymentglconfigdetail',
        ];

        foreach ($allTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'pos_type')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('pos_type');
                });
            }
        }
    }
}
