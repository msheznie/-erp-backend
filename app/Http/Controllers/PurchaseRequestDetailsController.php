<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseRequestDetailsRequest;
use App\Http\Requests\UpdatePurchaseRequestDetailsRequest;
use App\Repositories\PurchaseRequestDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseRequestDetailsController extends AppBaseController
{
    /** @var  PurchaseRequestDetailsRepository */
    private $purchaseRequestDetailsRepository;

    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo)
    {
        $this->purchaseRequestDetailsRepository = $purchaseRequestDetailsRepo;
    }

    /**
     * Display a listing of the PurchaseRequestDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestDetailsRepository->pushCriteria(new RequestCriteria($request));
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->all();

        return view('purchase_request_details.index')
            ->with('purchaseRequestDetails', $purchaseRequestDetails);
    }

    /**
     * Show the form for creating a new PurchaseRequestDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_request_details.create');
    }

    /**
     * Store a newly created PurchaseRequestDetails in storage.
     *
     * @param CreatePurchaseRequestDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestDetailsRequest $request)
    {
        $input = $request->all();

        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->create($input);

        Flash::success('Purchase Request Details saved successfully.');

        return redirect(route('purchaseRequestDetails.index'));
    }

    /**
     * Display the specified PurchaseRequestDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            Flash::error('Purchase Request Details not found');

            return redirect(route('purchaseRequestDetails.index'));
        }

        return view('purchase_request_details.show')->with('purchaseRequestDetails', $purchaseRequestDetails);
    }

    /**
     * Show the form for editing the specified PurchaseRequestDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            Flash::error('Purchase Request Details not found');

            return redirect(route('purchaseRequestDetails.index'));
        }

        return view('purchase_request_details.edit')->with('purchaseRequestDetails', $purchaseRequestDetails);
    }

    /**
     * Update the specified PurchaseRequestDetails in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseRequestDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestDetailsRequest $request)
    {
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            Flash::error('Purchase Request Details not found');

            return redirect(route('purchaseRequestDetails.index'));
        }

        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->update($request->all(), $id);

        Flash::success('Purchase Request Details updated successfully.');

        return redirect(route('purchaseRequestDetails.index'));
    }

    /**
     * Remove the specified PurchaseRequestDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseRequestDetails = $this->purchaseRequestDetailsRepository->findWithoutFail($id);

        if (empty($purchaseRequestDetails)) {
            Flash::error('Purchase Request Details not found');

            return redirect(route('purchaseRequestDetails.index'));
        }

        $this->purchaseRequestDetailsRepository->delete($id);

        Flash::success('Purchase Request Details deleted successfully.');

        return redirect(route('purchaseRequestDetails.index'));
    }
}
