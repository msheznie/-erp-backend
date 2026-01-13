<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderPurchaseRequest extends Model
{
    public $table = 'srm_tender_purchase_request';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['tender_id', 'purchase_request_id', 'company_id', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'purchase_request_id' => 'integer',
        'company_id' => 'integer'
    ];

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id', 'purchaseRequestID');
    }

    public function tender()
    {
        return $this->belongsTo(TenderMaster::class, 'tender_id', 'id');
    }

    public static function getTenderPurchaseRequestForAmd($tenderID){
        return self::where('tender_id', $tenderID)->get();
    }
    public static function getTenderPurchaseForEdit($tenderMasterID){
        return self::select(
            'purchase_request_id as id',
            'erp_purchaserequest.purchaseRequestCode as itemName'
        )
            ->leftJoin('erp_purchaserequest', 'erp_purchaserequest.purchaseRequestID', '=', 'purchase_request_id')
            ->where('tender_id', $tenderMasterID)
            ->get();
    }
    public static function getProcurementLifecycleReportData($companyId){
        return self::with([
            'purchase_request' => function ($q) use ($companyId) {
                return $q->where('companySystemID', $companyId)
                    ->where('PRConfirmedYN', 1)
                    ->where('cancelledYN', 0)
                    ->with([
                        'currency_by:currencyID,CurrencyCode,DecimalPlaces',
                        'details:purchaseRequestDetailsID,purchaseRequestID,totalCost',
                        'all_approvals' => function ($q) {
                            return $q->where('approvedYN', -1)
                                ->with('employee:employeeSystemID,empName');
                        },
                        'po_details' => function ($q) {
                            return $q->with([
                                'order' => function ($q){
                                    $q->with([
                                        'all_approvals' => function ($q) {
                                            return $q->where('approvedYN', -1)
                                                ->with('employee:employeeSystemID,empName');
                                        }
                                    ]);
                                }
                            ]);
                        }
                    ]);
            }, 'tender' => function ($q) {
                $q->select('id', 'tender_code', 'document_system_id', 'published_at', 'bid_submission_opening_date',
                    'technical_bid_opening_date', 'commerical_bid_opening_date', 'contract_id')
                    ->with([
                        'all_approvals' => function ($q) {
                            return $q->where('approvedYN', -1)
                                ->with('employee:employeeSystemID,empName');
                        },
                        'contract' => function ($q) {
                            $q->select('id', 'contractCode', 'startDate', 'endDate', 'agreementSignDate')
                                ->with([
                                    'contract_status' => function ($q) {
                                        $q->select('id', 'contract_history_id', 'status', 'contract_id')
                                            ->whereIn('status', [1, 2, 3, 4, 5, 6]);
                                    }
                                ]);
                        }
                    ]);
            }
        ])
            ->whereHas('purchase_request', function ($q) use ($companyId) {
                return $q->where('companySystemID', $companyId)
                    ->where('PRConfirmedYN', 1)
                    ->where('cancelledYN', 0);
            }
            )
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->get();
    }
}
