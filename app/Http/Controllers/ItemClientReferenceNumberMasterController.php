<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemClientReferenceNumberMasterRequest;
use App\Http\Requests\UpdateItemClientReferenceNumberMasterRequest;
use App\Repositories\ItemClientReferenceNumberMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemClientReferenceNumberMasterController extends AppBaseController
{
    /** @var  ItemClientReferenceNumberMasterRepository */
    private $itemClientReferenceNumberMasterRepository;

    public function __construct(ItemClientReferenceNumberMasterRepository $itemClientReferenceNumberMasterRepo)
    {
        $this->itemClientReferenceNumberMasterRepository = $itemClientReferenceNumberMasterRepo;
    }

    /**
     * Display a listing of the ItemClientReferenceNumberMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemClientReferenceNumberMasterRepository->pushCriteria(new RequestCriteria($request));
        $itemClientReferenceNumberMasters = $this->itemClientReferenceNumberMasterRepository->all();

        return view('item_client_reference_number_masters.index')
            ->with('itemClientReferenceNumberMasters', $itemClientReferenceNumberMasters);
    }

    /**
     * Show the form for creating a new ItemClientReferenceNumberMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('item_client_reference_number_masters.create');
    }

    /**
     * Store a newly created ItemClientReferenceNumberMaster in storage.
     *
     * @param CreateItemClientReferenceNumberMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateItemClientReferenceNumberMasterRequest $request)
    {
        $input = $request->all();

        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->create($input);

        Flash::success('Item Client Reference Number Master saved successfully.');

        return redirect(route('itemClientReferenceNumberMasters.index'));
    }

    /**
     * Display the specified ItemClientReferenceNumberMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            Flash::error('Item Client Reference Number Master not found');

            return redirect(route('itemClientReferenceNumberMasters.index'));
        }

        return view('item_client_reference_number_masters.show')->with('itemClientReferenceNumberMaster', $itemClientReferenceNumberMaster);
    }

    /**
     * Show the form for editing the specified ItemClientReferenceNumberMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            Flash::error('Item Client Reference Number Master not found');

            return redirect(route('itemClientReferenceNumberMasters.index'));
        }

        return view('item_client_reference_number_masters.edit')->with('itemClientReferenceNumberMaster', $itemClientReferenceNumberMaster);
    }

    /**
     * Update the specified ItemClientReferenceNumberMaster in storage.
     *
     * @param  int              $id
     * @param UpdateItemClientReferenceNumberMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemClientReferenceNumberMasterRequest $request)
    {
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            Flash::error('Item Client Reference Number Master not found');

            return redirect(route('itemClientReferenceNumberMasters.index'));
        }

        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->update($request->all(), $id);

        Flash::success('Item Client Reference Number Master updated successfully.');

        return redirect(route('itemClientReferenceNumberMasters.index'));
    }

    /**
     * Remove the specified ItemClientReferenceNumberMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $itemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepository->findWithoutFail($id);

        if (empty($itemClientReferenceNumberMaster)) {
            Flash::error('Item Client Reference Number Master not found');

            return redirect(route('itemClientReferenceNumberMasters.index'));
        }

        $this->itemClientReferenceNumberMasterRepository->delete($id);

        Flash::success('Item Client Reference Number Master deleted successfully.');

        return redirect(route('itemClientReferenceNumberMasters.index'));
    }
}
