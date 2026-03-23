<?php

namespace App\Http\Requests\POS;

use Illuminate\Foundation\Http\FormRequest;

class PosShiftSyncRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $base = [
            'type'                    => 'required|string|in:GPOS,RPOS',
            'shift'                   => 'required|array',
            'shift.shiftID'           => 'required|string|max:100',
            'shift.companyID'         => 'required|integer',
            'shift.posTypeID'         => 'required|integer|in:1,2',
            'shift.openTime'          => 'required|date',
            'shift.closeTime'         => 'required|date|after:shift.openTime',
            'shift.totalSales'        => 'required|numeric|min:0',
            'shift.totalReturn'       => 'required|numeric|min:0',
            'shift.totalCash'         => 'nullable|numeric|min:0',
            'shift.totalCard'         => 'nullable|numeric|min:0',
            'shift.status'            => 'nullable|integer',
            'taxes'                   => 'nullable|array',
            'taxes.*.taxCode'         => 'required_with:taxes|string',
            'taxes.*.taxName'         => 'nullable|string',
            'taxes.*.taxRate'         => 'required_with:taxes|numeric|min:0',
            'payments'                => 'nullable|array',
            'payments.*.paymentID'    => 'required_with:payments|string',
            'payments.*.paymentName'  => 'required_with:payments|string',
            'payments.*.glCode'       => 'nullable|string',
        ];

        if ($this->input('type') === 'GPOS') {
            return array_merge($base, $this->gposRules());
        }

        return array_merge($base, $this->rposRules());
    }

    private function gposRules(): array
    {
        return [
            'invoices'                              => 'required|array|min:1',
            'invoices.*.invoiceID'                  => 'required|string',
            'invoices.*.invoiceDate'                => 'required|date',
            'invoices.*.totalAmount'                => 'required|numeric',
            'invoices.*.vatAmount'                  => 'nullable|numeric',
            'invoices.*.discountAmount'             => 'nullable|numeric',
            'invoices.*.isCreditSales'              => 'nullable|boolean',
            'invoices.*.customerID'                 => 'nullable|string',
            'invoices.*.paymentMethod'              => 'nullable|string',
            'invoices.*.details'                    => 'required|array|min:1',
            'invoices.*.details.*.itemCode'         => 'required|string',
            'invoices.*.details.*.quantity'         => 'required|numeric|min:0',
            'invoices.*.details.*.unitPrice'        => 'required|numeric|min:0',
            'invoices.*.details.*.lineTotal'        => 'nullable|numeric',
            'invoices.*.details.*.taxCode'          => 'nullable|string',
            'invoices.*.details.*.warehouseID'      => 'nullable|integer',
            'salesReturns'                          => 'nullable|array',
            'salesReturns.*.returnID'               => 'required_with:salesReturns|string',
            'salesReturns.*.originalInvoiceID'      => 'nullable|string',
            'salesReturns.*.returnDate'             => 'nullable|date',
            'salesReturns.*.totalAmount'            => 'required_with:salesReturns|numeric',
            'salesReturns.*.details'                => 'required_with:salesReturns|array|min:1',
            'salesReturns.*.details.*.itemCode'     => 'required|string',
            'salesReturns.*.details.*.quantity'     => 'required|numeric|min:0',
            'salesReturns.*.details.*.unitPrice'    => 'nullable|numeric|min:0',
            'customers'                             => 'nullable|array',
            'customers.*.customerID'                => 'required_with:customers|string',
            'customers.*.customerName'              => 'required_with:customers|string',
            'customers.*.phone'                     => 'nullable|string',
            'customers.*.email'                     => 'nullable|email',
        ];
    }

    private function rposRules(): array
    {
        return [
            'menuSales'                            => 'required|array|min:1',
            'menuSales.*.menuSalesID'              => 'required|string',
            'menuSales.*.openTime'                 => 'required|date',
            'menuSales.*.closeTime'                => 'required|date',
            'menuSales.*.totalAmount'              => 'required|numeric',
            'menuSales.*.tableNumber'              => 'nullable|string',
            'menuSales.*.items'                    => 'required|array|min:1',
            'menuSales.*.items.*.menuItemCode'     => 'required|string',
            'menuSales.*.items.*.menuItemName'     => 'nullable|string',
            'menuSales.*.items.*.quantity'         => 'required|numeric|min:0',
            'menuSales.*.items.*.unitPrice'        => 'required|numeric|min:0',
            'menuSales.*.items.*.lineTotal'        => 'nullable|numeric',
            'menuSales.*.items.*.taxCode'          => 'nullable|string',
        ];
    }
}
