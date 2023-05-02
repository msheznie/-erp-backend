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
        $supplierList = $input['supplierList'];

        $this->supplierTenderNegotiationRepository->deleteSuppliersOfNegotiation($input['tender_negotiation_id']);

        foreach($supplierList as $supplier) {
            $data = [
                'tender_negotiation_id' => $input['tender_negotiation_id'],
                'suppliermaster_id' =>  $supplier
            ];

            $checkSupplierExist = $this->checkSupplierExist($data);


            if(!$checkSupplierExist) {
                $supplierTenderNegotiation = $this->supplierTenderNegotiationRepository->create($data);
                return $this->sendResponse($supplierTenderNegotiation->toArray(), 'Supplier added successfully');
            }
        }

        return $this->sendResponse(null, 'Supplier added successfully');

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
        if(count($checkSupplierAlreadyInserted) > 0) 
            return true;

        return false;
    }

    public function getTenderNegotiatedSupplierIds(Request $request) {
        $supplierTenderNegotiations = $this->supplierTenderNegotiationRepository->all();
       
        return $this->sendResponse($supplierTenderNegotiations->where('tender_negotiation_id',$request['tender_negotiation_id'])->pluck('suppliermaster_id')->toArray(), 'Data retrieved successfully');

    }
}
