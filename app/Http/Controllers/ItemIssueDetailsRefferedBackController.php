<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemIssueDetailsRefferedBackRequest;
use App\Http\Requests\UpdateItemIssueDetailsRefferedBackRequest;
use App\Repositories\ItemIssueDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemIssueDetailsRefferedBackController extends AppBaseController
{
    /** @var  ItemIssueDetailsRefferedBackRepository */
    private $itemIssueDetailsRefferedBackRepository;

    public function __construct(ItemIssueDetailsRefferedBackRepository $itemIssueDetailsRefferedBackRepo)
    {
        $this->itemIssueDetailsRefferedBackRepository = $itemIssueDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the ItemIssueDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemIssueDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $itemIssueDetailsRefferedBacks = $this->itemIssueDetailsRefferedBackRepository->all();

        return view('item_issue_details_reffered_backs.index')
            ->with('itemIssueDetailsRefferedBacks', $itemIssueDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new ItemIssueDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('item_issue_details_reffered_backs.create');
    }

    /**
     * Store a newly created ItemIssueDetailsRefferedBack in storage.
     *
     * @param CreateItemIssueDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateItemIssueDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->create($input);

        Flash::success('Item Issue Details Reffered Back saved successfully.');

        return redirect(route('itemIssueDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified ItemIssueDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            Flash::error('Item Issue Details Reffered Back not found');

            return redirect(route('itemIssueDetailsRefferedBacks.index'));
        }

        return view('item_issue_details_reffered_backs.show')->with('itemIssueDetailsRefferedBack', $itemIssueDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified ItemIssueDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            Flash::error('Item Issue Details Reffered Back not found');

            return redirect(route('itemIssueDetailsRefferedBacks.index'));
        }

        return view('item_issue_details_reffered_backs.edit')->with('itemIssueDetailsRefferedBack', $itemIssueDetailsRefferedBack);
    }

    /**
     * Update the specified ItemIssueDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateItemIssueDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemIssueDetailsRefferedBackRequest $request)
    {
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            Flash::error('Item Issue Details Reffered Back not found');

            return redirect(route('itemIssueDetailsRefferedBacks.index'));
        }

        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Item Issue Details Reffered Back updated successfully.');

        return redirect(route('itemIssueDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified ItemIssueDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $itemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueDetailsRefferedBack)) {
            Flash::error('Item Issue Details Reffered Back not found');

            return redirect(route('itemIssueDetailsRefferedBacks.index'));
        }

        $this->itemIssueDetailsRefferedBackRepository->delete($id);

        Flash::success('Item Issue Details Reffered Back deleted successfully.');

        return redirect(route('itemIssueDetailsRefferedBacks.index'));
    }
}
