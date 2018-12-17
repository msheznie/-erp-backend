<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemMasterRefferedBackRequest;
use App\Http\Requests\UpdateItemMasterRefferedBackRequest;
use App\Repositories\ItemMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemMasterRefferedBackController extends AppBaseController
{
    /** @var  ItemMasterRefferedBackRepository */
    private $itemMasterRefferedBackRepository;

    public function __construct(ItemMasterRefferedBackRepository $itemMasterRefferedBackRepo)
    {
        $this->itemMasterRefferedBackRepository = $itemMasterRefferedBackRepo;
    }

    /**
     * Display a listing of the ItemMasterRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $itemMasterRefferedBacks = $this->itemMasterRefferedBackRepository->all();

        return view('item_master_reffered_backs.index')
            ->with('itemMasterRefferedBacks', $itemMasterRefferedBacks);
    }

    /**
     * Show the form for creating a new ItemMasterRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('item_master_reffered_backs.create');
    }

    /**
     * Store a newly created ItemMasterRefferedBack in storage.
     *
     * @param CreateItemMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateItemMasterRefferedBackRequest $request)
    {
        $input = $request->all();

        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->create($input);

        Flash::success('Item Master Reffered Back saved successfully.');

        return redirect(route('itemMasterRefferedBacks.index'));
    }

    /**
     * Display the specified ItemMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            Flash::error('Item Master Reffered Back not found');

            return redirect(route('itemMasterRefferedBacks.index'));
        }

        return view('item_master_reffered_backs.show')->with('itemMasterRefferedBack', $itemMasterRefferedBack);
    }

    /**
     * Show the form for editing the specified ItemMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            Flash::error('Item Master Reffered Back not found');

            return redirect(route('itemMasterRefferedBacks.index'));
        }

        return view('item_master_reffered_backs.edit')->with('itemMasterRefferedBack', $itemMasterRefferedBack);
    }

    /**
     * Update the specified ItemMasterRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateItemMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemMasterRefferedBackRequest $request)
    {
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            Flash::error('Item Master Reffered Back not found');

            return redirect(route('itemMasterRefferedBacks.index'));
        }

        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->update($request->all(), $id);

        Flash::success('Item Master Reffered Back updated successfully.');

        return redirect(route('itemMasterRefferedBacks.index'));
    }

    /**
     * Remove the specified ItemMasterRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $itemMasterRefferedBack = $this->itemMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemMasterRefferedBack)) {
            Flash::error('Item Master Reffered Back not found');

            return redirect(route('itemMasterRefferedBacks.index'));
        }

        $this->itemMasterRefferedBackRepository->delete($id);

        Flash::success('Item Master Reffered Back deleted successfully.');

        return redirect(route('itemMasterRefferedBacks.index'));
    }
}
