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
        $employeeSystemID=746;
        $where = "";
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $documentType = $input['documentType'];
        $filter = 'AND erp_documentapproved.documentSystemID IN (0) ';


        if (!empty($documentType)) {

            $filter = " AND erp_documentapproved.documentSystemID IN (" . implode(',', $documentType) . ")";
        }

         $qry = "SELECT * FROM (SELECT
	* 
FROM
	(
SELECT
rollLevelOrder,
erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	$filter
	) AS PendingRequestApprovals UNION ALL
SELECT
	* 
FROM
	(
SELECT
rollLevelOrder,
erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	$filter
	) AS PendingOrderApprovals UNION ALL
	SELECT 
	*
	FROM
	(
	SELECT
	rollLevelOrder,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	$filter
	) AS PendingPaymentApprovals UNION ALL
	SELECT 
	*
	FROM
	(
	SELECT
	rollLevelOrder,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	$filter
	) AS PendingSupplierInvoiceApprovals UNION ALL
	SELECT 
	*
	FROM
	(
	SELECT
	rollLevelOrder,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	AND erp_documentapproved.documentSystemID IN (11)
	AND employeesdepartments.employeeSystemID=$employeeSystemID
	$filter
	) AS PendingDebiteNoteApprovals UNION ALL
	SELECT 
	*
	FROM
	(
	SELECT
	rollLevelOrder,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	AND erp_documentapproved.documentSystemID IN (11)
	AND employeesdepartments.employeeSystemID=$employeeSystemID
	$filter
	) AS PendingCustomerInvoiceApprovals UNION ALL
	SELECT 
	*
	FROM
	(
	SELECT
	rollLevelOrder,
	erp_documentapproved.approvalLevelID,
	erp_documentapproved.companySystemID,
	erp_documentapproved.documentApprovedID,
	erp_documentapproved.companyID,
	erp_documentapproved.documentSystemID,
	erp_documentapproved.documentID,
	erp_documentapproved.documentSystemCode,
	erp_documentapproved.documentCode,
	erp_documentapproved.docConfirmedDate,
	employeesdepartments.employeeID,
	erp_documentapproved.approvedYN 
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
	AND erp_documentapproved.documentSystemID IN (11)
	AND employeesdepartments.employeeSystemID=$employeeSystemID
	$filter
	) AS PendingCreditNoteApprovals )t ORDER BY documentSystemID $sort";
        $output = DB::select($qry);
        $request->request->remove('search.value');
        $col[0] = $input['order'][0]['column'];
        $col[1] = $input['order'][0]['dir'];
        $request->request->remove('order');
        $data['order'] = [];
        /*  $data['order'][0]['column'] = '';
          $data['order'][0]['dir'] = '';*/
        $data['search']['value'] = '';
        $request->merge($data);
        return \DataTables::of($output)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

}
