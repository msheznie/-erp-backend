<?php 


//approval
Route::group([], function () {
    //salesQuotationApproval
    Route::post('getSalesQuotationApprovals', 'QuotationMasterAPIController@getSalesQuotationApprovals')->name("Get sales quotation approvals");
    Route::post('getApprovedSalesQuotationForUser', 'QuotationMasterAPIController@getApprovedSalesQuotationForUser')->name("Get approved sales quotation for user");
    Route::post('approveSalesQuotation', 'QuotationMasterAPIController@approveSalesQuotation')->name("Approve sales quotation");
    Route::post('rejectSalesQuotation', 'QuotationMasterAPIController@rejectSalesQuotation')->name("Reject sales quotation");

    Route::get('getSalesQuotationMasterRecord', 'QuotationMasterAPIController@getSalesQuotationMasterRecord')->name("Get sales quotation master records");

    //salesDeliveryOrderApproval
    Route::post('getDeliveryOrderApprovals', 'DeliveryOrderAPIController@getDeliveryOrderApprovals')->name("Get delivery order approvals");
    Route::post('getApprovedDeliveryOrderForUser', 'DeliveryOrderAPIController@getApprovedDeliveryOrderForUser')->name("Get approved delivery order for user");
    Route::post('rejectDeliveryOrder', 'DeliveryOrderAPIController@rejectDeliveryOrder')->name("Reject delivery order");
    Route::post('approveDeliveryOrder', 'DeliveryOrderAPIController@approveDeliveryOrder')->name("Approve delivery order");

    //salesReturnApproval
    Route::post('getSalesReturnApprovals', 'SalesReturnAPIController@getSalesReturnApprovals')->name("Get sales return approvals");
    Route::post('getApprovedSalesReturnForUser', 'SalesReturnAPIController@getApprovedSalesReturnForUser')->name("Get approved sales return for user");
    Route::post('rejectSalesReturn', 'SalesReturnAPIController@rejectSalesReturn')->name("Reject sales return");
    Route::get('getSalesReturnRecord', 'SalesReturnAPIController@getSalesReturnRecord')->name("Get sales return record");


});