<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchaseRequestReferredRequest;
use App\Http\Requests\UpdatePurchaseRequestReferredRequest;
use App\Repositories\PurchaseRequestReferredRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PurchaseRequestReferredController extends AppBaseController
{
    /** @var  PurchaseRequestReferredRepository */
    private $purchaseRequestReferredRepository;

    public function __construct(PurchaseRequestReferredRepository $purchaseRequestReferredRepo)
    {
        $this->purchaseRequestReferredRepository = $purchaseRequestReferredRepo;
    }

    /**
     * Display a listing of the PurchaseRequestReferred.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->purchaseRequestReferredRepository->pushCriteria(new RequestCriteria($request));
        $purchaseRequestReferreds = $this->purchaseRequestReferredRepository->all();

        return view('purchase_request_referreds.index')
            ->with('purchaseRequestReferreds', $purchaseRequestReferreds);
    }

    /**
     * Show the form for creating a new PurchaseRequestReferred.
     *
     * @return Response
     */
    public function create()
    {
        return view('purchase_request_referreds.create');
    }

    /**
     * Store a newly created PurchaseRequestReferred in storage.
     *
     * @param CreatePurchaseRequestReferredRequest $request
     *
     * @return Response
     */
    public function store(CreatePurchaseRequestReferredRequest $request)
    {
        $input = $request->all();

        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->create($input);

        Flash::success('Purchase Request Referred saved successfully.');

        return redirect(route('purchaseRequestReferreds.index'));
    }

    /**
     * Display the specified PurchaseRequestReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            Flash::error('Purchase Request Referred not found');

            return redirect(route('purchaseRequestReferreds.index'));
        }

        return view('purchase_request_referreds.show')->with('purchaseRequestReferred', $purchaseRequestReferred);
    }

    /**
     * Show the form for editing the specified PurchaseRequestReferred.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            Flash::error('Purchase Request Referred not found');

            return redirect(route('purchaseRequestReferreds.index'));
        }

        return view('purchase_request_referreds.edit')->with('purchaseRequestReferred', $purchaseRequestReferred);
    }

    /**
     * Update the specified PurchaseRequestReferred in storage.
     *
     * @param  int              $id
     * @param UpdatePurchaseRequestReferredRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePurchaseRequestReferredRequest $request)
    {
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            Flash::error('Purchase Request Referred not found');

            return redirect(route('purchaseRequestReferreds.index'));
        }

        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->update($request->all(), $id);

        Flash::success('Purchase Request Referred updated successfully.');

        return redirect(route('purchaseRequestReferreds.index'));
    }

    /**
     * Remove the specified PurchaseRequestReferred from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            Flash::error('Purchase Request Referred not found');

            return redirect(route('purchaseRequestReferreds.index'));
        }

        $this->purchaseRequestReferredRepository->delete($id);

        Flash::success('Purchase Request Referred deleted successfully.');

        return redirect(route('purchaseRequestReferreds.index'));
    }
}
