

Route::resource('erpAddresses', 'ErpAddressController');

Route::resource('poPaymentTerms', 'PoPaymentTermsController');

Route::resource('poAdvancePayments', 'PoAdvancePaymentController');

Route::resource('poPaymentTermTypes', 'PoPaymentTermTypesController');

Route::resource('gRVMasters', 'GRVMasterController');

Route::resource('gRVDetails', 'GRVDetailsController');

Route::resource('purchaseOrderProcessDetails', 'PurchaseOrderProcessDetailsController');

Route::resource('taxAuthorities', 'TaxAuthorityController');

Route::resource('taxes', 'TaxController');

Route::resource('taxTypes', 'TaxTypeController');

Route::resource('taxFormulaMasters', 'TaxFormulaMasterController');

Route::resource('taxFormulaDetails', 'TaxFormulaDetailController');

Route::resource('advancePaymentDetails', 'AdvancePaymentDetailsController');