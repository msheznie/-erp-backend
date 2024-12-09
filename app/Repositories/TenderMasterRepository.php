<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DocumentApproved;
use App\Models\EnvelopType;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\SRMTenderPaymentProof;
use App\Models\TenderBoqItems;
use App\Models\TenderMaster;
use App\Models\TenderMasterSupplier;
use App\Models\TenderType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/**
 * Class TenderMasterRepository
 * @package App\Repositories
 * @version March 10, 2022, 1:54 pm +04
 *
 * @method TenderMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderMaster find($id, $columns = ['*'])
 * @method TenderMaster first($columns = ['*'])
 */
class TenderMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'title_sec_lang',
        'description',
        'description_sec_lang',
        'tender_type_id',
        'currency_id',
        'envelop_type_id',
        'procument_cat_id',
        'procument_sub_cat_id',
        'evaluation_type_id',
        'estimated_value',
        'allocated_budget',
        'budget_document',
        'tender_document_fee',
        'bank_id',
        'bank_account_id',
        'document_sales_start_date',
        'document_sales_end_date',
        'pre_bid_clarification_start_date',
        'pre_bid_clarification_end_date',
        'pre_bid_clarification_method',
        'site_visit_date',
        'bid_submission_opening_date',
        'bid_submission_closing_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'bid_opening_date',
        'bid_opening_end_date',
        'technical_bid_opening_date',
        'technical_bid_closing_date',
        'commerical_bid_opening_date',
        'commerical_bid_closing_date',
        'is_negotiation_started',
        'negotiation_published'

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderMaster::class;
    }

    public function getTenderFilterData(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $currency = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
        ->get();

        $selection = TenderType::select('id', 'name')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            });

        $envelope = EnvelopType::select('id','name')
        ->get()
        ->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
            ];
        });

        $published =  array(
            array('value'=> 0 , 'label'=> 'Not Published'),
            array('value'=> 1 , 'label'=> 'Published'),
        );

        $tenderNegotiationStatus =  array(
            array('value'=> 1 , 'label'=> 'Negotiation Not Started'),
            array('value'=> 2 , 'label'=> 'Negotiation Started'), 
            array('value'=> 3 , 'label'=> 'Negotiation Completed'), 
        );

        $status =array(
            array('value'=> 1 , 'label'=> 'Not Confirmed'),
            array('value'=> 2 , 'label'=> 'Pending Approval'), 
            array('value'=> 3 , 'label'=> 'Fully Approved'),  
            array('value'=> 4 , 'label'=> 'Referred Back'), 
            array('value'=> 5 , 'label'=> 'Rejected'),
        );

        $rfxTypes = array(
            array('value'=> 1 , 'label'=> 'RFQ'),
            array('value'=> 2 , 'label'=> 'RFI'), 
            array('value'=> 3 , 'label'=> 'RFP'), 
        );

        $gonogo = array(
            array('value'=> 1 , 'label'=> 'Not Completed'),
            array('value'=> 2 , 'label'=> 'Completed'),  
        );  

        $technical = array(
            array('value'=> 0 , 'label'=> 'Not Completed'),
            array('value'=> 1 , 'label'=> 'Completed'),
        );  

        $stage = array(
            array('value'=> 1 , 'label'=> 'Single Stage'),
            array('value'=> 2 , 'label'=> 'Two Stage'),  
        );  

        $commercial = array(
            array('value'=> 0 , 'label'=> 'Not Completed'),
            array('value'=> 1 , 'label'=> 'Completed'),
        );

        $data = array(
            'currency' => $currency,
            'selection' => $selection,
            'envelope' => $envelope,
            'published' => $published,
            'status' => $status,
            'rfxTypes' => $rfxTypes,
            'technical' => $technical,
            'gonogo' => $gonogo,
            'stage' => $stage,
            'commercial' => $commercial,
            'tenderNegotiationStatus' => $tenderNegotiationStatus
        );
 
        return $data;
    }

    public function getTenderPr(Request $request){ 
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $companyId = $input['companyId'];

        $data = PurchaseRequest::select('purchaseRequestID','companyID','purchaseRequestCode')
        ->with(['tender_purchase_request' => function ($query) use ($tenderId) {
            $query->where('tender_id', $tenderId);
        }])
        ->where('companySystemID',$companyId)
        ->whereHas('tender_purchase_request', function ($query) use ($tenderId) {
                $query->where('tender_id', $tenderId);
        })
        ->get();

        return $data;

    }

    public function getPurchaseRequestDetails(Request $request)
    {
        $purchaseRequestID = $request->input('purchaseRequestID');
        $tender_id = $request->input('tenderId');
        $main_work_id = $request->input('main_work_id');

        $purchaseRequestIDToCheck = $purchaseRequestID;

        $result = TenderBoqItems::where('tender_id', $tender_id)
            ->whereRaw("FIND_IN_SET('$purchaseRequestIDToCheck', purchase_request_id) > 0")
            ->where('main_work_id', $main_work_id)
            ->first();

        if ($result) {
            return [
                'success' => false,
                'message' => 'Line items are already added',
                'data' => ''
            ];
        }

        $pr = PurchaseRequestDetails::where('purchaseRequestID', $purchaseRequestID);

        $pr = $pr->with(['uom' , 'purchase_request'])->get()
            ->transform(function ($data) {
                return $this->prDetailFormat($data);
            });

        $result['prDetail'] = $pr;
        return [
            'success' => true,
            'message' => 'PR Details Retrieved',
            'data' => $result
        ];
    }

    public function prDetailFormat($data)
    {
        return [
            'purchaseRequestID' => $data['purchaseRequestID'],
            'purchaseRequestCode' => $data['purchase_request']['purchaseRequestCode'],
            'purchaseRequestDetailsID' => $data['purchaseRequestDetailsID'],
            'itemPrimaryCode' => $data['itemPrimaryCode'],
            'itemDescription' => $data['itemDescription'],
            'noQty' => $data['quantityRequested'],
            'unitID' => $data['uom']['UnitID'],
            'unitShortCode' => $data['uom']['UnitShortCode'],
            'item_id' => $data['itemCode']
        ];
    }

    public static function getTenderDidOpeningDates($tenderId, $companyId)
    {
        $current_date = Carbon::now();
        $tender = TenderMaster::getTenderDidOpeningDates($tenderId, $companyId);

        if (!$tender) {
            return [
                'error' => 'Tender not found.',
            ];
        }

        $opening_date_comp = $tender->stage === 1 ? $tender->bid_opening_date : $tender->technical_bid_opening_date;
        $opening_date_comp_end = $tender->stage === 1 ? $tender->bid_opening_end_date : $tender->technical_bid_closing_date;
        return $current_date->gt($opening_date_comp) && ($opening_date_comp_end === null || $opening_date_comp_end->gt($current_date));
    }


    public static function getTenderPOData($tenderId, $companyId)
    {
        return TenderMaster::getTenderPOData($tenderId, $companyId);
    }
    public function getPaymentProofDocumentApproval($request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empId = \Helper::getEmployeeSystemID();
        $tenderPaymentProof =  SRMTenderPaymentProof::getTenderPaymentReview($companyId,$empId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $tenderPaymentProof = $tenderPaymentProof->where(function($query) use ($search) {
                $query->where('tm.title', 'LIKE', "%{$search}%")
                    ->orWhere('tm.tender_code', 'LIKE', "%{$search}%");
            });
        }
        return \DataTables::of($tenderPaymentProof)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getSupplierWiseProof($request)
    {
        $input = $request->all();
        $empId = \Helper::getEmployeeSystemID();
        $companyId = $input['companyId'];
        $tenderUuid = $input['uuid'];
        $tenderData = TenderMaster::getTenderByUuid($tenderUuid);


        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $supplierPaymentProof = SRMTenderPaymentProof::getSupplierWiseData($companyId,$empId,$tenderData['id']);
        return \DataTables::of($supplierPaymentProof)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function approveSupplierWiseTender($request)
    {
        $input = $request->all();
        $data = $this->prepareDocumentData($input);

        unset($data['approvedComments']);
        $data['approvedComments'] = ($input['approvedComments']) ?? null;

        $approve = \Helper::approveDocument($data);

        if ($approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']) {
            $this->purchaseTender($request);
        }

        return ['success' => $approve["success"], 'message' => $approve["message"], 'data'=> $approve];
    }

    public function purchaseTender($request)
    {
        $input = $request->all();
        $getPaymentProofDocument = SRMTenderPaymentProof::getPaymentProofDataByUuid($input['uuid']);
        $result = DB::transaction(function () use ($getPaymentProofDocument) {
                $data = [
                    'tender_master_id' => $getPaymentProofDocument['tender_id'],
                    'purchased_date' =>  Carbon::parse(now())->format('Y-m-d H:i:s'),
                    'purchased_by' => $getPaymentProofDocument['srm_supplier_id'],
                    'created_by' => $getPaymentProofDocument['srm_supplier_id']
                ];
            TenderMasterSupplier::create($data);
        });
    }

    public function rejectSupplierWiseTender($request)
    {
        $input = $request->all();
        $data = DocumentApproved::getDocumentApprovedData($input['documentApCode']);
        $data = $this->prepareDocumentData($input);
        unset($data['rejectedComments']);
        $data['rejectedComments'] = ($input['rejectedComments']) ?? null;

        $approve = \Helper::rejectDocument($data);
        return ['success' => $approve["success"], 'message' => $approve["message"], 'data'=> $approve];
    }

    protected function prepareDocumentData($input)
    {
        $documentData = DocumentApproved::getDocumentApprovedData($input['documentApCode']);
        $paymentProofData = SRMTenderPaymentProof::getPaymentProofDataByUuid($input['uuid']);
        $tenderData = TenderMaster::getTenderDidOpeningDates(
            $paymentProofData['tender_id'] ?? null,
            $paymentProofData['company_id'] ?? null
        );

        $documentData->tenderCode = $tenderData->tender_code ?? null;
        $documentData->tenderTitle = $tenderData->title ?? null;
        $documentData->supplierName = $input['supplierName'] ?? null;

        return $documentData;
    }
}
