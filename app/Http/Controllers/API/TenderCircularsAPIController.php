<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TenderDetails;
use App\Models\CircularAmendments;
use App\Models\CircularSuppliers;
use App\Http\Requests\API\CreateTenderCircularsAPIRequest;
use App\Http\Requests\API\UpdateTenderCircularsAPIRequest;
use App\Mail\EmailForQueuing;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\SupplierRegistrationLink;
use App\Models\SystemConfigurationAttributes;
use App\Models\TenderCirculars;
use App\Models\TenderMaster;
use App\Repositories\TenderCircularsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\email;
/**
 * Class TenderCircularsController
 * @package App\Http\Controllers\API
 */

class TenderCircularsAPIController extends AppBaseController
{
    /** @var  TenderCircularsRepository */
    private $tenderCircularsRepository;

    public function __construct(TenderCircularsRepository $tenderCircularsRepo)
    {
        $this->tenderCircularsRepository = $tenderCircularsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCirculars",
     *      summary="Get a listing of the TenderCirculars.",
     *      tags={"TenderCirculars"},
     *      description="Get all TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TenderCirculars")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->tenderCircularsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderCircularsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderCirculars = $this->tenderCircularsRepository->all();

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars retrieved successfully');
    }

    /**
     * @param CreateTenderCircularsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderCirculars",
     *      summary="Store a newly created TenderCirculars in storage",
     *      tags={"TenderCirculars"},
     *      description="Store TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCirculars that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCirculars")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderCircularsAPIRequest $request)
    {
        $input = $request->all();

        $tenderCirculars = $this->tenderCircularsRepository->create($input);

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCirculars/{id}",
     *      summary="Display the specified TenderCirculars",
     *      tags={"TenderCirculars"},
     *      description="Get TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderCircularsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderCirculars/{id}",
     *      summary="Update the specified TenderCirculars in storage",
     *      tags={"TenderCirculars"},
     *      description="Update TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCirculars that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCirculars")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderCircularsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        $tenderCirculars = $this->tenderCircularsRepository->update($input, $id);

        return $this->sendResponse($tenderCirculars->toArray(), 'TenderCirculars updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderCirculars/{id}",
     *      summary="Remove the specified TenderCirculars from storage",
     *      tags={"TenderCirculars"},
     *      description="Delete TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        $tenderCirculars->delete();

        return $this->sendSuccess('Tender Circulars deleted successfully');
    }

    public function getTenderCircularList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tender_id = $input['tender_id'];



        $tenderMaster = TenderCirculars::with(['document_attachments'])->where('tender_id', $tender_id)->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhere('circular_name', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', 'asc');
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAttachmentDropCircular(Request $request)
    {
        $input = $request->all();
        $attachment = CircularAmendments::where('tender_id',$input['tenderMasterId'])->get();
        $attchArray = array();
        if(count($attachment) > 0){
            $attchArray = $attachment->pluck('amendment_id');
            $attchArray = $attchArray->filter();
        }

        $attachmentDrop = DocumentAttachments::whereNotIn('attachmentID',$attchArray)
            ->where('documentSystemID',108)
            ->where('attachmentType',3)
            ->where('parent_id', null)
            ->where('documentSystemCode',$input['tenderMasterId'])->orderBy('attachmentID', 'asc')->get()->toArray();

        $i = 0;
        foreach  ($attachmentDrop as $row){
            $attachmentDrop[$i]['menu'] =   $row['attachmentDescription'] . '_' . $row['order_number'];
            $i++;
        }

        $data['attachmentDrop'] = $attachmentDrop;

        if(isset($input['circularId']) && $input['circularId'] > 0){
           $circular = CircularAmendments::select('amendment_id')->where('circular_id',$input['circularId'])->get()->toArray();
           if(sizeof($circular) > 0){
               $attachmentAmended = DocumentAttachments::whereIn('attachmentID',$circular)->get();

               $i = 0;
               foreach  ($attachmentAmended as $r){
                   $attachmentAmended[$i]['menu'] =   $r['attachmentDescription'] . '_' . $r['order_number'];
                   $i++;
               }
               $data['amended'] = $attachmentAmended;
           }
        }

        return $data;
    }

    public function addCircular(Request $request)
    {
        $input = $request->all();

        $tenderMaster = TenderMaster::select('id','tender_type_id','document_system_id')
            ->where('id',$input['tenderMasterId'])
            ->first();

        if($input['isRequestProcessComplete'] && $input['requestType'] == 'Amend')
        {        
            if(!isset($input['attachment_id'])){
                return ['success' => false, 'message' => 'Amendment is required'];
            } 
        }
   
        if(isset($input['attachment_id' ])){
            $attachmentList = $input['attachment_id'];
        }

        if ($tenderMaster['document_system_id'] == 113 ||
            ($tenderMaster['document_system_id'] == 108 && $input['tenderTypeId'] == 3)) {
            if(isset($input['supplier_id'])){
                if(sizeof($input['supplier_id' ]) == 0){
                    return ['success' => false, 'message' => 'Supplier is required'];
                }
                $supplierList = $input['supplier_id' ];
            } else {
                return ['success' => false, 'message' => 'Supplier is required'];
            }
        }


        $input = $this->convertArrayToSelectedValue($request->all(), array('attachment_id'));

        if(!isset($input['description']) && !isset($input['attachment_id'])){
            return ['success' => false, 'message' => 'Description or Amendment is required'];
        }

        if(isset($input['id'])) {
            $exist = TenderCirculars::where('id','!=',$input['id'])->where('tender_id', $input['tenderMasterId'])->where('circular_name', $input['circular_name'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Circular name can not be duplicated'];
            }
        }else{
            $exist = TenderCirculars::where('circular_name', $input['circular_name'])->where('tender_id', $input['tenderMasterId'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Circular name can not be duplicated'];
            }
        }

        if(isset($input['attachment_id'])){
            if(isset($input['id'])) {
                $exist = TenderCirculars::where('id','!=',$input['id'])->where('tender_id', $input['tenderMasterId'])->where('attachment_id', $input['attachment_id'])->where('company_id', $input['companySystemID'])->first();

                if(!empty($exist)){
                    return ['success' => false, 'message' => 'Selected Attachment has been used in a different circular'];
                }
            }else{
               $exist = TenderCirculars::where('attachment_id', $input['attachment_id'])->where('tender_id', $input['tenderMasterId'])->where('company_id', $input['companySystemID'])->first();

                if(!empty($exist)){
                    return ['success' => false, 'message' => 'Selected Attachment has been used in a different circular'];
                }
            }
        }

        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['tender_id']=$input['tenderMasterId'];
            $data['circular_name']=$input['circular_name'];
            if(isset($input['description'])){
                $data['description']=$input['description'];
            }else{
                $data['description']=null;
            }
            /*if(isset($input['attachment_id'])){
                $data['attachment_id']=$input['attachment_id'];
            }else{
                $data['attachment_id']=null;
            }*/
            $data['company_id']=$input['companySystemID'];

            if(isset($input['id'])){
                $circulatAmends =  CircularAmendments::where('circular_id', $input['id'])->select('id')->count();
                if ($circulatAmends == 0) {
                    if($input['isRequestProcessComplete'] && $input['requestType'] == 'Amend')
                    {
                        return ['success' => false, 'message' => 'Amendment is required'];
                    }
                    
                }


                $data['updated_by'] = $employee->employeeSystemID;
                $data['updated_at'] = Carbon::now();
                $tenderCircular = TenderCirculars::find($input['id']);
                $result = $tenderCircular->update($data);
                if($result){
                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
                }
            }else{
                $data['created_by'] = $employee->employeeSystemID;
                $data['created_at'] = Carbon::now();
                $result = TenderCirculars::create($data);
                if($result){
                    if(isset($attachmentList)){
                        foreach ($attachmentList as $attachment){
                            $dataAttachment['tender_id'] = $input['tenderMasterId'];
                            $dataAttachment['circular_id'] = $result->id;
                            $dataAttachment['amendment_id'] = $attachment['id'];
                            $dataAttachment['status'] = null;
                            $dataAttachment['created_by'] = $employee->employeeSystemID;
                            $dataAttachment['created_at'] = Carbon::now();
                            CircularAmendments::create($dataAttachment);
                        }
                    }

                    if(isset($supplierList)){
                        foreach ($supplierList as $supplier){
                            $dataSupplier['circular_id'] = $result->id;
                            $dataSupplier['supplier_id'] = $supplier['id'];
                            $dataSupplier['created_by'] = $employee->employeeSystemID;
                            $dataSupplier['created_at'] = Carbon::now();
                            CircularSuppliers::create($dataSupplier);
                        }
                    }

                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function getCircularMaster(Request $request)
    {
        $input = $request->all();
        return TenderCirculars::where('id',$input['id'])->first();
    }

    public function deleteTenderCircular(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $tenderCircular =  TenderCirculars::find($input['id']);
            $result = $tenderCircular->delete();
            
            if($result){
                
                $circular = $this->deleteCircularAmend($input['id']);

                if (!$circular['success']) {
                    return $this->sendError($circular['message'], 500);
                }       

                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }

    }

    public function tenderCircularPublish(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $companyName = "";
        $company = Company::find($request->input('company_id'));
        if(isset($company->CompanyName)){
            $companyName =  $company->CompanyName;
        }
        DB::beginTransaction();
        try {
            $att['updated_by'] = $employee->employeeSystemID;
            $att['status'] = 1;
            $result = TenderCirculars::where('id', $input['id'])->update($att);
            $amendmentsList = CircularAmendments::with('document_attachments')->where('circular_id', $input['id'])->get();
            $circular = TenderCirculars::where('id', $input['id'])->get()->toArray();
            $tenderObj = TenderDetails::getTenderMasterData($input['tender_id']);
            $supplierList = Helper::getTenderCircularSupplierList($tenderObj, $input['id'], $input['tender_id'], $input['company_id']);
            $file = array();
            foreach ($amendmentsList as $amendments){
                $file[$amendments->document_attachments->originalFileName] = Helper::getFileUrlFromS3($amendments->document_attachments->path);
            }

            Log::info($file);

            $fromName = \Helper::getEmailConfiguration('mail_name','GEARS');

            if ($result && $supplierList) {
                DB::commit();
                foreach ($supplierList as $supplier){
                    $description = "";
                    if(isset($circular[0]['description'])){
                        $description = "<b>Circular Description : </b>" . $circular[0]['description']. "<br /><br />";
                    }

                    $email = ($tenderObj->document_system_id == 108 && $tenderObj->tender_type_id == 2) ?
                        $supplier->supplierAssigned->supEmail :
                        $supplier->supplier_registration_link->email;

                    $emailFormatted = email::emailAddressFormat($email);
                    
                    $dataEmail['companySystemID'] = $request->input('company_id');
                    $dataEmail['alertMessage'] = "Tender Circular";
                    $dataEmail['empEmail'] = $emailFormatted;
                    $body = "Dear Supplier,"."<br /><br />"." Please find published tender circular details below."."<br /><br /><b>". "Circular Name : ". "</b>".$circular[0]['circular_name'] ." "."<br /><br />". $description .$companyName."</b><br /><br />"."Thank You"."<br /><br /><b>";
                    $dataEmail['emailAlertMessage'] = $body;
                    $dataEmail['attachmentList'] = $file;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                }

                return ['success' => true, 'message' => 'Successfully Published'];
            } else {
                return ['fail' => true, 'message' => 'Published failed'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'message' => $e];
        }
    }

    public function getTenderPurchasedSupplierList(Request $request)
    {
        $input = $request->all();
        $purchased = SupplierRegistrationLink::selectRaw('*')
            ->join('srm_tender_master_supplier', 'srm_tender_master_supplier.purchased_by', '=', 'srm_supplier_registration_link.id')
            ->where('srm_tender_master_supplier.tender_master_id', $input['tenderMasterId'])
            ->get();

        $data['purchased'] = $purchased;

        if(isset($input['circularId']) && $input['circularId'] > 0){
            $dataAssigned = SupplierRegistrationLink::selectRaw('*')
                ->join('srm_circular_suppliers', 'srm_circular_suppliers.supplier_id', '=', 'srm_supplier_registration_link.id')
                ->where('srm_circular_suppliers.circular_id', $input['circularId'])
                ->get();

            $data['dataAssigned'] = $dataAssigned;

            $dataAssignedArr = SupplierRegistrationLink::selectRaw('supplier_id')
                ->join('srm_circular_suppliers', 'srm_circular_suppliers.supplier_id', '=', 'srm_supplier_registration_link.id')
                ->where('srm_circular_suppliers.circular_id', $input['circularId'])
                ->get()->toArray();

            if(sizeof($dataAssignedArr) > 0){
                $i = 0;
                foreach ($dataAssignedArr as $assigned){
                    $supplier[$i] = $assigned['supplier_id'];
                    $i++;
                }

                $purchased = SupplierRegistrationLink::selectRaw('*')
                    ->join('srm_tender_master_supplier', 'srm_tender_master_supplier.purchased_by', '=', 'srm_supplier_registration_link.id')
                    ->where('srm_tender_master_supplier.tender_master_id', $input['tenderMasterId'])
                    ->whereNotIn('srm_tender_master_supplier.purchased_by', $supplier)
                    ->get();

                $data['purchased'] = $purchased;
            }
        }

        return $data;
    }

    public function deleteCircularSupplier(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $result = CircularSuppliers::where('id',$input['id'])->delete();
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function deleteCircularAmendment(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $output = CircularAmendments::where('amendment_id',$input['attachmentID'])
                ->where('tender_id', $input['tenderMasterId'])
                ->where('circular_id', $input['circularId'])
                ->first();

            $model = CircularAmendments::find($output->id);
            $result = $model->delete();
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }

    }

    public function addCircularSupplier(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $dataSupplier['circular_id'] = $input['circularId'];
            $dataSupplier['supplier_id'] = $input['selectedSupplierId'];
            $dataSupplier['created_by'] = $employee->employeeSystemID;
            $dataSupplier['created_at'] = Carbon::now();
            $result = CircularSuppliers::create($dataSupplier);
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully created', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'message' => $e];
        }

    }

    public function addCircularAmendment(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $dataAttachment['tender_id'] = $input['tenderMasterId'];
            $dataAttachment['circular_id'] = $input['circularId'];
            $dataAttachment['amendment_id'] = $input['amendmentId'];
            $dataAttachment['status'] = null;
            $dataAttachment['created_by'] = $employee->employeeSystemID;
            $dataAttachment['created_at'] = Carbon::now();
            $result = CircularAmendments::create($dataAttachment);
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully created', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'message' => $e];
        }
    }

    public function checkAmendmentIsUsedInCircular(Request $request)
    {
        $input = $request->all();
        try{
            $count = CircularAmendments::where('amendment_id',  $input['amendmentId'])->where('tender_id', $input['tenderMasterId'])->get()->count();
            if($count === 1){
                if($input['action'] == 'U'){
                    return ['success' => true, 'message' => 'This amendment is assigned to a circular, you cannot update.', 'data' => $count];
                } elseif ($input['action'] == 'D'){
                    return ['success' => true, 'message' => 'This amendment is assigned to a circular, you cannot delete.', 'data' => $count];
                }
            } else {
                return ['success' => true, 'message' => '', 'data' => $count];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'message' => $e];
        }

    }

    public function deleteCircularAmend($id)
    {

        DB::beginTransaction();
        try {
            $circularObj = CircularAmendments::select('id')->where('circular_id', $id)->get();

            foreach ($circularObj as $val) {
                $circular = CircularAmendments::find($val->id);
                $circular->delete();
            }

            DB::commit();
            return ['success' => true, 'message' => 'Successfully Deleted'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
