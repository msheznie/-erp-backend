<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TenderNegotiationRepository;
use App\Http\Requests\StorePostTenderNegotiation;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderMaster;
use App\Models\TenderFinalBids;
use App\Models\BidSubmissionMaster;
use App\Models\TenderNegotiation;
use App\Models\SupplierTenderNegotiation;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\YesNoSelection;
use App\Models\CurrencyMaster;
use Carbon\Carbon;
use Auth;

class TenderNegotiationController extends AppBaseController
{
    private $tenderNegotiationRepository;

    public function __construct(TenderNegotiationRepository $tenderNegotiationRepository)
    {
        $this->tenderNegotiationRepository = $tenderNegotiationRepository;
    }

 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostTenderNegotiation $request)
    {
        
        $input = $request->all();
        $input['started_by'] = \Helper::getEmployeeSystemID();
        $input['status'] = 1;
        $tenderMaster = TenderMaster::find($input['srm_tender_master_id'])->select('min_approval_bid_opening')->first();
        $input['no_to_approve'] =  ($tenderMaster) ? $tenderMaster->min_approval_bid_opening :  0;
        $updateTenderMasterRecord = $this->updateTenderMasterRecord($input);

        if(isset($updateTenderMasterRecord)) {
            $input['currencyId'] = $updateTenderMasterRecord->currency_id;
            $tenderNeotiation = $this->tenderNegotiationRepository->create($input);
            return $this->sendResponse($tenderNeotiation->toArray(), 'Tender negotiation started successfully');
        }else {
            return $this->sendError('Tender Master not found!', 404);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tenderNeotiation = $this->tenderNegotiationRepository->withRelations($id,['confirmed_by']);
        return $this->sendResponse($tenderNeotiation->toArray(), 'Data reterived successfully');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input =  $request->all();
        $confirmationValidation = $this->validateConfirmation($input);
        if(!$confirmationValidation['success']) {
            return $this->sendError($confirmationValidation['message'], $confirmationValidation['code']);
        }
      
        $tenderNeotiation = $this->tenderNegotiationRepository->find($id);
        $this->sendEmailToCommitteMembers($tenderNeotiation,$input);
        $tenderMaster = TenderMaster::find($input['srm_tender_master_id'])->select('min_approval_bid_opening')->first();
        $input['confirmed_by'] =   \Helper::getEmployeeSystemID();
        $input['confirmed_at'] =  Carbon::now();
        $input['no_to_approve'] =  ($tenderMaster) ? $tenderMaster->min_approval_bid_opening :  0;
        $tenderNeotiation = $this->tenderNegotiationRepository->update($input, $id);
        return $this->sendResponse($tenderNeotiation->toArray(), "Tender Negotiation Updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateTenderMasterRecord($input) {

        $tender = TenderMaster::where('id',$input['srm_tender_master_id'])->select('is_negotiation_started','id','currency_id')->first();

        if($tender) {
            $tender->is_negotiation_started = 1;
            $tender->save();
            return $tender;
        }


        return false;
    }


    public function validateConfirmation($input) {
        $messages = [
            'id.required' => 'ID is required'
        ];
      
        $validator = \Validator::make($input, [
            'id' => 'required'
        ], $messages);
      
        if ($validator->fails()) {
            return ['status' => false, 'code' => 422, 'message' => $validator->messages()];
        } 
      
        $tenderNegotiation =$this->tenderNegotiationRepository->find($input['id'])->with(['area' => function($a) {
                $a->select('id','tender_negotiation_id');
            },'SupplierTenderNegotiation' => function ($s) {
                $s->select('id','tender_negotiation_id');
            }])->first();
      
        if(empty($tenderNegotiation->area)) {
            return ['success' => false,'message' => 'Tender negotiation area not selected!','code'=> 403];
        }
      
        if(empty($tenderNegotiation->SupplierTenderNegotiation)) {
            return ['success' => false,'message' => 'Tender negotiation supplier/s not selected!','code'=> 403];
      
        } 

        return ['success' => true, 'code' => 200, 'message' => ''];
    }


    public function getFinalBidsForTenderNegotiation(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $tenderId = $request['tenderId'];
        $query = TenderFinalBids::select('id','status','award','bid_id','com_weightage','supplier_id','tender_id','total_weightage','tech_weightage')->with(['supplierTenderNegotiation' => function ($a) {
            $a->select('id','srm_bid_submission_master_id','bidSubmissionCode','tender_negotiation_id','suppliermaster_id');
        },'bid_submission_master' => function ($q) {
            $q->select('bidSubmittedDatetime','bidSubmissionCode','line_item_total','id','supplier_registration_id')->with(['SupplierRegistrationLink' => function ($s) {
                $s->select('name','id');
            }]);
        }])->where('tender_id',$tenderId)->where('status',1)->orderBy('total_weightage','desc');

        

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('bidSubmissionCode', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($query)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getFormData(Request $request) {
        $yesNoSelection = YesNoSelection::all();
        return $this->sendResponse($yesNoSelection, 'Data reterived successfully');
    }

    public function sendEmailToCommitteMembers($tenderNeotiation,$input) {

        $srmTenderBidEmployeeDetails = SrmTenderBidEmployeeDetails::select('id','emp_id','tender_id')->where('tender_id', $tenderNeotiation['srm_tender_master_id'])->with(['employee' => function ($q){ 
            $q->select('employeeSystemID','empFullName','empID','empCompanySystemID','empEmail');
        }])->get();
        $supplierTenderNegotiations = SupplierTenderNegotiation::where('tender_negotiation_id',$input['id'])->select('bidSubmissionCode')->get();

        if($srmTenderBidEmployeeDetails) {
            foreach($srmTenderBidEmployeeDetails as $srmTenderBidEmployeeDetail) {
                $employee = ($srmTenderBidEmployeeDetail) ? $srmTenderBidEmployeeDetail->employee : null;
                foreach($supplierTenderNegotiations as $supplierTenderNegotiation) {
                    if(isset($employee) &&  $employee->empEmail) {
                        $dataEmail['empEmail'] = $employee->empEmail;
                        $dataEmail['companySystemID'] = $employee->empCompanySystemID;
                        $loginUrl = env('SRM_LINK');
                        $url = trim($loginUrl,"/register");
                        $redirectUrl= $url."/tender-management/tenders";
                        $companyName = (Auth::user()->employee && Auth::user()->employee->company) ? Auth::user()->employee->company->CompanyName : null ;
                        $temp = "Hi  $employee->empFullName , <br><br> The tender negotiation $supplierTenderNegotiation->bidSubmissionCode has been available for the approval. <br><br> <a href=$redirectUrl>Click here to approve</a> <br><br>Thank you.";
                        $dataEmail['alertMessage'] = $supplierTenderNegotiation->bidSubmissionCode." - Tender negotiation for approval";
                        $dataEmail['emailAlertMessage'] = $temp;
                        $sendEmail = \Email::sendEmailErp($dataEmail);
                    }
                }
            }
        }

    }


}
