<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseOrderProcessDetailsRequest;
use App\Http\Requests\UpdatePurchaseOrderProcessDetailsRequest;
use App\Repositories\PurchaseOrderProcessDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseOrderProcessDetailsController extends AppBaseController
{
    /** @var  PurchaseOrderProcessDetailsRepository */
    private $purchaseOrderProcessDetailsRepository;

    public function __construct(PurchaseOrderProcessDetailsRepository $purchaseOrderProcessDetailsRepo)
    {
        $this->purchaseOrderProcessDetailsRepository = $purchaseOrderProcessDetailsRepo;
    }

    /**
     * Display a listing of the PurchaseOrderProcessDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseOrderProcessDetailsRepository->pushCriteria(new RequestCriteria($request));
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->all();

        return view('purchase_order_process_details.index')
            ->with('purchaseOrderProcessDetails', $purchaseOrderProcessDetails);
    }

    /**
     * Show the form for creating a new PurchaseOrderProcessDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_order_process_details.create');
    }

    /**
     * Store a newly created PurchaseOrderProcessDetails in storage.
     *
     * @param CreatePurchaseOrderProcessDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseOrderProcessDetailsRequest $request)
    {
        $input = $request->all();

        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->create($input);

        Flash::success('Purchase Order Process Details saved successfully.');

        return redirect(route('purchaseOrderProcessDetails.index'));
    }

    /**
     * Display the specified PurchaseOrderProcessDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            Flash::error('Purchase Order Process Details not found');

            return redirect(route('purchaseOrderProcessDetails.index'));
        }

        return view('purchase_order_process_details.show')->with('purchaseOrderProcessDetails', $purchaseOrderProcessDetails);
    }

    /**
     * Show the form for editing the specified PurchaseOrderProcessDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            Flash::error('Purchase Order Process Details not found');

            return redirect(route('purchaseOrderProcessDetails.index'));
        }

        return view('purchase_order_process_details.edit')->with('purchaseOrderProcessDetails', $purchaseOrderProcessDetails);
    }

    /**
     * Update the specified PurchaseOrderProcessDetails in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseOrderProcessDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseOrderProcessDetailsRequest $request)
    {
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            Flash::error('Purchase Order Process Details not found');

            return redirect(route('purchaseOrderProcessDetails.index'));
        }

        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->update($request->all(), $id);

        Flash::success('Purchase Order Process Details updated successfully.');

        return redirect(route('purchaseOrderProcessDetails.index'));
    }

    /**
     * Remove the specified PurchaseOrderProcessDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepository->findWithoutFail($id);

        if (empty($purchaseOrderProcessDetails)) {
            Flash::error('Purchase Order Process Details not found');

            return redirect(route('purchaseOrderProcessDetails.index'));
        }

        $this->purchaseOrderProcessDetailsRepository->delete($id);

        Flash::success('Purchase Order Process Details deleted successfully.');

        return redirect(route('purchaseOrderProcessDetails.index'));
    }
}
