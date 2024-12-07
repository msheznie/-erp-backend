<?php
/**
 * =============================================
 * -- File Name : ChequeRegisterDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Cheque Registry
 * -- Author : Mohamed Rilwan
 * -- Create date : 24 - September 2019
 * -- Description : This file contains the all CRUD for Cheque Registry master
 * -- REVISION HISTORY

 * --  By: Rilwan Description: Added new functions named as getAllChequeRegisterDetails()
 * --  By: Rilwan Description: Added new functions named as chequeRegisterDetailCancellation()
 * --  By: Rilwan Description: Added new functions named as getAllUnusedCheckDetails()
 * --  By: Rilwan Description: Added new functions named as getChequeSwitchFormData()
 * --  By: Rilwan Description: Added new functions named as chequeRegisterDetailSwitch()
 * --  Date: 14 - October 2019 By: Rilwan Description: Added new functions named as chequeRegisterDetailsAudit()

 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateChequeRegisterDetailAPIRequest;
use App\Http\Requests\API\UpdateChequeRegisterDetailAPIRequest;
use App\Models\ChequeRegister;
use App\Models\ChequeRegisterDetail;
use App\Models\CompanyPolicyMaster;
use App\Models\PdcLog;
use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\ChequeRegisterDetailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChequeRegisterDetailController
 * @package App\Http\Controllers\API
 */
class ChequeRegisterDetailAPIController extends AppBaseController
{
    /** @var  ChequeRegisterDetailRepository */
    private $chequeRegisterDetailRepository;

    public function __construct(ChequeRegisterDetailRepository $chequeRegisterDetailRepo)
    {
        $this->chequeRegisterDetailRepository = $chequeRegisterDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeRegisterDetails",
     *      summary="Get a listing of the ChequeRegisterDetails.",
     *      tags={"ChequeRegisterDetail"},
     *      description="Get all ChequeRegisterDetails",
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
     *                  @SWG\Items(ref="#/definitions/ChequeRegisterDetail")
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
        $this->chequeRegisterDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->chequeRegisterDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chequeRegisterDetails = $this->chequeRegisterDetailRepository->all();

        return $this->sendResponse($chequeRegisterDetails->toArray(), 'Cheque Register Details retrieved successfully');
    }

    /**
     * @param CreateChequeRegisterDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chequeRegisterDetails",
     *      summary="Store a newly created ChequeRegisterDetail in storage",
     *      tags={"ChequeRegisterDetail"},
     *      description="Store ChequeRegisterDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeRegisterDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeRegisterDetail")
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
     *                  ref="#/definitions/ChequeRegisterDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChequeRegisterDetailAPIRequest $request)
    {
        $input = $request->all();

        $chequeRegisterDetail = $this->chequeRegisterDetailRepository->create($input);

        return $this->sendResponse($chequeRegisterDetail->toArray(), 'Cheque Register Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeRegisterDetails/{id}",
     *      summary="Display the specified ChequeRegisterDetail",
     *      tags={"ChequeRegisterDetail"},
     *      description="Get ChequeRegisterDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeRegisterDetail",
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
     *                  ref="#/definitions/ChequeRegisterDetail"
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
        /** @var ChequeRegisterDetail $chequeRegisterDetail */
        $chequeRegisterDetail = $this->chequeRegisterDetailRepository->findWithoutFail($id);

        if (empty($chequeRegisterDetail)) {
            return $this->sendError('Cheque Register Detail not found');
        }

        return $this->sendResponse($chequeRegisterDetail->toArray(), 'Cheque Register Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateChequeRegisterDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chequeRegisterDetails/{id}",
     *      summary="Update the specified ChequeRegisterDetail in storage",
     *      tags={"ChequeRegisterDetail"},
     *      description="Update ChequeRegisterDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeRegisterDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeRegisterDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeRegisterDetail")
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
     *                  ref="#/definitions/ChequeRegisterDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChequeRegisterDetailAPIRequest $request)
    {
        $input = $request->all();
        /** @var ChequeRegisterDetail $chequeRegisterDetail */
        $chequeRegisterDetail = $this->chequeRegisterDetailRepository->findWithoutFail($id);
        if (empty($chequeRegisterDetail)) {
            return $this->sendError('Cheque Register Detail not found');
        }

        $chequeRegister = ChequeRegister::find($chequeRegisterDetail->cheque_register_master_id);
        if (empty($chequeRegister)) {
            return $this->sendError('Cheque Register not found');
        }

        $checkNoWithZero = $input['cheque_no'];
        $isExist = ChequeRegister::whereHas('details', function ($query) use ($checkNoWithZero, $id) {
            $query->where('cheque_no', $checkNoWithZero);
            $query->where('id', '!=', $id);
        })
            ->where('bank_id', $chequeRegister->bank_id)
            ->where('bank_account_id', $chequeRegister->bank_account_id)->first();
        if (!empty($isExist)) {
            return $this->sendError('Cheque No should be unique for bank and accounts. Cheque no ' . $checkNoWithZero . ' Already exist', 500);
        }


        $chequeRegisterDetail = $this->chequeRegisterDetailRepository->update($input, $id);

        return $this->sendResponse($chequeRegisterDetail->toArray(), 'Cheque Register Detail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chequeRegisterDetails/{id}",
     *      summary="Remove the specified ChequeRegisterDetail from storage",
     *      tags={"ChequeRegisterDetail"},
     *      description="Delete ChequeRegisterDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeRegisterDetail",
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
        /** @var ChequeRegisterDetail $chequeRegisterDetail */
        $chequeRegisterDetail = $this->chequeRegisterDetailRepository->findWithoutFail($id);

        if (empty($chequeRegisterDetail)) {
            return $this->sendError('Cheque Register Detail not found');
        }

        $chequeRegisterDetail->delete();

        return $this->sendResponse($id, 'Cheque Register Detail deleted successfully');
    }

    public function getAllChequeRegisterDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                                                                ->where('companyPolicyCategoryID', 35)
                                                                ->where('isYesNO', 1)
                                                                ->first();

        $isExistPolicyGCNFCR = ($is_exist_policy_GCNFCR) ? true : false;

        $chequeRegisterDetails = ChequeRegisterDetail::with(['document', 'pdc_printed_history' => function($query) {
                                    $query->with(['cheque_printed_by', 'changed_by', 'pay_supplier', 'currency']);
                                }])->where('cheque_register_master_id', $id);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $chequeRegisterDetails = $chequeRegisterDetails->where(function ($query) use ($search) {
                $query->whereHas('document', function ($q) use ($search) {
                    $q->where('BPVcode', 'LIKE', "%{$search}%");
                })
                    ->orWhere('cheque_no', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($chequeRegisterDetails)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('isExistPolicyGCNFCR', $isExistPolicyGCNFCR)
            ->make(true);

    }

    /*
     * chequeRegisterDetailCancellation, switch cheque both process done by this function
     * if isChange=0 => cancellation process
     * if isChange=1 => switch process
     * */
    public function chequeRegisterDetailCancellation(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('new_cheque_id', 'id', 'cancel_narration','isChange'));
        $isChange = 0;
        if (isset($input['isChange']) && $input['isChange']==0){
            $messages = [
                'cancel_narration.required' => 'Cancel comment is required.'
            ];

            $validator = \Validator::make($input, [
                'id' => 'required',
                'cancel_narration' => 'required',
                'new_cheque_id' => 'required',
            ], $messages);
            $success_msg = 'Cheque cancellation process done successfully...';
            $msg = 'cancelled';
        }else{
            $isChange = 1;
            $validator = \Validator::make($input, [
                'id' => 'required',
                'new_cheque_id' => 'required',
            ]);
            $success_msg = 'Change cheques process done successfully...';
            $msg = 'changed';
        }

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $chequeRegisterDetails = ChequeRegisterDetail::find($input['id']);
        if (empty($chequeRegisterDetails)) {
            return $this->sendError('Cheque Register Details Not Found', 404);
        }


        $document_id = $chequeRegisterDetails->document_id;
        $document_master_id = $chequeRegisterDetails->document_master_id;

        $unUsedChequeRegisterDetails = ChequeRegisterDetail::find($input['new_cheque_id']);
        if (empty($unUsedChequeRegisterDetails)) {
            return $this->sendError('New Cheque Details Not Found', 404);
        }

        $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($document_id);
        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Payment voucher is not found', 404);
        }

        if($paySupplierInvoiceMaster->approved == -1){
            return $this->sendError('Fully approved payment voucher\'s cheque can not be '.$msg, 500);
        }

        $update_array = [
            'document_id' => $document_id,
            'document_master_id' => $document_master_id,
            'status' => 1,
            'updated_by'=>Helper::getEmployeeSystemID(),
            'updated_pc'=>gethostname()
        ];

        DB::beginTransaction();
        try {
            $is_update = $this->chequeRegisterDetailRepository->update($update_array, $input['new_cheque_id']);  // update new old check documents to new cheque
            if ($is_update) {

                if ($paySupplierInvoiceMaster->pdcChequeYN) {
                    PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                          ->where('documentmasterAutoID', $document_id)
                          ->where('chequeNo', $chequeRegisterDetails->cheque_no)
                          ->update(['chequeNo' => $unUsedChequeRegisterDetails->cheque_no, 'chequePrinted' => 0, 'chequePrintedDate' => null, 'chequePrintedBy' => null]);
                } else {
                    // update supplier invoice master
                    PaySupplierInvoiceMaster::find($document_id)->update(
                        [
                            'BPVchequeNo' => $unUsedChequeRegisterDetails->cheque_no,
                            'chequePrintedYN'=> 0,
                            'chequePrintedDateTime'=> null,
                            'chequePrintedByEmpSystemID'=> 0,
                            'chequePrintedByEmpID'=> null,
                            'chequePrintedByEmpName'=> null
                        ]
                    );
                }

                // update cheque register details
                if ($isChange){
                    $this->chequeRegisterDetailRepository->update(
                        ['status' => 0, 'document_id' => null, 'document_master_id' => null,'updated_by'=>Helper::getEmployeeSystemID(), 'updated_pc'=>gethostname()], $input['id']);    // update unused status to old cheque
                }else{
                    $this->chequeRegisterDetailRepository->update(['status' => 2, 'cancel_narration' => $input['cancel_narration'],'updated_by'=>Helper::getEmployeeSystemID(), 'updated_pc'=>gethostname()], $input['id']);    // update cancel status to old cheque
                }

            }

            DB::commit();
            return $this->sendResponse($is_update, $success_msg);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 500);
        }

    }

    public function getAllUnusedCheckDetails(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $chequeRegisterDetails = ChequeRegisterDetail::with(['master'])->where('id',$input['id'])->first();
        if (empty($chequeRegisterDetails)) {
            return $this->sendError('Cheque Register Details Not Found', 404);
        }

        $unUsedChequeRegisterDetails = ChequeRegisterDetail::
            whereHas('master', function ($q) use ($chequeRegisterDetails) {
                $q->where('bank_account_id', $chequeRegisterDetails->master->bank_account_id);
                $q->where('company_id', $chequeRegisterDetails->master->company_id);
            })
            ->where('status', 0)
            ->orderBy('id', 'asc')
            ->get();
        if (empty($unUsedChequeRegisterDetails)) {
            return $this->sendError('Unused Cheques Not Found', 404);
        }
        return $this->sendResponse($unUsedChequeRegisterDetails->toArray(), 'Data Retrieved Successfully');
    }

    public function getChequeSwitchFormData(Request $request){
        $input = $request->all();
        $validator = \Validator::make($input, [
            'master_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $master = ChequeRegister::find($input['master_id']);
        if(empty($master)){
            return $this->sendError('Cheque Register Master Not Found', 500);
        }

        $output['usedChequeWithDocument'] = ChequeRegisterDetail::
            whereHas('master',function ($query) use ($master){
                $query->where('company_id',$master->company_id)
                    ->where('bank_account_id',$master->bank_account_id)
                    ->where('isActive',1);
            })
            ->where('erp_cheque_register_detail.status',1)  // used cheque
            ->join('erp_paysupplierinvoicemaster','erp_cheque_register_detail.document_id','=','erp_paysupplierinvoicemaster.PayMasterAutoId')
            ->where('erp_paysupplierinvoicemaster.approved',0)  // not approved
            ->get();
        return $this->sendResponse($output, 'Data Retrieved Successfully');
    }

    public function chequeRegisterDetailSwitch(Request $request) {
        $input = $request->all();

        $messages = [
            'from_cheque_id.required' => 'From document field is required.',
            'to_cheque_id.required' => 'To document field is required.'
        ];

        $validator = \Validator::make($input, [
            'from_cheque_id' => 'required',
            'to_cheque_id' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        if($input['from_cheque_id'] == $input['to_cheque_id']){
            return $this->sendError('You can not select same documents for switch',500);
        }
        $from_cheque_details = ChequeRegisterDetail::where('id',$input['from_cheque_id'])->with(['document'])->first();

        if(empty($from_cheque_details) || empty($from_cheque_details->document)){
            return $this->sendError('From Document Cheque Details Not Found',500);
        }

        $to_cheque_details = ChequeRegisterDetail::where('id',$input['to_cheque_id'])->with(['document'])->first();
        if(empty($to_cheque_details) || empty($to_cheque_details->document)){
            return $this->sendError('To Document Cheque Details Not Found',500);
        }

        if($from_cheque_details->document->approved == -1){
            return $this->sendError('From Document Detail in Approved status. Approved status documents can not be switch',500);
        }
        if($to_cheque_details->document->approved == -1){
            return $this->sendError('To Document Detail in Approved status. Approved status documents can not be switch',500);
        }

        DB::beginTransaction();
        try {
            $fromPdcID = 0;
            if ($from_cheque_details->document->pdcChequeYN == 0) {
                PaySupplierInvoiceMaster::find($from_cheque_details->document_id)->update(['BPVchequeNo'=>$to_cheque_details->cheque_no]);
            } else {
                $fromPdcData = PdcLog::where('documentSystemID', $from_cheque_details->document_master_id)
                          ->where('documentmasterAutoID', $from_cheque_details->document_id)
                          ->where('chequeNo', $from_cheque_details->cheque_no)
                          ->first();

                if ($fromPdcData) {
                    $fromPdcID = $fromPdcData->id;
                }

                PdcLog::where('documentSystemID', $from_cheque_details->document_master_id)
                          ->where('documentmasterAutoID', $from_cheque_details->document_id)
                          ->where('chequeNo', $from_cheque_details->cheque_no)
                          ->update([
                              'chequeNo' => $to_cheque_details->cheque_no,
                              'chequeRegisterAutoID' => $to_cheque_details->id
                          ]);
            }
            
            if ($to_cheque_details->document->pdcChequeYN == 0) {
                PaySupplierInvoiceMaster::find($to_cheque_details->document_id)->update(['BPVchequeNo'=>$from_cheque_details->cheque_no]);
            } else {
                 PdcLog::where('documentSystemID', $to_cheque_details->document_master_id)
                          ->where('documentmasterAutoID', $to_cheque_details->document_id)
                          ->where('chequeNo', $to_cheque_details->cheque_no)
                          ->when($fromPdcID > 0, function($query) use ($fromPdcID) {
                                $query->where('id', '!=', $fromPdcID);
                          })
                          ->update([
                              'chequeNo' => $from_cheque_details->cheque_no,
                              'chequeRegisterAutoID' => $from_cheque_details->id
                          ]);
            }


            $from_to_temp_document_id = $from_cheque_details->document_id;
            $from_to_temp_document_master_id = $from_cheque_details->document_master_id;

            $from_cheque_details->document_id = $to_cheque_details->document_id;
            $from_cheque_details->document_master_id = $to_cheque_details->document_master_id;
            $from_cheque_details->updated_by = Helper::getEmployeeSystemID();
            $from_cheque_details->updated_pc = gethostname();

            $to_cheque_details->document_id = $from_to_temp_document_id;
            $to_cheque_details->document_master_id = $from_to_temp_document_master_id;
            $to_cheque_details->updated_by = Helper::getEmployeeSystemID();
            $to_cheque_details->updated_pc = gethostname();

            $from_cheque_details->save();
            $to_cheque_details->save();

            $output['from_cheque_details'] = $from_cheque_details;
            $output['to_cheque_details'] = $to_cheque_details;

            DB::commit();
            return $this->sendResponse($output,'Cheques are switch between the documents successfully');
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getMessage(),500);
        }

    }

    public function chequeRegisterDetailsAudit(Request $request) {

        $input = $request->all();
        $messages = [
            'id.required' => 'Cheque register details id is required.'
        ];

        $validator = \Validator::make($input, [
            'id' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $chequeRegisterDetails = ChequeRegisterDetail::with(['document','createdBy','updatedBy'])->where('id', $input['id'])->first();
        $output['auditDetails'] = $chequeRegisterDetails;
        return $this->sendResponse($output,'Audit details retrieved successfully');
    }

}
