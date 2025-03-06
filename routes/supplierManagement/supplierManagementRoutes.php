<?php
/**
 * This file contains supplier management module related routes
 *
 *
 * */


//approval - suppliers
Route::group([], function(){

    Route::group(['prefix' => 'suppliers/registration'], function () {
        Route::post('approvals', 'SupplierRegistrationApprovalController@index')->name("Get all supplier approvals");
        Route::post('approvals/status', 'SupplierRegistrationApprovalController@update')->name("Update supplier approval status");

    });

});

//approval - appointments
Route::group([], function(){

    Route::post('getAppointmentListSummaryView', 'AppointmentAPIController@getAppointmentListSummaryView')->name("Get appointment list summery view");
    Route::post('checkDeliveryAppoinrmentApproval', 'AppointmentAPIController@checkDeliveryAppoinrmentApproval')->name("Check delivery appointment approval");
    Route::post('approveCalanderDelAppointment', 'AppointmentAPIController@approveCalanderDelAppointment')->name("Approve calendar del appointment");
    Route::post('rejectCalanderDelAppointment', 'AppointmentAPIController@rejectCalanderDelAppointment')->name("Reject calendar del appointment");
    Route::post('getAppointmentAttachmentList', 'AppointmentAPIController@getAppointmentAttachmentList')->name("Get appointment attachement list");
    Route::post('getAppointmentById', 'AppointmentAPIController@getAppointmentById')->name("Get appointment by id");

});

//delivery appointment
Route::group([], function(){

    Route::get('getFormDataCalander', 'SlotMasterAPIController@getFormDataCalander')->name("Get form data calendar");
    Route::get('getCalanderSlotData', 'SlotMasterAPIController@getCalanderSlotData')->name("Get calendar slot data");
    Route::post('clanderSlotMasterData', 'SlotMasterAPIController@clanderSlotMasterData')->name("Calendar slot master data");
    Route::post('getAppointments', 'AppointmentAPIController@getAppointments')->name("Get appointments");
    Route::post('clanderSlotDateRangeValidation', 'SlotMasterAPIController@clanderSlotDateRangeValidation')->name("Calendar slot date range validation");
    Route::post('saveCalanderSlots', 'SlotMasterAPIController@saveCalanderSlots')->name("Save calendar slots");
    Route::post('removeCalanderSlot', 'SlotMasterAPIController@removeCalanderSlot')->name("Remove calendar slot");
    Route::post('getSegmentOfAppointment', 'AppointmentAPIController@getSegmentOfAppointment')->name("Get Segment Of Appointment");
    Route::post('createAppointmentGrv', 'AppointmentAPIController@createAppointmentGrv')->name("Create appointment GRV");
    Route::post('removeCalenderSlotDetail', 'SlotDetailsAPIController@removeCalenderSlotDetail')->name("Remove calender slot detail");
    Route::post('removeDateRangeSlots', 'SlotDetailsAPIController@removeDateRangeSlots')->name("Remove date range calender slot detail");
    Route::post('getSlotDetailsFormData', 'SlotDetailsAPIController@getSlotDetailsFormData')->name("Get form data slot detail");

});

//supplier KYC
Route::group([], function(){

    Route::get('getSearchSupplierByCompanySRM', 'SupplierMasterAPIController@getSearchSupplierByCompanySRM')->name("Get search supplier by company SRM");
    Route::group(['prefix' => 'suppliers/registration'], function () {
        Route::post('/', 'SupplierRegistrationController@index')->name("Get supplier registrations");
        Route::post('/attach', 'SupplierRegistrationController@linkKYCWithSupplier')->name("Link KYC with supplier");
        Route::post('/supplierCreation', 'SupplierRegistrationApprovalController@supplierCreation')->name("Create supplier");
    });

});


