<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateSupplierTenderNegotiationRequest;
use App\Http\Requests\UpdateSupplierTenderNegotiationRequest;
use App\Repositories\SupplierTenderNegotiationRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\SupplierTenderNegotiation;
use Illuminate\Http\Request;
use App\Models\TenderFinalBids;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SupplierTenderNegotiationController extends AppBaseController
{
    /** @var  SupplierTenderNegotiationRepository */
    private $supplierTenderNegotiationRepository;

    public function __construct(SupplierTenderNegotiationRepository $supplierTenderNegotiationRepo)
    {
        $this->supplierTenderNegotiationRepository = $supplierTenderNegotiationRepo;
    }

    /**
     * Display a listing of the SupplierTenderNegotiation.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierTenderNegotiationRepository->pushCriteria(new RequestCriteria($request));
        $supplierTenderNegotiations = $this->supplierTenderNegotiationRepository->all();

        return view('supplier_tender_negotiations.index')
            ->with('supplierTenderNegotiations', $supplierTenderNegotiations);
    }



    /**
     * Store a newly created SupplierTenderNegotiation in storage.
     *
     * @param CreateSupplierTenderNegotiationRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierTenderNegotiationRequest $request)
    {
        $input = $request->all();

        $data = $input['data'];
        $tenderNegotiationId = $input['tenderNegotiationId'];
        $input['tenderNegotiationID'] = $tenderNegotiationId;
        $deleteSuppliers =  $this->supplierTenderNegotiationRepository->deleteSuppliersOfNegotiation($input);
        $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->insert($data);

        if(!$supplierTenderNegotiation) {
            return $this->sendError("Cannot add Supplier to Negotiation", 500);
        }
        
        return $this->sendResponse($supplierTenderNegotiation, trans('custom.supplier_added_successfully'));


    }

    /**
     * Display the specified SupplierTenderNegotiation.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->findWithoutFail($id);

        if (empty($supplierTenderNegotiation)) {
            Flash::error('Supplier tender negotiation not found');

            return redirect(route('supplierTenderNegotiations.index'));
        }

        return view('supplier_tender_negotiations.show')->with('supplierTenderNegotiation', $supplierTenderNegotiation);
    }


    /**
     * Update the specified SupplierTenderNegotiation in storage.
     *
     * @param  int              $id
     * @param UpdateSupplierTenderNegotiationRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierTenderNegotiationRequest $request)
    {
        $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->findWithoutFail($id);

        if (empty($supplierTenderNegotiation)) {
            Flash::error('Supplier Tender Negotiation not found');

            return redirect(route('supplierTenderNegotiations.index'));
        }

        $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->update($request->all(), $id);

        Flash::success('Supplier Tender Negotiation updated successfully.');

        return redirect(route('supplierTenderNegotiations.index'));
    }

    /**
     * Remove the specified SupplierTenderNegotiation from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->findWithoutFail($id);

        if (empty($supplierTenderNegotiation)) {
            Flash::error('Supplier Tender Negotiation not found');

            return redirect(route('supplierTenderNegotiations.index'));
        }

        $this->supplierTenderNegotiationRepository->delete($id);

        Flash::success('Supplier Tender Negotiation deleted successfully.');

        return redirect(route('supplierTenderNegotiations.index'));
    }

    public function checkSupplierExist($data) {
        $checkSupplierAlreadyInserted = $this->supplierTenderNegotiationRepository->checkSupplierAlreadyInserted($data);
        return (count($checkSupplierAlreadyInserted) > 0) ?  true :  false;
    }

    public function getTenderNegotiatedSupplierIds(Request $request) {
        $data = $this->supplierTenderNegotiationRepository->select('suppliermaster_id','tender_negotiation_id','srm_bid_submission_master_id','bidSubmissionCode')->where('tender_negotiation_id',$request['tenderNegotiationID'])->get();
        return $this->sendResponse($data ,trans('custom.data_retrieved_successfully'));
    }

    public function addAllSuppliersToNegotiation(Request $request) {
        $input = $request->all();

        $tenderId = $input['tenderId'];
        $tenderFinalBids = TenderFinalBids::select('id','bid_id','supplier_id','tender_id')->with(['bid_submission_master' => function ($q) {
            $q->select('bidSubmissionCode','id','supplier_registration_id')->with(['SupplierRegistrationLink' => function ($s) {
                $s->select('name','id');
            }]);
        }])->where('tender_id',$tenderId)->where('status',1)->orderBy('total_weightage','desc')->get();

        $this->supplierTenderNegotiationRepository->deleteSuppliersOfNegotiation($input);

        if($tenderFinalBids) {
            foreach($tenderFinalBids as $tenderFinalBid) {

                $data = [
                    'tender_negotiation_id' => $input['tenderNegotiationID'],
                    'suppliermaster_id' => $tenderFinalBid->supplier_id,
                    'srm_bid_submission_master_id' => $tenderFinalBid->bid_id,
                    'bidSubmissionCode' => $tenderFinalBid->bid_submission_master->bidSubmissionCode,
                    'tenderNegotiationID' => $input['tenderNegotiationID'],
                    'supplierList'=> $tenderFinalBid->supplier_id,
                ];
    
                $this->supplierTenderNegotiationRepository->create($data);
    
            }

            return $this->sendResponse($data ,trans('custom.all_suppliers_added_successfully'));

        }else {
            return $this->sendError("Sorry, Cannot add suppliers", 500);
        }



    }


    public function deleteAllSuppliersFromNegotiation(Request $request) {
        $input = $request->all();
        $deleteAllRecords = SupplierTenderNegotiation::where('tender_negotiation_id',$input['tenderNegotiationID'])->delete();
        if($deleteAllRecords) {
            return $this->sendResponse($deleteAllRecords ,trans('custom.all_suppliers_deleted_successfully'));
        }else {
            return $this->sendError("Sorry, Can't delete suppliers", 500);
        }

    }
}
