<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoPaymentTermTypesRequest;
use App\Http\Requests\UpdatePoPaymentTermTypesRequest;
use App\Repositories\PoPaymentTermTypesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoPaymentTermTypesController extends AppBaseController
{
    /** @var  PoPaymentTermTypesRepository */
    private $poPaymentTermTypesRepository;

    public function __construct(PoPaymentTermTypesRepository $poPaymentTermTypesRepo)
    {
        $this->poPaymentTermTypesRepository = $poPaymentTermTypesRepo;
    }

    /**
     * Display a listing of the PoPaymentTermTypes.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poPaymentTermTypesRepository->pushCriteria(new RequestCriteria($request));
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->all();

        return view('po_payment_term_types.index')
            ->with('poPaymentTermTypes', $poPaymentTermTypes);
    }

    /**
     * Show the form for creating a new PoPaymentTermTypes.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_payment_term_types.create');
    }

    /**
     * Store a newly created PoPaymentTermTypes in storage.
     *
     * @param CreatePoPaymentTermTypesRequest $request
     *
     * @return Response
     */
    public function store(CreatePoPaymentTermTypesRequest $request)
    {
        $input = $request->all();

        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->create($input);

        Flash::success('Po Payment Term Types saved successfully.');

        return redirect(route('poPaymentTermTypes.index'));
    }

    /**
     * Display the specified PoPaymentTermTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            Flash::error('Po Payment Term Types not found');

            return redirect(route('poPaymentTermTypes.index'));
        }

        return view('po_payment_term_types.show')->with('poPaymentTermTypes', $poPaymentTermTypes);
    }

    /**
     * Show the form for editing the specified PoPaymentTermTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            Flash::error('Po Payment Term Types not found');

            return redirect(route('poPaymentTermTypes.index'));
        }

        return view('po_payment_term_types.edit')->with('poPaymentTermTypes', $poPaymentTermTypes);
    }

    /**
     * Update the specified PoPaymentTermTypes in storage.
     *
     * @param  int              $id
     * @param UpdatePoPaymentTermTypesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoPaymentTermTypesRequest $request)
    {
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            Flash::error('Po Payment Term Types not found');

            return redirect(route('poPaymentTermTypes.index'));
        }

        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->update($request->all(), $id);

        Flash::success('Po Payment Term Types updated successfully.');

        return redirect(route('poPaymentTermTypes.index'));
    }

    /**
     * Remove the specified PoPaymentTermTypes from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            Flash::error('Po Payment Term Types not found');

            return redirect(route('poPaymentTermTypes.index'));
        }

        $this->poPaymentTermTypesRepository->delete($id);

        Flash::success('Po Payment Term Types deleted successfully.');

        return redirect(route('poPaymentTermTypes.index'));
    }
}
