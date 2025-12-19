<?php
/**
 * This file contains sourcing management module related routes
 * 
 * 
 * */


//approval - tender approval
Route::group([], function(){

    Route::post('getPricingScheduleList', 'PricingScheduleMasterAPIController@getPricingScheduleList')->name("Get pricing schedule list");
    Route::post('getPricingScheduleDropDowns', 'PricingScheduleMasterAPIController@getPricingScheduleDropDowns')->name("Get pricing schedule dropdowns");
    Route::post('getPricingScheduleMaster', 'PricingScheduleMasterAPIController@getPricingScheduleMaster')->name("Get pricing schedule master");
    Route::post('addPricingSchedule', 'PricingScheduleMasterAPIController@addPricingSchedule')->name("Add pricing schedule");
    Route::post('deletePricingSchedule', 'PricingScheduleMasterAPIController@deletePricingSchedule')->name("Delete pricing schedule");
    Route::post('getEvaluationCriteriaDetails', 'EvaluationCriteriaDetailsAPIController@getEvaluationCriteriaDetails')->name("Get evaluation criteria details");
    Route::post('getEvaluationCriteriaDropDowns', 'EvaluationCriteriaDetailsAPIController@getEvaluationCriteriaDropDowns')->name("Get evaluation criteria dropdowns");
    Route::post('validateWeightage', 'EvaluationCriteriaDetailsAPIController@validateWeightage')->name("Validate weightage");
    Route::post('addEvaluationCriteria', 'EvaluationCriteriaDetailsAPIController@addEvaluationCriteria')->name("Add evaluation criteria");
    Route::post('deleteEvaluationCriteria', 'EvaluationCriteriaDetailsAPIController@deleteEvaluationCriteria')->name("Delete evaluation criteria");
    Route::post('getEvaluationDetailById', 'EvaluationCriteriaDetailsAPIController@getEvaluationDetailById')->name("Get evaluation detail by id");
    Route::post('validateWeightageEdit', 'EvaluationCriteriaDetailsAPIController@validateWeightageEdit')->name("Validate wightage edit");
    Route::post('editEvaluationCriteria', 'EvaluationCriteriaDetailsAPIController@editEvaluationCriteria')->name("Edit evaluation criteria");
    Route::post('removeCriteriaConfig', 'EvaluationCriteriaScoreConfigAPIController@removeCriteriaConfig')->name("Remove criteria config");
    Route::post('updateCriteriaScore', 'EvaluationCriteriaScoreConfigAPIController@updateCriteriaScore')->name("Update criteria score");
    Route::post('addEvaluationCriteriaConfig', 'EvaluationCriteriaScoreConfigAPIController@addEvaluationCriteriaConfig')->name("Add evaluation criteria config");
    Route::post('getTenderBitsDoc', 'DocumentAttachmentsAPIController@getTenderBitsDoc')->name('Get Tender Bits Documents');
});

