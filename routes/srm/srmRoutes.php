<?php

/**
 * This file contains SRM module related routes
 * 
 * 
 * */


Route::post('store_tender_documents', 'DocumentAttachmentsAPIController@storeTenderDocuments');
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
Route::post('getActiveEmployeesForBid','TenderCommitteeController@getActiveEmployeesForBid');

Route::resource('tender-bid-employee-details','TenderBidEmployeeDetailsController');
Route::post('tender-bid-employee-get-all','TenderBidEmployeeDetailsController@getEmployees');
Route::post('tender-bid-employee-delete','TenderBidEmployeeDetailsController@deleteEmp');
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


Route::post('getTenderMasterList', 'TenderMasterAPIController@getTenderMasterList');
Route::post('getTenderDropDowns', 'TenderMasterAPIController@getTenderDropDowns');
Route::post('createTender', 'TenderMasterAPIController@createTender');
Route::post('deleteTenderMaster', 'TenderMasterAPIController@deleteTenderMaster');
Route::post('getTenderMasterData', 'TenderMasterAPIController@getTenderMasterData');
Route::post('loadTenderSubCategory', 'TenderMasterAPIController@loadTenderSubCategory');
Route::post('loadTenderSubActivity', 'TenderMasterAPIController@loadTenderSubActivity');
Route::post('loadTenderBankAccount', 'TenderMasterAPIController@loadTenderBankAccount');
Route::post('updateTender', 'TenderMasterAPIController@updateTender');
Route::post('getPurchasedTenderList', 'TenderMasterAPIController@getPurchasedTenderList');
Route::post('getPurchaseTenderMasterData', 'TenderMasterAPIController@getPurchaseTenderMasterData');
Route::post('tenderCommiteApproveal', 'TenderMasterAPIController@tenderCommiteApproveal');
Route::post('getTenderTechniqalEvaluation', 'TenderMasterAPIController@getTenderTechniqalEvaluation');


Route::post('addFormula', 'TenderBidFormatMasterAPIController@addFormula');
Route::post('formulaGenerate', 'TenderBidFormatMasterAPIController@formulaGenerate');
Route::post('tenderBidDocVerification', 'TenderMasterAPIController@tenderBidDocVerification');

Route::post('getMainWorksList', 'TenderMainWorksAPIController@getMainWorksList');
Route::post('addMainWorks', 'TenderMainWorksAPIController@addMainWorks');
Route::get('downloadMainWorksUploadTemplate', 'TenderMainWorksAPIController@downloadMainWorksUploadTemplate');
Route::post('mainWorksItemsUpload', 'TenderMainWorksAPIController@mainWorksItemsUpload');
Route::post('deleteMainWorks', 'TenderMainWorksAPIController@deleteMainWorks');
Route::post('updateWorkOrderDescription', 'TenderMainWorksAPIController@updateWorkOrderDescription');

Route::post('getFaqFormData', 'TenderMasterAPIController@getFaqFormData');
Route::post('createFaq', 'TenderFaqAPIController@createFaq');
Route::post('getFaqList', 'TenderFaqAPIController@getFaqList');
Route::post('getFaq', 'TenderFaqAPIController@getFaq');
Route::post('deleteFaq', 'TenderFaqAPIController@deleteFaq');

Route::post('loadTenderBoqItems', 'TenderBoqItemsAPIController@loadTenderBoqItems');
Route::post('addTenderBoqItems', 'TenderBoqItemsAPIController@addTenderBoqItems');
Route::post('updateTenderBoqItem', 'TenderBoqItemsAPIController@updateTenderBoqItem');
Route::get('downloadTenderBoqItemUploadTemplate', 'TenderBoqItemsAPIController@downloadTenderBoqItemUploadTemplate');
Route::post('deleteTenderBoqItem', 'TenderBoqItemsAPIController@deleteTenderBoqItem');
Route::post('tenderBoqItemsUpload', 'TenderBoqItemsAPIController@tenderBoqItemsUpload');
Route::post('getPreBidClarifications', 'TenderBidClarificationsAPIController@getPreBidClarifications');
Route::post('getPreBidClarificationsResponse', 'TenderBidClarificationsAPIController@getPreBidClarificationsResponse');
Route::post('createResponse', 'TenderBidClarificationsAPIController@createResponse');
Route::post('getTenderMasterApproval', 'TenderMasterAPIController@getTenderMasterApproval');
Route::post('getTenderMasterFullApproved', 'TenderMasterAPIController@getTenderMasterFullApproved');
Route::post('approveTender', 'TenderMasterAPIController@approveTender');
Route::post('rejectTender', 'TenderMasterAPIController@rejectTender');
Route::post('deletePreTender', 'TenderBidClarificationsAPIController@deletePreTender');
Route::post('getPreBidEditData', 'TenderBidClarificationsAPIController@getPreBidEditData');
Route::post('updatePreBid', 'TenderBidClarificationsAPIController@updatePreBid');
Route::post('closeThread', 'TenderBidClarificationsAPIController@closeThread');
Route::post('reOpenTender', 'TenderMasterAPIController@reOpenTender');
Route::post('tenderMasterPublish', 'TenderMasterAPIController@tenderMasterPublish');

Route::post('getSupplierList', 'TenderMasterAPIController@getSupplierList');
Route::post('saveSupplierAssigned', 'TenderMasterAPIController@saveSupplierAssigned');
Route::post('getSupplierAssignedList', 'TenderMasterAPIController@getSupplierAssignedList');
Route::post('deleteSupplierAssign', 'TenderSupplierAssigneeAPIController@deleteSupplierAssign');
Route::post('supplierAssignCRUD', 'TenderSupplierAssigneeAPIController@supplierAssignCRUD');
Route::post('sendSupplierInvitation', 'TenderSupplierAssigneeAPIController@sendSupplierInvitation');
Route::post('reSendInvitaitonLink', 'TenderSupplierAssigneeAPIController@reSendInvitaitonLink');

Route::post('getSupplierCategoryList', 'TenderMasterAPIController@getSupplierCategoryList');
Route::post('removeCalenderDate', 'TenderMasterAPIController@removeCalenderDate');
Route::post('updateCalenderDate', 'TenderMasterAPIController@updateCalenderDate');
Route::post('getTenderAttachmentType', 'TenderDocumentTypesAPIController@getTenderAttachmentType');
Route::post('assignDocumentTypes', 'TenderDocumentTypesAPIController@assignDocumentTypes');
Route::post('deleteAssignDocumentTypes', 'TenderDocumentTypesAPIController@deleteAssignDocumentTypes');
Route::post('getNotSentEmail', 'TenderSupplierAssigneeAPIController@getNotSentEmail');

Route::post('updateTenderStrategy', 'TenderMasterAPIController@updateTenderStrategy');

Route::post('getTenderCircularList', 'TenderCircularsAPIController@getTenderCircularList');
Route::post('getAttachmentDropCircular', 'TenderCircularsAPIController@getAttachmentDropCircular');
Route::post('addCircular', 'TenderCircularsAPIController@addCircular');
Route::post('addCircularSupplier', 'TenderCircularsAPIController@addCircularSupplier');
Route::post('addCircularAmendment', 'TenderCircularsAPIController@addCircularAmendment');
Route::post('checkAmendmentIsUsedInCircular', 'TenderCircularsAPIController@checkAmendmentIsUsedInCircular');
Route::post('getCircularMaster', 'TenderCircularsAPIController@getCircularMaster');
Route::post('deleteTenderCircular', 'TenderCircularsAPIController@deleteTenderCircular');
Route::post('deleteCircularSupplier', 'TenderCircularsAPIController@deleteCircularSupplier');
Route::post('deleteCircularAmendment', 'TenderCircularsAPIController@deleteCircularAmendment');
Route::post('tenderCircularPublish', 'TenderCircularsAPIController@tenderCircularPublish');
Route::post('getTenderPurchasedSupplierList', 'TenderCircularsAPIController@getTenderPurchasedSupplierList');


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


Route::post('getPriceBidFormatDetails', 'PricingScheduleMasterAPIController@getPriceBidFormatDetails');
Route::post('addPriceBidDetails', 'PricingScheduleMasterAPIController@addPriceBidDetails');
Route::post('getNotPulledPriceBidDetails', 'PricingScheduleMasterAPIController@getNotPulledPriceBidDetails');
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