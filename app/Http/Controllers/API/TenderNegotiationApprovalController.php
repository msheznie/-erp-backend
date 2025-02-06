<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\CreateTenderNegotiationApprovalRequest;
use App\Http\Requests\UpdateTenderNegotiationApprovalRequest;
use App\Models\TenderCustomEmail;
use App\Repositories\TenderNegotiationApprovalRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\TenderNegotiationApproval;
use App\Models\TenderNegotiation;
use App\Models\SupplierTenderNegotiation;
use App\Models\TenderMaster;
use App\Models\SupplierRegistrationLink;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Auth;
class TenderNegotiationApprovalController extends AppBaseController
{
    /** @var  TenderNegotiationApprovalRepository */
    private $tenderNegotiationApprovalRepository;

    public function __construct(TenderNegotiationApprovalRepository $tenderNegotiationApprovalRepo)
    {
        $this->tenderNegotiationApprovalRepository = $tenderNegotiationApprovalRepo;
    }

    /**
     * Display a listing of the TenderNegotiationApproval.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->tenderNegotiationApprovalRepository->pushCriteria(new RequestCriteria($request));
        $tenderNegotiationApprovals = $this->tenderNegotiationApprovalRepository->all();

        return view('tender_negotiation_approvals.index')
            ->with('tenderNegotiationApprovals', $tenderNegotiationApprovals);
    }


    /**
     * Store a newly created TenderNegotiationApproval in storage.
     *
     * @param CreateTenderNegotiationApprovalRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->create($input);
        
        if(!$tenderNegotiationApproval || !isset($input['status'])) { 
            return $this->sendError('Tender negotiation approval connot create',404);
        }

        if($this->checkPublishNegotiation($input)) {
            $tenderNegotiation = TenderNegotiation::find($input['tender_negotiation_id']);
            $tenderNegotiation->approved_yn = true;
            $tenderNegotiation->save();
        }

        if($input['status'] == 1) {
            $message = 'Tender negotiation approved successfully';
        }else {
            $message = 'Tender negotiation rejected successfully';   
        }
        
        return $this->sendResponse($tenderNegotiationApproval->toArray(),$message);

        
    }

    /**
     * Display the specified TenderNegotiationApproval.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiationApproval)) {
            Flash::error('Tender negotiation approval not found');

            return redirect(route('tenderNegotiationApprovals.index'));
        }


        return view('tender_negotiation_approvals.show')->with('tenderNegotiationApproval', $tenderNegotiationApproval);
    }

   

    /**
     * Update the specified TenderNegotiationApproval in storage.
     *
     * @param  int              $id
     * @param UpdateTenderNegotiationApprovalRequest $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();


        $tenderNegotiatonApprovals = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiatonApprovals)) {
            return $this->sendError('Tender negotiation approvals not found');
        }

        $tenderNegotiatonApprovals = $this->tenderNegotiationApprovalRepository->update($input, $id);
        if($this->checkPublishNegotiation($input)) {
            $tenderNegotiation = TenderNegotiation::find($input['tender_negotiation_id']);
            $tenderNegotiation->approved_yn = true;
            $tenderNegotiation->save();
        }

        return $this->sendResponse($tenderNegotiatonApprovals->toArray(), 'Tender negotiation approvals updated successfully');
       
    }

    /**
     * Remove the specified TenderNegotiationApproval from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->findWithoutFail($id);

        if (empty($tenderNegotiationApproval)) {
            Flash::error('Tender Negotiation Approval not found');

            return redirect(route('tenderNegotiationApprovals.index'));
        }

        $this->tenderNegotiationApprovalRepository->delete($id);

        Flash::success('Tender Negotiation Approval deleted successfully.');

        return redirect(route('tenderNegotiationApprovals.index'));
    }

    public function getEmployees(Request $request) {
        
        $data = SrmTenderBidEmployeeDetails::select('id','emp_id','tender_id')->where('tender_id', $request['tender_id'])->with(['employee' => function ($q) {
            $q->select('employeeSystemID','empFullName','empID');
        }])->get();

        foreach($data as $dt) {
            $emp = $dt->employee;
            $status = TenderNegotiationApproval::where('tender_negotiation_id',$request['tenderNegotiationId'])->where('emp_id',$emp->employeeSystemID)->select(['status','id'])->first();
            $emp['tender_negotiation_approval_status'] = ($status) ? $status->status : 0;
            $dt['status_id'] = ($status) ? $status->id : 0;
          
        }
        return $this->sendResponse($data, 'Employee reterived successfully');
    
    }

    public function checkPublishNegotiation($input){
        $tenderNegotiation = TenderNegotiation::select('no_to_approve')->find($input['tender_negotiation_id']);
        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->select('id')->where('tender_negotiation_id',$input['tender_negotiation_id'])->where('status',1)->count();
        
        return ($tenderNegotiation->no_to_approve > 0 && ($tenderNegotiation->no_to_approve == $tenderNegotiationApproval));
   
    }

    public function publishNegotiation(Request $request){
        $input = $request->input('item');
        $tenderNegotiation = TenderNegotiation::select('status','id')->find($input['id']);
        $tenderNegotiation->status = 2;
        $tenderNegotiation->save();
        $tenderMaster = TenderMaster::select('negotiation_published','id', 'tender_code', 'title')->find($input['srm_tender_master_id']);
        $tenderMaster->negotiation_published = 1;
        $tenderMaster->save();

        $this->sendEmailToSuppliers($input, $tenderMaster->tender_code, $tenderMaster->title);
        return $this->sendResponse($tenderNegotiation->toArray(), 'Tender Negotiation published successfully');
    }

    public function sendEmailToSuppliers($input, $code, $title) {
            $srmTenderBidEmployeeDetails = SrmTenderBidEmployeeDetails::select('id','emp_id','tender_id')->where('tender_id', $input['srm_tender_master_id'])->with('employee')->get();
            $supplierTenderNegotiations = SupplierTenderNegotiation::where('tender_negotiation_id',$input['id'])->select('suppliermaster_id','bidSubmissionCode')->get();
            if($srmTenderBidEmployeeDetails) {
                foreach($supplierTenderNegotiations as $supplierTenderNegotiation) {
                    $employee = SupplierRegistrationLink::select('email','company_id','name')->find($supplierTenderNegotiation->suppliermaster_id);
                    if(isset($employee) &&  $employee->email) {
                        $file = array();
                        $tenderCustomEmail = TenderCustomEmail::getSupplierCustomEmailBody($input['srm_tender_master_id'], $supplierTenderNegotiation->suppliermaster_id);
                        if ($tenderCustomEmail && $tenderCustomEmail->attachment) {
                            $file[$tenderCustomEmail->attachment->originalFileName] = Helper::getFileUrlFromS3($tenderCustomEmail->attachment->path);
                        }

                        $dataEmail['empEmail'] = $employee->email;
                        $dataEmail['companySystemID'] = $employee->company_id;
                        $loginUrl = env('SRM_LINK');
                        $url = trim($loginUrl,"/register");
                        $redirectUrl= $url."/tender-management/tenders/1";
                        $companyName = (Auth::user()->employee && Auth::user()->employee->company) ? Auth::user()->employee->company->CompanyName : null ;

                        if ($tenderCustomEmail) {
                            $emailBody =  "<p>Dear " . $employee->name . $tenderCustomEmail->email_body . $companyName . '</p>';
                            $ccEmails = json_decode($tenderCustomEmail->cc_email, true);
                        } else {
                            $emailBody = "<p>Dear " . $employee->name . ',</p><p>We would like to inform you that you have been shortlisted for the tender negotiation ' . $code . ' | ' . $title . ' tender, and for that we would like to arrange a meeting with you, before submitting the final proposal.</p><br/><br/><p>Kind Regards,</p><p>' . $companyName . '</p>';
                        }

                        $dataEmail['alertMessage'] = "Tender Negotiation Invitation";
                        $dataEmail['emailAlertMessage'] = $emailBody;

                        if (!empty($ccEmails)) {
                            $dataEmail['ccEmail'] = $ccEmails;
                        }

                        if (!empty($tenderCustomEmail->attachment)) {
                            $dataEmail['attachmentList'] = $file;
                        }

                        $sendEmail = \Email::sendEmailSRM($dataEmail);
                    }
                }
            }
    }
}
