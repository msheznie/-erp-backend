<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDirectReceiptDetailsRefferedHistoryRequest;
use App\Http\Requests\UpdateDirectReceiptDetailsRefferedHistoryRequest;
use App\Repositories\DirectReceiptDetailsRefferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DirectReceiptDetailsRefferedHistoryController extends AppBaseController
{
    /** @var  DirectReceiptDetailsRefferedHistoryRepository */
    private $directReceiptDetailsRefferedHistoryRepository;

    public function __construct(DirectReceiptDetailsRefferedHistoryRepository $directReceiptDetailsRefferedHistoryRepo)
    {
        $this->directReceiptDetailsRefferedHistoryRepository = $directReceiptDetailsRefferedHistoryRepo;
    }

    /**
     * Display a listing of the DirectReceiptDetailsRefferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->directReceiptDetailsRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $directReceiptDetailsRefferedHistories = $this->directReceiptDetailsRefferedHistoryRepository->all();

        return view('direct_receipt_details_reffered_histories.index')
            ->with('directReceiptDetailsRefferedHistories', $directReceiptDetailsRefferedHistories);
    }

    /**
     * Show the form for creating a new DirectReceiptDetailsRefferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('direct_receipt_details_reffered_histories.create');
    }

    /**
     * Store a newly created DirectReceiptDetailsRefferedHistory in storage.
     *
     * @param CreateDirectReceiptDetailsRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreateDirectReceiptDetailsRefferedHistoryRequest $request)
    {
        $input = $request->all();

        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->create($input);

        Flash::success('Direct Receipt Details Reffered History saved successfully.');

        return redirect(route('directReceiptDetailsRefferedHistories.index'));
    }

    /**
     * Display the specified DirectReceiptDetailsRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            Flash::error('Direct Receipt Details Reffered History not found');

            return redirect(route('directReceiptDetailsRefferedHistories.index'));
        }

        return view('direct_receipt_details_reffered_histories.show')->with('directReceiptDetailsRefferedHistory', $directReceiptDetailsRefferedHistory);
    }

    /**
     * Show the form for editing the specified DirectReceiptDetailsRefferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            Flash::error('Direct Receipt Details Reffered History not found');

            return redirect(route('directReceiptDetailsRefferedHistories.index'));
        }

        return view('direct_receipt_details_reffered_histories.edit')->with('directReceiptDetailsRefferedHistory', $directReceiptDetailsRefferedHistory);
    }

    /**
     * Update the specified DirectReceiptDetailsRefferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdateDirectReceiptDetailsRefferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDirectReceiptDetailsRefferedHistoryRequest $request)
    {
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            Flash::error('Direct Receipt Details Reffered History not found');

            return redirect(route('directReceiptDetailsRefferedHistories.index'));
        }

        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->update($request->all(), $id);

        Flash::success('Direct Receipt Details Reffered History updated successfully.');

        return redirect(route('directReceiptDetailsRefferedHistories.index'));
    }

    /**
     * Remove the specified DirectReceiptDetailsRefferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            Flash::error('Direct Receipt Details Reffered History not found');

            return redirect(route('directReceiptDetailsRefferedHistories.index'));
        }

        $this->directReceiptDetailsRefferedHistoryRepository->delete($id);

        Flash::success('Direct Receipt Details Reffered History deleted successfully.');

        return redirect(route('directReceiptDetailsRefferedHistories.index'));
    }
}
