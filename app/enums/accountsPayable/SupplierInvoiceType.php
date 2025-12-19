<?php

namespace App\enums\accountsPayable;

class SupplierInvoiceType
{
    const SUPPLIER_PO_INVOICE = 0;
    const SUPPLIER_DIRECT_INVOICE = 1;
    const SUPPLIER_INVOICE_FOR_DIRECT_GRV = 2;
    const SUPPLIER_ITEM_INVOICE = 3;
    const EMPLOYEE_DIRECT_INVOICE = 4;

    public static function getValues(): array
    {
        return [
            self::SUPPLIER_PO_INVOICE,
            self::SUPPLIER_DIRECT_INVOICE,
            self::SUPPLIER_INVOICE_FOR_DIRECT_GRV,
            self::SUPPLIER_ITEM_INVOICE,
            self::EMPLOYEE_DIRECT_INVOICE
        ];
    }

    public static function getLabels(): array
    {
        return [
            self::SUPPLIER_PO_INVOICE => 'Supplier  PO Invoice',
            self::SUPPLIER_DIRECT_INVOICE => 'Supplier Direct Invoice',
            self::SUPPLIER_INVOICE_FOR_DIRECT_GRV => 'Invoice for Direct GRV',
            self::SUPPLIER_ITEM_INVOICE => 'Supplier Item Invoice',
            self::EMPLOYEE_DIRECT_INVOICE => 'Employee Direct Invoice'
        ];
    }

}
