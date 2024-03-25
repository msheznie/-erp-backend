<?php

namespace App\Repositories;

use App\Models\CurrencyMaster;
use App\Models\EnvelopType;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\TenderBoqItems;
use App\Models\TenderMaster;
use App\Models\TenderType;
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

        $selection = TenderType::select('id','name')
        ->get();

        $envelope = EnvelopType::select('id','name')
        ->get();

        $published =  array(
            array('value'=> 1 , 'label'=> 'Not Published'),
            array('value'=> 2 , 'label'=> 'Published'), 
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
}
