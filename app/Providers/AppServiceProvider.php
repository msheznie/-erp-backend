<?php

namespace App\Providers;

use App\helper\ExchangeSetupConfig;
use App\Models\AssetCapitalization;
use App\Models\AssetCapitalizationDetail;
use App\Models\AssetDisposalMaster;
use App\Models\FixedAssetDepreciationMaster;
use App\Observers\CapitalizationDetailObserver;
use App\Observers\CapitalizationObserver;
use App\Observers\DepreciationObserver;
use App\Observers\AssetObserver;
use App\Observers\DisposalObserver;
use App\Observers\TenderObserver;
use Carbon\Carbon;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\FixedAssetMaster;
use App\Models\TenderMaster;
use App\Observers\TenderBidEmployeeObserver;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\PricingScheduleMaster;
use App\Models\ScheduleBidFormatDetails;
use App\Observers\PricingScheduleMasterObserver;
use App\Observers\ScheduleBidFormatDetailsObserver;
use App\Models\PricingScheduleDetail;
use App\Observers\PricingScheduleDetailObserver;
use App\Models\TenderBoqItems;
use App\Observers\TenderBoqItemsObserver;
use App\Models\EvaluationCriteriaDetails;
use App\Observers\EvaluationCriteriaDetailsObserver;
use App\Models\DocumentAttachments;
use App\Observers\DocumentAttachmentsObserver;
use App\Models\TenderCirculars;
use App\Observers\TenderCircularsObserver;
use App\Models\CircularAmendments;
use App\Observers\CircularAmendmentsObserver;
use App\Models\CalendarDatesDetail;
use App\Observers\CalendarDatesDetailObserver;
use App\Models\ProcumentActivity;
use App\Observers\ProcumentActivityObserver;
use App\Models\TenderDocumentTypeAssign;
use App\Observers\TenderDocumentTypeObserver;
use App\Models\FinanceItemcategorySubAssigned;
use App\Observers\FinanceItemcategorySubAssignedObserver;
class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('exchangeSetupConfig', function ($app) {
            return new ExchangeSetupConfig();
        });
    }
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
        });
        FinanceItemcategorySubAssigned::observe(FinanceItemcategorySubAssignedObserver::class);
        AssetCapitalizationDetail::observe(CapitalizationDetailObserver::class);
        FixedAssetDepreciationMaster::observe(DepreciationObserver::class);
        AssetCapitalization::observe(CapitalizationObserver::class);
        AssetDisposalMaster::observe(DisposalObserver::class);
        TenderMaster::observe(TenderObserver::class);
        SrmTenderBidEmployeeDetails::observe(TenderBidEmployeeObserver::class);
        PricingScheduleMaster::observe(PricingScheduleMasterObserver::class);
        ScheduleBidFormatDetails::observe(ScheduleBidFormatDetailsObserver::class);
        PricingScheduleDetail::observe(PricingScheduleDetailObserver::class);
        TenderBoqItems::observe(TenderBoqItemsObserver::class);
        EvaluationCriteriaDetails::observe(EvaluationCriteriaDetailsObserver::class);
        DocumentAttachments::observe(DocumentAttachmentsObserver::class);
        TenderCirculars::observe(TenderCircularsObserver::class);
        CircularAmendments::observe(CircularAmendmentsObserver::class);
        CalendarDatesDetail::observe(CalendarDatesDetailObserver::class);
        ProcumentActivity::observe(ProcumentActivityObserver::class);
        TenderDocumentTypeAssign::observe(TenderDocumentTypeObserver::class);

        Validator::extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value > $min_value;
        });

        Validator::extend('greater_than_or_equal_field', function($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value >= $min_value;
        });

        Validator::replacer('greater_than_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        Validator::replacer('greater_than_or_equal_field', function($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        Passport::routes();
        passport::$revokeOtherTokens;
        passport::$pruneRevokedTokens;
        Passport::tokensExpireIn(Carbon::now()->addHours(1));
        Passport::refreshTokensExpireIn(Carbon::now()->addHours(1));
    }
}
