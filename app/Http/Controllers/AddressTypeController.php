<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddressTypeRequest;
use App\Http\Requests\UpdateAddressTypeRequest;
use App\Repositories\AddressTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AddressTypeController extends AppBaseController
{
    /** @var  AddressTypeRepository */
    private $addressTypeRepository;

    public function __construct(AddressTypeRepository $addressTypeRepo)
    {
        $this->addressTypeRepository = $addressTypeRepo;
    }

    /**
     * Display a listing of the AddressType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->addressTypeRepository->pushCriteria(new RequestCriteria($request));
        $addressTypes = $this->addressTypeRepository->all();

        return view('address_types.index')
            ->with('addressTypes', $addressTypes);
    }

    /**
     * Show the form for creating a new AddressType.
     *
     * @return Response
     */
    public function create()
    {
        return view('address_types.create');
    }

    /**
     * Store a newly created AddressType in storage.
     *
     * @param CreateAddressTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateAddressTypeRequest $request)
    {
        $input = $request->all();

        $addressType = $this->addressTypeRepository->create($input);

        Flash::success('Address Type saved successfully.');

        return redirect(route('addressTypes.index'));
    }

    /**
     * Display the specified AddressType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            Flash::error('Address Type not found');

            return redirect(route('addressTypes.index'));
        }

        return view('address_types.show')->with('addressType', $addressType);
    }

    /**
     * Show the form for editing the specified AddressType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            Flash::error('Address Type not found');

            return redirect(route('addressTypes.index'));
        }

        return view('address_types.edit')->with('addressType', $addressType);
    }

    /**
     * Update the specified AddressType in storage.
     *
     * @param  int              $id
     * @param UpdateAddressTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAddressTypeRequest $request)
    {
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            Flash::error('Address Type not found');

            return redirect(route('addressTypes.index'));
        }

        $addressType = $this->addressTypeRepository->update($request->all(), $id);

        Flash::success('Address Type updated successfully.');

        return redirect(route('addressTypes.index'));
    }

    /**
     * Remove the specified AddressType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $addressType = $this->addressTypeRepository->findWithoutFail($id);

        if (empty($addressType)) {
            Flash::error('Address Type not found');

            return redirect(route('addressTypes.index'));
        }

        $this->addressTypeRepository->delete($id);

        Flash::success('Address Type deleted successfully.');

        return redirect(route('addressTypes.index'));
    }
}
