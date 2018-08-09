





Route::resource('customerInvoiceDirects', 'CustomerInvoiceDirectController');

Route::resource('customerInvoiceDirectDetails', 'CustomerInvoiceDirectDetailController');

Route::resource('bookInvSuppMasters', 'BookInvSuppMasterController');

Route::resource('bookInvSuppDets', 'BookInvSuppDetController');

Route::resource('directInvoiceDetails', 'DirectInvoiceDetailsController');

Route::resource('paySupplierInvoiceMasters', 'PaySupplierInvoiceMasterController');

Route::resource('paySupplierInvoiceDetails', 'PaySupplierInvoiceDetailController');

Route::resource('directPaymentDetails', 'DirectPaymentDetailsController');

Route::resource('advancePaymentDetails', 'AdvancePaymentDetailsController');