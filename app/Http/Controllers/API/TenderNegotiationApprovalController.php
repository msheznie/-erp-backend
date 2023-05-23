<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateTenderNegotiationApprovalRequest;
use App\Http\Requests\UpdateTenderNegotiationApprovalRequest;
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
        
        if($tenderNegotiationApproval) {
            if($this->checkPublishNegotiation($input)) {
                $tenderNegotiation = TenderNegotiation::find($input['tender_negotiation_id']);
                $tenderNegotiation->approved_yn = true;
                $tenderNegotiation->save();
            }
            return $this->sendResponse($tenderNegotiationApproval->toArray(), 'Tender Negotiation Approval created successfully');
        }else {
            return $this->sendError('Tender Negotiation Approval Connot Create',404);
        }
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
            Flash::error('Tender Negotiation Approval not found');

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
            return $this->sendError('Tender Negotiation Approvals not found');
        }

        $tenderNegotiatonApprovals = $this->tenderNegotiationApprovalRepository->update($input, $id);
        if($this->checkPublishNegotiation($input)) {
            $tenderNegotiation = TenderNegotiation::find($input['tender_negotiation_id']);
            $tenderNegotiation->approved_yn = true;
            $tenderNegotiation->save();
        }

        return $this->sendResponse($tenderNegotiatonApprovals->toArray(), 'Tender Negotiation Approvals updated successfully');
       
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
        
        $data = SrmTenderBidEmployeeDetails::where('tender_id', $request['tender_id'])->with(['employee' => function ($q) {
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

        $tenderNegotiationApproval = $this->tenderNegotiationApprovalRepository->select('id')->get();
        $totalApprovedTenderNegotiations = $tenderNegotiationApproval->where('tender_negotiation_id',$input['tender_negotiation_id'])->where('status',1)->count();
        return ($tenderNegotiation->no_to_approve == $totalApprovedTenderNegotiations);
   
    }

    public function publishNegotiation(Request $request){
        $input = $request->input('item');
        $tenderNegotiation = TenderNegotiation::select('status','id')->find($input['id']);
        $tenderNegotiation->status = 2;
        $tenderNegotiation->save();

        $tenderMaster = TenderMaster::select('is_awarded','id')->find($input['srm_tender_master_id']);
        $tenderMaster->is_awarded = false;
        $tenderMaster->save();

        $this->sendEmailToSuppliers($input);
        return $this->sendResponse($tenderNegotiation->toArray(), 'Tender Negotiation published successfully');
    }

    public function sendEmailToSuppliers($input) {
            $srmTenderBidEmployeeDetails = SrmTenderBidEmployeeDetails::select('id')->where('tender_id', $input['srm_tender_master_id'])->with('employee')->get();
            $supplierTenderNegotiations = SupplierTenderNegotiation::where('tender_negotiation_id',$input['id'])->select('suppliermaster_id','bidSubmissionCode')->get();
            if($srmTenderBidEmployeeDetails) {
                foreach($supplierTenderNegotiations as $supplierTenderNegotiation) {
                    $employee = SupplierRegistrationLink::select('email','company_id','name')->find($supplierTenderNegotiation->suppliermaster_id);
                    if(isset($employee) &&  $employee->email) {
                        $dataEmail['empEmail'] = $employee->email;
                        $dataEmail['companySystemID'] = $employee->company_id;
                        $loginUrl = env('SRM_LINK');
                        $url = trim($loginUrl,"/register");
                        $redirectUrl= $url."/tender-management/tenders";
                        $companyName = (Auth::user()->employee && Auth::user()->employee->company) ? Auth::user()->employee->company->CompanyName : null ;
                        $temp = "<p>Dear " . $employee->name . ',</p><p>We are pleased to inform you that we have selected your bid for negotiation. We appreciate the time and effort you put into preparing your proposal, and we were impressed by the quality and value it provides</p><br/><p>We believe that your proposal aligns with our business needs, and we look forward to discussing it in more detail during the negotiation process.</p><p>Please let us know if you have any questions or concerns regarding the negotiation process. We are committed to working collaboratively with you to ensure that we arrive at a mutually beneficial agreement that meets both our needs.</p><p>Thank you again for your bid and your interest in working with us. We look forward to a successful negotiation and a long and productive business relationship.</p><p>Please find the link below.</p><p><a href="' . $redirectUrl . '">Click here to view</a></p><br/><br/><p>Best Regards</p><p>' . $companyName . '</p>';
                        $dataEmail['alertMessage'] = "Tender Bid For Negotiation - ".$supplierTenderNegotiation->bidSubmissionCode;
                        $dataEmail['emailAlertMessage'] = $temp;
                        $sendEmail = \Email::sendEmailErp($dataEmail);
                    }
                }
            }
    }
}
