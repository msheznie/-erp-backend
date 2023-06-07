<?php
Route::get('attendance-clock-out', 'HRJobInvokeAPIController@clockOutDebug');
Route::get('attendance-clock-in', 'HRJobInvokeAPIController@attendanceClockIn');
Route::get('travel-request-notification', 'HRJobInvokeAPIController@sendTravelRequestNotifications');
Route::get('attendance-notification-debug', 'HRJobInvokeAPIController@attendance_notification_debug');
Route::get('birthdayWishesEmailDebug', 'HRJobInvokeAPIController@birthdayWishesEmailDebug');
Route::get('maximum-leave-carry-forward-debug', 'HRJobInvokeAPIController@maximumLeaveCarryForwardDebug');
Route::get('hr-document-notification', 'HRJobInvokeAPIController@sendHrDocNotifications');