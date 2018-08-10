





Route::resource('customerInvoiceDirects', 'CustomerInvoiceDirectController');

Route::resource('customerInvoiceDirectDetails', 'CustomerInvoiceDirectDetailController');

Route::resource('bookInvSuppMasters', 'BookInvSuppMasterController');

Route::resource('bookInvSuppDets', 'BookInvSuppDetController');

Route::resource('directInvoiceDetails', 'DirectInvoiceDetailsController');

Route::resource('paySupplierInvoiceMasters', 'PaySupplierInvoiceMasterController');

Route::resource('paySupplierInvoiceDetails', 'PaySupplierInvoiceDetailController');

Route::resource('directPaymentDetails', 'DirectPaymentDetailsController');

Route::resource('advancePaymentDetails', 'AdvancePaymentDetailsController');

Route::resource('performaDetails', 'PerformaDetailsController');

Route::resource('freeBillingMasterPerformas', 'FreeBillingMasterPerformaController');

Route::resource('ticketMasters', 'TicketMasterController');

Route::resource('fieldMasters', 'FieldMasterController');

Route::resource('taxdetails', 'TaxdetailController');