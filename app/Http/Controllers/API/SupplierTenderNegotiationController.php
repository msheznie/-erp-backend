<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateSupplierTenderNegotiationRequest;
use App\Http\Requests\UpdateSupplierTenderNegotiationRequest;
use App\Repositories\SupplierTenderNegotiationRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
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


        if(!empty($input['supplierList']) && $input['isChecked']) {
                $supplierId = $input['supplierList'];

                $data = [
                    'tender_negotiation_id' => $input['tenderNegotiationID'],
                    'suppliermaster_id' =>  $supplierId,
                    'srm_bid_submission_master_id' => $input['srm_bid_submission_master_id'],
                    'bidSubmissionCode' => $input['bidSubmissionCode']
                ];
    
                $checkSupplierExist = $this->checkSupplierExist($data);
                
                if($checkSupplierExist) { 
                    return $this->sendResponse($checkSupplierExist, 'Data Already Exists');
                }
    
                $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->create($data);

                if($supplierTenderNegotiation) {
                    return $this->sendResponse($supplierTenderNegotiation->toArray(), 'Supplier added successfully');
                }else {
                    return $this->sendError("Cannot add Supplier to Negotiation", 500);
                }


        }else {
            if(!$input['isChecked']) {
                if(isset($input['tenderNegotiationID']) && isset($input['supplierList'])) {
                    $this->supplierTenderNegotiationRepository->deleteSuppliersOfNegotiation($input);
                }
            }
        return $this->sendResponse(null, 'Supplier removed successfully');

        }
       


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
            Flash::error('Supplier Tender Negotiation not found');

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
        $supplierTenderNegotiations = $this->supplierTenderNegotiationRepository->all();
        return $this->sendResponse($supplierTenderNegotiations->where('tender_negotiation_id',$request['tenderNegotiationID'])->pluck('suppliermaster_id')->toArray(), 'Data retrieved successfully');

    }
}
