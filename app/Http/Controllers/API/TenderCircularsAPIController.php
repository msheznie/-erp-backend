<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TenderDetails;
use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\Models\CircularSuppliers;
use App\Http\Requests\API\CreateTenderCircularsAPIRequest;
use App\Http\Requests\API\UpdateTenderCircularsAPIRequest;
use App\Mail\EmailForQueuing;
use App\Models\CircularSuppliersEditLog;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\SupplierRegistrationLink;
use App\Models\SystemConfigurationAttributes;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use App\Models\TenderMaster;
use App\Models\TenderSupplierAssignee;
use App\Models\TenderSupplierAssigneeEditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TenderCircularsRepository;
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

        return $this->sendResponse($tenderCirculars->toArray(), trans('custom.tender_circulars_retrieved_successfully'));
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

        return $this->sendResponse($tenderCirculars->toArray(), trans('custom.tender_circulars_saved_successfully'));
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
            return $this->sendError(trans('custom.tender_circulars_not_found'));
        }

        return $this->sendResponse($tenderCirculars->toArray(), trans('custom.tender_circulars_retrieved_successfully'));
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
            return $this->sendError(trans('custom.tender_circulars_not_found'));
        }

        $tenderCirculars = $this->tenderCircularsRepository->update($input, $id);

        return $this->sendResponse($tenderCirculars->toArray(), trans('custom.tendercirculars_updated_successfully'));
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
            return $this->sendError(trans('custom.tender_circulars_not_found'));
        }

        $tenderCirculars->delete();

        return $this->sendSuccess('Tender Circulars deleted successfully');
    }

    public function getTenderCircularList(Request $request)
    {
        return $this->tenderCircularsRepository->getCircularList($request);
    }

    public function getAttachmentDropCircular(Request $request)
    {
        return $this->tenderCircularsRepository->getAttachmentDropCircular($request);
    }

    public function addCircular(Request $request)
    {
        $input = $request->all();
        $tenderMasterID = $input['tenderMasterId'] ?? 0;
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;
        $requestType = $input['requestType'] ?? '';
        $companySystemID = $input['companySystemID'] ?? 0;
        $id = $input['id'] ?? 0;
        $amd_id = $input['amd_id'] ?? 0;
        $supplierList = $input['supplier_id'] ?? [];

        $tenderMaster = TenderMaster::select('id','tender_type_id','document_system_id')
            ->where('id',$input['tenderMasterId'])
            ->first();

        if($editOrAmend && $requestType == 'Amend')
        {        
            if(!isset($input['attachment_id'])){
                return ['success' => false, 'message' => trans('srm_tender_rfx.amendment_is_required')];
            } 
        }
   
        if(isset($input['attachment_id' ])){
            $attachmentList = $input['attachment_id'];
        }

        if (($input['tenderTypeId'] ?? null) === 3) {
            if(isset($input['supplier_id'])){
                if(sizeof($input['supplier_id' ]) == 0){
                    return ['success' => false, 'message' => trans('srm_tender_rfx.supplier_is_required')];
                }
            } else {
                return ['success' => false, 'message' => trans('srm_tender_rfx.supplier_is_required')];
            }
        }


        $input = $this->convertArrayToSelectedValue($request->all(), array('attachment_id'));

        if(!isset($input['description']) && !isset($input['attachment_id'])){
            return ['success' => false, 'message' => trans('srm_tender_rfx.description_or_amendment_is_required')];
        }

        if($id > 0 || $amd_id > 0) {
            $exist = $editOrAmend ?
                TenderCircularsEditLog::checkCircularNameExists($input['circular_name'], $tenderMasterID, $companySystemID, $versionID, $id, $amd_id) :
                TenderCirculars::checkCircularNameExists($input['circular_name'], $tenderMasterID, $companySystemID, $id);

            if(!empty($exist)){
                return ['success' => false, 'message' => trans('srm_tender_rfx.circular_name_cannot_be_duplicated')];
            }
        }else{
            $exist = $editOrAmend ?
                TenderCircularsEditLog::checkCircularNameExists($input['circular_name'], $tenderMasterID, $companySystemID, $versionID) :
                TenderCirculars::checkCircularNameExists($input['circular_name'], $tenderMasterID, $companySystemID);

            if(!empty($exist)){
                return ['success' => false, 'message' => trans('srm_tender_rfx.circular_name_cannot_be_duplicated')];
            }
        }

        /*if(isset($input['attachment_id'])){
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
        }*/

        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['tender_id'] = $tenderMasterID;
            $data['circular_name'] = $input['circular_name'];
            if(isset($input['description'])){
                $data['description'] = $input['description'];
            }else{
                $data['description'] = null;
            }

            $data['company_id']= $input['companySystemID'];

            if($id > 0 || $amd_id > 0){
                $circulatAmends =  $editOrAmend ?
                    CircularAmendmentsEditLog::getCircularAmendmentByID($amd_id, $versionID) :
                    CircularAmendments::getCircularAmendmentByID($id);
                if (count($circulatAmends) == 0 && $editOrAmend && $input['requestType'] == 'Amend') {
                    return ['success' => false, 'message' => trans('srm_tender_rfx.amendment_is_required')];
                }


                $data['updated_by'] = $employee->employeeSystemID;
                $data['updated_at'] = Carbon::now();
                if($editOrAmend) {
                    $tenderCircular = TenderCircularsEditLog::find($amd_id);
                } else {
                    $tenderCircular = TenderCirculars::find($id);
                }
                $result = $tenderCircular->update($data);
                if($result){
                    DB::commit();
                    return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_updated'), 'data' => $result];
                }
            }else{
                $data['status'] = 0;
                $data['created_by'] = $employee->employeeSystemID;
                $data['created_at'] = Carbon::now();
                if($editOrAmend) {
                    $data['id'] = null;
                    $data['level_no'] = 1;
                    $data['vesion_id'] = $versionID;
                    $result = TenderCircularsEditLog::create($data);
                } else {
                    $result = TenderCirculars::create($data);
                }

                if($result){
                    if(isset($attachmentList)){
                        foreach ($attachmentList as $attachment){
                            $dataAttachment['tender_id'] = $input['tenderMasterId'];
                            $dataAttachment['circular_id'] = $editOrAmend ? $result->amd_id : $result->id;
                            $dataAttachment['amendment_id'] = $attachment['id'];
                            $dataAttachment['status'] = null;
                            $dataAttachment['created_by'] = $employee->employeeSystemID;
                            $dataAttachment['created_at'] = Carbon::now();
                            if($editOrAmend){
                                $dataAttachment['id'] = null;
                                $dataAttachment['level_no'] = 1;
                                $dataAttachment['vesion_id'] = $versionID;
                                CircularAmendmentsEditLog::create($dataAttachment);
                            } else {
                                CircularAmendments::create($dataAttachment);
                            }
                        }
                    }

                    if(isset($supplierList)){
                        foreach ($supplierList as $key => $value){
                            $dataSupplier['circular_id'] = $editOrAmend ? $result->amd_id : $result->id;
                            $dataSupplier['supplier_id'] = $value['id'];
                            $dataSupplier['created_by'] = $employee->employeeSystemID;
                            $dataSupplier['created_at'] = Carbon::now();
                            if($editOrAmend){
                                $dataSupplier['id'] = null;
                                $dataSupplier['level_no'] = 1;
                                $dataSupplier['version_id'] = $versionID;
                                CircularSuppliersEditLog::create($dataSupplier);
                            } else {
                                CircularSuppliers::create($dataSupplier);
                            }
                        }
                    }

                    DB::commit();
                    return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_saved'), 'data' => $result];
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getCircularMaster(Request $request)
    {
        $input = $request->all();
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;

        return $editOrAmend ? TenderCircularsEditLog::find($input['id']) : TenderCirculars::find($input['id']);
    }

    public function deleteTenderCircular(Request $request)
    {
        try{
            $input = $request->all();
            return $this->tenderCircularsRepository->deleteTenderCircular($input);
        } catch (\Exception $exception){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $exception->getMessage()])];
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
                $documentName = $tenderObj->document_system_id == 108 ? 'Tender' : 'RFX';
                foreach ($supplierList as $supplier){
                    $description = "";
                    if(isset($circular[0]['description'])){
                        $description = "<b>Circular Description : </b>" . $circular[0]['description']. "<br /><br />";
                    }

                    $email = ($tenderObj->tender_type_id ?? null) == 2 ?
                        $supplier->supplierAssigned->supEmail :
                        $supplier->supplier_registration_link->email;

                    $emailFormatted = email::emailAddressFormat($email);
                    
                    $dataEmail['companySystemID'] = $request->input('company_id');
                    $dataEmail['alertMessage'] = $documentName . " Circular";
                    $dataEmail['empEmail'] = $emailFormatted;
                    $body = "Dear Supplier,"."<br /><br />"." Please find published <span style='text-transform: lowercase;'>". $documentName ."</span> circular details below."."<br /><br /><b>". "Circular Name : ". "</b>".$circular[0]['circular_name'] ." "."<br /><br />". $description .$companyName."</b><br /><br />"."Thank You"."<br /><br /><b>";
                    $dataEmail['emailAlertMessage'] = $body;
                    $dataEmail['attachmentList'] = $file;
                    $sendEmail = \Email::sendEmailErp($dataEmail);
                }

                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_published')];
            } else {
                return ['fail' => true, 'message' => trans('srm_tender_rfx.published_failed')];
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
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;

        $purchased = SupplierRegistrationLink::selectRaw('*')
            ->join('srm_tender_master_supplier', 'srm_tender_master_supplier.purchased_by', '=', 'srm_supplier_registration_link.id')
            ->where('srm_tender_master_supplier.tender_master_id', $input['tenderMasterId'])
            ->get();

        if ($purchased->isEmpty()) {
            $assignSupplier = $editOrAmend ?
                TenderSupplierAssigneeEditLog::getAssignSupplier($input['companySystemID'],$input['tenderMasterId'], $versionID) :
                TenderSupplierAssignee::getAssignSupplier($input['companySystemID'],$input['tenderMasterId']);

            $purchased = $assignSupplier->map(function ($assignee) {
                $link = $assignee->supplierAssigned->supplierRegistrationLink ?? null;
                $name = $link && !empty($link->name) ? $link->name : $assignee->supplierAssigned->supplierName ;

                return $link ? [
                    'purchased_by' => $link->purchased_by,
                    'name' => $name,
                ] : null;
            })->filter()->values();
        }

        return response()->json([
            'purchased' => $purchased,
        ]);

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
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted'), 'data' => $result];
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
            $versionID = $input['versionID'] ?? 0;
            $editOrAmend = $versionID > 0;
            $attachmentID = $input['attachmentID'] ?? 0;
            $tenderMasterId = $input['tenderMasterId'] ?? 0;
            $circularId = (int) $input['circularId'] ?? 0;

            $checkExist = $editOrAmend ?
                CircularAmendmentsEditLog::getAmendmentAttachment($attachmentID, $circularId, $tenderMasterId , $versionID) :
                CircularAmendments::getAmendmentAttachment($attachmentID, $circularId, $tenderMasterId);

            if(empty($checkExist)){
                return ['success' => false, 'message' => trans('srm_tender_rfx.attachment_not_found')];
            }
            $id = $editOrAmend && $versionID > 0 ? $checkExist->amd_id : $checkExist->id;
            $model = $editOrAmend ?
                CircularAmendmentsEditLog::find($id) :
                CircularAmendments::find($id);

            if($editOrAmend){
                $model->is_deleted = 1;
                $result = $model->save();
            } else {
                $result = $model->delete();
            }
            if($result){
                DB::commit();
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted'), 'data' => $result];
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
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_created'), 'data' => $result];
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
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;

        DB::beginTransaction();
        try {
            $dataAttachment['tender_id'] = $input['tenderMasterId'];
            $dataAttachment['circular_id'] = $input['circularId'];
            $dataAttachment['amendment_id'] = $input['amendmentId'];
            $dataAttachment['status'] = null;
            $dataAttachment['created_by'] = $employee->employeeSystemID;
            $dataAttachment['created_at'] = Carbon::now();
            if($editOrAmend && $versionID > 0){
                $dataAttachment['vesion_id'] = $versionID;
                $dataAttachment['level_no'] = 1;
                $result = CircularAmendmentsEditLog::create($dataAttachment);
            } else {
                $result = CircularAmendments::create($dataAttachment);
            }
            if($result){
                DB::commit();
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_created'), 'data' => $result];
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
            $validator = Validator::make($input, [
                'amendmentId' => 'required',
                'tenderMasterId' => 'required',
            ], [
                'amendmentId.required' => trans('srm_tender_rfx.attachment_id_required'),
                'tenderMasterId.required' => trans('srm_tender_rfx.tender_master_id_required'),
            ]);

            if ($validator->fails()) {
                return $this->sendError(implode(', ', $validator->errors()->all()));
            }

            $amendmentResponse = $this->tenderCircularsRepository->checkAmendmentIsUsedInCircular($input);
            if(!$amendmentResponse['success']){
                return $this->sendError($amendmentResponse['message']);
            }
            return $this->sendResponse([], trans('srm_tender_rfx.document_attachment_can_be_deleted'));

        } catch (\Exception $e) {
            return $this->sendError(trans('srm_tender_rfx.unexpected_error', ['message' => $e->getMessage()]));
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
            return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
