<?php

/**
 * This file contains SRM module related routes
 *
 *
 * */


Route::post('store_tender_documents', 'DocumentAttachmentsAPIController@storeTenderDocuments')->name("Store tender documents");
Route::post('tenderBIdDocApproveal', 'DocumentAttachmentsAPIController@tenderBIdDocApproveal')->name("Tender bid doc approval");
Route::post('tenderBIdDocTypeApproveal', 'DocumentAttachmentsAPIController@tenderBIdDocTypeApproveal')->name("Tender bid doc type approval");
Route::post('tenderBIdDocSubmission', 'DocumentAttachmentsAPIController@tenderBIdDocSubmission')->name("Tender bid doc submission");
Route::post('checkTenderBidDocExist', 'DocumentAttachmentsAPIController@checkTenderBidDocExist')->name("Check tender bid doc exist");

Route::post('getAllProcurementCategory', 'TenderProcurementCategoryController@getAllProcurementCategory')->name("Get all procurement category");
Route::resource('procurement_categories', 'TenderProcurementCategoryController');

Route::post('get_all_calendar_dates', 'TenderCalendarDatesController@getAllCalendarDates')->name("Get all calendar dates");
Route::resource('calendar_date', 'TenderCalendarDatesController', ['names' => ['Calender Date Master']]);
Route::resource('srm_department', 'SrmDepartmentMasterAPIController', ['names' => ['Department Master']]);
Route::post('get_all_departments', 'SrmDepartmentMasterAPIController@getAllDepartments')->name("Get all departments");
Route::post('update_department_status', 'SrmDepartmentMasterAPIController@updateDepartmentStatus')->name("Update department Status");

Route::post('get-tender-committee', 'TenderCommitteeController@getAll')->name("Get all committee");
Route::post('add-employees-to-tender-committee', 'TenderCommitteeController@assignEmployeesToTenderCommitee')->name("Add employees to committee");
Route::post('delete-tender-committee', 'TenderCommitteeController@delete')->name("Delete committee");

Route::resource('tender-committee', 'TenderCommitteeController');
Route::post('tender-committee/{id}','TenderCommitteeController@update')->name("Update committee");
Route::post('getActiveEmployeesForBid','TenderCommitteeController@getActiveEmployeesForBid')->name("Get active employees for bid");

Route::resource('tender-bid-employee-details','TenderBidEmployeeDetailsController');
Route::post('tender-bid-employee-get-all','TenderBidEmployeeDetailsController@getEmployees')->name("Get tender bid employees");
Route::post('tender-bid-employee-delete','TenderBidEmployeeDetailsController@deleteEmp')->name("Delete tender bid employee");
Route::post('tender-bid-employee-approval-count','TenderBidEmployeeDetailsController@getEmployeesApproval')->name("Get employees approval");

Route::post('getTenderBits', 'BidSubmissionMasterAPIController@getTenderBits')->name("Get tender bits");
Route::post('getTenderBidGoNoGoResponse', 'BidSubmissionMasterAPIController@getTenderBidGoNoGoResponse')->name("Get tender bid go no go response");
Route::post('updateTenderBidGoNoGoResponse', 'BidSubmissionMasterAPIController@updateTenderBidGoNoGoResponse')->name("Update tender bid go no go response");

Route::post('getTenderBidFormats', 'TenderBidFormatMasterAPIController@getTenderBidFormats')->name("Get tender bid formats");
Route::post('storeBidFormat', 'TenderBidFormatMasterAPIController@storeBidFormat')->name("Store bid format");
Route::post('loadBidFormatMaster', 'TenderBidFormatMasterAPIController@loadBidFormatMaster')->name("Load bid format master");
Route::post('addPriceBidDetail', 'TenderBidFormatMasterAPIController@addPriceBidDetail')->name("Add price bid detail");
Route::post('updatePriceBidDetail', 'TenderBidFormatMasterAPIController@updatePriceBidDetail')->name("Update price bid detail");
Route::post('updateBidFormat', 'TenderBidFormatMasterAPIController@updateBidFormat')->name("Update bid format");
Route::post('deletePriceBideDetail', 'TenderBidFormatMasterAPIController@deletePriceBideDetail')->name("Delete price bid detail");
Route::post('deletePriceBidMaster', 'TenderBidFormatMasterAPIController@deletePriceBidMaster')->name("Delete price bid master");

Route::post('getBitFormatItems', 'TenderBidFormatMasterAPIController@getBitFormatItems')->name("Get bid format items");


Route::post('getTenderMasterList', 'TenderMasterAPIController@getTenderMasterList')->name("Get tender master list");
Route::post('getTenderDropDowns', 'TenderMasterAPIController@getTenderDropDowns')->name("Get tender drop downs");
Route::post('createTender', 'TenderMasterAPIController@createTender')->name("Create tender");
Route::post('deleteTenderMaster', 'TenderMasterAPIController@deleteTenderMaster')->name("Delete tender master");
Route::post('getTenderMasterData', 'TenderMasterAPIController@getTenderMasterData')->name("Get tender master data");
Route::post('loadTenderSubCategory', 'TenderMasterAPIController@loadTenderSubCategory')->name("Load tender sub category");
Route::post('loadTenderSubActivity', 'TenderMasterAPIController@loadTenderSubActivity')->name("Load tender sub activity");
Route::post('loadTenderBankAccount', 'TenderMasterAPIController@loadTenderBankAccount')->name("Load tender bank account");
Route::post('updateTender', 'TenderMasterAPIController@updateTender')->name("Update tender");
Route::post('getPurchasedTenderList', 'TenderMasterAPIController@getPurchasedTenderList')->name("Get purchased tender list");
Route::post('getPurchaseTenderMasterData', 'TenderMasterAPIController@getPurchaseTenderMasterData')->name("Get purchase tender master data");
Route::post('tenderCommiteApproveal', 'TenderMasterAPIController@tenderCommiteApproveal')->name("Tender commite approval");
Route::post('getTenderTechniqalEvaluation', 'TenderMasterAPIController@getTenderTechniqalEvaluation')->name("Get tender technical evaluation");


Route::post('addFormula', 'TenderBidFormatMasterAPIController@addFormula')->name("Add formula");
Route::post('formulaGenerate', 'TenderBidFormatMasterAPIController@formulaGenerate');
Route::post('tenderBidDocVerification', 'TenderMasterAPIController@tenderBidDocVerification')->name("Tender bid doc verification");

Route::post('getMainWorksList', 'TenderMainWorksAPIController@getMainWorksList')->name("Get main works list");
Route::post('addMainWorks', 'TenderMainWorksAPIController@addMainWorks')->name("Add Main works");
Route::get('downloadMainWorksUploadTemplate', 'TenderMainWorksAPIController@downloadMainWorksUploadTemplate')->name("Download main works upload template");
Route::post('mainWorksItemsUpload', 'TenderMainWorksAPIController@mainWorksItemsUpload')->name("Main works items upload");
Route::post('deleteMainWorks', 'TenderMainWorksAPIController@deleteMainWorks')->name("Delete Main Works");
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
Route::post('getPreBidClarifications', 'TenderBidClarificationsAPIController@getPreBidClarifications')->name("Get pre bid clarifications");
Route::post('getPreBidClarificationsResponse', 'TenderBidClarificationsAPIController@getPreBidClarificationsResponse')->name("Get pre bid clarifications response");
Route::post('forwardPreBidClarification', 'TenderBidClarificationsAPIController@forwardPreBidClarification')->name("Forward pre bid clarifications");
Route::post('getPreBidClarificationsPolicyData', 'TenderBidClarificationsAPIController@getPreBidClarificationsPolicyData')->name("Get pre bid clarifications policy data");
Route::post('createResponse', 'TenderBidClarificationsAPIController@createResponse')->name("Create response");
Route::post('getTenderMasterApproval', 'TenderMasterAPIController@getTenderMasterApproval')->name("Get tender master approval");
Route::post('getTenderMasterFullApproved', 'TenderMasterAPIController@getTenderMasterFullApproved')->name("Get tender master full approved");
Route::post('approveTender', 'TenderMasterAPIController@approveTender')->name("Approve tender");
Route::post('rejectTender', 'TenderMasterAPIController@rejectTender')->name("Reject tender");
Route::post('deletePreTender', 'TenderBidClarificationsAPIController@deletePreTender')->name("Delete pre tender");
Route::post('getPreBidEditData', 'TenderBidClarificationsAPIController@getPreBidEditData')->name("Get pre bid edit data");
Route::post('updatePreBid', 'TenderBidClarificationsAPIController@updatePreBid')->name("Update pre bid");
Route::post('closeThread', 'TenderBidClarificationsAPIController@closeThread')->name("Close thread");
Route::post('reOpenTender', 'TenderMasterAPIController@reOpenTender')->name("Reopen tender");
Route::post('tenderMasterPublish', 'TenderMasterAPIController@tenderMasterPublish')->name("Tender master publish");

Route::post('getSourcingManagementSupplierList', 'TenderMasterAPIController@getSupplierList')->name("Get supplier list");
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

Route::post('store_tender_bid_documents', 'SrmBidDocumentattachmentsAPIController@storeTenderBidDocuments')->name("Store tender bid documents");
Route::get('download_tender_files', 'SrmBidDocumentattachmentsAPIController@downloadFile')->name("Download tender files");

Route::post('getEmployeesCommercialApproval','TenderBidEmployeeDetailsController@getEmployeesCommercialApproval')->name("Get employees commercial approval");
Route::post('getTenderCommercialBids', 'BidSubmissionMasterAPIController@getTenderCommercialBids')->name("Get tender commercial bids");
Route::post('getSupplierItemList', 'BidSubmissionMasterAPIController@getSupplierItemList')->name("Get supplier item list");
Route::post('generateSupplierItemReportTableView', 'BidSubmissionMasterAPIController@generateSupplierItemReportTableView')->name("Generate supplier item report table view");
Route::post('getCommercialBidTenderList', 'TenderMasterAPIController@getCommercialBidTenderList')->name("Get commercial bid tender list");
Route::post('getCommercialEval', 'TenderMasterAPIController@getCommercialEval')->name("Get commercial eval");
Route::post('getCommercialEvalBoq', 'TenderMasterAPIController@getCommercialEvalBoq')->name("Get commercial eval boq");



Route::resource('bid_document_verifications', 'BidDocumentVerificationAPIController');

Route::resource('srm_bid_documentattachments', 'SrmBidDocumentattachmentsAPIController', ['names' => '  Supplier Bid Document Attachments']);

Route::resource('bid_document_verifications', 'BidDocumentVerificationAPIController');
Route::resource('bid_evaluation_selections', 'BidEvaluationSelectionAPIController');
Route::post('getBidSelection', 'BidEvaluationSelectionAPIController@getBidSelection')->name("Get bid selection");

Route::resource('bid_schedules', 'BidScheduleAPIController');
Route::resource('bid_main_works', 'BidMainWorkAPIController');
Route::resource('bid_boqs', 'BidBoqAPIController');

Route::resource('bid_submission_masters', 'BidSubmissionMasterAPIController');
Route::resource('bid_submission_details', 'BidSubmissionDetailAPIController');

Route::resource('schedule_bid_format_details', 'ScheduleBidFormatDetailsAPIController');


Route::post('getPriceBidFormatDetails', 'PricingScheduleMasterAPIController@getPriceBidFormatDetails')->name("Get price bid format details");
Route::post('addPriceBidDetails', 'PricingScheduleMasterAPIController@addPriceBidDetails')->name("Add price bid details");
Route::post('getNotPulledPriceBidDetails', 'PricingScheduleMasterAPIController@getNotPulledPriceBidDetails')->name("Get not pulled price bid details");
Route::post('bidGoNoGoCommentAndStatus', 'BidSubmissionMasterAPIController@bidGoNoGoCommentAndStatus')->name("Bid go no go comment and status");

Route::post('getBidVerificationStatus', 'BidSubmissionMasterAPIController@getBidVerificationStatus')->name("Get bid verification status");
Route::post('getVerifieddBids', 'BidSubmissionMasterAPIController@getVerifieddBids')->name("Get verified bids");
Route::post('saveTechnicalEvalBidSubmissionLine', 'BidSubmissionMasterAPIController@saveTechnicalEvalBidSubmissionLine')->name("Save technical eval bid submission line");
Route::post('removeBid', 'BidEvaluationSelectionAPIController@removeBid')->name("Remove bid");
Route::post('addBid', 'BidEvaluationSelectionAPIController@addBid')->name("Add bid");
Route::post('getEvalCompletedTenderList', 'TenderMasterAPIController@getEvalCompletedTenderList')->name("Get eval completed tender list");
Route::post('getTechnicalRanking', 'TenderMasterAPIController@getTechnicalRanking')->name("Get technical ranking");
Route::post('getCommercialRanking', 'TenderMasterAPIController@getCommercialRanking')->name("Get commercial ranking");
Route::post('getBidItemSelection', 'TenderMasterAPIController@getBidItemSelection')->name("Get bid item selection");
Route::post('updateBidLineItem', 'TenderMasterAPIController@updateBidLineItem')->name("Update bid line item");
Route::post('confirmCommBidLineItem', 'TenderMasterAPIController@confirmCommBidLineItem')->name("Confirm commercial bid line item");
Route::post('confirmFinalCommercial', 'TenderMasterAPIController@confirmFinalCommercial')->name("Confirm final commercial");
Route::post('getFinalBids', 'TenderFinalBidsAPIController@getFinalBids')->name("Get final bids");
Route::post('confirmFinalBid', 'TenderFinalBidsAPIController@confirmFinalBid')->name("Confirm final bid");
Route::post('getRankingCompletedTenderList', 'TenderMasterAPIController@getRankingCompletedTenderList')->name("Get ranking completed tender list");
Route::post('getAwardedFormData', 'TenderMasterAPIController@getAwardedFormData')->name("Get awarded form data");
Route::post('getEmployeesTenderAwardinglApproval','TenderBidEmployeeDetailsController@getEmployeesTenderAwardinglApproval')->name("Get employees tender awarding approval");
Route::post('confirmFinalBidAwardComment', 'TenderMasterAPIController@confirmFinalBidAwardComment')->name("Confirm final bid award comment");
Route::post('sendTenderAwardEmail', 'TenderMasterAPIController@sendTenderAwardEmail')->name("Send tender award email");
Route::post('getNegotiationStartedTenderList', 'TenderMasterAPIController@getNegotiationStartedTenderList')->name("Get negotiation started tender list");
Route::post('getContractTypes', 'TenderMasterAPIController@getContractTypes')->name("Get Contract Types");
Route::post('createContract', 'TenderMasterAPIController@createContract')->name("Create Contract");
Route::post('viewContract', 'TenderMasterAPIController@viewContract')->name("View Contract");
Route::post('addAttachment', 'TenderMasterAPIController@addAttachment')->name("Add Attachment");
Route::post('deleteAttachment', 'TenderMasterAPIController@deleteAttachment')->name("Delete Attachment");

Route::resource('document_modify_requests', 'DocumentModifyRequestAPIController');

Route::post('createEditRequest', 'DocumentModifyRequestAPIController@createEditRequest')->name("Create edit request");
Route::post('getTenderEditMasterApproval', 'TenderMasterAPIController@getTenderEditMasterApproval')->name("Get tender edit master approval");
Route::post('approveEditDocument', 'DocumentModifyRequestAPIController@approveEditDocument')->name("Approve edit document");
Route::post('getTenderEditMasterFullApproved', 'TenderMasterAPIController@getTenderEditMasterFullApproved')->name("Get tender edit master full approved");
Route::post('rejectTenderEditDocument', 'TenderEditLogMasterAPIController@rejectTenderEditDocument')->name("Reject edit document");

Route::post('startTenderNegotiation', 'TenderMasterAPIController@startTenderNegotiation')->name("Start tender negotiation");
Route::post('closeTenderNegotiation', 'TenderMasterAPIController@closeTenderNegotiation')->name("Close tender negotiation");
Route::post('getFormDataTenderNegotiation', 'TenderNegotiationController@getFormData')->name("Get tender edit master negotiation");

Route::resource('tender_negotiation', 'TenderNegotiationController');

Route::resource('supplierTenderNegotiations', 'SupplierTenderNegotiationController');

Route::post('getFinalBidsForTenderNegotiation', 'TenderNegotiationController@getFinalBidsForTenderNegotiation')->name("Get final bids for tender negotiation");

Route::post('getTenderNegotiatedSupplierIds','SupplierTenderNegotiationController@getTenderNegotiatedSupplierIds')->name("Get tender negotiated supplier");

Route::resource('tenderNegotiationAreas', 'TenderNegotiationAreaController');

Route::post('getSelectedAreas', 'TenderNegotiationAreaController@getSelectedAreas')->name("Get selected areas");

Route::resource('tenderNegotiationApprovals', 'TenderNegotiationApprovalController');

Route::post('tenderNegotiationApprovalsGetEmployees', 'TenderNegotiationApprovalController@getEmployees')->name("Get negotiation approval employees");

Route::post('publishNegotiation', 'TenderNegotiationApprovalController@publishNegotiation')->name("Publish negotiation");
Route::resource('calendar_dates_detail_edit_logs', 'CalendarDatesDetailEditLogAPIController');
Route::resource('procument_activity_edit_logs', 'ProcumentActivityEditLogAPIController');

Route::post('getTenderFilterData', 'TenderMasterAPIController@getTenderFilterData')->name("Get tender filter data");

Route::post('approveBidOpening', 'TenderMasterAPIController@approveBidOpening')->name("Approve bid opening");

Route::post('addAllSuppliersToNegotiation', 'SupplierTenderNegotiationController@addAllSuppliersToNegotiation')->name("Add all suppliers to negotiation");

Route::post('deleteAllSuppliersFromNegotiation', 'SupplierTenderNegotiationController@deleteAllSuppliersFromNegotiation')->name("Delete all suppliers negotiation");


Route::post('saveTenderNegotiationDetails', 'TenderNegotiationController@saveTenderNegotiationDetails')->name("Save tender negotiation details");
Route::get('getTenderPr', 'TenderMasterAPIController@getTenderPr')->name("Get tender pr");
Route::get('getPurchaseRequestDetails', 'TenderMasterAPIController@getPurchaseRequestDetails')->name("Get purchase request details");
Route::post('referBackTenderMaster', 'TenderMasterAPIController@referBackTenderMaster')->name("Referback tender master");
Route::post('getTenderAmendHistory', 'TenderMasterAPIController@getTenderAmendHistory')->name("Get tender amend history");
Route::post('getTenderRfxAudit', 'TenderMasterAPIController@getTenderRfxAudit')->name("Get tender rfx audit");


Route::group(['prefix' => 'srm'], function (){

    Route::group(['middleware' => ['tenant']], function (){
        Route::post('fetch', 'SRM\APIController@fetch')->name("Get supplier KYC details");
    });

});

Route::post('get_all_document_attachment_type', 'DocumentAttachmentTypeController@getAllDocumentAttachmentTypes')->name("Get all document attachment types");
Route::resource('document_attachment_type', 'DocumentAttachmentTypeController');
Route::post('remove_document_attachment_type', 'DocumentAttachmentTypeController@removeDocumentAttachmentType')->name("Remove document attachment type");
Route::post('getTenderNegotiationList', 'TenderMasterAPIController@getTenderNegotiationList')->name("Get tender negotiation list");
Route::post('getIsExistCommonAttachment', 'BidSubmissionMasterAPIController@getIsExistCommonAttachment');
Route::post('getTenderPurchaseList', 'TenderMasterAPIController@getTenderPurchaseList')->name("Get tender purchase list");
Route::post('getBudgetItemTotalAmount', 'TenderMasterAPIController@getBudgetItemTotalAmount')->name("Get budget item total amount");
Route::post('removeTenderUserAccess', 'TenderBidEmployeeDetailsController@removeTenderUserAccess');
Route::post('addUserAccessEmployee', 'TenderBidEmployeeDetailsController@addUserAccessEmployee');
Route::post('getPublicSupplierLinkData', 'SRMPublicLinkAPIController@getPublicSupplierLinkData');
Route::post('saveSupplierPublicLink', 'SRMPublicLinkAPIController@saveSupplierPublicLink');
Route::post('requestKycSubmit', 'SupplierMasterAPIController@requestSubmitKyc')->name("KYC Request");
Route::post('checkBidOpeningDateValidation', 'BidSubmissionMasterAPIController@checkDateDisabled')->name("Bid Date Validation");
Route::post('getTenderPOData', 'TenderMasterAPIController@getTenderPOData')->name("Create PO From Tender");
Route::post('getPaymentProofDocumentApproval', 'TenderMasterAPIController@getPaymentProofDocumentApproval');
Route::post('getSupplierWiseProofNotApproved', 'TenderMasterAPIController@getSupplierWiseProofNotApproved');
Route::post('approveSupplierWiseTender', 'TenderMasterAPIController@approveSupplierWiseTender');
Route::post('rejectSupplierWiseTender', 'TenderMasterAPIController@rejectSupplierWiseTender');
Route::post('getSupplierWiseProofApproved', 'TenderMasterAPIController@getSupplierWiseProofApproved');
Route::post('updateTenderCalendarDays', 'TenderMasterAPIController@updateTenderCalendarDays');
Route::post('getTenderCalendarValidation', 'TenderMasterAPIController@getTenderCalendarValidation');
Route::post('getCalendarDateAuditLogs', 'TenderMasterAPIController@getCalendarDateAuditLogs');
Route::post('getNegotiationStartedSupplierList', 'TenderNegotiationController@getNegotiationStartedSupplierList')->name("Get tender negotiated supplier list");
Route::post('saveCustomEmail', 'TenderCustomEmailController@store')->name("create Custom tender email");
Route::post('getSupplierListCustomEmail', 'TenderCustomEmailController@getSupplierListCustomEmail')->name("Get Negotiation Custom Email Supplier List");
Route::post('removeCustomEmailSupplier', 'TenderCustomEmailController@deleteBySupplierId')->name("Remove Negotiation Supplier Custom Email");
Route::post('getCustomEmailSupplier', 'TenderCustomEmailController@getCustomEmailSupplier')->name("Get Negotiation Supplier Custom Email");
Route::post('deleteAllBidMinimumApprovalDetails', 'TenderBidEmployeeDetailsController@deleteAllBidMinimumApprovalDetails')->name("Delete All Bid Minimum Approval Details");
Route::post('deleteAllTenderUserAccess', 'TenderBidEmployeeDetailsController@deleteAllTenderUserAccess')->name("Delete All Tender User Access");
/*TenderPaymentDetailAPIController*/
