<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateErpAddressRequest;
use App\Http\Requests\UpdateErpAddressRequest;
use App\Repositories\ErpAddressRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ErpAddressController extends AppBaseController
{
    /** @var  ErpAddressRepository */
    private $erpAddressRepository;

    public function __construct(ErpAddressRepository $erpAddressRepo)
    {
        $this->erpAddressRepository = $erpAddressRepo;
    }

    /**
     * Display a listing of the ErpAddress.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpAddressRepository->pushCriteria(new RequestCriteria($request));
        $erpAddresses = $this->erpAddressRepository->all();

        return view('erp_addresses.index')
            ->with('erpAddresses', $erpAddresses);
    }

    /**
     * Show the form for creating a new ErpAddress.
     *
     * @return Response
     */
    public function create()
    {
        return view('erp_addresses.create');
    }

    /**
     * Store a newly created ErpAddress in storage.
     *
     * @param CreateErpAddressRequest $request
     *
     * @return Response
     */
    public function store(CreateErpAddressRequest $request)
    {
        $input = $request->all();

        $erpAddress = $this->erpAddressRepository->create($input);

        Flash::success('Erp Address saved successfully.');

        return redirect(route('erpAddresses.index'));
    }

    /**
     * Display the specified ErpAddress.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            Flash::error('Erp Address not found');

            return redirect(route('erpAddresses.index'));
        }

        return view('erp_addresses.show')->with('erpAddress', $erpAddress);
    }

    /**
     * Show the form for editing the specified ErpAddress.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            Flash::error('Erp Address not found');

            return redirect(route('erpAddresses.index'));
        }

        return view('erp_addresses.edit')->with('erpAddress', $erpAddress);
    }

    /**
     * Update the specified ErpAddress in storage.
     *
     * @param  int              $id
     * @param UpdateErpAddressRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpAddressRequest $request)
    {
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            Flash::error('Erp Address not found');

            return redirect(route('erpAddresses.index'));
        }

        $erpAddress = $this->erpAddressRepository->update($request->all(), $id);

        Flash::success('Erp Address updated successfully.');

        return redirect(route('erpAddresses.index'));
    }

    /**
     * Remove the specified ErpAddress from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            Flash::error('Erp Address not found');

            return redirect(route('erpAddresses.index'));
        }

        $this->erpAddressRepository->delete($id);

        Flash::success('Erp Address deleted successfully.');

        return redirect(route('erpAddresses.index'));
    }
}
