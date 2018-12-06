<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemReturnDetailsRefferedBackRequest;
use App\Http\Requests\UpdateItemReturnDetailsRefferedBackRequest;
use App\Repositories\ItemReturnDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemReturnDetailsRefferedBackController extends AppBaseController
{
    /** @var  ItemReturnDetailsRefferedBackRepository */
    private $itemReturnDetailsRefferedBackRepository;

    public function __construct(ItemReturnDetailsRefferedBackRepository $itemReturnDetailsRefferedBackRepo)
    {
        $this->itemReturnDetailsRefferedBackRepository = $itemReturnDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the ItemReturnDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemReturnDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $itemReturnDetailsRefferedBacks = $this->itemReturnDetailsRefferedBackRepository->all();

        return view('item_return_details_reffered_backs.index')
            ->with('itemReturnDetailsRefferedBacks', $itemReturnDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new ItemReturnDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('item_return_details_reffered_backs.create');
    }

    /**
     * Store a newly created ItemReturnDetailsRefferedBack in storage.
     *
     * @param CreateItemReturnDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateItemReturnDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->create($input);

        Flash::success('Item Return Details Reffered Back saved successfully.');

        return redirect(route('itemReturnDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified ItemReturnDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            Flash::error('Item Return Details Reffered Back not found');

            return redirect(route('itemReturnDetailsRefferedBacks.index'));
        }

        return view('item_return_details_reffered_backs.show')->with('itemReturnDetailsRefferedBack', $itemReturnDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified ItemReturnDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            Flash::error('Item Return Details Reffered Back not found');

            return redirect(route('itemReturnDetailsRefferedBacks.index'));
        }

        return view('item_return_details_reffered_backs.edit')->with('itemReturnDetailsRefferedBack', $itemReturnDetailsRefferedBack);
    }

    /**
     * Update the specified ItemReturnDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateItemReturnDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemReturnDetailsRefferedBackRequest $request)
    {
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            Flash::error('Item Return Details Reffered Back not found');

            return redirect(route('itemReturnDetailsRefferedBacks.index'));
        }

        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Item Return Details Reffered Back updated successfully.');

        return redirect(route('itemReturnDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified ItemReturnDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $itemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnDetailsRefferedBack)) {
            Flash::error('Item Return Details Reffered Back not found');

            return redirect(route('itemReturnDetailsRefferedBacks.index'));
        }

        $this->itemReturnDetailsRefferedBackRepository->delete($id);

        Flash::success('Item Return Details Reffered Back deleted successfully.');

        return redirect(route('itemReturnDetailsRefferedBacks.index'));
    }
}
