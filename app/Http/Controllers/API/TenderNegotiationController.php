<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TenderNegotiationRepository;
use App\Repositories\SupplierTenderNegotiationRepository;
use App\Repositories\TenderNegotiationAreaRepository;
use App\Http\Requests\StorePostTenderNegotiation;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderMaster;
use App\Models\TenderFinalBids;
use App\Models\TenderNegotiationArea;
use App\Models\TenderNegotiation;
use App\Models\BidSubmissionMaster;
use App\Models\Company;
use App\Models\SupplierTenderNegotiation;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\YesNoSelection;
use App\Models\CurrencyMaster;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TenderNegotiationController extends AppBaseController
{
    private $tenderNegotiationRepository;
    private $supplierTenderNegotiationRepository;
    private $tenderNegotiationAreaRepository;
    public function __construct(TenderNegotiationRepository $tenderNegotiationRepository, 
                                SupplierTenderNegotiationRepository $supplierTenderNegotiationRepository,
                                TenderNegotiationAreaRepository $tenderNegotiationAreaRepository
    )
    {
        $this->tenderNegotiationRepository = $tenderNegotiationRepository;
        $this->supplierTenderNegotiationRepository = $supplierTenderNegotiationRepository;
        $this->tenderNegotiationAreaRepository = $tenderNegotiationAreaRepository;

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
   
        if(!isset($updateTenderMasterRecord)) {
            return $this->sendError('Tender Master not found!', 404);
        }

        $input['currencyId'] = $updateTenderMasterRecord->currency_id;
        $tenderNeotiation = $this->tenderNegotiationRepository->create($input);
        return $this->sendResponse($tenderNeotiation->toArray(), 'Tender negotiation started successfully');


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
        $resValidation = $this->validateConfirmation($input);
       
        if (!$resValidation['status']) {
            $statusCode = isset($resValidation['code']) ? $resValidation['code'] : 404;
            return $this->sendError($resValidation['message'], $statusCode);
        }
        
        $tenderMasterId = $input['srm_tender_master_id'];
        $noToApproval = $this->getTenderMaster($tenderMasterId);

        $userId = \Helper::getEmployeeSystemID();
        $selectedSupplierList = $input['selectedSupplierList']; 
        $supplierList = $this->getTenderNegotiationsSuppliers($id);  
        $unCheckedSupList =  collect($supplierList)->whereNotIn('srm_bid_submission_master_id',array_column($selectedSupplierList, 'srm_bid_submission_master_id'));
        $checkedSupplierList = collect($selectedSupplierList)->whereNotIn('srm_bid_submission_master_id',array_column($supplierList, 'srm_bid_submission_master_id')); 
       
        $selectedAreaList = $input['selectedArealList']; 
        $areaList = $this->getTenderNegotiationsAreas($id);  





        if($unCheckedSupList->isNotEmpty()){   
            $removeUncheckedSuppliers = $this->removeUncheckedSuppliers($unCheckedSupList,$id);
                if(!$removeUncheckedSuppliers['status']){ 
                    return $this->sendError($removeUncheckedSuppliers['message']);
                }
        } 

        if($checkedSupplierList->isNotEmpty()){
            $addSelectedSuppliers = $this->addSelectedSuppliers($checkedSupplierList,$id);
                if(!$addSelectedSuppliers['status']){
                    return $this->sendError($addSelectedSuppliers['message']);
                }
        }


        if(empty($areaList)) {
            $this->saveAreaList($selectedAreaList,$id);
        }else {
            $this->updateAreaList($selectedAreaList,$id);
        }


        $tenderNeotiation = $this->tenderNegotiationRepository->find($id);
        $this->sendEmailToCommitteMembers($tenderNeotiation,$input);

        $input['confirmed_by'] =  $userId;
        $input['confirmed_at'] =  Carbon::now();
        $input['no_to_approve'] =  $noToApproval;
        $tenderNeotiation = $this->tenderNegotiationRepository->update($input, $id);
        
         
        return $this->sendResponse([], "Tender Negotiation Updated successfully");

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

        $tenderMaster = TenderMaster::select('is_negotiation_started','id','currency_id','negotiation_serial_no','negotiation_code','company_id');
        $tenderList = $tenderMaster->get(); 
        $tender = $tenderMaster->where('id',$input['srm_tender_master_id'])->first(); 
        if($tender) {
            $tender->is_negotiation_started = 1;   
            $negotiationCode = $this->generateNegotiationSerial($input['companySystemID'],$tenderList);  
            $tender->negotiation_code = $negotiationCode['code']; 
            $tender->negotiation_serial_no = $negotiationCode['lastSerialNo']; 
            $tender->save();
            return $tender;
        }


        return false;
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
        $query = TenderFinalBids::select('id','status','award','bid_id','com_weightage','supplier_id','tender_id','total_weightage','tech_weightage', 'combined_ranking')->with(['supplierTenderNegotiation' => function ($a) {
            $a->select('id','srm_bid_submission_master_id','bidSubmissionCode','tender_negotiation_id','suppliermaster_id');
        },'bid_submission_master' => function ($q) {
            $q->select('bidSubmittedDatetime','bidSubmissionCode','line_item_total','id','supplier_registration_id')->with(['SupplierRegistrationLink' => function ($s) {
                $s->select('name','id');
            }]);
        }])->where('tender_id',$tenderId)->where('status',1)->orderBy('total_weightage','desc');

        

        $search = $request->input('search.value');
        if ($search) {
            $query = $query->where(function ($a) use ($search) {
                $a->orWhereHas('bid_submission_master', function ($b) use ($search) {
                    $b->where('bidSubmissionCode', 'LIKE', "%{$search}%");
                    $b->orWhereHas('SupplierRegistrationLink', function ($c) use ($search) {
                        $c->where('name', 'LIKE', "%{$search}%");
                    });
                });
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
        $supplierTenderNegotiations = SupplierTenderNegotiation::where('tender_negotiation_id',$input['tenderNegotiationId'])->with(['bid_submission_master'  => function ($q) {
            $q->select('bidSubmissionCode','id','bidSubmittedDatetime','supplier_registration_id');
            $q->with(['SupplierRegistrationLink' => function ($s) {
                $s->select('name','id');
            }]);
        }])->select('bidSubmissionCode','srm_bid_submission_master_id','suppliermaster_id')->get();

        $tenderMaster = TenderMaster::where('id',$input['srm_tender_master_id'])->select('tender_code')->first();

        $table = "<table style='width:100%;'><thead style='
        padding: 1%;
        width: 100%;
        '><tr><th style='padding:1%;'>Bid Code</th><th style='padding:1%;'>Bid Submission Date</th><th style='padding:1%;'>Supplier Name</th></tr></thead><tbody>";

        foreach($supplierTenderNegotiations as $supplierTenderNegotiation) {

            $date = ($supplierTenderNegotiation->bid_submission_master) ? new Carbon($supplierTenderNegotiation->bid_submission_master->bidSubmittedDatetime) : null;
            $supplierName = ($supplierTenderNegotiation->bid_submission_master->SupplierRegistrationLink) ? $supplierTenderNegotiation->bid_submission_master->SupplierRegistrationLink->name : null;
            $suppplierName = ($supplierTenderNegotiation->supplier) ? $supplierTenderNegotiation->supplier->name : null;
            $table.= "<tr><td style='padding:1%;'>".$supplierTenderNegotiation->bidSubmissionCode."</td><td style='padding:1%;'>".$date->toDayDateTimeString()."</td><td style='padding:1%;'>".$suppplierName."</td></tr>";
        }

        $table .= "</tbody></table>";
        if($srmTenderBidEmployeeDetails) {
            foreach($srmTenderBidEmployeeDetails as $srmTenderBidEmployeeDetail) {
                $employee = ($srmTenderBidEmployeeDetail) ? $srmTenderBidEmployeeDetail->employee : null;
                    if(isset($employee) &&  $employee->empEmail) {
                        if(($employee->discharegedYN == 0) && ($employee->ActivationFlag == -1) && ($employee->empLoginActive == 1) && ($employee->empActive == 1)){
                            $dataEmail['empEmail'] = $employee->empEmail;
                            $dataEmail['companySystemID'] = $employee->empCompanySystemID;
                            $redirectUrl = env('ERP_APPROVE_URL');
                            $companyName = (Auth::user()->employee && Auth::user()->employee->company) ? Auth::user()->employee->company->CompanyName : null ;
                            // $temp = "Hi  $employee->empFullName , <br><br>The tender ". $tenderMaster->tender_code ."  has been available for the negotitaion approval.<br><br> The Follwing bid submission are available $table <a href=$redirectUrl>Click here to approve</a> <br><br>Thank you.";
                            $temp = "Hi  $employee->empFullName , <br><br>The tender ". $tenderMaster->tender_code ."  has been available for the negotitaion approval.<br><br><a href=$redirectUrl>Click here to approve</a> <br><br>Thank you.";
                            $dataEmail['alertMessage'] = $tenderMaster->tender_code." - Tender negotiation for approval";
                            $dataEmail['emailAlertMessage'] = $temp;
                            $sendEmail = \Email::sendEmailErp($dataEmail);
                        }
                    }
            }
        }

    }


    public function saveTenderNegotiationDetails(Request $request) {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmYn'));

        $supplierDataArray = $input['selectedSupplierList'];
        $selectedAreaList = $input['selectedArealList'];
        $tenderNegotiationId = $input['tenderNegotiationId'];
        $input['tenderNegotiationID'] = $tenderNegotiationId;


        $userId = \Helper::getEmployeeSystemID();
        $selectedSupplierList = $supplierDataArray; 
        $supplierList = $this->getTenderNegotiationsSuppliers($tenderNegotiationId);  
        $unCheckedSupList =  collect($supplierList)->whereNotIn('srm_bid_submission_master_id',array_column($selectedSupplierList, 'srm_bid_submission_master_id'));
        $checkedSupplierList = collect($selectedSupplierList)->whereNotIn('srm_bid_submission_master_id',array_column($supplierList, 'srm_bid_submission_master_id')); 
       
        $selectedAreaList = $input['selectedArealList']; 
        $areaList = $this->getTenderNegotiationsAreas($tenderNegotiationId);  


        if($unCheckedSupList->isNotEmpty()){   
            $removeUncheckedSuppliers = $this->removeUncheckedSuppliers($unCheckedSupList,$tenderNegotiationId);
                if(!$removeUncheckedSuppliers['status']){ 
                    return $this->sendError($removeUncheckedSuppliers['message']);
                }
        } 

        if($checkedSupplierList->isNotEmpty()){
            $addSelectedSuppliers = $this->addSelectedSuppliers($checkedSupplierList,$tenderNegotiationId);
                if(!$addSelectedSuppliers['status']){
                    return $this->sendError($addSelectedSuppliers['message']);
                }
        }

        
        if(empty($areaList)) {
            $this->saveAreaList($selectedAreaList,$tenderNegotiationId);
        }else {
            $this->updateAreaList($selectedAreaList,$tenderNegotiationId);
        }

        $saveTenderNegotiation = TenderNegotiation::find($tenderNegotiationId);
        $tenderMasterId= $input['tenderId'];
        $noToApproval = $this->getTenderMaster($tenderMasterId);

        $saveTenderNegotiation->comments = $input['comments'];
        $saveTenderNegotiation->no_to_approve = $noToApproval;

        $result =  $saveTenderNegotiation->save();



        if($result) {
            return $this->sendResponse($saveTenderNegotiation, 'Record updated successfully');
        }else {
            return $this->sendError('Sorry! Cannot update record', 404);

        }

    }

    public function validateConfirmation($input){

        $messages = [
            'id.required_if' => 'ID is required',
            'comments.required'  => 'Comment is required',
            'selectedSupplierList.required'  => 'Supplier is required',
            'selectedArealList.required'  => 'Area is required',
        ];
      
        $validator = \Validator::make($input, [
            'id' => ['required_if:confirmYn,1'],
            'comments'=>'required',
            'selectedSupplierList'=> 'required|array',
            'selectedArealList'=> 'required|array',
        ], $messages);

        if ($validator->fails()) {
            return ['status' => false, 'code' => 422, 'message' => $validator->messages()]; 
        }  

        return ['status' => true, 'message' => "success"]; 

    }

    public function getTenderMaster($tenderMasterId){
        $tenderMaster = TenderMaster::select('min_approval_bid_opening')
        ->where('id',$tenderMasterId)
        ->first();
        return ($tenderMaster) ? $tenderMaster->min_approval_bid_opening :  0;
  
      }

    public function getTenderNegotiationsSuppliers($id){ 
        return SupplierTenderNegotiation::select('suppliermaster_id','srm_bid_submission_master_id')
        ->where('tender_negotiation_id',$id) 
        ->get()
        ->toArray();
    }

    public function getTenderNegotiationsAreas($id) {
        return TenderNegotiationArea::select('pricing_schedule','technical_evaluation','tender_documents')
        ->where('tender_negotiation_id',$id) 
        ->get()
        ->toArray();
    }

    public function removeUncheckedSuppliers($unCheckedSupList,$id){
       
        $supplierList = collect($unCheckedSupList)->toArray();   

        $supplierUnchecked = SupplierTenderNegotiation::where('tender_negotiation_id',$id)
        ->whereIn('srm_bid_submission_master_id',array_column($supplierList,'srm_bid_submission_master_id')) 
        ->whereIn('suppliermaster_id',array_column($supplierList,'suppliermaster_id')) 
        ->delete();

        if(!$supplierUnchecked){ 
            return ['status' => false,'message' =>'Supplier deltation failed'];  
        }

        return ['status' => true,'message' =>'Supplier deltation success'];  
    }


    public function addSelectedSuppliers($checkedSupplierList,$id){   
        $data = [];
    
        foreach ($checkedSupplierList as $val ){
            $data[] = [
                'tender_negotiation_id'=> $id,
                'suppliermaster_id'=> $val['suppliermaster_id'],
                'srm_bid_submission_master_id'=> $val['srm_bid_submission_master_id'],
                'bidSubmissionCode'=> $val['bidSubmissionCode']
            ];
        }

        $results = SupplierTenderNegotiation::insert($data);

        if(!$results){ 
            return ['status' => false,'message' =>'Supplier insertion failed'];  
        }

        return ['status' => true,'message' =>'Supplier insertion success'];
    }

    public function saveAreaList($checkedAreaList,$id){   

        $results = TenderNegotiationArea::create($checkedAreaList);

        if(!$results){ 
            return ['status' => false,'message' =>'Area creation failed'];  
        }

        return ['status' => true,'message' =>'Area creation success'];  
    }


    public function updateAreaList($checkedAreaList,$id) {
        $updateArea = TenderNegotiationArea::where('tender_negotiation_id',$id)->update($checkedAreaList);
        if(!$updateArea){ 
            return ['status' => false,'message' =>'Area cannot update'];  
        }

        return ['status' => true,'message' =>'Area updated success'];  

    }

    public function generateNegotiationSerial($companyId,$tenderList){  
        $company = Company::where('companySystemID', $companyId)->select('companySystemID', 'CompanyID')->first();

        $tenderCollection = collect($tenderList); 
        $tenderCollection = $tenderCollection->sortByDesc('negotiation_serial_no'); 
        $firstNegotiation = $tenderCollection->first();   
        $lastSerial = $firstNegotiation->negotiation_serial_no;   
        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial) + 1;
        }


       
        $documentCode = 'NTNDR';

        $code = ($company->CompanyID . '/' . $documentCode . '/' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT)); 
        $data = [
            'code'=>$code,
            'lastSerialNo' => $lastSerialNumber
        ];
        return $data;

    }

    public function getNegotiationStartedSupplierList(Request $request){
    try {
        $validatedData = $request->validate([
            'negotiationId' => 'required|integer',
            'tenderUuid' => 'required',
        ]);

        $result =  $this->supplierTenderNegotiationRepository->getSupplierList($validatedData['negotiationId'], $validatedData['tenderUuid']);
        return $this->sendResponse($result,'Received supplier List');
        } catch (\Exception $e) {
            return $this->sendError('Error occurred');
        }
    }
}

