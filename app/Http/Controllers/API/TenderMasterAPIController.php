<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderMasterAPIRequest;
use App\Models\BankAccount;
use App\Models\BankMaster;
use App\Models\CurrencyMaster;
use App\Models\EnvelopType;
use App\Models\EvaluationType;
use App\Models\ProcumentActivity;
use App\Models\TenderMaster;
use App\Models\TenderProcurementCategory;
use App\Models\TenderSiteVisitDates;
use App\Models\TenderType;
use App\Repositories\TenderMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderMasterController
 * @package App\Http\Controllers\API
 */

class TenderMasterAPIController extends AppBaseController
{
    /** @var  TenderMasterRepository */
    private $tenderMasterRepository;

    public function __construct(TenderMasterRepository $tenderMasterRepo)
    {
        $this->tenderMasterRepository = $tenderMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasters",
     *      summary="Get a listing of the TenderMasters.",
     *      tags={"TenderMaster"},
     *      description="Get all TenderMasters",
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
     *                  @SWG\Items(ref="#/definitions/TenderMaster")
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
        $this->tenderMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderMasters = $this->tenderMasterRepository->all();

        return $this->sendResponse($tenderMasters->toArray(), 'Tender Masters retrieved successfully');
    }

    /**
     * @param CreateTenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderMasters",
     *      summary="Store a newly created TenderMaster in storage",
     *      tags={"TenderMaster"},
     *      description="Store TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMaster")
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
     *                  ref="#/definitions/TenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderMaster = $this->tenderMasterRepository->create($input);

        return $this->sendResponse($tenderMaster->toArray(), 'Tender Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasters/{id}",
     *      summary="Display the specified TenderMaster",
     *      tags={"TenderMaster"},
     *      description="Get TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
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
     *                  ref="#/definitions/TenderMaster"
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
        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        return $this->sendResponse($tenderMaster->toArray(), 'Tender Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderMasters/{id}",
     *      summary="Update the specified TenderMaster in storage",
     *      tags={"TenderMaster"},
     *      description="Update TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMaster")
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
     *                  ref="#/definitions/TenderMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        $tenderMaster = $this->tenderMasterRepository->update($input, $id);

        return $this->sendResponse($tenderMaster->toArray(), 'TenderMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderMasters/{id}",
     *      summary="Remove the specified TenderMaster from storage",
     *      tags={"TenderMaster"},
     *      description="Delete TenderMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMaster",
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
        /** @var TenderMaster $tenderMaster */
        $tenderMaster = $this->tenderMasterRepository->findWithoutFail($id);

        if (empty($tenderMaster)) {
            return $this->sendError('Tender Master not found');
        }

        $tenderMaster->delete();

        return $this->sendSuccess('Tender Master deleted successfully');
    }

    public function getTenderMasterList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];



        $tenderMaster = TenderMaster::with(['tender_type','envelop_type','currency'])->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhereHas('tender_type', function ($query1) use ($search) {
                    $query1->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('envelop_type', function ($query1) use ($search) {
                    $query1->where('name', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('currency', function ($query1) use ($search) {
                    $query1->where('CurrencyName', 'LIKE', "%{$search}%");
                    $query1->orWhere('CurrencyCode', 'LIKE', "%{$search}%");
                });
                $query->orWhere('description', 'LIKE', "%{$search}%");
                $query->orWhere('title', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
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

    public function getTenderDropDowns(Request $request)
    {
        $input = $request->all();

        $data['tenderType'] = TenderType::get();
        $data['envelopType'] = EnvelopType::get();
        $data['currency'] = CurrencyMaster::get();
        $data['evaluationTypes'] = EvaluationType::get();
        $data['bank'] = BankMaster::get();
        $data['procurementCategory'] = TenderProcurementCategory::where('level',0)->get();

        return $data;
    }

    public function createTender(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['currency_id']= isset($input['currency_id'])?$input['currency_id'] : null;
            $data['description']= isset($input['description'])?$input['description'] : null;
            $data['description_sec_lang']=isset($input['description_sec_lang'])?$input['description_sec_lang'] : null;
            $data['envelop_type_id']=$input['envelop_type_id'];
            $data['tender_type_id']=$input['tender_type_id'];
            $data['title']=$input['title'];
            $data['title_sec_lang']=$input['title_sec_lang'];
            $data['company_id']=$input['companySystemID'];
            $data['created_by'] = $employee->employeeSystemID;

            $result = TenderMaster::create($data);

            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function deleteTenderMaster(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['deleted_by'] = $employee->employeeSystemID;
            $data['deleted_at'] = now();
            $result = TenderMaster::where('id',$input['id'])->update($data);
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

    public function getTenderMasterData(Request $request)
    {
        $input = $request->all();
        $data['master'] = TenderMaster::with(['procument_activity'])->where('id',$input['tenderMasterId'])->first();
        $activity = ProcumentActivity::with(['tender_procurement_category'])->where('tender_id',$input['tenderMasterId'])->where('company_id',$input['companySystemID'])->get();
        $act = array();
        if(!empty($activity)){
            foreach ($activity as $vl){
                $dt['id'] = $vl['tender_procurement_category']['id'];
                $dt['itemName'] = $vl['tender_procurement_category']['code'].' | '.$vl['tender_procurement_category']['description'];
                array_push($act,$dt);
            }
        }
        $data['activity'] = $act;
        return $data;


    }

    public function loadTenderSubCategory(Request $request)
    {
        $input = $request->all();
        $data['procurementSubCategory'] = TenderProcurementCategory::where('parent_id',$input['procument_cat_id'])->get();

        return $data;
    }

    public function loadTenderBankAccount(Request $request)
    {
        $input = $request->all();
        $data['bankAccountDrop'] = BankAccount::where('bankmasterAutoID',$input['bank_id'])->where('companySystemID',$input['companySystemID'])->get();

        return $data;
    }

    public function updateTender(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), array('bank_account_id', 'bank_id', 'currency_id', 'currency_id', 'envelop_type_id', 'evaluation_type_id', 'procument_cat_id', 'procument_sub_cat_id', 'tender_type_id'));


        $resValidate = $this->validateTenderHeader($input);

        if (!$resValidate['status']) {
            return $resValidate;
        }

        $document_sales_start_date = new Carbon($input['document_sales_start_date']);
        $document_sales_start_date = $document_sales_start_date->format('Y-m-d');

        $document_sales_end_date = new Carbon($input['document_sales_end_date']);
        $document_sales_end_date = $document_sales_end_date->format('Y-m-d');

        $bid_submission_opening_date = new Carbon($input['bid_submission_opening_date']);
        $bid_submission_opening_date = $bid_submission_opening_date->format('Y-m-d');

        $bid_submission_closing_date = new Carbon($input['bid_submission_closing_date']);
        $bid_submission_closing_date = $bid_submission_closing_date->format('Y-m-d');

        $pre_bid_clarification_start_date = new Carbon($input['pre_bid_clarification_start_date']);
        $pre_bid_clarification_start_date = $pre_bid_clarification_start_date->format('Y-m-d');

        $pre_bid_clarification_end_date = new Carbon($input['pre_bid_clarification_end_date']);
        $pre_bid_clarification_end_date = $pre_bid_clarification_end_date->format('Y-m-d');
        $site_visit_date = null;
        if($input['site_visit_date']){
            $site_visit_date = new Carbon($input['site_visit_date']);
            $site_visit_date = $site_visit_date->format('Y-m-d');
        }


        if($document_sales_start_date>$document_sales_end_date){
            return ['success' => false, 'message' => 'Document sales start date cannot be greater than Document sales end date'];
        }

        if($pre_bid_clarification_start_date>$pre_bid_clarification_end_date){
            return ['success' => false, 'message' => 'Pre-bid clarification end date cannot be greater than Pre-bid clarification start date'];
        }

        if($bid_submission_opening_date>$bid_submission_closing_date){
            return ['success' => false, 'message' => 'Bid submission opening date cannot be greater than Bid submission closing date'];
        }

        $employee = \Helper::getEmployeeInfo();
        $exist = TenderMaster::where('id',$input['id'])->first();
        DB::beginTransaction();
        try {

            $data['title']=$input['title'];
            $data['title_sec_lang']=$input['title_sec_lang'];
            $data['tender_type_id']=$input['tender_type_id'];
            $data['currency_id']=$input['currency_id'];
            $data['envelop_type_id']=$input['envelop_type_id'];
            $data['procument_cat_id']=$input['procument_cat_id'];
            $data['procument_sub_cat_id']=$input['procument_sub_cat_id'];
            $data['evaluation_type_id']=$input['evaluation_type_id'];
            $data['estimated_value']=$input['estimated_value'];
            $data['allocated_budget']=$input['allocated_budget'];
            $data['tender_document_fee']=$input['tender_document_fee'];
            $data['bank_id']=$input['bank_id'];
            $data['bank_account_id']=$input['bank_account_id'];
            $data['document_sales_start_date']=$document_sales_start_date;
            $data['document_sales_end_date']=$document_sales_end_date;
            $data['pre_bid_clarification_start_date']=$pre_bid_clarification_start_date;
            $data['pre_bid_clarification_end_date']=$pre_bid_clarification_end_date;
            $data['pre_bid_clarification_method']=$input['pre_bid_clarification_method'];
            $data['site_visit_date']=$site_visit_date;
            $data['bid_submission_opening_date']=$bid_submission_opening_date;
            $data['bid_submission_closing_date']=$bid_submission_closing_date;



            $data['updated_by'] = $employee->employeeSystemID;

            $result = TenderMaster::where('id',$input['id'])->update($data);

            if($result){
                if(isset($input['procument_activity'])){
                    if(count($input['procument_activity'])>0){
                        ProcumentActivity::where('tender_id',$input['id'])->where('company_id',$input['company_id'])->delete();
                        foreach ($input['procument_activity'] as $vl){
                            $activity['tender_id']=$input['id'];
                            $activity['category_id'] = $vl['id'];
                            $activity['company_id'] = $input['company_id'];
                            $activity['created_by'] = $employee->employeeSystemID;

                            ProcumentActivity::create($activity);
                        }
                    }
                }

                if($exist['site_visit_date'] != $site_visit_date){
                    $site['tender_id'] = $input['id'];
                    $site['date'] = $site_visit_date;
                    $site['company_id'] = $input['company_id'];
                    $site['created_by'] = $employee->employeeSystemID;

                    TenderSiteVisitDates::create($site);
                }

                if(isset($input['Attachment']) && !empty($input['Attachment'])){
                    $attachment = $input['Attachment'];

                    if(!empty($attachment) && isset($attachment['file'])){
                        $extension = $attachment['fileType'];
                        $allowExtensions = ['pdf','txt','xlsx','docx'];

                        if (!in_array($extension, $allowExtensions))
                        {
                            return $this->sendError('This type of file not allow to upload.',500);
                        }

                        if(isset($attachment['size'])){
                            if ($attachment['size'] > 2097152) {
                                return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.",500);
                            }
                        }

                        $file = $attachment['file'];
                        $decodeFile = base64_decode($file);

                        $attch = time().'_TenderBudgetDocument.' . $extension;

                        $path = $input['company_id'].'/TenderBudgetDocument/' . $attch;

                        Storage::disk(Helper::policyWiseDisk($input['company_id'], 'public'))->put($path, $decodeFile);

                        $att['budget_document'] = $path;
                        TenderMaster::where('id',$input['id'])->update($att);
                    }
                }


                DB::commit();
                return ['success' => true, 'message' => 'Successfully updated'];
            }



        }catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function validateTenderHeader($input)
    {
        $messages = [
            'title.required' => 'Title is required.',
            'title_sec_lang.required' => 'Title In Arabic is required.',
            'tender_type_id.required' => 'Type is required.',
            'envelop_type_id.required' => 'Envelop Type is required.',
            'procument_cat_id.required' => 'Procurement Category is required.',
            'procument_sub_cat_id.required' => 'Procurement Sub Category is required.',
            'estimated_value.required' => 'Estimated Value is required.',
            'allocated_budget.required' => 'Allocated Budget is required.',
            'tender_document_fee.required' => 'Tender Document Fee is required.',
            'bank_id.required' => 'Bank is required.',
            'bank_account_id.required' => 'Bank Account is required.',
            'document_sales_start_date.required' => 'Document Sales Start Date is required.',
            'document_sales_end_date.required' => 'Document Sales End Date is required.',
            'pre_bid_clarification_start_date.required' => 'Pre-bid Clarification Start Date.',
            'pre_bid_clarification_end_date.required' => 'Pre-bid Clarification End Date.',
            'pre_bid_clarification_method.required' => 'Pre-bid Clarifications Method.',
            'bid_submission_opening_date.required' => 'Bid Submission Opening Date.',
            'bid_submission_closing_date.required' => 'Bid Submission Closing Date.',

        ];

        $validator = \Validator::make($input, [
            'title' => 'required',
            'title_sec_lang' => 'required',
            'tender_type_id' => 'required',
            'envelop_type_id' => 'required',
            'procument_cat_id' => 'required',
            'procument_sub_cat_id' => 'required',
            'estimated_value' => 'required',
            'allocated_budget' => 'required',
            'tender_document_fee' => 'required',
            'bank_id' => 'required',
            'bank_account_id' => 'required',
            'document_sales_start_date' => 'required',
            'document_sales_end_date' => 'required',
            'pre_bid_clarification_start_date' => 'required',
            'pre_bid_clarification_end_date' => 'required',
            'pre_bid_clarification_method' => 'required',
            'bid_submission_opening_date' => 'required',
            'bid_submission_closing_date' => 'required',

        ], $messages);

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->messages()];
        }

        return ['status' => true, 'message' => "success"];
    }
}
