<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TenderNegotiationRepository;
use App\Http\Requests\StorePostTenderNegotiation;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderMaster;
use App\Models\TenderFinalBids;
use App\Models\TenderNegotiation;
use App\Models\SupplierTenderNegotiation;
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
        $input['started_by'] = Auth::user()->employee_id;
        $input['status'] = 1;
        $updateTenderMasterRecord = $this->updateTenderMasterRecord($input);

        if($updateTenderMasterRecord) {
            $tenderNeotiation = $this->tenderNegotiationRepository->create($input);
            return $this->sendResponse($tenderNeotiation->toArray(), 'Tender Negotiation started successfully');
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
  
        $input = $request->input();
        $input['confirmed_by'] =  Auth::user()->employee_id;
        $input['confirmed_at'] =  Carbon::now();
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

        $tender = TenderMaster::where('id',$input['srm_tender_master_id'])->first();

        if($tender) {
            $tender->is_negotiation_started = 1;
            $tender->save();
            return true;
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
        $query = TenderFinalBids::selectRaw('srm_tender_final_bids.id,srm_tender_final_bids.status,srm_tender_final_bids.supplier_id,srm_tender_final_bids.com_weightage,srm_tender_final_bids.tech_weightage,srm_tender_final_bids.total_weightage,srm_tender_final_bids.bid_id,srm_bid_submission_master.bidSubmittedDatetime,srm_supplier_registration_link.name,srm_bid_submission_master.bidSubmissionCode,srm_bid_submission_master.line_item_total,srm_tender_final_bids.award')
        ->join('srm_bid_submission_master', 'srm_bid_submission_master.id', '=', 'srm_tender_final_bids.bid_id')
        ->join('srm_supplier_registration_link', 'srm_supplier_registration_link.id', '=', 'srm_bid_submission_master.supplier_registration_id')
        ->where('srm_tender_final_bids.status',1)
        ->where('srm_tender_final_bids.tender_id', $tenderId)
        ->orderBy('srm_tender_final_bids.total_weightage','desc');

      

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
        $currencyIds = [];

        $tenderNegotiations = TenderNegotiation::with(['tenderMaster' => function ($q){ 
            $q->with(['currency','tender_type','envelop_type']);
        }])->get();

        foreach($tenderNegotiations as $tendderNegotiation) {
            array_push($currencyIds,$tendderNegotiation->tenderMaster->currency_id);
        }
        

        $yesNoSelection = YesNoSelection::all();

        $currencies = CurrencyMaster::whereIn('currencyID',$currencyIds)->select(['currencyID as value','CurrencyName as label'])->get();


        $data = [
            'yesNoSelection' => $yesNoSelection,
            'currencies' => $currencies
        ];

        return $this->sendResponse($data, 'Tender Negotiation started successfully');
 
    }


}
