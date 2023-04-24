





Route::resource('poCategories', 'PoCategoryController');

Route::resource('barcodeConfigurations', 'BarcodeConfigurationController');

Route::resource('thirdPartySystems', 'ThirdPartySystemsController');

Route::resource('thirdPartyIntegrationKeys', 'ThirdPartyIntegrationKeysController');

Route::resource('iOUBookingMasters', 'IOUBookingMasterController');

Route::resource('pricingScheduleDetails', 'PricingScheduleDetailController');

Route::resource('bidDocumentVerifications', 'BidDocumentVerificationController');

Route::resource('srmBidDocumentattachments', 'SrmBidDocumentattachmentsController');

Route::resource('bidEvaluationSelections', 'BidEvaluationSelectionController');

Route::resource('commercialBidRankingItems', 'CommercialBidRankingItemsController');

Route::resource('tenderFinalBids', 'TenderFinalBidsController');

Route::resource('documentModifyRequests', 'DocumentModifyRequestController');

Route::resource('documentModifyRequestDetails', 'DocumentModifyRequestDetailController');


Route::resource('calendarDatesDetailEditLogs', 'CalendarDatesDetailEditLogController');

Route::resource('procumentActivityEditLogs', 'ProcumentActivityEditLogController');