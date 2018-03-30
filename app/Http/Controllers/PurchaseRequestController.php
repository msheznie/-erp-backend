<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseRequestRequest;
use App\Http\Requests\UpdatePurchaseRequestRequest;
use App\Repositories\PurchaseRequestRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseRequestController extends AppBaseController
{
    /** @var  PurchaseRequestRepository */
    private $purchaseRequestRepository;

    public function __construct(PurchaseRequestRepository $purchaseRequestRepo)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepo;
    }

    /**
     * Display a listing of the PurchaseRequest.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestRepository->pushCriteria(new RequestCriteria($request));
        $purchaseRequests = $this->purchaseRequestRepository->all();

        return view('purchase_requests.index')
            ->with('purchaseRequests', $purchaseRequests);
    }

    /**
     * Show the form for creating a new PurchaseRequest.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_requests.create');
    }

    /**
     * Store a newly created PurchaseRequest in storage.
     *
     * @param CreatePurchaseRequestRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestRequest $request)
    {
        $input = $request->all();

        $purchaseRequest = $this->purchaseRequestRepository->create($input);

        Flash::success('Purchase Request saved successfully.');

        return redirect(route('purchaseRequests.index'));
    }

    /**
     * Display the specified PurchaseRequest.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            Flash::error('Purchase Request not found');

            return redirect(route('purchaseRequests.index'));
        }

        return view('purchase_requests.show')->with('purchaseRequest', $purchaseRequest);
    }

    /**
     * Show the form for editing the specified PurchaseRequest.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            Flash::error('Purchase Request not found');

            return redirect(route('purchaseRequests.index'));
        }

        return view('purchase_requests.edit')->with('purchaseRequest', $purchaseRequest);
    }

    /**
     * Update the specified PurchaseRequest in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseRequestRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestRequest $request)
    {
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            Flash::error('Purchase Request not found');

            return redirect(route('purchaseRequests.index'));
        }

        $purchaseRequest = $this->purchaseRequestRepository->update($request->all(), $id);

        Flash::success('Purchase Request updated successfully.');

        return redirect(route('purchaseRequests.index'));
    }

    /**
     * Remove the specified PurchaseRequest from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseRequest = $this->purchaseRequestRepository->findWithoutFail($id);

        if (empty($purchaseRequest)) {
            Flash::error('Purchase Request not found');

            return redirect(route('purchaseRequests.index'));
        }

        $this->purchaseRequestRepository->delete($id);

        Flash::success('Purchase Request deleted successfully.');

        return redirect(route('purchaseRequests.index'));
    }
}
