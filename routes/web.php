

Route::resource('customerCatalogMasters', 'CustomerCatalogMasterController');

Route::resource('customerCatalogDetails', 'CustomerCatalogDetailController');
Route::resource('reportColumnTemplates', 'ReportColumnTemplateController');

Route::resource('reportColumnTemplateDetails', 'ReportColumnTemplateDetailController');


Route::resource('dashboardWidgetMasters', 'DashboardWidgetMasterController');

Route::resource('fcmTokens', 'FcmTokenController');

Route::resource('deliveryOrders', 'DeliveryOrderController');

Route::resource('deliveryOrderDetails', 'DeliveryOrderDetailController');

Route::resource('tenants', 'TenantController');

Route::resource('deliveryOrderRefferedbacks', 'DeliveryOrderRefferedbackController');

Route::resource('deliveryOrderDetailRefferedbacks', 'DeliveryOrderDetailRefferedbackController');