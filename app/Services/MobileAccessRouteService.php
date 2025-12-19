<?php

namespace App\Services;

class MobileAccessRouteService
{
    public static function getRoutes(){
        $data['routes'] = [
            'api/v1/getProfileDetails',
            'api/v1/getSmeProfilePersonalDetails',
            'user/companies',
            'api/v1/getAllDocumentApproval',
            'api/v1/sme-profile-pending-approvals',
            'api/v1/getProcurementOrderRecord',
            'api/v1/getPaymentVoucherMaster',
            'api/v1/getInvoiceMasterRecord',
            'api/v1/purchase_requests/{purchaseRequestID}',
            'api/v1/getDebitNoteMasterRecord',
            'api/v1/getCreditNoteMasterRecord',
            'api/v1/customerInvoiceDetails',
            'api/v1/getReceiptVoucherMasterRecord',
            'api/v1/document_attachments',
            'api/v1/sme-attachment/{masterID}}/{documentID}',
            'api/v1/downloadFile',
            'api/v1/downloadHrmsFile',
            'api/v1/approvalPreCheckAllDoc',
            'api/v1/approvePurchaseRequest',
            'api/v1/rejectPurchaseRequest',
            'api/v1/sme-pay-slip-months/{isNonPayRoll}',
            'api/v1/sme-pay-slip',
            'api/v1/getLeaveTypeWithBalance',
            'api/v1/getLeaveAvailability',
            'api/v1/getLeaveHistory',
            'api/v1/sme-leaves-dataTable',
            'api/v1/getLeaveDetails',
            'api/v1/sme-leave/{leaveMasterID}',
            'api/v1/sme-leave',
            'api/v1/sme-leave/{leaveMasterID}',
            'sme-leave-reOpen/{leaveMasterID}',
            'api/v1/sme-leave-reOpen/{leaveMasterID}?isCancel=1',
            'api/v1/sme-attachment/{attachmentID}',
            'api/v1/sme-expenseClaimFormData',
            'api/v1/sme-expense-claim',
            'api/v1/sme-expense-claim/{expenseClaimMasterAutoID}',
            'api/v1/sme-expense-claim-add-det/{expenseClaimMasterAutoID}',
            'api/v1/sme-expense-claim-det/{expenseClaimDetailID}',
            'api/v1/saveExpenseClaimAttachments',
            'api/v1/sme-get-expenseClaims',
            'api/v1/sme-expense-claim-print/{claimMasterID}',
            'api/v1/sme-expense-claim/{expenseClaimMasterAutoID}',
            'api/v1/fcm_tokens',
            'api/v1/logoutApiUser',
            'api/v1/getSmeApprovalData',
            'api/v1/sme-leave-formData',
            'api/v1/sme-leave-days-calculation',
            'api/v1/sme-leave-summary',
            'api/v1/sme-attachment',
            'api/v1/sme-expense-claim-confirm/{expenseClaimMasterAutoID}',
            'api/v1/sme-expense-claim-reopen/{expenseClaimMasterAutoID}',
            'api/v1/sme-leavesApprove',
            'api/v1/sme-expense-claim-approve',
            'api/v1/sme-leave-cancellation-req',
            'api/v1/attendance-status',
            'api/v1/attendance-register',
            'api/v1/attendance-review',
            'api/v1/attendance-report-data',
            'api/v1/getAppearance',
            'api/v1/getConfigurationInfo',
            'api/v1/login'
        ];

        $data['nonAuthRoutes'] =
        [
            'api/v1/getAppearance',
            'api/v1/getConfigurationInfo',
            'api/v1/login'
        ];

        return $data;
    }

}
