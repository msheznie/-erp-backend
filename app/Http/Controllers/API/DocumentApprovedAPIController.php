<?php
/**
 * =============================================
 * -- File Name : DocumentApprovedAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Approval
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Document approved.
 * -- REVISION HISTORY
 * --
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentApprovedAPIRequest;
use App\Http\Requests\API\UpdateDocumentApprovedAPIRequest;
use App\Models\ApprovalLevel;
use App\Models\DocumentApproved;
use App\Repositories\DocumentApprovedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentApprovedController
 * @package App\Http\Controllers\API
 */
class DocumentApprovedAPIController extends AppBaseController
{
    /** @var  DocumentApprovedRepository */
    private $documentApprovedRepository;

    public function __construct(DocumentApprovedRepository $documentApprovedRepo)
    {
        $this->documentApprovedRepository = $documentApprovedRepo;
    }

    /**
     * Display a listing of the DocumentApproved.
     * GET|HEAD /documentApproveds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentApprovedRepository->pushCriteria(new RequestCriteria($request));
        $this->documentApprovedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentApproveds = $this->documentApprovedRepository->all();

        return $this->sendResponse($documentApproveds->toArray(), 'Document Approveds retrieved successfully');
    }

    /**
     * Store a newly created DocumentApproved in storage.
     * POST /documentApproveds
     *
     * @param CreateDocumentApprovedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        $documentApproveds = $this->documentApprovedRepository->create($input);

        return $this->sendResponse($documentApproveds->toArray(), 'Document Approved saved successfully');
    }

    /**
     * Display the specified DocumentApproved.
     * GET|HEAD /documentApproveds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DocumentApproved $documentApproved */
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            return $this->sendError('Document Approved not found');
        }

        return $this->sendResponse($documentApproved->toArray(), 'Document Approved retrieved successfully');
    }

    /**
     * Update the specified DocumentApproved in storage.
     * PUT/PATCH /documentApproveds/{id}
     *
     * @param  int $id
     * @param UpdateDocumentApprovedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentApproved $documentApproved */
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            return $this->sendError('Document Approved not found');
        }

        $documentApproved = $this->documentApprovedRepository->update($input, $id);

        return $this->sendResponse($documentApproved->toArray(), 'DocumentApproved updated successfully');
    }

    /**
     * Remove the specified DocumentApproved from storage.
     * DELETE /documentApproveds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DocumentApproved $documentApproved */
        $documentApproved = $this->documentApprovedRepository->findWithoutFail($id);

        if (empty($documentApproved)) {
            return $this->sendError('Document Approved not found');
        }

        $documentApproved->delete();

        return $this->sendResponse($id, 'Document Approved deleted successfully');
    }

    public function getAllDocumentApproval(request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');

        $employeeSystemID = \Helper::getEmployeeSystemID();

        $limit = '';
        if(isset($input['forDashboardWidget']) && $input['forDashboardWidget'] ==1){
            $limit = 'LIMIT 6 ';
        }

        $where = "";
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $documentType = isset($input['documentType']) ? $input['documentType'] : array();
        $companies    = isset($input['companies']) ? $input['companies'] : array();


        $filter = 'AND erp_documentapproved.documentSystemID IN (0) ';


        if (!empty($documentType)) {
            $filter = " AND erp_documentapproved.documentSystemID IN (" . implode(',', $documentType) . ")";
        }


        if ($companies) {
            $filter .= " AND erp_documentapproved.companySystemID IN (" . implode(',', $companies) . ")";
        }

        $where = '';
        if ($search) {
            $search = str_replace("\\", "\\\\\\\\", $search);
            $where .= " WHERE  (documentCode LIKE '%$search%' OR  comments LIKE '%$search%' OR SupplierOrCustomer LIKE '%$search%' OR DocumentValue LIKE '%$search%' )";
        }

        $isApproved   = isset($input['isApproved']) ? $input['isApproved'] : 0;
        if($isApproved){
            $qry = "SELECT t.*,companymaster.*,erp_documentmaster.documentDescription FROM (SELECT
	*
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_purchaserequest.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	'' AS SupplierOrCustomer,
	2 as DecimalPlaces ,
	cur.CurrencyCode AS DocumentCurrency,
	SUM(prd.totalCost) AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	- 1 AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_purchaserequest ON erp_purchaserequest.companySystemID = erp_documentapproved.companySystemID 
	AND erp_purchaserequest.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_purchaserequest.serviceLineSystemID = erp_documentapproved.serviceLineSystemID 
	AND erp_purchaserequest.purchaseRequestID = erp_documentapproved.documentSystemCode 
	AND erp_purchaserequest.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	LEFT JOIN erp_purchaserequestdetails as prd ON prd.purchaseRequestID = erp_purchaserequest.purchaseRequestID 
	LEFT JOIN currencymaster as cur ON cur.currencyID = erp_purchaserequest.currency 
	AND erp_purchaserequest.PRConfirmedYN = 1 
	AND erp_purchaserequest.cancelledYN = 0 
	AND erp_purchaserequest.refferedBackYN = 0 
	AND erp_purchaserequest.approved = -1
WHERE
	erp_documentapproved.approvedYN = -1 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 1, 50, 51 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingRequestApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_purchaseordermaster.narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	erp_purchaseordermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_purchaseordermaster.poTotalSupplierTransactionCurrency AS DocumentValue,
	erp_purchaseordermaster.amended AS amended,
	erp_documentapproved.approvedYN,
	- 1 AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_purchaseordermaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_purchaseordermaster.serviceLineSystemID = erp_documentapproved.serviceLineSystemID 
	AND erp_purchaseordermaster.purchaseOrderID = erp_documentapproved.documentSystemCode 
	AND erp_purchaseordermaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_purchaseordermaster.poConfirmedYN = 1 
	AND erp_purchaseordermaster.approved = -1
	AND erp_purchaseordermaster.poCancelledYN = 0 
	AND erp_purchaseordermaster.refferedBackYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_purchaseordermaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 2, 5, 52 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingOrderApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_paysupplierinvoicemaster.BPVNarration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	erp_paysupplierinvoicemaster.directPaymentPayee AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_paysupplierinvoicemaster.payAmountSuppTrans AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_paysupplierinvoicemaster.invoiceType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_paysupplierinvoicemaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_paysupplierinvoicemaster.PayMasterAutoId = erp_documentapproved.documentSystemCode 
	AND erp_paysupplierinvoicemaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_paysupplierinvoicemaster.confirmedYN = 1 
	AND erp_paysupplierinvoicemaster.approved = -1  
	AND erp_paysupplierinvoicemaster.cancelYN = 0 
	AND erp_paysupplierinvoicemaster.ReversedYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_paysupplierinvoicemaster.supplierTransCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 4 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingPaymentApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_bookinvsuppmaster.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	suppliermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_bookinvsuppmaster.bookingAmountTrans AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_bookinvsuppmaster.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_bookinvsuppmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_documentapproved.documentSystemCode 
	AND erp_bookinvsuppmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_bookinvsuppmaster.confirmedYN = 1 
	AND erp_bookinvsuppmaster.approved = -1
	AND erp_bookinvsuppmaster.cancelYN = 0
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_bookinvsuppmaster.supplierID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_bookinvsuppmaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 11 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingSupplierInvoiceApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_debitnote.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	suppliermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_debitnote.debitAmountTrans AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_debitnote.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_debitnote ON erp_debitnote.companySystemID = erp_documentapproved.companySystemID 
	AND erp_debitnote.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_debitnote.debitNoteAutoID = erp_documentapproved.documentSystemCode 
	AND erp_debitnote.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_debitnote.confirmedYN = 1 
	AND erp_debitnote.approved = -1
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_debitnote.supplierID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_debitnote.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 15 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingDebiteNoteApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_custinvoicedirect.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_custinvoicedirect.bookingAmountTrans + IFNULL(VATAmount,0) AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_custinvoicedirect.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.companySystemID = erp_documentapproved.companySystemID 
	AND erp_custinvoicedirect.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_custinvoicedirect.custInvoiceDirectAutoID = erp_documentapproved.documentSystemCode 
	AND erp_custinvoicedirect.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_custinvoicedirect.confirmedYN = 1 
	AND erp_custinvoicedirect.approved = -1
	AND erp_custinvoicedirect.canceledYN = 0
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_custinvoicedirect.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_custinvoicedirect.custTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 20 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingCustomerInvoiceApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_creditnote.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_creditnote.creditAmountTrans AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_creditnote.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_creditnote ON erp_creditnote.companySystemID = erp_documentapproved.companySystemID 
	AND erp_creditnote.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_creditnote.creditNoteAutoID = erp_documentapproved.documentSystemCode 
	AND erp_creditnote.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_creditnote.approved = -1 
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_creditnote.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_creditnote.customerCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 19 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingCreditNoteApprovals
	UNION All
	SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_quotationmaster.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_quotationmaster.transactionAmount AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_quotationmaster.quotationType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_quotationmaster ON erp_quotationmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_quotationmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_quotationmaster.quotationMasterID = erp_documentapproved.documentSystemCode 
	AND erp_quotationmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_quotationmaster.approvedYN = -1 
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_quotationmaster.customerSystemCode
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_quotationmaster.customerCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 67 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingQuotationApprovals
	UNION All
	SELECT
	* 
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_quotationmaster.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_quotationmaster.transactionAmount AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_quotationmaster.quotationType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_quotationmaster ON erp_quotationmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_quotationmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_quotationmaster.quotationMasterID = erp_documentapproved.documentSystemCode 
	AND erp_quotationmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_quotationmaster.approvedYN = -1 
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_quotationmaster.customerSystemCode
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_quotationmaster.customerCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 68 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingSalesOrderApprovals
	UNION All
	SELECT
	*
FROM
	(
SELECT
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_customerreceivepayment.narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	ABS(erp_customerreceivepayment.receivedAmount) AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_customerreceivepayment.documentType AS documentType
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_customerreceivepayment ON erp_customerreceivepayment.companySystemID = erp_documentapproved.companySystemID
	AND erp_customerreceivepayment.documentSystemID = erp_documentapproved.documentSystemID
	AND erp_customerreceivepayment.custReceivePaymentAutoID = erp_documentapproved.documentSystemCode
	AND erp_customerreceivepayment.RollLevForApp_curr = erp_documentapproved.rollLevelOrder
	AND erp_customerreceivepayment.confirmedYN = 1
	AND erp_customerreceivepayment.approved = -1
	LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_customerreceivepayment.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0
	AND erp_documentapproved.approvalGroupID > 0
    $filter
	AND erp_documentapproved.documentSystemID IN ( 21 )
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingReceiptVoucherApprovals
	)t 
	INNER JOIN companymaster ON t.companySystemID = companymaster.companySystemID 
	LEFT JOIN erp_documentmaster ON t.documentSystemID = erp_documentmaster.documentSystemID 
	$where ORDER BY approvedDate $sort $limit";


        }else{
            $qry = "SELECT t.*,companymaster.*,erp_documentmaster.documentDescription FROM (SELECT
	*
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_purchaserequest.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 as DecimalPlaces ,
	cur.CurrencyCode AS DocumentCurrency,
	SUM(prd.totalCost) AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	- 1 AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.ServiceLineSystemID = erp_documentapproved.serviceLineSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_purchaserequest ON erp_purchaserequest.companySystemID = erp_documentapproved.companySystemID 
	AND erp_purchaserequest.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_purchaserequest.serviceLineSystemID = erp_documentapproved.serviceLineSystemID 
	AND erp_purchaserequest.purchaseRequestID = erp_documentapproved.documentSystemCode 
	AND erp_purchaserequest.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	LEFT JOIN erp_purchaserequestdetails as prd ON prd.purchaseRequestID = erp_purchaserequest.purchaseRequestID 
	LEFT JOIN currencymaster as cur ON cur.currencyID = erp_purchaserequest.currency 
	AND erp_purchaserequest.PRConfirmedYN = 1 
	AND erp_purchaserequest.cancelledYN = 0 
	AND erp_purchaserequest.refferedBackYN = 0 
	AND erp_purchaserequest.approved = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 1, 50, 51 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingRequestApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_purchaseordermaster.narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	erp_purchaseordermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_purchaseordermaster.poTotalSupplierTransactionCurrency AS DocumentValue,
	erp_purchaseordermaster.amended AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	- 1 AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.ServiceLineSystemID = erp_documentapproved.serviceLineSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_purchaseordermaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_purchaseordermaster.serviceLineSystemID = erp_documentapproved.serviceLineSystemID 
	AND erp_purchaseordermaster.purchaseOrderID = erp_documentapproved.documentSystemCode 
	AND erp_purchaseordermaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_purchaseordermaster.poConfirmedYN = 1 
	AND erp_purchaseordermaster.approved = 0
	AND erp_purchaseordermaster.poCancelledYN = 0 
	AND erp_purchaseordermaster.refferedBackYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_purchaseordermaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 2, 5, 52 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingOrderApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_paysupplierinvoicemaster.BPVNarration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	erp_paysupplierinvoicemaster.directPaymentPayee AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_paysupplierinvoicemaster.payAmountSuppTrans AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_paysupplierinvoicemaster.invoiceType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_paysupplierinvoicemaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_paysupplierinvoicemaster.PayMasterAutoId = erp_documentapproved.documentSystemCode 
	AND erp_paysupplierinvoicemaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_paysupplierinvoicemaster.confirmedYN = 1 
	AND erp_paysupplierinvoicemaster.approved = 0  
	AND erp_paysupplierinvoicemaster.cancelYN = 0 
	AND erp_paysupplierinvoicemaster.ReversedYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_paysupplierinvoicemaster.supplierTransCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 4 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingPaymentApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_bookinvsuppmaster.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	suppliermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_bookinvsuppmaster.bookingAmountTrans AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_bookinvsuppmaster.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_bookinvsuppmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_documentapproved.documentSystemCode 
	AND erp_bookinvsuppmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_bookinvsuppmaster.confirmedYN = 1 
	AND erp_bookinvsuppmaster.approved = 0
	AND erp_bookinvsuppmaster.cancelYN = 0
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_bookinvsuppmaster.supplierID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_bookinvsuppmaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 11 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingSupplierInvoiceApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_debitnote.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	suppliermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_debitnote.debitAmountTrans AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_debitnote.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_debitnote ON erp_debitnote.companySystemID = erp_documentapproved.companySystemID 
	AND erp_debitnote.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_debitnote.debitNoteAutoID = erp_documentapproved.documentSystemCode 
	AND erp_debitnote.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_debitnote.confirmedYN = 1 
	AND erp_debitnote.approved = 0
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_debitnote.supplierID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_debitnote.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 15 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingDebiteNoteApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_custinvoicedirect.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_custinvoicedirect.bookingAmountTrans + IFNULL(VATAmount,0) AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_custinvoicedirect.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.companySystemID = erp_documentapproved.companySystemID 
	AND erp_custinvoicedirect.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_custinvoicedirect.custInvoiceDirectAutoID = erp_documentapproved.documentSystemCode 
	AND erp_custinvoicedirect.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_custinvoicedirect.confirmedYN = 1 
	AND erp_custinvoicedirect.approved = 0
	AND erp_custinvoicedirect.canceledYN = 0
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_custinvoicedirect.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_custinvoicedirect.custTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 20 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingCustomerInvoiceApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_creditnote.comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_creditnote.creditAmountTrans AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_creditnote.documentType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_creditnote ON erp_creditnote.companySystemID = erp_documentapproved.companySystemID 
	AND erp_creditnote.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_creditnote.creditNoteAutoID = erp_documentapproved.documentSystemCode 
	AND erp_creditnote.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_creditnote.approved = 0 
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_creditnote.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_creditnote.customerCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 19 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingCreditNoteApprovals
	UNION All 
	SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_quotationmaster.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_quotationmaster.transactionAmount AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_quotationmaster.quotationType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_quotationmaster ON erp_quotationmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_quotationmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_quotationmaster.quotationMasterID = erp_documentapproved.documentSystemCode 
	AND erp_quotationmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_quotationmaster.approvedYN = 0 
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_quotationmaster.customerSystemCode
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_quotationmaster.customerCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 67 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingQuotationApprovals
	UNION All
	SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_quotationmaster.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_quotationmaster.transactionAmount AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_quotationmaster.quotationType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_quotationmaster ON erp_quotationmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_quotationmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_quotationmaster.quotationMasterID = erp_documentapproved.documentSystemCode 
	AND erp_quotationmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_quotationmaster.approvedYN = 0 
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_quotationmaster.customerSystemCode
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_quotationmaster.customerCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 68 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingSalesOrderApprovals
	UNION All
	SELECT
	*
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_customerreceivepayment.narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	ABS(erp_customerreceivepayment.receivedAmount) AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN,
	erp_customerreceivepayment.documentType AS documentType
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_customerreceivepayment ON erp_customerreceivepayment.companySystemID = erp_documentapproved.companySystemID
	AND erp_customerreceivepayment.documentSystemID = erp_documentapproved.documentSystemID
	AND erp_customerreceivepayment.custReceivePaymentAutoID = erp_documentapproved.documentSystemCode
	AND erp_customerreceivepayment.RollLevForApp_curr = erp_documentapproved.rollLevelOrder
	AND erp_customerreceivepayment.confirmedYN = 1
	AND erp_customerreceivepayment.approved = 0
	LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_customerreceivepayment.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0
	AND erp_documentapproved.approvalGroupID > 0
    $filter
	AND erp_documentapproved.documentSystemID IN ( 21 )
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingReceiptVoucherApprovals
	)t 
	INNER JOIN companymaster ON t.companySystemID = companymaster.companySystemID 
	LEFT JOIN erp_documentmaster ON t.documentSystemID = erp_documentmaster.documentSystemID 
	$where ORDER BY docConfirmedDate $sort $limit";
        }

        $output = DB::select($qry);

        if(isset($input['forDashboardWidget']) && $input['forDashboardWidget'] ==1){
            return $this->sendResponse($output,'data retrived successfully');
        }


        $request->request->remove('search.value');
        $request->request->remove('order');
        $data['order'] = [];
        /*  $data['order'][0]['column'] = '';
          $data['order'][0]['dir'] = '';*/
        $data['search']['value'] = '';
        $request->merge($data);

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $output = [];
        }

        return \DataTables::of($output)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getTotalCountOfApproval()
    {
        $employeeSystemID = \Helper::getEmployeeSystemID();
        $qry = " SELECT IFNULL(SUM(totalCount),0) as totalCount FROM (
        SELECT
        *
        FROM
            (
        SELECT
            count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
            AND employeesdepartments.ServiceLineSystemID = erp_documentapproved.serviceLineSystemID 
            AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
            INNER JOIN erp_purchaserequest ON erp_purchaserequest.companySystemID = erp_documentapproved.companySystemID 
            AND erp_purchaserequest.documentSystemID = erp_documentapproved.documentSystemID 
            AND erp_purchaserequest.serviceLineSystemID = erp_documentapproved.serviceLineSystemID 
            AND erp_purchaserequest.purchaseRequestID = erp_documentapproved.documentSystemCode 
            AND erp_purchaserequest.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
            AND erp_purchaserequest.PRConfirmedYN = 1 
            AND erp_purchaserequest.approved = 0 
            AND erp_purchaserequest.cancelledYN = 0 
            AND erp_purchaserequest.refferedBackYN = 0 
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN ( 1, 50, 51 ) 
            AND employeesdepartments.employeeSystemID = $employeeSystemID 
            ) AS PendingRequestApprovals UNION ALL
        SELECT
            * 
        FROM
            (
        SELECT
                count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
            AND employeesdepartments.ServiceLineSystemID = erp_documentapproved.serviceLineSystemID 
            AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
            INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.companySystemID = erp_documentapproved.companySystemID 
            AND erp_purchaseordermaster.documentSystemID = erp_documentapproved.documentSystemID 
            AND erp_purchaseordermaster.serviceLineSystemID = erp_documentapproved.serviceLineSystemID 
            AND erp_purchaseordermaster.purchaseOrderID = erp_documentapproved.documentSystemCode 
            AND erp_purchaseordermaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
            AND erp_purchaseordermaster.poConfirmedYN = 1 
            AND erp_purchaseordermaster.approved = 0 
            AND erp_purchaseordermaster.poCancelledYN = 0 
            AND erp_purchaseordermaster.refferedBackYN = 0 
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN ( 2, 5, 52 ) 
            AND employeesdepartments.employeeSystemID = $employeeSystemID 
            ) AS PendingOrderApprovals UNION ALL
            SELECT 
            *
            FROM
            (
            SELECT
                count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
            AND employeesdepartments.employeeGroupID=erp_documentapproved.approvalGroupID	
            INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.companySystemID=erp_documentapproved.companySystemID AND erp_paysupplierinvoicemaster.documentSystemID=erp_documentapproved.documentSystemID  AND erp_paysupplierinvoicemaster.PayMasterAutoId=erp_documentapproved.documentSystemCode AND erp_paysupplierinvoicemaster.RollLevForApp_curr=erp_documentapproved.rollLevelOrder AND erp_paysupplierinvoicemaster.confirmedYN=1 AND erp_paysupplierinvoicemaster.approved=0 AND erp_paysupplierinvoicemaster.cancelYN=0 AND erp_paysupplierinvoicemaster.ReversedYN=0
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN (4)
            AND employeesdepartments.employeeSystemID=$employeeSystemID
            ) AS PendingPaymentApprovals UNION ALL
            SELECT 
            *
            FROM
            (
            SELECT
                count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
            AND employeesdepartments.employeeGroupID=erp_documentapproved.approvalGroupID	
            INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.companySystemID=erp_documentapproved.companySystemID AND erp_bookinvsuppmaster.documentSystemID=erp_documentapproved.documentSystemID  AND erp_bookinvsuppmaster.bookingSuppMasInvAutoID=erp_documentapproved.documentSystemCode AND erp_bookinvsuppmaster.RollLevForApp_curr=erp_documentapproved.rollLevelOrder AND erp_bookinvsuppmaster.confirmedYN=1 AND erp_bookinvsuppmaster.approved=0 AND erp_bookinvsuppmaster.cancelYN=0
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN (11)
            AND employeesdepartments.employeeSystemID=$employeeSystemID
            ) AS PendingSupplierInvoiceApprovals UNION ALL
            SELECT 
            *
            FROM
            (
            SELECT
                count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
            AND employeesdepartments.employeeGroupID=erp_documentapproved.approvalGroupID	
            INNER JOIN erp_debitnote ON erp_debitnote.companySystemID=erp_documentapproved.companySystemID AND erp_debitnote.documentSystemID=erp_documentapproved.documentSystemID  AND erp_debitnote.debitNoteAutoID=erp_documentapproved.documentSystemCode AND erp_debitnote.RollLevForApp_curr=erp_documentapproved.rollLevelOrder AND erp_debitnote.confirmedYN=1 AND erp_debitnote.approved=0
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN (15)
            AND employeesdepartments.employeeSystemID=$employeeSystemID
            ) AS PendingDebiteNoteApprovals UNION ALL
            SELECT 
            *
            FROM
            (
            SELECT
                count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
            AND employeesdepartments.employeeGroupID=erp_documentapproved.approvalGroupID	
            INNER JOIN erp_custinvoicedirect ON erp_custinvoicedirect.companySystemID=erp_documentapproved.companySystemID AND erp_custinvoicedirect.documentSystemID=erp_documentapproved.documentSystemID  AND erp_custinvoicedirect.custInvoiceDirectAutoID=erp_documentapproved.documentSystemCode AND erp_custinvoicedirect.RollLevForApp_curr=erp_documentapproved.rollLevelOrder AND erp_custinvoicedirect.confirmedYN=1 AND erp_custinvoicedirect.approved=0 AND erp_custinvoicedirect.canceledYN=0
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN (20)
            AND employeesdepartments.employeeSystemID=$employeeSystemID
            ) AS PendingCustomerInvoiceApprovals UNION ALL
            SELECT 
            *
            FROM
            (
            SELECT
            count(erp_documentapproved.companySystemID) as totalCount
        FROM
            erp_documentapproved
            INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
            AND employeesdepartments.employeeGroupID=erp_documentapproved.approvalGroupID	
            INNER JOIN erp_creditnote ON erp_creditnote.companySystemID=erp_documentapproved.companySystemID AND erp_creditnote.documentSystemID=erp_documentapproved.documentSystemID  AND erp_creditnote.creditNoteAutoID=erp_documentapproved.documentSystemCode AND erp_creditnote.RollLevForApp_curr=erp_documentapproved.rollLevelOrder AND erp_creditnote.confirmedYN=1 AND erp_creditnote.approved=0
        WHERE
            erp_documentapproved.approvedYN = 0 
            AND erp_documentapproved.rejectedYN = 0 
            AND erp_documentapproved.approvalGroupID > 0 
            AND erp_documentapproved.documentSystemID IN (19)
            AND employeesdepartments.employeeSystemID=$employeeSystemID
            ) AS PendingCreditNoteApprovals
        )t";

        $qry1="SELECT
                        count(erp_documentapproved.companySystemID)  as totalCount
                    FROM
                        (
                        SELECT
                            companySystemID,
                            departmentSystemID,
                    
                        documentSystemID,
                        serviceLineSystemID,
                            approvalGroupID
                    
                            
                        FROM
                            erp_documentapproved 
                        WHERE
                            erp_documentapproved.approvedYN = 0 
                            AND erp_documentapproved.rejectedYN = 0 
                            AND erp_documentapproved.approvalGroupID > 0 
                            AND documentSystemID IN ( 1, 50, 51, 2, 5, 52, 4, 11, 15, 20, 19 ) 
                        ) erp_documentapproved
                        INNER JOIN (
                        SELECT
                        companySystemID,
                                departmentSystemID,
                                    documentSystemID,
                            ServiceLineSystemID,
                                employeeGroupID
                             
                        FROM
                            employeesdepartments 
                        WHERE
                            employeeSystemID = $employeeSystemID 
                            AND documentSystemID IN ( 1, 50, 51, 2, 5, 52, 4, 11, 15, 20, 19 ) )employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
                            AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
                            AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
                        AND employeesdepartments.ServiceLineSystemID = erp_documentapproved.serviceLineSystemID 
                        AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID";
        // $output = DB::select($qry);

        /*$output  = DocumentApproved::where('approvedYN',0)
            ->where('rejectedYN',0)
            ->where('approvalGroupID','>',0)
            ->whereIn('documentSystemID',[1, 50, 51, 2, 5, 52, 4, 11, 15, 20, 19,17])
            ->count('documentApprovedID');*/

        return $this->sendResponse(0, 'Document approved count retrieved successfully');

    }

    public function getAllcompaniesByDepartment(Request $request)
    {
        $employeeSystemID = \Helper::getEmployeeSystemID();

        $allCompanies = DB::select("select `companymaster`.`companySystemID`, `companymaster`.`CompanyID`, `companymaster`.`CompanyName` FROM `employeesdepartments` INNER JOIN `companymaster` ON `employeesdepartments`.`companySystemID` = `companymaster`.`companySystemID` WHERE `employeeSystemID` = $employeeSystemID AND `isGroup` = 0 GROUP BY employeesdepartments.companySystemID");
        return $this->sendResponse($allCompanies, '');

    }

    public function approvalPreCheckAllDoc(Request $request)
    {
        $approve = \Helper::postedDatePromptInFinalApproval($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"],500,['type' => $approve["type"]]);
        } else {
            return $this->sendResponse(array('type' => $approve["type"]), $approve["message"]);
        }

    }

}
