<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemReturnMasterRefferedBackRequest;
use App\Http\Requests\UpdateItemReturnMasterRefferedBackRequest;
use App\Repositories\ItemReturnMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemReturnMasterRefferedBackController extends AppBaseController
{
    /** @var  ItemReturnMasterRefferedBackRepository */
    private $itemReturnMasterRefferedBackRepository;

    public function __construct(ItemReturnMasterRefferedBackRepository $itemReturnMasterRefferedBackRepo)
    {
        $this->itemReturnMasterRefferedBackRepository = $itemReturnMasterRefferedBackRepo;
    }

    /**
     * Display a listing of the ItemReturnMasterRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemReturnMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $itemReturnMasterRefferedBacks = $this->itemReturnMasterRefferedBackRepository->all();

        return view('item_return_master_reffered_backs.index')
            ->with('itemReturnMasterRefferedBacks', $itemReturnMasterRefferedBacks);
    }

    /**
     * Show the form for creating a new ItemReturnMasterRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('item_return_master_reffered_backs.create');
    }

    /**
     * Store a newly created ItemReturnMasterRefferedBack in storage.
     *
     * @param CreateItemReturnMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateItemReturnMasterRefferedBackRequest $request)
    {
        $input = $request->all();

        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->create($input);

        Flash::success('Item Return Master Reffered Back saved successfully.');

        return redirect(route('itemReturnMasterRefferedBacks.index'));
    }

    /**
     * Display the specified ItemReturnMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            Flash::error('Item Return Master Reffered Back not found');

            return redirect(route('itemReturnMasterRefferedBacks.index'));
        }

        return view('item_return_master_reffered_backs.show')->with('itemReturnMasterRefferedBack', $itemReturnMasterRefferedBack);
    }

    /**
     * Show the form for editing the specified ItemReturnMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            Flash::error('Item Return Master Reffered Back not found');

            return redirect(route('itemReturnMasterRefferedBacks.index'));
        }

        return view('item_return_master_reffered_backs.edit')->with('itemReturnMasterRefferedBack', $itemReturnMasterRefferedBack);
    }

    /**
     * Update the specified ItemReturnMasterRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateItemReturnMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemReturnMasterRefferedBackRequest $request)
    {
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            Flash::error('Item Return Master Reffered Back not found');

            return redirect(route('itemReturnMasterRefferedBacks.index'));
        }

        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->update($request->all(), $id);

        Flash::success('Item Return Master Reffered Back updated successfully.');

        return redirect(route('itemReturnMasterRefferedBacks.index'));
    }

    /**
     * Remove the specified ItemReturnMasterRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            Flash::error('Item Return Master Reffered Back not found');

            return redirect(route('itemReturnMasterRefferedBacks.index'));
        }

        $this->itemReturnMasterRefferedBackRepository->delete($id);

        Flash::success('Item Return Master Reffered Back deleted successfully.');

        return redirect(route('itemReturnMasterRefferedBacks.index'));
    }
}
