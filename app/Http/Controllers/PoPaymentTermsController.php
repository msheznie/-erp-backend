<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoPaymentTermsRequest;
use App\Http\Requests\UpdatePoPaymentTermsRequest;
use App\Repositories\PoPaymentTermsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoPaymentTermsController extends AppBaseController
{
    /** @var  PoPaymentTermsRepository */
    private $poPaymentTermsRepository;

    public function __construct(PoPaymentTermsRepository $poPaymentTermsRepo)
    {
        $this->poPaymentTermsRepository = $poPaymentTermsRepo;
    }

    /**
     * Display a listing of the PoPaymentTerms.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poPaymentTermsRepository->pushCriteria(new RequestCriteria($request));
        $poPaymentTerms = $this->poPaymentTermsRepository->all();

        return view('po_payment_terms.index')
            ->with('poPaymentTerms', $poPaymentTerms);
    }

    /**
     * Show the form for creating a new PoPaymentTerms.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_payment_terms.create');
    }

    /**
     * Store a newly created PoPaymentTerms in storage.
     *
     * @param CreatePoPaymentTermsRequest $request
     *
     * @return Response
     */
    public function store(CreatePoPaymentTermsRequest $request)
    {
        $input = $request->all();

        $poPaymentTerms = $this->poPaymentTermsRepository->create($input);

        Flash::success('Po Payment Terms saved successfully.');

        return redirect(route('poPaymentTerms.index'));
    }

    /**
     * Display the specified PoPaymentTerms.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            Flash::error('Po Payment Terms not found');

            return redirect(route('poPaymentTerms.index'));
        }

        return view('po_payment_terms.show')->with('poPaymentTerms', $poPaymentTerms);
    }

    /**
     * Show the form for editing the specified PoPaymentTerms.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            Flash::error('Po Payment Terms not found');

            return redirect(route('poPaymentTerms.index'));
        }

        return view('po_payment_terms.edit')->with('poPaymentTerms', $poPaymentTerms);
    }

    /**
     * Update the specified PoPaymentTerms in storage.
     *
     * @param  int              $id
     * @param UpdatePoPaymentTermsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoPaymentTermsRequest $request)
    {
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            Flash::error('Po Payment Terms not found');

            return redirect(route('poPaymentTerms.index'));
        }

        $poPaymentTerms = $this->poPaymentTermsRepository->update($request->all(), $id);

        Flash::success('Po Payment Terms updated successfully.');

        return redirect(route('poPaymentTerms.index'));
    }

    /**
     * Remove the specified PoPaymentTerms from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poPaymentTerms = $this->poPaymentTermsRepository->findWithoutFail($id);

        if (empty($poPaymentTerms)) {
            Flash::error('Po Payment Terms not found');

            return redirect(route('poPaymentTerms.index'));
        }

        $this->poPaymentTermsRepository->delete($id);

        Flash::success('Po Payment Terms deleted successfully.');

        return redirect(route('poPaymentTerms.index'));
    }
}
