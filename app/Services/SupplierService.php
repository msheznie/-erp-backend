<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Log;

class SupplierService
{

    public function __construct() {}

    /**
     * @param $token
     * @return mixed
     * @throws Throwable
     */

    public function checkValidTokenData($token)
    {
        // Check Token Expired
        $supplierTokenExpired = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()]
        ])
            ->first();

        if (is_null($supplierTokenExpired)) {
            return 1;
        }

        // Check linked already used
        $supplierDataUsingToken = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()],
            ['status', 0]
        ])
            ->first();

        if (is_null($supplierDataUsingToken)) {
            return 2;
        }
    }

    public function getTokenData($token)
    {
        return $supplierDataUsingToken = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()],
            ['status', 0]
        ])
            ->first();
    }

    public function updateTokenStatus($token, $supplierUuid, $name, $email)
    {
        $data = [
            'status' => 1,
            'uuid' => $supplierUuid,
            'name' => $name,
            'email' => $email,
        ];

        LOG::info($token);
        return DB::table('srm_supplier_registration_link')->where('token', $token)->update($data);
    }

    /**
     * create supplier approval setup
     * @param $data
     * @return array
     * @throws Throwable
     */
    public function createSupplierApprovalSetup($data)
    {
        $params = [
            'autoID'    => $data['autoID'],
            'company'   => $data['company'],
            'document'  => $data['documentID'],
            'email'  => $data['email']
        ];

        $confirm = Helper::confirmDocument($params);
        //  throw_unless($confirm && $confirm['success'], $confirm['message']);

        return [
            'success'   => $confirm['success'],
            'message'   => $confirm['message'],
            'data'      => $params
        ];
    }

    public function getPaySupplierInvoiceDetails($input)
    {
        return PaySupplierInvoiceMaster::select([
            'PayMasterAutoId',
            'BPVsupplierID',
            'invoiceType',
            'approved',
            'BPVcode',
            'BPVNarration',
            'suppAmountDocTotal',
            'payAmountBank',
            'BPVchequeNo',
            'directPaymentPayee',
            'createdUserSystemID',
            'supplierTransCurrencyID',
            'BPVbankCurrency',
            'expenseClaimOrPettyCash',
            'payment_mode',
            'projectID',
            'BPVdate',
            'approvedDate',
            'confirmedYN',
            'retentionVatAmount',
            'payAmountSuppTrans',
            'VATAmount',
            'BPVAccount',
            'confirmedByEmpSystemID',
            'modifiedUserSystemID',
            'chequeSentToTreasuryByEmpSystemID',
            'companySystemID',
            'localCurrencyID',
            'companyRptCurrencyID',
            'cancelledByEmpSystemID'
        ])
            ->where('PayMasterAutoId',  $input['extra']['id'])
            ->with($this->getRelations())
            ->first();
    }

    private function getRelations()
    {
        return [
            'project' => function ($q) {
                $q->select(['id', 'projectCode']);
            },
            'supplier' => function ($q) {
                $q->select(['supplierCodeSystem', 'supplierName', 'primarySupplierCode']);
            },
            'bankaccount' => function ($q) {
                $q->select(['bankAccountAutoID', 'bankName', 'AccountNo']);
            },
            'transactioncurrency' => function ($q) {
                $q->select(['currencyID', 'CurrencyName', 'CurrencyCode', 'DecimalPlaces']);
            },
            'paymentmode' => function ($q) {
                $q->select(['id', 'description']);
            },
            'supplierdetail' => function ($query) {
                $query->select(['payDetailAutoID', 'PayMasterAutoId', 'purchaseOrderID', 'bookingInvDocCode', 'supplierInvoiceNo',
                  'supplierInvoiceAmount', 'supplierInvoiceDate','supplierDefaultAmount', 'supplierPaymentAmount',
                  'paymentBalancedAmount', 'retentionVatAmount'
                ])
                ->with(['pomaster' => function ($q) {
                    $q->select(['purchaseOrderID', 'purchaseOrderCode', 'poProcessId']);
                }]);
            },
            'company' => function ($q) {
                $q->select(['companySystemID', 'CompanyID', 'CompanyName', 'CompanyAddress', 'logoPath', 'masterCompanySystemIDReorting']);
            },
            'localcurrency' => function ($q) {
                $q->select(['currencyID', 'CurrencyName', 'CurrencyCode', 'DecimalPlaces']);
            },
            'rptcurrency' => function ($q) {
                $q->select(['currencyID', 'CurrencyName', 'CurrencyCode', 'DecimalPlaces']);
            },
            'advancedetail' => function ($q) {
                $q->select([
                    'PayMasterAutoId',
                    'purchaseOrderCode',
                    'comments',
                    'paymentAmount',
                    'localAmount',
                    'comRptAmount',
                ]);
            },
            'confirmed_by' => function ($q) {
                $q->select(['employeeSystemID', 'empName']);
            },
            'modified_by' => function ($q) {
                $q->select(['employeeSystemID', 'empName']);
            },
            'cheque_treasury_by' => function ($q) {
                $q->select(['employeeSystemID', 'empName']);
            },
            'directdetail' => function ($query) {
                $this->addDirectDetailRelations($query);
            },
            'approved_by' => function ($query) {
                $query->select(['employeeSystemID',
                'approvedDate',
                'approvedYN',
                'documentSystemCode'])
                ->with(['employee' => function ($q) {
                    $q->select(
                        [
                            "employeeSystemID",
                            "empID",
                            "empName",
                            "empFullName"
                        ]
                    );
                }])->where('documentSystemID', 4);
            },
            'created_by' => function ($q) {
                $q->select(['employeeSystemID', 'empName']);
            },
            'cancelled_by' => function ($q) {
                $q->select(['employeeSystemID', 'empName']);
            },
            'bankledgers' => function ($query) {
                $query->select([
                    'documentSystemCode',
                    'documentSystemID',
                    'bankRecAutoID'
                ])->where('documentSystemID', 4)->with(['bankrec_by' => function ($q) {
                    $q->select(['bankRecAutoID', 'documentSystemID', 'documentID']);
                }]);
            },
            'bankledger_by' => function ($query) {
                $query->select([
                    'documentSystemCode',
                    'documentSystemID',
                    'bankRecAutoID',
                    'paymentBankTransferID',
                ])->where('documentSystemID', 4)
                    ->with(['bankrec_by' => function ($q) {
                    $q->select(['bankRecAutoID', 'documentSystemID', 'documentID']);
                }, 'bank_transfer' => function ($q) {
                    $q->select(['paymentBankTransferID', 'documentSystemID', 'documentID']);
                }]);
            },
            'audit_trial.modified_by' => function ($q) {
                $q->select(['employeeSystemID', 'empName']);
            },
        ];
    }

    private function addDirectDetailRelations($query)
    {
        $query->select([
            'directPaymentDetailsID',
            'directPaymentAutoID',
            'glCode',
            'glCodeDes',
            'DPAmount',
            'VATAmountLocal',
            'vatAmount',
            'VATAmountRpt',
            'localAmount',
            'comRptAmount',
            'detail_project_id',
            'serviceLineSystemID',
        ])->with([
            'project' => function ($q) {
                $q->select(['id', 'projectCode', 'description']);
            },
            'segment' => function ($q) {
                $q->select(['serviceLineSystemID', 'ServiceLineDes']);
            },
        ]);
    }
}
