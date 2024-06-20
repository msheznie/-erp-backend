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

use App\helper\Helper;
use App\Http\Requests\API\CreateDocumentApprovedAPIRequest;
use App\Http\Requests\API\UpdateDocumentApprovedAPIRequest;
use App\Models\ApprovalLevel;
use App\Models\DocumentApproved;
use App\Models\SupplierRegistrationLink;
use App\Repositories\BookInvSuppMasterRepository;
use App\Repositories\DocumentApprovedRepository;
use App\Repositories\DocumentAttachmentsRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use App\Repositories\TenderBidClarificationsRepository;
use App\Services\InvoiceService;
use App\Services\POService;
use App\Services\Shared\SharedService;
use App\Services\SRMService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\DocumentModifyRequest;
use App\Repositories\DocumentModifyRequestRepository;
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
        $employee = Helper::getEmployeeInfo();

        $fromPms = (isset($input['fromPms']) && $input['fromPms']) ? true : false;

        if ($fromPms) {
        	$customerInvoiceWhere = " AND erp_custinvoicedirect.createdFrom = 5";
        } else {
        	$customerInvoiceWhere = " AND erp_custinvoicedirect.createdFrom != 5";
        }
 
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
        if(!is_array($documentType)){
            $documentType = array();
        }

        $companies = isset($input['companies']) ? $input['companies'] : array();
        if(!is_array($companies)){
            $companies = array();
        }

        $filter = 'AND erp_documentapproved.documentSystemID IN (0) ';
		$tenderFilter = '';


        if (!empty($documentType)) {
            $filter = " AND erp_documentapproved.documentSystemID IN (" . implode(',', $documentType) . ")";
        }


        if ($companies) {
            $filter .= " AND erp_documentapproved.companySystemID IN (" . implode(',', $companies) . ")";
            $tenderFilter .= " AND dmr.companySystemID IN (" . implode(',', $companies) . ")";
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
	prd.prq_tot AS DocumentValue,
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
	LEFT JOIN (
        SELECT purchaseRequestID, SUM(IFNULL(totalCost, 0)) AS prq_tot, altUnitValue, altUnit
        FROM erp_purchaserequestdetails
        GROUP BY purchaseRequestID
    )  as prd ON prd.purchaseRequestID = erp_purchaserequest.purchaseRequestID 
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
	erp_paysupplierinvoicemaster.payAmountSuppTrans + erp_paysupplierinvoicemaster.VATAmount + erp_paysupplierinvoicemaster.retentionVatAmount AS DocumentValue,
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
	AND erp_documentapproved.employeeSystemID = $employeeSystemID GROUP BY erp_bookinvsuppmaster.bookingInvCode
	) AS PendingSupplierInvoiceApprovals UNION ALL SELECT
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
	customer.empName AS SupplierOrCustomer,
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
	INNER JOIN employees as customer ON customer.employeeSystemID = erp_bookinvsuppmaster.employeeID
	AND erp_bookinvsuppmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_documentapproved.documentSystemCode 
	AND erp_bookinvsuppmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_bookinvsuppmaster.confirmedYN = 1 
	AND erp_bookinvsuppmaster.approved = -1
	AND erp_bookinvsuppmaster.documentType = 4
	AND erp_bookinvsuppmaster.cancelYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_bookinvsuppmaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = -1 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 11 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID GROUP BY erp_bookinvsuppmaster.bookingInvCode
	) AS PendingSupplierEmployeeDirectInvoiceApprovals UNION ALL
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
	AND erp_custinvoicedirect.canceledYN = 0".$customerInvoiceWhere."
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
	AND erp_quotationmaster.cancelledYN = 0 
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
	AND erp_quotationmaster.cancelledYN = 0
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
	erp_jvmaster.JVNarration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	'' AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	jvDetails.debitSum AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	erp_jvmaster.jvType AS documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN erp_jvmaster ON erp_jvmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_jvmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_jvmaster.jvMasterAutoId = erp_documentapproved.documentSystemCode 
	AND erp_jvmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_jvmaster.approved = -1 
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_jvmaster.currencyID 
	INNER JOIN(
                SELECT
                    COALESCE(SUM(debitAmount),0) as debitSum,
                    jvMasterAutoId 
                FROM
                    erp_jvdetail
                GROUP BY jvMasterAutoId
                ) AS jvDetails
        ON
            jvDetails.jvMasterAutoId = erp_jvmaster.jvMasterAutoId 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 17 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingJVApprovals
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
	recurring_voucher_setup.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS approvedEmployee,
	'' AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	rrvDetails.debitSum AS DocumentValue,
	0 AS amended,
	erp_documentapproved.approvedYN,
	recurring_voucher_setup.documentType 
FROM
	erp_documentapproved
	INNER JOIN employees ON erp_documentapproved.employeeSystemID = employees.employeeSystemID
	INNER JOIN recurring_voucher_setup ON recurring_voucher_setup.companySystemID = erp_documentapproved.companySystemID 
	AND recurring_voucher_setup.documentSystemID = erp_documentapproved.documentSystemID 
	AND recurring_voucher_setup.recurringVoucherAutoId = erp_documentapproved.documentSystemCode 
	AND recurring_voucher_setup.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND recurring_voucher_setup.approved = -1 
	INNER JOIN currencymaster ON currencymaster.currencyID = recurring_voucher_setup.currencyID 
	INNER JOIN(
                SELECT
                    COALESCE(SUM(debitAmount),0) as debitSum,
                    recurringVoucherAutoId 
                FROM
                    recurring_voucher_setup_detail
                GROUP BY recurringVoucherAutoId
                ) AS rrvDetails
        ON
            rrvDetails.recurringVoucherAutoId = recurring_voucher_setup.recurringVoucherAutoId 
WHERE
	erp_documentapproved.approvedYN = -1
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 119 ) 
	AND erp_documentapproved.employeeSystemID = $employeeSystemID
	) AS PendingRRVApprovals
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
	'' as approval_remarks,
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
	prd.prq_tot AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	- 1 AS documentType,
	'' as srmValue
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
	LEFT JOIN (
        SELECT purchaseRequestID, SUM(IFNULL(totalCost, 0)) AS prq_tot 
        FROM erp_purchaserequestdetails
        GROUP BY purchaseRequestID
    )  as prd ON prd.purchaseRequestID = erp_purchaserequest.purchaseRequestID  
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
	erp_purchaseordermaster.approval_remarks,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	- 1 AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_purchaseordermaster.documentSystemID = erp_documentapproved.documentSystemID 
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
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0 GROUP BY erp_purchaseordermaster.purchaseOrderID
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
	'' as approval_remarks,
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
	erp_paysupplierinvoicemaster.payAmountSuppTrans + erp_paysupplierinvoicemaster.VATAmount + erp_paysupplierinvoicemaster.retentionVatAmount AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_paysupplierinvoicemaster.invoiceType AS documentType,
	'' as srmValue
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
	) AS PendingPaymentApprovals 
UNION ALL
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
	'' as approval_remarks,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_bookinvsuppmaster.documentType AS documentType,
	'' as srmValue
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
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0 GROUP BY erp_bookinvsuppmaster.bookingInvCode
	) AS PendingSupplierInvoiceApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_grvmaster.grvNarration as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	suppliermaster.supplierName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_grvmaster.grvTotalSupplierTransactionCurrency AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_grvmaster.grvType AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_grvmaster ON erp_grvmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_grvmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_grvmaster.grvAutoID = erp_documentapproved.documentSystemCode 
	AND erp_grvmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_grvmaster.grvConfirmedYN = 1 
	AND erp_grvmaster.approved = 0
	AND erp_grvmaster.grvCancelledYN = 0
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_grvmaster.supplierID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_grvmaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 3 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingGrvApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_itemissuemaster.comment as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_itemissuemaster.issueType AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_itemissuemaster ON erp_itemissuemaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_itemissuemaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_itemissuemaster.itemIssueAutoID = erp_documentapproved.documentSystemCode 
	AND erp_itemissuemaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_itemissuemaster.confirmedYN = 1 
	AND erp_itemissuemaster.approved = 0
	LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_itemissuemaster.customerSystemID
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 8 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingMaterialIssueApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_request.comments as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_request ON erp_request.companySystemID = erp_documentapproved.companySystemID 
	AND erp_request.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_request.RequestID = erp_documentapproved.documentSystemCode 
	AND erp_request.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_request.ConfirmedYN = 1 
	AND erp_request.approved = 0
	AND erp_request.cancelledYN = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 9 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingMaterialIssueApprovals UNION ALL 
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_bookinvsuppmaster.comments as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customer.empName AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_bookinvsuppmaster.bookingAmountTrans AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.companySystemID = erp_documentapproved.companySystemID 
	INNER JOIN employees ON erp_bookinvsuppmaster.confirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN employees as customer ON customer.employeeSystemID = erp_bookinvsuppmaster.employeeID
	AND erp_bookinvsuppmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_documentapproved.documentSystemCode 
	AND erp_bookinvsuppmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_bookinvsuppmaster.confirmedYN = 1
	AND erp_bookinvsuppmaster.approved = 0
	AND erp_bookinvsuppmaster.documentType = 4
	AND erp_bookinvsuppmaster.cancelYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_bookinvsuppmaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 	
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 11 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0 GROUP BY erp_bookinvsuppmaster.bookingInvCode
	) AS PendingSupplierEmployeeDirectInvoiceApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_itemreturnmaster.comment as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_itemreturnmaster ON erp_itemreturnmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_itemreturnmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_itemreturnmaster.itemReturnAutoID = erp_documentapproved.documentSystemCode 
	AND erp_itemreturnmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_itemreturnmaster.confirmedYN = 1 
	AND erp_itemreturnmaster.approved = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 12 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingMaterialRetuenApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_purchasereturnmaster.narration as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	suppliermaster.supplierName AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_purchasereturnmaster ON erp_purchasereturnmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_purchasereturnmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_purchasereturnmaster.purhaseReturnAutoID = erp_documentapproved.documentSystemCode 
	AND erp_purchasereturnmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_purchasereturnmaster.confirmedYN = 1 
	AND erp_purchasereturnmaster.approved = 0
	INNER JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_purchasereturnmaster.supplierID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_purchasereturnmaster.supplierTransactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 24 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingPurchaseRetuenApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_stockadjustment.comment as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue 
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_stockadjustment ON erp_stockadjustment.companySystemID = erp_documentapproved.companySystemID 
	AND erp_stockadjustment.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_stockadjustment.stockAdjustmentAutoID = erp_documentapproved.documentSystemCode 
	AND erp_stockadjustment.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_stockadjustment.confirmedYN = 1 
	AND erp_stockadjustment.approved = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 7 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingStockAdjustmentApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_stocktransfer.comment as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_stocktransfer ON erp_stocktransfer.companySystemID = erp_documentapproved.companySystemID 
	AND erp_stocktransfer.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_stocktransfer.stockTransferAutoID = erp_documentapproved.documentSystemCode 
	AND erp_stocktransfer.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_stocktransfer.confirmedYN = 1 
	AND erp_stocktransfer.approved = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 13 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingStockTransferApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_stockreceive.comment as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_stockreceive ON erp_stockreceive.companySystemID = erp_documentapproved.companySystemID 
	AND erp_stockreceive.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_stockreceive.stockReceiveAutoID = erp_documentapproved.documentSystemCode 
	AND erp_stockreceive.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_stockreceive.confirmedYN = 1 
	AND erp_stockreceive.approved = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 10 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingStockReciveApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_stockcount.comment as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_stockcount ON erp_stockcount.companySystemID = erp_documentapproved.companySystemID 
	AND erp_stockcount.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_stockcount.stockCountAutoID = erp_documentapproved.documentSystemCode 
	AND erp_stockcount.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_stockcount.confirmedYN = 1 
	AND erp_stockcount.approved = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 97 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingStockCountApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_bankrecmaster.description as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_bankrecmaster ON erp_bankrecmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_bankrecmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_bankrecmaster.bankRecAutoID = erp_documentapproved.documentSystemCode 
	AND erp_bankrecmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_bankrecmaster.confirmedYN = 1 
	AND erp_bankrecmaster.approvedYN = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 62 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingBankReconciliationApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_paymentbanktransfer.narration as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType ,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_paymentbanktransfer ON erp_paymentbanktransfer.companySystemID = erp_documentapproved.companySystemID 
	AND erp_paymentbanktransfer.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_paymentbanktransfer.paymentBankTransferID = erp_documentapproved.documentSystemCode 
	AND erp_paymentbanktransfer.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_paymentbanktransfer.confirmedYN = 1 
	AND erp_paymentbanktransfer.approvedYN = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 64 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingBankTransferApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_bankaccount.AccountNo as documentCode,
	erp_bankaccount.AccountName as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	currencymaster.CurrencyCode AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_bankaccount ON erp_bankaccount.companySystemID = erp_documentapproved.companySystemID 
	AND erp_bankaccount.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_bankaccount.bankAccountAutoID = erp_documentapproved.documentSystemCode 
	AND erp_bankaccount.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_bankaccount.confirmedYN = 1 
	AND erp_bankaccount.approvedYN = 0
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_bankaccount.accountCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 66 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingBankAccountApprovals
UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	currency_conversion_master.description as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN currency_conversion_master ON currency_conversion_master.id = erp_documentapproved.documentSystemCode 
	AND currency_conversion_master.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND currency_conversion_master.confirmedYN = 1 
	AND currency_conversion_master.approvedYN = 0
WHERE
	erp_documentapproved.approvedYN = 0 
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 96 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingCurrencyConversionApprovals
UNION ALL
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_debitnote.documentType AS documentType,
	'' as srmValue 
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
	) AS PendingDebiteNoteApprovals 
UNION ALL
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_custinvoicedirect.documentType AS documentType,
	'' as srmValue
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
	) AS PendingCustomerInvoiceApprovals 
UNION ALL
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_delivery_order.narration as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_delivery_order.transactionAmount + IFNULL(VATAmount,0) AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_delivery_order.orderType AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_delivery_order ON erp_delivery_order.companySystemID = erp_documentapproved.companySystemID 
	AND erp_delivery_order.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_delivery_order.deliveryOrderID = erp_documentapproved.documentSystemCode 
	AND erp_delivery_order.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_delivery_order.confirmedYN = 1 
	AND erp_delivery_order.approvedYN = 0
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_delivery_order.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_delivery_order.transactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 71 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingDeliveryOrderApprovals 
UNION ALL
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	salesreturn.narration as comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	salesreturn.transactionAmount + IFNULL(VATAmount,0) AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	salesreturn.returnType AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN salesreturn ON salesreturn.companySystemID = erp_documentapproved.companySystemID 
	AND salesreturn.documentSystemID = erp_documentapproved.documentSystemID 
	AND salesreturn.id = erp_documentapproved.documentSystemCode 
	AND salesreturn.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND salesreturn.confirmedYN = 1 
	AND salesreturn.approvedYN = 0
	INNER JOIN customermaster ON customermaster.customerCodeSystem = salesreturn.customerID
	INNER JOIN currencymaster ON currencymaster.currencyID = salesreturn.transactionCurrencyID 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 87 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingSalesRetuenApprovals 
UNION ALL
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_creditnote.documentType AS documentType,
	'' as srmValue 
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_quotationmaster.quotationType AS documentType,
	'' as srmValue
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
	AND erp_quotationmaster.cancelledYN = 0 
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_quotationmaster.quotationType AS documentType,
	'' as srmValue
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
	AND erp_quotationmaster.cancelledYN = 0 
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_jvmaster.JVNarration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	jvDetails.debitSum AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_jvmaster.jvType AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_jvmaster ON erp_jvmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_jvmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_jvmaster.jvMasterAutoId = erp_documentapproved.documentSystemCode 
	AND erp_jvmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_jvmaster.approved = 0 
	AND erp_jvmaster.confirmedYN = 1 
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_jvmaster.currencyID 
	INNER JOIN(
                SELECT
                    COALESCE(SUM(debitAmount),0) as debitSum,
                    jvMasterAutoId 
                FROM
                    erp_jvdetail
                GROUP BY jvMasterAutoId
                ) AS jvDetails
        ON
            jvDetails.jvMasterAutoId = erp_jvmaster.jvMasterAutoId 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 17 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingJVApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_fa_asset_master.assetDescription AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_fa_asset_master ON erp_fa_asset_master.companySystemID = erp_documentapproved.companySystemID 
	AND erp_fa_asset_master.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_fa_asset_master.faID = erp_documentapproved.documentSystemCode 
	AND erp_fa_asset_master.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_fa_asset_master.approved = 0 
	AND erp_fa_asset_master.confirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 22 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingFixedAssetCostingApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_fa_asset_disposalmaster.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_fa_asset_disposalmaster ON erp_fa_asset_disposalmaster.companySystemID = erp_documentapproved.companySystemID 
	AND erp_fa_asset_disposalmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_documentapproved.documentSystemCode 
	AND erp_fa_asset_disposalmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_fa_asset_disposalmaster.approvedYN = 0 
	AND erp_fa_asset_disposalmaster.confirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 41 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingFixedAssetDisposalApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_companyreporttemplate.description as documentCode,
	'' AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_budgetmaster ON erp_budgetmaster.companySystemID = erp_documentapproved.companySystemID 
	INNER JOIN erp_companyreporttemplate ON erp_budgetmaster.templateMasterID = erp_companyreporttemplate.companyReportTemplateID 
	INNER JOIN serviceline ON erp_budgetmaster.serviceLineSystemID = serviceline.serviceLineSystemID 
	AND erp_budgetmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_budgetmaster.budgetmasterID = erp_documentapproved.documentSystemCode 
	AND erp_budgetmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_budgetmaster.approvedYN = 0 
	AND erp_budgetmaster.confirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 65 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingBudgetApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_budgettransferform.comments AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_budgettransferform ON erp_budgettransferform.companySystemID = erp_documentapproved.companySystemID 
	AND erp_budgettransferform.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_budgettransferform.budgetTransferFormAutoID = erp_documentapproved.documentSystemCode 
	AND erp_budgettransferform.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_budgettransferform.approvedYN = 0 
	AND erp_budgettransferform.confirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 46 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingBudgetTransferApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_budgetaddition.comments AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_budgetaddition ON erp_budgetaddition.companySystemID = erp_documentapproved.companySystemID 
	AND erp_budgetaddition.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_budgetaddition.id = erp_documentapproved.documentSystemCode 
	AND erp_budgetaddition.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_budgetaddition.approvedYN = 0 
	AND erp_budgetaddition.confirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 102 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingBudgetAdditionApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_fa_fa_asset_transfer.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_fa_fa_asset_transfer ON erp_fa_fa_asset_transfer.company_id = erp_documentapproved.companySystemID 
	AND erp_fa_fa_asset_transfer.document_id = erp_documentapproved.documentID 
	AND erp_fa_fa_asset_transfer.id = erp_documentapproved.documentSystemCode 
	AND erp_fa_fa_asset_transfer.current_level_no = erp_documentapproved.rollLevelOrder 
	AND erp_fa_fa_asset_transfer.approved_yn = 0 
	AND erp_fa_fa_asset_transfer.confirmed_yn = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 103 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingFixedAssetTransferApprovals
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
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	'' AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	currencymaster.DecimalPlaces AS DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	erp_fa_depmaster.depAmountRpt AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.companySystemID = erp_documentapproved.companySystemID 
	INNER JOIN currencymaster ON currencymaster.currencyID = erp_fa_depmaster.depRptCur 
	AND erp_fa_depmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND erp_fa_depmaster.depMasterAutoID = erp_documentapproved.documentSystemCode 
	AND erp_fa_depmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND erp_fa_depmaster.approved = 0 
	AND erp_fa_depmaster.confirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 23 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingFixedAssetSDepreciationApprovals
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
	'' as approval_remarks,
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
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	erp_customerreceivepayment.documentType AS documentType,
	'' as srmValue
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
	) AS PendingReceiptVoucherApprovals UNION All
	SELECT
	* 
FROM
	(
SELECT
DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	'' AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	itemmaster.itemDescription AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	2 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	'' AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN itemmaster ON itemmaster.primaryCompanySystemID = erp_documentapproved.companySystemID 
	AND itemmaster.documentSystemID = erp_documentapproved.documentSystemID 
	AND itemmaster.itemCodeSystem = erp_documentapproved.documentSystemCode 
	AND itemmaster.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND itemmaster.itemApprovedYN = 0 
	AND itemmaster.itemConfirmedYN = 1 
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 57 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS pendingItemMasterApprovals UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	'' AS narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	customermaster.CustomerName AS SupplierOrCustomer,
			0 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	0 AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN customermaster ON erp_documentapproved.documentSystemCode = customerCodeSystem AND erp_documentapproved.rollLevelOrder = RollLevForApp_curr

WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0
	AND erp_documentapproved.approvalGroupID > 0
	AND customermaster.approvedYN = 0
	AND customermaster.confirmedYN = 1
    $filter
	AND erp_documentapproved.documentSystemID IN ( 58 )
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS pendingCustomerMasterApprovals UNION ALL
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
	'' as approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	'' AS narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
			0 AS DecimalPlaces ,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	0 AS documentType,
	'' as srmValue
FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN chartofaccounts ON erp_documentapproved.documentSystemCode = chartOfAccountSystemID AND erp_documentapproved.rollLevelOrder = RollLevForApp_curr
WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0
	AND erp_documentapproved.approvalGroupID > 0
	AND chartofaccounts.isApproved = 0 
	AND chartofaccounts.confirmedYN = 1
    $filter
	AND erp_documentapproved.documentSystemID IN ( 59 )
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS pendingChartOfAccountsApprovals UNION ALL 
	SELECT
	* 
FROM
	(
	SELECT
		DATEDIFF( CURDATE(), erp_documentapproved.docConfirmedDate ) AS dueDays,
		erp_documentapproved.documentApprovedID,
		erp_documentapproved.approvalLevelID,
		erp_documentapproved.rollLevelOrder,
		erp_approvallevel.noOfLevels AS NoOfLevels,
		erp_documentapproved.companySystemID,
		erp_documentapproved.companyID,
		'' AS approval_remarks,
		erp_documentapproved.documentSystemID,
		erp_documentapproved.documentID,
		erp_documentapproved.documentSystemCode,
		erp_documentapproved.documentCode,
		'' AS narration,
		erp_documentapproved.docConfirmedDate,
		erp_documentapproved.approvedDate,
		employees.empName AS confirmedEmployee,
		suppliermaster.supplierName AS SupplierOrCustomer,
		currencymaster.DecimalPlaces AS DecimalPlaces,
		currencymaster.CurrencyCode AS DocumentCurrency,
		'' AS DocumentValue,
		0 AS amended,
		employeesdepartments.employeeID,
		employeesdepartments.approvalDeligated,
		erp_documentapproved.approvedYN,
		0 AS documentType,
		'' as srmValue
	FROM
		erp_documentapproved
		INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
		AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
		AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
		AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
		INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
		INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID 
		INNER JOIN suppliermaster ON erp_documentapproved.documentSystemCode = supplierCodeSystem AND erp_documentapproved.rollLevelOrder = RollLevForApp_curr
		INNER JOIN currencymaster ON suppliermaster.currency = currencymaster.currencyID
	WHERE
		erp_documentapproved.approvedYN = 0 
		AND erp_documentapproved.rejectedYN = 0 
		AND suppliermaster.approvedYN = 0 
		$filter
		AND suppliermaster.supplierConfirmedYN = 1
		AND erp_documentapproved.approvalGroupID > 0 
		AND erp_documentapproved.documentSystemID IN ( 56 ) 
		AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS pendingSupplierMasterApprovals 
		UNION ALL  
		SELECT 
		* 
		FROM (
			".self::getTenderAmendNotApproved($filter,$employeeSystemID,117,$tenderFilter)."
			) AS pendingTenderAmendRequestConfirmApprovals
		UNION ALL  
		SELECT 
		* 
		FROM (
			".self::getTenderAmendNotApproved($filter,$employeeSystemID,118,$tenderFilter)."
			) AS pendingTenderAmendRequestApprovals	
       UNION ALL  
		SELECT 
		* 
		FROM (
		SELECT
	DATEDIFF( CURDATE(), erp_documentapproved.docConfirmedDate ) AS dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	'' AS approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	spr.name as documentCode,
	spr.email AS narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	' ' AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	0 AS DecimalPlaces,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	0 AS documentType,
	spr.uuid AS srmValue 
FROM
	`erp_documentapproved`
	INNER JOIN `employeesdepartments` ON `erp_documentapproved`.`companySystemID` = `employeesdepartments`.`companySystemID`
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND `erp_documentapproved`.`documentSystemID` = `employeesdepartments`.`documentSystemID` 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID 
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN srm_supplier_registration_link spr ON erp_documentapproved.documentSystemCode = spr.id  AND erp_documentapproved.rollLevelOrder = RollLevForApp_curr 
WHERE
	 `erp_documentapproved`.`approvedYN` = 0 
	AND`erp_documentapproved`.`rejectedYN` = 0 
	$filter
	AND spr.confirmed_yn = 1
	AND erp_documentapproved.approvalGroupID > 0 
	AND erp_documentapproved.documentSystemID = 107
	AND employeesdepartments.employeeSystemID = $employeeSystemID 
	AND employeesdepartments.isActive = 1 
	AND employeesdepartments.removedYN = 0
		) AS pendingSupplierRegistrationApprovals
		
		 UNION ALL  
		SELECT 
		* 
		FROM (
		SELECT
	DATEDIFF( CURDATE(), erp_documentapproved.docConfirmedDate ) AS dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	'' AS approval_remarks,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	title AS narration,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	' ' AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	0 AS DecimalPlaces,
	'' AS DocumentCurrency,
	'' AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	0 AS documentType,
	tm.id AS srmValue 
	FROM 
	erp_documentapproved
	INNER JOIN `employeesdepartments` ON `erp_documentapproved`.`companySystemID` = `employeesdepartments`.`companySystemID`
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND `erp_documentapproved`.`documentSystemID` = `employeesdepartments`.`documentSystemID` 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID 
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees em ON erp_documentapproved.docConfirmedByEmpSystemID = em.employeeSystemID 
		INNER JOIN srm_tender_master tm ON erp_documentapproved.documentSystemCode = id AND erp_documentapproved.rollLevelOrder = RollLevForApp_curr
		INNER JOIN currencymaster ON tm.currency_id = currencymaster.currencyID
		Where
		erp_documentapproved.approvedYN = 0 
		AND erp_documentapproved.rejectedYN = 0 
		AND tm.approved = 0 
        $filter
		AND tm.confirmed_yn = 1
		AND erp_documentapproved.approvalGroupID > 0 
		AND erp_documentapproved.documentSystemID IN ( 108,113 ) 
		AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
		) as tenderPendingApprovals UNION ALL 
	SELECT * FROM (
    SELECT
    DATEDIFF(CURDATE(),erp_documentapproved.docConfirmedDate) as dueDays,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.rollLevelOrder,
	erp_approvallevel.noOfLevels AS NoOfLevels,
	erp_documentapproved.companySystemID,
	erp_documentapproved.companyID,
	'' as approval_remarks,	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	recurring_voucher_setup.narration AS comments,
	erp_documentapproved.docConfirmedDate,
	erp_documentapproved.approvedDate,
	employees.empName AS confirmedEmployee,
	'' AS SupplierOrCustomer,
	currencymaster.DecimalPlaces ,
	currencymaster.CurrencyCode AS DocumentCurrency,
	rrvDetails.debitSum AS DocumentValue,
	0 AS amended,
	employeesdepartments.employeeID,
	employeesdepartments.approvalDeligated,
	erp_documentapproved.approvedYN,
	recurring_voucher_setup.documentType,
	'' as srmValue
    FROM
	erp_documentapproved
	INNER JOIN employeesdepartments ON employeesdepartments.companySystemID = erp_documentapproved.companySystemID 
	AND employeesdepartments.departmentSystemID = erp_documentapproved.departmentSystemID 
	AND employeesdepartments.documentSystemID = erp_documentapproved.documentSystemID 
	AND employeesdepartments.employeeGroupID = erp_documentapproved.approvalGroupID
	INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
	INNER JOIN employees ON erp_documentapproved.docConfirmedByEmpSystemID = employees.employeeSystemID
	INNER JOIN recurring_voucher_setup ON recurring_voucher_setup.companySystemID = erp_documentapproved.companySystemID 
	AND recurring_voucher_setup.documentSystemID = erp_documentapproved.documentSystemID 
	AND recurring_voucher_setup.recurringVoucherAutoId = erp_documentapproved.documentSystemCode 
	AND recurring_voucher_setup.RollLevForApp_curr = erp_documentapproved.rollLevelOrder 
	AND recurring_voucher_setup.approved = 0 
	AND recurring_voucher_setup.confirmedYN = 1 
	INNER JOIN currencymaster ON currencymaster.currencyID = recurring_voucher_setup.currencyID 
	INNER JOIN(SELECT COALESCE(SUM(debitAmount),0) as debitSum,recurringVoucherAutoId FROM recurring_voucher_setup_detail GROUP BY recurringVoucherAutoId) AS rrvDetails
    ON rrvDetails.recurringVoucherAutoId = recurring_voucher_setup.recurringVoucherAutoId  
    WHERE
	erp_documentapproved.approvedYN = 0
	AND erp_documentapproved.rejectedYN = 0 
	AND erp_documentapproved.approvalGroupID > 0 
	$filter
	AND erp_documentapproved.documentSystemID IN ( 119 ) 
	AND employeesdepartments.employeeSystemID = $employeeSystemID AND employeesdepartments.isActive = 1 AND employeesdepartments.removedYN = 0
	) AS PendingRRVApprovals
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

    public function approveDocument(Request $request)
    {
        $apiKey = $request->input('api_key');
        $request->except('api_key');
		if($request->input('documentSystemID') && ($request->input('documentSystemID') == 117 || $request->input('documentSystemID') == 118) ){ 
				$id = $request->input('documentSystemCode');
				$documentModifyRequestRepo = app(DocumentModifyRequestRepository::class); 
				$controller = new DocumentModifyRequestAPIController($documentModifyRequestRepo); 
				
				$tenderData = $this->getTenderData($id); 

				$requestData = $request->all();  
				$requestData['reference_document_id'] = 108;
				$requestData['bid_submission_opening_date'] = $tenderData->tenderMaster->bid_submission_opening_date; 
				$requestData['id'] = $tenderData->tenderMaster->id; 
				$request->merge($requestData);  
				$result = $controller->approveEditDocument($request);
				return $result;

		}else if($request->input('documentSystemID') && ($request->input('documentSystemID') == 108 || $request->input('documentSystemID') == 113)){
            $requestData['id'] = $request->input('documentSystemCode');
            $request->merge($requestData);
            $approve = \Helper::approveDocument($request);
            if (!$approve["success"]) {
                return $this->sendError($approve["message"], 404, ['type' => isset($approve["type"]) ? $approve["type"] : ""]);
            } else {
                return $this->sendResponse(array(), $approve["message"]);
            }
        }else if ($request->input('documentSystemID') && ($request->input('documentSystemID') == 107 ))
        { 
            $requestData['id'] =$request->input('documentSystemCode');
            $requestData['api_key'] =$apiKey;
            $requestData['uuid'] = $this->getSupplierUUID($requestData['id']);
            $requestData['company_id'] = $request->input('companySystemID');
            $request->merge($requestData);
            $controller =  $this->getController();
            $result = $controller->approveSupplierKYC($request);
            return $result;
        }else {
			$approve = \Helper::approveDocument($request);
			if (!$approve["success"]) {
				return $this->sendError($approve["message"], 404, ['type' => isset($approve["type"]) ? $approve["type"] : ""]);
			} else {
				return $this->sendResponse(array(), $approve["message"]);
			}
		} 
    }

	public function getTenderAmendNotApproved($filter,$employeeSystemID,$documentId,$tenderFilter){ 


		$rollOver = $documentId == 117?'RollLevForApp_curr':'confirmation_RollLevForApp_curr';
        $approved = $documentId == 117?'dmr.approved':'dmr.confirmation_approved';

		return "SELECT
		DATEDIFF( CURDATE(), erp_documentapproved.docConfirmedDate ) AS dueDays,
		erp_documentapproved.documentApprovedID,
		erp_documentapproved.approvalLevelID,
		erp_documentapproved.rollLevelOrder,
		erp_approvallevel.noOfLevels AS NoOfLevels,
		erp_documentapproved.companySystemID,
		erp_documentapproved.companyID,
		'' AS approval_remarks,
		erp_documentapproved.documentSystemID,
		erp_documentapproved.documentID,
		erp_documentapproved.documentSystemCode,
		tm.tender_code AS documentCode,
		CONCAT('Amend Code : ',dmr.code)  AS comments,
		erp_documentapproved.docConfirmedDate,
		erp_documentapproved.approvedDate,
		em.empName AS confirmedEmployee,
		'' AS SupplierOrCustomer,
		2,
		'' AS DocumentCurrency,
		'' AS DocumentValue,
		1 AS amended,
		emd.employeeID,
		emd.approvalDeligated,
		erp_documentapproved.approvedYN,
		dmr.type AS documentType ,
		tm.id as srmValue
	FROM
		erp_documentapproved
		JOIN document_modify_request dmr ON dmr.id = erp_documentapproved.documentSystemCode 
		AND erp_documentapproved.rollLevelOrder =  $rollOver 
		$tenderFilter
		AND $approved = 0
		JOIN srm_tender_master tm ON tm.id = dmr.documentSystemCode
		INNER JOIN employeesdepartments emd ON emd.companySystemID = erp_documentapproved.companySystemID 
		AND emd.documentSystemID = 108 
		AND emd.employeeGroupID = erp_documentapproved.approvalGroupID
		INNER JOIN erp_approvallevel ON erp_approvallevel.approvalLevelID = erp_documentapproved.approvalLevelID
		INNER JOIN employees em ON erp_documentapproved.docConfirmedByEmpSystemID = em.employeeSystemID 
	WHERE
		erp_documentapproved.approvedYN = 0 
		AND erp_documentapproved.rejectedYN = 0 
		AND erp_documentapproved.approvalGroupID > 0 
		AND erp_documentapproved.documentSystemID IN ($documentId)  
		AND $approved = 0
		$filter
		AND emd.employeeSystemID = $employeeSystemID AND emd.isActive = 1 AND emd.removedYN = 0
			";
	}

	public function getTenderData($id){ 
		return DocumentModifyRequest::select('id','documentSystemCode')
		->with(['tenderMaster' => function ($q){ 
			$q->select('id','tender_code','bid_submission_opening_date');
		}])
		->where('id',$id)
		->first();
	}

    public function getSupplierUUID($id)
    {
        $supReg = SupplierRegistrationLink::select('uuid')
            ->where('id',$id)
            ->first();

        return $supReg['uuid'];
    }

    public function getController(){
        $bookInvoiceSupMasterRepo = app(BookInvSuppMasterRepository::class);
        $POService = new POService();
        $supplierService = new SupplierService();
        $sharedService =new SharedService();
        $invoiceService = new InvoiceService();
        $supplierInvoiceItemDetailRepo = app(SupplierInvoiceItemDetailRepository::class);
        $tenderBidClarificationsRepo = app(TenderBidClarificationsRepository::class);
        $documentAttachmentsRepo = app(DocumentAttachmentsRepository::class);
        $paySupplierInvoiceMasterRepository = app(PaySupplierInvoiceMasterRepository::class);

        $srmService = new SRMService($bookInvoiceSupMasterRepo,$POService,$supplierService,$sharedService,$invoiceService,$supplierInvoiceItemDetailRepo,
            $tenderBidClarificationsRepo,$documentAttachmentsRepo,$paySupplierInvoiceMasterRepository);

        $controller = new SupplierRegistrationApprovalController($srmService);
        return $controller;
    }

	public function approveDocumentBulk(Request $request)
    {
		$input = $request->all();
		$companyId = $input['companyId'];
		$grv_id = $input['docOriginSystemCode'];
		$empID = \Helper::getEmployeeSystemID();
		
		$results = $this->getPendingData($companyId,$grv_id); 

		if(count($results) == 0)
		{
			return $this->sendError('There is no document to approve');
		}

		foreach($results as $result)
		{	
			$params = array(
				'documentApprovedID' => $result->documentApprovedID,
				'documentSystemCode' => $result->documentSystemCode,
					'documentSystemID' => $$result->documentSystemID,
				'approvalLevelID' => $result->approvalLevelID,
				'rollLevelOrder' => $result->rollLevelOrder,
				'approvedComments' => $input['approvedComments'],
			);
			$approve = \Helper::approveDocument($params);
			if (!$approve["success"]) {
				return $this->sendError($approve["message"], 404, ['type' => isset($approve["type"]) ? $approve["type"] : ""]);
			} 

		}

		return $this->sendResponse(true, 'Document Approved succesfully');

	}


	public function rejectDocumentBulk(Request $request)
	{
		$input = $request->all();
		$companyId = $input['companyId'];
		$grv_id = $input['docOriginSystemCode'];
		$empID = \Helper::getEmployeeSystemID();

		$results = $this->getPendingData($companyId,$grv_id); 
		if(count($results) == 0)
		{
			return $this->sendError('There is no document to reject');
		}

		foreach($results as $result)
		{	
			$params = array(
				'documentApprovedID' => $result->documentApprovedID,
				'documentSystemCode' => $result->documentSystemCode,
					'documentSystemID' => $$result->documentSystemID,
				'approvalLevelID' => $result->approvalLevelID,
				'rollLevelOrder' => $result->rollLevelOrder,
				'approvedComments' => $input['approvedComments'],
			);
			$reject = \Helper::rejectDocument($params);
            if (!$reject["success"]) {
                return $this->sendError($reject["message"]);
            } 

		}

		return $this->sendResponse(true, 'Document Rejected succesfully');
	}

	
	public function getPendingData($companyId,$grv_id){ 
		
		$empID = \Helper::getEmployeeSystemID();
		return DB::table('erp_documentapproved')
		->select(
			'employeesdepartments.approvalDeligated',
			'erp_fa_asset_master.*',
			'employees.empName As created_emp',
			'erp_documentapproved.documentApprovedID',
			'rollLevelOrder',
			'approvalLevelID',
			'documentSystemCode',
			'erp_fa_category.catDescription as catDescription',
			'erp_fa_categorysub.catDescription as subCatDescription'
		)
		->join('employeesdepartments', function ($query) use ($companyId, $empID) {
			$query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
				->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
				->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

			$query->whereIn('employeesdepartments.documentSystemID', [22])
				->where('employeesdepartments.companySystemID', $companyId)
				->where('employeesdepartments.employeeSystemID', $empID)
				->where('employeesdepartments.isActive', 1)
				->where('employeesdepartments.removedYN', 0);
		})
		->join('erp_fa_asset_master', function ($query) use ($companyId,$grv_id) {
			$query->on('erp_documentapproved.documentSystemCode', '=', 'faID')
				->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
				->where('erp_fa_asset_master.companySystemID', $companyId)
				->where('erp_fa_asset_master.approved', 0)
				->where('erp_fa_asset_master.docOriginSystemCode', $grv_id)
				->where('erp_fa_asset_master.confirmedYN', 1)
				->whereNull('erp_fa_asset_master.deleted_at');
		})
		->where('erp_documentapproved.approvedYN', 0)
		->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
		->leftJoin('erp_fa_category', 'erp_fa_category.faCatID', 'erp_fa_asset_master.faCatID')
		->leftJoin('erp_fa_categorysub', 'erp_fa_categorysub.faCatSubID', 'erp_fa_asset_master.faSubCatID')
		->where('erp_documentapproved.rejectedYN', 0)
		->whereIn('erp_documentapproved.documentSystemID', [22])
		->where('erp_documentapproved.companySystemID', $companyId)->get();
	}
}
