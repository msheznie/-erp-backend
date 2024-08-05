<?php

namespace App\enums\paymentVoucher;

class PaymentVoucherType
{
    const SUPPLIER_PAYMENT = 2;
    const DIRECT_PAYMENT = 3;
    const SUPPLIER_ADVANCE_PAYMENT = 5;
    const EMPLOYEE_PAYMWNT = 6;
    const EMPLOYEE_ADVANCE_PAYMENT = 7;

    public static function getValues(): array
    {
        return [
            self::SUPPLIER_PAYMENT,
            self::DIRECT_PAYMENT,
            self::SUPPLIER_ADVANCE_PAYMENT,
            self::EMPLOYEE_PAYMWNT,
            self::EMPLOYEE_ADVANCE_PAYMENT
        ];
    }

    public static function getLabels(): array
    {
        return [
            self::SUPPLIER_PAYMENT => 'Supplier Payment',
            self::DIRECT_PAYMENT => 'Direct Payment',
            self::SUPPLIER_ADVANCE_PAYMENT => 'Supplier Advance Payment',
            self::EMPLOYEE_PAYMWNT => 'Employee Payment',
            self::EMPLOYEE_ADVANCE_PAYMENT => 'Employee Advance Payment',
        ];
    }

    public static function getSlugs(): array
    {
        return [
            self::SUPPLIER_PAYMENT => 'supplier-payment',
            self::DIRECT_PAYMENT => 'direct-payment',
            self::SUPPLIER_ADVANCE_PAYMENT => 'supplier-advance-payment-direct',
            self::EMPLOYEE_PAYMWNT => 'employee-payment',
            self::EMPLOYEE_ADVANCE_PAYMENT => 'employee-advance-payment',
        ];
    }

    public static function getValueLabelPairs(): array
    {
        return [
            ['value' => self::SUPPLIER_PAYMENT, 'label' => 'Supplier Payment'],
            ['value' => self::DIRECT_PAYMENT, 'label' => 'Direct Payment'],
            ['value' => self::SUPPLIER_ADVANCE_PAYMENT, 'label' => 'Supplier Advance Payment'],
            ['value' => self::EMPLOYEE_PAYMWNT, 'label' => 'Employee Payment'],
            ['value' => self::EMPLOYEE_ADVANCE_PAYMENT, 'label' => 'Employee Advance Payment'],
        ];
    }

    public static function  getSlugById(int $id) {
        $labels = self::getSlugs();
        return $labels[$id] ?? null;
    }

}
