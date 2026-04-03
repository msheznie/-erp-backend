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
        return static::rulesForPayload($this->all());
    }


    public static function rulesForPayload(array $input): array
    {
        $base = [
            'type' => 'required|string|in:GPOS,RPOS',
            'company_id' => 'required|integer',
            'shift' => 'required|array',
            'shift.shiftID' => 'required|integer',
            'shift.posType' => 'required|integer|in:1,2',
            'shift.startTime' => 'required|date',
            'shift.endTime' => 'required|date|after:shift.startTime',
            'shift.wareHouseID' => 'required|integer',
            'shift.createdUserName' => 'nullable|string',
            'shift.transactionCurrencyDecimalPlaces' => 'nullable|integer',
        ];

        $typeNorm = null;
        if (array_key_exists('type', $input) && is_string($input['type'])) {
            $typeNorm = strtoupper(trim($input['type']));
        } elseif (array_key_exists('type', $input)) {
            $typeNorm = $input['type'];
        }

        $self = new self;
        if ($typeNorm === 'GPOS') {
            return array_merge($base, $self->gposRules());
        }
        if ($typeNorm === 'RPOS') {
            return array_merge($base, $self->rposRules());
        }
        return $base;
    }

 
    public static function attributeLabels(): array
    {
        return [
            'type' => 'POS type',
            'company_id' => 'Company ID',
            'shift' => 'Shift',
            'shift.shiftID' => 'Shift ID',
            'shift.posType' => 'POS type (shift)',
            'shift.startTime' => 'Shift start time',
            'shift.endTime' => 'Shift end time',
            'shift.wareHouseID' => 'Warehouse ID',
            'shift.createdUserName' => 'Created user name',
            'shift.transactionCurrencyDecimalPlaces' => 'Transaction currency decimal places',

            'invoices' => 'Invoices',
            'invoices.*.invoiceID' => 'Invoice ID',
            'invoices.*.invoiceDate' => 'Invoice date',
            'invoices.*.netTotal' => 'Net total',
            'invoices.*.details' => 'Invoice details',
            'invoices.*.details.*.invoiceID' => 'Invoice line invoice ID',
            'invoices.*.details.*.invoiceDetailsID' => 'Invoice details ID',
            'invoices.*.details.*.qty' => 'Invoice line quantity',
            'invoices.*.details.*.price' => 'Invoice line price',
        ];
    }

    public function attributes(): array
    {
        return static::attributeLabels();
    }

    private function gposRules(): array
    {
        return [
            // Shift-only payloads may omit invoices.
            'invoices'                              => 'nullable|array|min:1',
            'invoices.*.invoiceID'                  => 'required|integer',
            'invoices.*.invoiceDate'                => 'required|date',
            'invoices.*.invoiceCode'                => 'nullable|string',
            'invoices.*.invoiceSequenceNo'          => 'nullable|integer',
            'invoices.*.documentSystemCode'         => 'nullable|string',
            'invoices.*.documentCode'               => 'nullable|string',
            'invoices.*.serialNo'                   => 'nullable|string',
            'invoices.*.customerID'                 => 'nullable|integer',
            'invoices.*.customerCode'               => 'nullable|string',
            'invoices.*.counterID'                  => 'nullable|integer',
            'invoices.*.shiftID'                    => 'nullable|integer',
            'invoices.*.subTotal'                   => 'nullable|numeric',
            'invoices.*.discountAmount'             => 'nullable|numeric',
            'invoices.*.discountPer'                => 'nullable|numeric',
            'invoices.*.netTotal'                   => 'required|numeric',
            'invoices.*.paidAmount'                 => 'nullable|numeric',
            'invoices.*.balanceAmount'              => 'nullable|numeric',
            'invoices.*.cashAmount'                 => 'nullable|numeric',
            'invoices.*.cardAmount'                 => 'nullable|numeric',
            'invoices.*.isCreditSales'              => 'required|boolean',
            'invoices.*.creditSalesAmount'          => 'nullable|numeric',
            'invoices.*.wareHouseAutoID'            => 'nullable|integer',

            // Nested details in each invoice
            'invoices.*.details'                    => 'required|array|min:1',
            'invoices.*.details.*.invoiceID'        => 'required|integer',
            'invoices.*.details.*.invoiceDetailsID' => 'required|integer',
            'invoices.*.details.*.itemAutoID'       => 'nullable|integer',
            'invoices.*.details.*.qty'              => 'required|numeric|min:0',
            'invoices.*.details.*.price'            => 'required|numeric|min:0',
            'invoices.*.details.*.totalCost'        => 'nullable|numeric',
            'invoices.*.details.*.taxAmount'        => 'nullable|numeric',
            'invoices.*.details.*.transactionAmount'=> 'nullable|numeric',

            // Nested payments in each invoice
            'invoices.*.payments'                   => 'nullable|array',
            'invoices.*.payments.*.PaymentID' => 'required|integer',
            'invoices.*.payments.*.invoiceID'       => 'required|integer',
            'invoices.*.payments.*.paymentConfigMasterID' => 'nullable|integer',
            'invoices.*.payments.*.paymentConfigDetailID' => 'nullable|integer',
            'invoices.*.payments.*.GLCode'          => 'nullable|string',
            'invoices.*.payments.*.amount'          => 'required_with:invoices.*.payments|numeric|min:0',
            'invoices.*.payments.*.reference'       => 'nullable|string',

            // Customer payload from POS
            'customer_details'                              => 'nullable|array',
            'customer_details.*.customerAutoID'           => 'required |integer',
            'customer_details.*.customerSystemCode'        => 'required_with:customer_details|string|max:100',
            'customer_details.*.customerName'              => 'required_with:customer_details|string|max:255',

            'customer_details.*.partyCategoryID'           => 'nullable|integer',
            'customer_details.*.masterID'                  => 'nullable|integer',
            'customer_details.*.levelNo'                   => 'nullable|integer',
            'customer_details.*.locationID'                => 'nullable|integer',
            'customer_details.*.customerAddress1'          => 'nullable|string',
            'customer_details.*.customerAddress2'          => 'nullable|string',
            'customer_details.*.customerCountryID'         => 'nullable|integer',
            'customer_details.*.customerCountry'           => 'nullable|string',
            'customer_details.*.IdCardNumber'              => 'nullable|string|max:100',
            'customer_details.*.customerTelephone'         => 'nullable|string|max:50',
            'customer_details.*.customerEmail'             => 'nullable|email',
            'customer_details.*.customerUrl'               => 'nullable|string',
            'customer_details.*.customerFax'               => 'nullable|string|max:50',
            'customer_details.*.secondaryCode'             => 'nullable|string|max:100',
            'customer_details.*.customerCurrencyID'        => 'nullable|integer',
            'customer_details.*.customerCurrency'          => 'nullable|string|max:50',
            'customer_details.*.customerCurrencyDecimalPlaces' => 'nullable|integer',
            'customer_details.*.customerCreditPeriod'      => 'nullable|numeric',
            'customer_details.*.customerCreditLimit'       => 'nullable|numeric',
            'customer_details.*.taxGroupID'                => 'nullable|integer',
            'customer_details.*.vatIdNo'                   => 'nullable|string|max:100',
            'customer_details.*.vatEligible'               => 'nullable|integer|in:0,1',
            'customer_details.*.vatNumber'                 => 'nullable|string|max:100',
            'customer_details.*.vatPercentage'             => 'nullable|numeric',
            'customer_details.*.isActive'                  => 'nullable|integer|in:0,1',
            'customer_details.*.capAmount'                 => 'nullable|numeric',

            // Tax payload from POS
            'tax_details'                              => 'nullable|array',
            'tax_details.*.taxMasterAutoID'            => 'required|integer',
            'tax_details.*.taxDescription'           => 'nullable|string|max:255',
            'tax_details.*.taxShortCode'            => 'required_with:tax_details|string|max:100',

            // Payment config payload from POS
            'payment_config'                              => 'nullable|array',
            'payment_config.*.autoID'                   => 'required_with:payment_config|integer',
            'payment_config.*.description'              => 'required_with:payment_config|string|max:255',
            'payment_config.*.glAccountType'            => 'nullable|string|max:100',
            'payment_config.*.image'                    => 'nullable|string|max:255',
            'payment_config.*.isActive'                 => 'nullable|integer|in:0,1',
            'payment_config.*.sortOrder'                => 'nullable|integer',
            'payment_config.*.selectBoxName'            => 'nullable|string|max:100',

            'payment_config.*.details'                  => 'nullable|array',
            'payment_config.*.details.*.ID'             => 'required_with:payment_config.*.details|integer',
            'payment_config.*.details.*.paymentConfigMasterID' => 'required_with:payment_config.*.details|integer',
            'payment_config.*.details.*.GLCode'         => 'required_with:payment_config.*.details|string|max:100',
            'payment_config.*.details.*.warehouseID'    => 'nullable|integer',
            'payment_config.*.details.*.isAuthRequired' => 'nullable|integer|in:0,1',

  
            // Sales return payload from POS
            'return'                                      => 'nullable|array',
            'return.*.salesReturnID'                      => 'required_with:return|integer|min:1',
            'return.*.invoiceID'                          => 'nullable|integer',
            'return.*.documentSystemCode'                 => 'nullable|string|max:100',
            'return.*.shiftID'                            => 'nullable|integer',
            'return.*.salesReturnDate'                    => 'required_with:return|date',
            'return.*.discountPer'                        => 'nullable|numeric',
            'return.*.discountAmount'                     => 'nullable|numeric',
            'return.*.netTotal'                           => 'required_with:return|numeric',
            'return.*.returnMode'                         => 'nullable|string|max:50',
            'return.*.isRefund'                           => 'nullable|integer|in:0,1',
            'return.*.refundAmount'                       => 'nullable|numeric',
            'return.*.isGroupBasedTax'                    => 'nullable|integer|in:0,1',
            'return.*.generalDiscountPercentage'          => 'nullable|numeric',
            'return.*.generalDiscountAmount'              => 'nullable|numeric',
            'return.*.subTotal'                           => 'nullable|numeric',
            'return.*.customerCurrencyAmount'             => 'nullable|numeric',

            // Return detail lines
            'return.*.details'                            => 'required_with:return|array|min:1',
            'return.*.details.*.salesReturnDetailID'     => 'nullable|integer',
            'return.*.details.*.salesReturnID'           => 'nullable|integer',
            'return.*.details.*.invoiceID'               => 'nullable|integer',
            'return.*.details.*.invoiceDetailID'         => 'nullable|integer',
            'return.*.details.*.itemAutoID'              => 'nullable|integer',
            'return.*.details.*.defaultUOMID'            => 'nullable|integer',
            'return.*.details.*.unitOfMeasure'           => 'nullable|string|max:50',
            'return.*.details.*.UOMID'                   => 'nullable|integer',
            'return.*.details.*.conversionRateUOM'       => 'nullable|numeric',
            'return.*.details.*.qty'                     => 'required_with:return.*.details|numeric|min:0',
            'return.*.details.*.price'                   => 'required_with:return.*.details|numeric|min:0',
            'return.*.details.*.discountPer'             => 'nullable|numeric',
            'return.*.details.*.transactionAmount'       => 'nullable|numeric',
            'return.*.details.*.taxAmount'               => 'nullable|numeric',
        ];
    }

    private function rposRules(): array
    {
        return [
            'menuSales'                                      => 'nullable|array|min:1',
            'menuSales.*.menuSalesID'                        => 'required|integer',
            'menuSales.*.invoiceSequenceNo'                  => 'nullable|integer',
            'menuSales.*.invoiceCode'                        => 'nullable|string|max:100',
            'menuSales.*.wareHouseAutoID'                    => 'nullable|integer',
            'menuSales.*.documentCode'                       => 'nullable|string|max:100',
            'menuSales.*.serialNo'                           => 'nullable|string|max:100',
            'menuSales.*.customerName'                       => 'nullable|string|max:255',
            'menuSales.*.customerTelephone'                  => 'nullable|string|max:50',
            'menuSales.*.customerTypeID'                     => 'nullable|integer',
            'menuSales.*.customerCode'                       => 'nullable|string|max:100',
            'menuSales.*.customerID'                         => 'nullable|integer',
            'menuSales.*.counterID'                          => 'nullable|integer',
            'menuSales.*.shiftID'                            => 'required|integer',
            'menuSales.*.menuSalesDate'                      => 'required|date',
            'menuSales.*.subTotal'                           => 'nullable|numeric',
            'menuSales.*.grossTotal'                         => 'nullable|numeric',
            'menuSales.*.discountPer'                        => 'nullable|numeric',
            'menuSales.*.discountAmount'                     => 'nullable|numeric',
            'menuSales.*.netTotal'                           => 'required|numeric',
            'menuSales.*.paidAmount'                         => 'nullable|numeric',
            'menuSales.*.balanceAmount'                      => 'nullable|numeric',

            // Items per menu sale (your payload key)
            'menuSales.*.menuSales_items'                    => 'nullable|array',
            'menuSales.*.menuSales_items.*.menuSalesItemID'  => 'required_with:menuSales.*.menuSales_items|integer',
            'menuSales.*.menuSales_items.*.menuSalesID'      => 'nullable|integer',
            'menuSales.*.menuSales_items.*.menuID'           => 'nullable|integer',
            'menuSales.*.menuSales_items.*.qty'              => 'required_with:menuSales.*.menuSales_items|numeric|min:0',
            'menuSales.*.menuSales_items.*.menuSalesPrice'   => 'required_with:menuSales.*.menuSales_items|numeric|min:0',

            // Item detail lines (nested under each item)
            'menuSales.*.menuSales_items.*.menuSales_itemdetails' => 'nullable|array',
            'menuSales.*.menuSales_items.*.menuSales_itemdetails.*.menuSalesItemDetailID' => 'required_with:menuSales.*.menuSales_items.*.menuSales_itemdetails|integer',
            'menuSales.*.menuSales_items.*.menuSales_itemdetails.*.menuSalesItemID' => 'nullable|integer',
            'menuSales.*.menuSales_items.*.menuSales_itemdetails.*.menuSalesID' => 'nullable|integer',
            'menuSales.*.menuSales_items.*.menuSales_itemdetails.*.itemAutoID' => 'nullable|integer',
            'menuSales.*.menuSales_items.*.menuSales_itemdetails.*.qty' => 'required_with:menuSales.*.menuSales_items.*.menuSales_itemdetails|numeric|min:0',
            'menuSales.*.menuSales_items.*.menuSales_itemdetails.*.cost' => 'nullable|numeric',

            // Service charge list
            'menuSales.*.menuSales_serviceCharge'            => 'nullable|array',
            'menuSales.*.menuSales_serviceCharge.*.menusalesServiceChargeID' => 'required_with:menuSales.*.menuSales_serviceCharge|integer',
            'menuSales.*.menuSales_serviceCharge.*.menuSalesID' => 'nullable|integer',
            'menuSales.*.menuSales_serviceCharge.*.menuSalesItemID' => 'nullable|integer',
            'menuSales.*.menuSales_serviceCharge.*.serviceChargeAmount' => 'nullable|numeric',

            // Item-level tax details
            'menuSales.*.menuSales_tax_details'              => 'nullable|array',
            'menuSales.*.menuSales_tax_details.*.menuSalesTaxID' => 'required_with:menuSales.*.menuSales_tax_details|integer',
            'menuSales.*.menuSales_tax_details.*.menuSalesID' => 'nullable|integer',
            'menuSales.*.menuSales_tax_details.*.menuSalesItemID' => 'nullable|integer',
            'menuSales.*.menuSales_tax_details.*.taxPercentage' => 'nullable|numeric',
            'menuSales.*.menuSales_tax_details.*.taxAmount' => 'nullable|numeric',

            // Outlet-level tax details
            'menuSales.*.menuSales_outletTax_details'        => 'nullable|array',
            'menuSales.*.menuSales_outletTax_details.*.menuSalesOutletTaxID' => 'required_with:menuSales.*.menuSales_outletTax_details|integer',
            'menuSales.*.menuSales_outletTax_details.*.menuSalesID' => 'nullable|integer',
            'menuSales.*.menuSales_outletTax_details.*.taxPercentage' => 'nullable|numeric',
            'menuSales.*.menuSales_outletTax_details.*.taxAmount' => 'nullable|numeric',

            // Additional RPOS master payloads
            'menuSales_customerType'                            => 'nullable|array',
            'menuSales_customerType.*.customerTypeID'           => 'required_with:menuSales_customerType|integer',
            'menuSales_customerType.*.customerDescription'      => 'nullable|string|max:255',
            'menuSales_customerType.*.displayDescription'       => 'nullable|string|max:255',
            'menuSales_customerType.*.isThirdPartyDelivery'     => 'nullable|integer',
            'menuSales_customerType.*.isDineIn'                 => 'nullable|integer',
            'menuSales_customerType.*.isDefault'                => 'nullable|integer',
            'menuSales_menuMaster'                              => 'nullable|array',
            'menuSales_menuMaster.*.menuMasterID'               => 'required_with:menuSales_menuMaster|integer',
            'menuSales_menuMaster.*.menuMasterDescription'      => 'nullable|string|max:255',
            'menuSales_menuMaster.*.menuImage'                  => 'nullable|string|max:255',
            'menuSales_menuMaster.*.menuCategoryID'             => 'nullable|integer',
            'menuSales_menuMaster.*.menuCost'                   => 'nullable|numeric',
            'menuSales_menuMaster.*.barcode'                    => 'nullable|string|max:100',
            'menuSales_menuMaster.*.sellingPrice'               => 'nullable|numeric',
            'menuSales_menuMaster.*.pricewithoutTax'            => 'nullable|numeric',
            'menuSales_menuMaster.*.revenueGLAutoID'            => 'nullable|integer',
            'menuSales_menuMaster.*.TAXpercentage'              => 'nullable|numeric',
            'menuSales_menuMaster.*.totalTaxAmount'             => 'nullable|numeric',
            'menuSales_menuMaster.*.taxMasterID'                => 'nullable|integer',
            'menuSales_menuMaster.*.totalServiceCharge'         => 'nullable|numeric',
            'menuSales_menuMaster.*.menuStatus'                 => 'nullable|string|max:50',
            'menuSales_menuMaster.*.kotID'                      => 'nullable|integer',
            'menuSales_menuMaster.*.preparationTime'            => 'nullable|numeric',
            'menuSales_menuMaster.*.isPass'                     => 'nullable|integer',
            'menuSales_menuMaster.*.isPack'                     => 'nullable|integer',
            'menuSales_menuMaster.*.isVeg'                      => 'nullable|integer',
            'menuSales_menuMaster.*.isAddOn'                    => 'nullable|integer',
            'menuSales_menuMaster.*.showImageYN'                => 'nullable|integer',
            'menuSales_menuMaster.*.menuSizeID'                 => 'nullable|integer',
            'menuSales_menuMaster.*.sortOrder'                  => 'nullable|integer',
            'menuSales_menuMaster.*.sortOder'                   => 'nullable|integer',
            'menuSales_menuMaster.*.isDeleted'                  => 'nullable|integer',

            'menuSales_menuCategory'                            => 'nullable|array',
            'menuSales_menuCategory.*.menuCategoryID'           => 'required_with:menuSales_menuCategory|integer',
            'menuSales_menuCategory.*.menuCategoryDescription'  => 'nullable|string|max:255',
            'menuSales_menuCategory.*.image'                    => 'nullable|string|max:255',
            'menuSales_menuCategory.*.revenueGLAutoID'          => 'nullable|integer',
            'menuSales_menuCategory.*.topSalesRptYN'            => 'nullable|integer',
            'menuSales_menuCategory.*.sortOrder'                => 'nullable|integer',
            'menuSales_menuCategory.*.isPack'                   => 'nullable|integer',
            'menuSales_menuCategory.*.masterLevelID'            => 'nullable|integer',
            'menuSales_menuCategory.*.levelNo'                  => 'nullable|integer',
            'menuSales_menuCategory.*.bgColor'                  => 'nullable|string|max:30',
            'menuSales_menuCategory.*.isActive'                 => 'nullable|integer',
            'menuSales_menuCategory.*.showImageYN'              => 'nullable|integer',
            'menuSales_menuCategory.*.isDeleted'                => 'nullable|integer',
        ];
    }

}
