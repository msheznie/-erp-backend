<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemIssueMasterRefferedBackRequest;
use App\Http\Requests\UpdateItemIssueMasterRefferedBackRequest;
use App\Repositories\ItemIssueMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemIssueMasterRefferedBackController extends AppBaseController
{
    /** @var  ItemIssueMasterRefferedBackRepository */
    private $itemIssueMasterRefferedBackRepository;

    public function __construct(ItemIssueMasterRefferedBackRepository $itemIssueMasterRefferedBackRepo)
    {
        $this->itemIssueMasterRefferedBackRepository = $itemIssueMasterRefferedBackRepo;
    }

    /**
     * Display a listing of the ItemIssueMasterRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemIssueMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $itemIssueMasterRefferedBacks = $this->itemIssueMasterRefferedBackRepository->all();

        return view('item_issue_master_reffered_backs.index')
            ->with('itemIssueMasterRefferedBacks', $itemIssueMasterRefferedBacks);
    }

    /**
     * Show the form for creating a new ItemIssueMasterRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('item_issue_master_reffered_backs.create');
    }

    /**
     * Store a newly created ItemIssueMasterRefferedBack in storage.
     *
     * @param CreateItemIssueMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateItemIssueMasterRefferedBackRequest $request)
    {
        $input = $request->all();

        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->create($input);

        Flash::success('Item Issue Master Reffered Back saved successfully.');

        return redirect(route('itemIssueMasterRefferedBacks.index'));
    }

    /**
     * Display the specified ItemIssueMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            Flash::error('Item Issue Master Reffered Back not found');

            return redirect(route('itemIssueMasterRefferedBacks.index'));
        }

        return view('item_issue_master_reffered_backs.show')->with('itemIssueMasterRefferedBack', $itemIssueMasterRefferedBack);
    }

    /**
     * Show the form for editing the specified ItemIssueMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            Flash::error('Item Issue Master Reffered Back not found');

            return redirect(route('itemIssueMasterRefferedBacks.index'));
        }

        return view('item_issue_master_reffered_backs.edit')->with('itemIssueMasterRefferedBack', $itemIssueMasterRefferedBack);
    }

    /**
     * Update the specified ItemIssueMasterRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateItemIssueMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemIssueMasterRefferedBackRequest $request)
    {
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            Flash::error('Item Issue Master Reffered Back not found');

            return redirect(route('itemIssueMasterRefferedBacks.index'));
        }

        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->update($request->all(), $id);

        Flash::success('Item Issue Master Reffered Back updated successfully.');

        return redirect(route('itemIssueMasterRefferedBacks.index'));
    }

    /**
     * Remove the specified ItemIssueMasterRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            Flash::error('Item Issue Master Reffered Back not found');

            return redirect(route('itemIssueMasterRefferedBacks.index'));
        }

        $this->itemIssueMasterRefferedBackRepository->delete($id);

        Flash::success('Item Issue Master Reffered Back deleted successfully.');

        return redirect(route('itemIssueMasterRefferedBacks.index'));
    }
}
