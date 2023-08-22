<?php

/**
 * This file contains SRM module related routes
 * 
 * 
 * */


Route::post('store_tender_documents', 'DocumentAttachmentsAPIController@storeTenderDocuments')->name("Store tender documents");
Route::post('tenderBIdDocApproveal', 'DocumentAttachmentsAPIController@tenderBIdDocApproveal');
Route::post('tenderBIdDocTypeApproveal', 'DocumentAttachmentsAPIController@tenderBIdDocTypeApproveal');
Route::post('tenderBIdDocSubmission', 'DocumentAttachmentsAPIController@tenderBIdDocSubmission');
Route::post('checkTenderBidDocExist', 'DocumentAttachmentsAPIController@checkTenderBidDocExist');

Route::post('getAllProcurementCategory', 'TenderProcurementCategoryController@getAllProcurementCategory');
Route::resource('procurement_categories', 'TenderProcurementCategoryController');

Route::post('get_all_calendar_dates', 'TenderCalendarDatesController@getAllCalendarDates');
Route::resource('calendar_date', 'TenderCalendarDatesController');

Route::post('get-tender-committee', 'TenderCommitteeController@getAll');
Route::post('add-employees-to-tender-committee', 'TenderCommitteeController@assignEmployeesToTenderCommitee');
Route::post('delete-tender-committee', 'TenderCommitteeController@delete');

Route::resource('tender-committee', 'TenderCommitteeController');
Route::post('tender-committee/{id}','TenderCommitteeController@update');
Route::post('getActiveEmployeesForBid','TenderCommitteeController@getActiveEmployeesForBid')->name("Get active employees for bid");

Route::resource('tender-bid-employee-details','TenderBidEmployeeDetailsController');
Route::post('tender-bid-employee-get-all','TenderBidEmployeeDetailsController@getEmployees')->name("Get tender bid employees");
Route::post('tender-bid-employee-delete','TenderBidEmployeeDetailsController@deleteEmp')->name("Delete tender bid employee");
Route::post('tender-bid-employee-approval-count','TenderBidEmployeeDetailsController@getEmployeesApproval');

Route::post('getTenderBits', 'BidSubmissionMasterAPIController@getTenderBits');
Route::post('getTenderBidGoNoGoResponse', 'BidSubmissionMasterAPIController@getTenderBidGoNoGoResponse');
Route::post('updateTenderBidGoNoGoResponse', 'BidSubmissionMasterAPIController@updateTenderBidGoNoGoResponse');

Route::post('getTenderBidFormats', 'TenderBidFormatMasterAPIController@getTenderBidFormats');
Route::post('storeBidFormat', 'TenderBidFormatMasterAPIController@storeBidFormat');
Route::post('loadBidFormatMaster', 'TenderBidFormatMasterAPIController@loadBidFormatMaster');
Route::post('addPriceBidDetail', 'TenderBidFormatMasterAPIController@addPriceBidDetail');
Route::post('updatePriceBidDetail', 'TenderBidFormatMasterAPIController@updatePriceBidDetail');
Route::post('updateBidFormat', 'TenderBidFormatMasterAPIController@updateBidFormat');
Route::post('deletePriceBideDetail', 'TenderBidFormatMasterAPIController@deletePriceBideDetail');
Route::post('deletePriceBidMaster', 'TenderBidFormatMasterAPIController@deletePriceBidMaster');

Route::post('getBitFormatItems', 'TenderBidFormatMasterAPIController@getBitFormatItems');


Route::post('getTenderMasterList', 'TenderMasterAPIController@getTenderMasterList')->name("Get tender master list");
Route::post('getTenderDropDowns', 'TenderMasterAPIController@getTenderDropDowns')->name("Get tender drop downs");
Route::post('createTender', 'TenderMasterAPIController@createTender')->name("Create tender");
Route::post('deleteTenderMaster', 'TenderMasterAPIController@deleteTenderMaster')->name("Delete tender master");
Route::post('getTenderMasterData', 'TenderMasterAPIController@getTenderMasterData')->name("Get tender master data");
Route::post('loadTenderSubCategory', 'TenderMasterAPIController@loadTenderSubCategory')->name("Load tender sub category");
Route::post('loadTenderSubActivity', 'TenderMasterAPIController@loadTenderSubActivity')->name("Load tender sub activity");
Route::post('loadTenderBankAccount', 'TenderMasterAPIController@loadTenderBankAccount')->name("Load tender bank account");
Route::post('updateTender', 'TenderMasterAPIController@updateTender')->name("Update tender");
Route::post('getPurchasedTenderList', 'TenderMasterAPIController@getPurchasedTenderList');
Route::post('getPurchaseTenderMasterData', 'TenderMasterAPIController@getPurchaseTenderMasterData');
Route::post('tenderCommiteApproveal', 'TenderMasterAPIController@tenderCommiteApproveal');
Route::post('getTenderTechniqalEvaluation', 'TenderMasterAPIController@getTenderTechniqalEvaluation');


Route::post('addFormula', 'TenderBidFormatMasterAPIController@addFormula');
Route::post('formulaGenerate', 'TenderBidFormatMasterAPIController@formulaGenerate');
Route::post('tenderBidDocVerification', 'TenderMasterAPIController@tenderBidDocVerification');

Route::post('getMainWorksList', 'TenderMainWorksAPIController@getMainWorksList')->name("Get main works list");
Route::post('addMainWorks', 'TenderMainWorksAPIController@addMainWorks');
Route::get('downloadMainWorksUploadTemplate', 'TenderMainWorksAPIController@downloadMainWorksUploadTemplate');
Route::post('mainWorksItemsUpload', 'TenderMainWorksAPIController@mainWorksItemsUpload');
Route::post('deleteMainWorks', 'TenderMainWorksAPIController@deleteMainWorks');
Route::post('updateWorkOrderDescription', 'TenderMainWorksAPIController@updateWorkOrderDescription')->name("Update work order description");

Route::post('getFaqFormData', 'TenderMasterAPIController@getFaqFormData')->name("Get FAQ form data");
Route::post('createFaq', 'TenderFaqAPIController@createFaq')->name("Create FAQ");
Route::post('getFaqList', 'TenderFaqAPIController@getFaqList')->name("Get FAQ list");
Route::post('getFaq', 'TenderFaqAPIController@getFaq')->name("Get FAQ");
Route::post('deleteFaq', 'TenderFaqAPIController@deleteFaq')->name("Delete FAQ");

Route::post('loadTenderBoqItems', 'TenderBoqItemsAPIController@loadTenderBoqItems')->name("Load tender boq items");
Route::post('addTenderBoqItems', 'TenderBoqItemsAPIController@addTenderBoqItems')->name("Add tender boq items");
Route::post('updateTenderBoqItem', 'TenderBoqItemsAPIController@updateTenderBoqItem')->name("Update tender boq item");
Route::get('downloadTenderBoqItemUploadTemplate', 'TenderBoqItemsAPIController@downloadTenderBoqItemUploadTemplate')->name("Download tender boq item upload template");
Route::post('deleteTenderBoqItem', 'TenderBoqItemsAPIController@deleteTenderBoqItem')->name("Delete tender boq item");
Route::post('tenderBoqItemsUpload', 'TenderBoqItemsAPIController@tenderBoqItemsUpload')->name("Tender boq items upload");
Route::post('getPreBidClarifications', 'TenderBidClarificationsAPIController@getPreBidClarifications');
Route::post('getPreBidClarificationsResponse', 'TenderBidClarificationsAPIController@getPreBidClarificationsResponse');
Route::post('createResponse', 'TenderBidClarificationsAPIController@createResponse');
Route::post('getTenderMasterApproval', 'TenderMasterAPIController@getTenderMasterApproval')->name("Get tender master approval");
Route::post('getTenderMasterFullApproved', 'TenderMasterAPIController@getTenderMasterFullApproved')->name("Get tender master full approved");
Route::post('approveTender', 'TenderMasterAPIController@approveTender')->name("Approve tender");
Route::post('rejectTender', 'TenderMasterAPIController@rejectTender')->name("Reject tender");
Route::post('deletePreTender', 'TenderBidClarificationsAPIController@deletePreTender');
Route::post('getPreBidEditData', 'TenderBidClarificationsAPIController@getPreBidEditData');
Route::post('updatePreBid', 'TenderBidClarificationsAPIController@updatePreBid');
Route::post('closeThread', 'TenderBidClarificationsAPIController@closeThread');
Route::post('reOpenTender', 'TenderMasterAPIController@reOpenTender')->name("Reopen tender");
Route::post('tenderMasterPublish', 'TenderMasterAPIController@tenderMasterPublish')->name("Tender master publish");

Route::post('getSupplierList', 'TenderMasterAPIController@getSupplierList')->name("Get supplier list");
Route::post('saveSupplierAssigned', 'TenderMasterAPIController@saveSupplierAssigned')->name("Save supplier assigned");
Route::post('getSupplierAssignedList', 'TenderMasterAPIController@getSupplierAssignedList')->name("Get supplier assigned list");
Route::post('deleteSupplierAssign', 'TenderSupplierAssigneeAPIController@deleteSupplierAssign')->name("Delete supplier assign");
Route::post('supplierAssignCRUD', 'TenderSupplierAssigneeAPIController@supplierAssignCRUD')->name("Supplier assign CRUD");
Route::post('sendSupplierInvitation', 'TenderSupplierAssigneeAPIController@sendSupplierInvitation')->name("Send supplier invitation");
Route::post('reSendInvitaitonLink', 'TenderSupplierAssigneeAPIController@reSendInvitaitonLink')->name("Resend invitation link");
Route::post('deleteAllSupplierAssign', 'TenderSupplierAssigneeAPIController@deleteAllSupplierAssign')->name("Delete all supplier assign");
Route::post('deleteSelectedSuppliers', 'TenderSupplierAssigneeAPIController@deleteSelectedSuppliers')->name("Delete selected suppliers");


Route::post('getSupplierCategoryList', 'TenderMasterAPIController@getSupplierCategoryList')->name("Get supplier category list");
Route::post('removeCalenderDate', 'TenderMasterAPIController@removeCalenderDate')->name("Remove calendar date");
Route::post('updateCalenderDate', 'TenderMasterAPIController@updateCalenderDate')->name("Update calendar date");
Route::post('getTenderAttachmentType', 'TenderDocumentTypesAPIController@getTenderAttachmentType')->name("Get tender attachment type");
Route::post('assignDocumentTypes', 'TenderDocumentTypesAPIController@assignDocumentTypes')->name("Assign document types");
Route::post('deleteAssignDocumentTypes', 'TenderDocumentTypesAPIController@deleteAssignDocumentTypes')->name("Delete assign document types");
Route::post('getNotSentEmail', 'TenderSupplierAssigneeAPIController@getNotSentEmail')->name("Get not sent email");

Route::post('updateTenderStrategy', 'TenderMasterAPIController@updateTenderStrategy')->name("Update tender strategy");

Route::post('getTenderCircularList', 'TenderCircularsAPIController@getTenderCircularList')->name("Get tender circular list");
Route::post('getAttachmentDropCircular', 'TenderCircularsAPIController@getAttachmentDropCircular')->name("Get attachment drop circular");
Route::post('addCircular', 'TenderCircularsAPIController@addCircular')->name("Add circular");
Route::post('addCircularSupplier', 'TenderCircularsAPIController@addCircularSupplier')->name("Add circular supplier");
Route::post('addCircularAmendment', 'TenderCircularsAPIController@addCircularAmendment')->name("Add circular ammendment");
Route::post('checkAmendmentIsUsedInCircular', 'TenderCircularsAPIController@checkAmendmentIsUsedInCircular')->name("Check amendment is used in circular");
Route::post('getCircularMaster', 'TenderCircularsAPIController@getCircularMaster')->name("Get circular master");
Route::post('deleteTenderCircular', 'TenderCircularsAPIController@deleteTenderCircular')->name("Delete tender circular");
Route::post('deleteCircularSupplier', 'TenderCircularsAPIController@deleteCircularSupplier')->name("Delete circular supplier");
Route::post('deleteCircularAmendment', 'TenderCircularsAPIController@deleteCircularAmendment')->name("Delete circular amendment");
Route::post('tenderCircularPublish', 'TenderCircularsAPIController@tenderCircularPublish')->name("Publish tender circular");
Route::post('getTenderPurchasedSupplierList', 'TenderCircularsAPIController@getTenderPurchasedSupplierList')->name("Get tender purchased supplier list");


Route::resource('tender_bid_format_masters', 'TenderBidFormatMasterAPIController');
Route::resource('tender_bid_format_details', 'TenderBidFormatDetailAPIController');
Route::resource('tender_field_types', 'TenderFieldTypeAPIController');
Route::resource('tender_masters', 'TenderMasterAPIController');
Route::resource('tender_types', 'TenderTypeAPIController');
Route::resource('tender_site_visit_dates', 'TenderSiteVisitDatesAPIController');

Route::resource('tender_master_suppliers', 'TenderMasterSupplierAPIController');
Route::resource('tender_main_works', 'TenderMainWorksAPIController');
Route::resource('tender_main_works', 'TenderMainWorksAPIController');
Route::resource('tender_boq_items', 'TenderBoqItemsAPIController');

Route::resource('tender_criteria_answer_types', 'TenderCriteriaAnswerTypeAPIController');

Route::resource('tender_supplier_assignees', 'TenderSupplierAssigneeAPIController');
Route::resource('tender_document_types', 'TenderDocumentTypesAPIController');

Route::resource('tender_circulars', 'TenderCircularsAPIController');

Route::post('store_tender_bid_documents', 'SrmBidDocumentattachmentsAPIController@storeTenderBidDocuments');
Route::get('download_tender_files', 'SrmBidDocumentattachmentsAPIController@downloadFile');

Route::post('getEmployeesCommercialApproval','TenderBidEmployeeDetailsController@getEmployeesCommercialApproval');
Route::post('getTenderCommercialBids', 'BidSubmissionMasterAPIController@getTenderCommercialBids');
Route::post('getSupplierItemList', 'BidSubmissionMasterAPIController@getSupplierItemList');
Route::post('generateSupplierItemReportTableView', 'BidSubmissionMasterAPIController@generateSupplierItemReportTableView');
Route::post('getCommercialBidTenderList', 'TenderMasterAPIController@getCommercialBidTenderList');
Route::post('getCommercialEval', 'TenderMasterAPIController@getCommercialEval');
Route::post('getCommercialEvalBoq', 'TenderMasterAPIController@getCommercialEvalBoq');



Route::resource('bid_document_verifications', 'BidDocumentVerificationAPIController');

Route::resource('srm_bid_documentattachments', 'SrmBidDocumentattachmentsAPIController');

Route::resource('bid_document_verifications', 'BidDocumentVerificationAPIController');
Route::resource('bid_evaluation_selections', 'BidEvaluationSelectionAPIController');
Route::post('getBidSelection', 'BidEvaluationSelectionAPIController@getBidSelection');

Route::resource('bid_schedules', 'BidScheduleAPIController');
Route::resource('bid_main_works', 'BidMainWorkAPIController');
Route::resource('bid_boqs', 'BidBoqAPIController');

Route::resource('bid_submission_masters', 'BidSubmissionMasterAPIController');
Route::resource('bid_submission_details', 'BidSubmissionDetailAPIController');

Route::resource('schedule_bid_format_details', 'ScheduleBidFormatDetailsAPIController');


Route::post('getPriceBidFormatDetails', 'PricingScheduleMasterAPIController@getPriceBidFormatDetails')->name("Get price bid format details");
Route::post('addPriceBidDetails', 'PricingScheduleMasterAPIController@addPriceBidDetails')->name("Add price bid details");
Route::post('getNotPulledPriceBidDetails', 'PricingScheduleMasterAPIController@getNotPulledPriceBidDetails')->name("Get not pulled price bid details");
Route::post('bidGoNoGoCommentAndStatus', 'BidSubmissionMasterAPIController@bidGoNoGoCommentAndStatus');

Route::post('getBidVerificationStatus', 'BidSubmissionMasterAPIController@getBidVerificationStatus');
Route::post('getVerifieddBids', 'BidSubmissionMasterAPIController@getVerifieddBids');
Route::post('saveTechnicalEvalBidSubmissionLine', 'BidSubmissionMasterAPIController@saveTechnicalEvalBidSubmissionLine');
Route::post('removeBid', 'BidEvaluationSelectionAPIController@removeBid');
Route::post('addBid', 'BidEvaluationSelectionAPIController@addBid');
Route::post('getEvalCompletedTenderList', 'TenderMasterAPIController@getEvalCompletedTenderList');
Route::post('getTechnicalRanking', 'TenderMasterAPIController@getTechnicalRanking');
Route::post('getCommercialRanking', 'TenderMasterAPIController@getCommercialRanking');
Route::post('getBidItemSelection', 'TenderMasterAPIController@getBidItemSelection');
Route::post('updateBidLineItem', 'TenderMasterAPIController@updateBidLineItem');
Route::post('confirmCommBidLineItem', 'TenderMasterAPIController@confirmCommBidLineItem');
Route::post('confirmFinalCommercial', 'TenderMasterAPIController@confirmFinalCommercial');
Route::post('getFinalBids', 'TenderFinalBidsAPIController@getFinalBids');
Route::post('confirmFinalBid', 'TenderFinalBidsAPIController@confirmFinalBid');
Route::post('getRankingCompletedTenderList', 'TenderMasterAPIController@getRankingCompletedTenderList');
Route::post('getAwardedFormData', 'TenderMasterAPIController@getAwardedFormData');
Route::post('getEmployeesTenderAwardinglApproval','TenderBidEmployeeDetailsController@getEmployeesTenderAwardinglApproval');
Route::post('confirmFinalBidAwardComment', 'TenderMasterAPIController@confirmFinalBidAwardComment');
Route::post('sendTenderAwardEmail', 'TenderMasterAPIController@sendTenderAwardEmail');
Route::post('getNegotiationStartedTenderList', 'TenderMasterAPIController@getNegotiationStartedTenderList');


Route::resource('document_modify_requests', 'DocumentModifyRequestAPIController');




Route::post('createEditRequest', 'DocumentModifyRequestAPIController@createEditRequest')->name("Create edit request");
Route::post('getTenderEditMasterApproval', 'TenderMasterAPIController@getTenderEditMasterApproval')->name("Get tender edit master approval");
Route::post('approveEditDocument', 'DocumentModifyRequestAPIController@approveEditDocument')->name("Approve edit document");
Route::post('getTenderEditMasterFullApproved', 'TenderMasterAPIController@getTenderEditMasterFullApproved')->name("Get tender edit master full approved");
Route::post('rejectTenderEditDocument', 'TenderEditLogMasterAPIController@rejectTenderEditDocument');

Route::post('startTenderNegotiation', 'TenderMasterAPIController@startTenderNegotiation');
Route::post('getFormDataTenderNegotiation', 'TenderNegotiationController@getFormData');

Route::resource('tender_negotiation', 'TenderNegotiationController');

Route::resource('supplierTenderNegotiations', 'SupplierTenderNegotiationController');

Route::post('getFinalBidsForTenderNegotiation', 'TenderNegotiationController@getFinalBidsForTenderNegotiation');

Route::post('getTenderNegotiatedSupplierIds','SupplierTenderNegotiationController@getTenderNegotiatedSupplierIds');

Route::resource('tenderNegotiationAreas', 'TenderNegotiationAreaController');

Route::post('getSelectedAreas', 'TenderNegotiationAreaController@getSelectedAreas');

Route::resource('tenderNegotiationApprovals', 'TenderNegotiationApprovalController');

Route::post('tenderNegotiationApprovalsGetEmployees', 'TenderNegotiationApprovalController@getEmployees');

Route::post('publishNegotiation', 'TenderNegotiationApprovalController@publishNegotiation');
Route::resource('calendar_dates_detail_edit_logs', 'CalendarDatesDetailEditLogAPIController');
Route::resource('procument_activity_edit_logs', 'ProcumentActivityEditLogAPIController');
Route::post('getTenderFilterData', 'TenderMasterAPIController@getTenderFilterData')->name("Get tender filter data");

Route::post('addAllSuppliersToNegotiation', 'SupplierTenderNegotiationController@addAllSuppliersToNegotiation');

Route::post('deleteAllSuppliersFromNegotiation', 'SupplierTenderNegotiationController@deleteAllSuppliersFromNegotiation');


Route::post('saveTenderNegotiationDetails', 'TenderNegotiationController@saveTenderNegotiationDetails');

Route::group(['prefix' => 'srm'], function (){
        
    Route::group(['middleware' => ['tenant']], function (){
        Route::post('fetch', 'SRM\APIController@fetch')->name("Get supplier KYC details");
    });

});

