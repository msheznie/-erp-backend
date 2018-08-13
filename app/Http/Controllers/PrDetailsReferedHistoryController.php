<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePrDetailsReferedHistoryRequest;
use App\Http\Requests\UpdatePrDetailsReferedHistoryRequest;
use App\Repositories\PrDetailsReferedHistoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PrDetailsReferedHistoryController extends AppBaseController
{
    /** @var  PrDetailsReferedHistoryRepository */
    private $prDetailsReferedHistoryRepository;

    public function __construct(PrDetailsReferedHistoryRepository $prDetailsReferedHistoryRepo)
    {
        $this->prDetailsReferedHistoryRepository = $prDetailsReferedHistoryRepo;
    }

    /**
     * Display a listing of the PrDetailsReferedHistory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->prDetailsReferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $prDetailsReferedHistories = $this->prDetailsReferedHistoryRepository->all();

        return view('pr_details_refered_histories.index')
            ->with('prDetailsReferedHistories', $prDetailsReferedHistories);
    }

    /**
     * Show the form for creating a new PrDetailsReferedHistory.
     *
     * @return Response
     */
    public function create()
    {
        return view('pr_details_refered_histories.create');
    }

    /**
     * Store a newly created PrDetailsReferedHistory in storage.
     *
     * @param CreatePrDetailsReferedHistoryRequest $request
     *
     * @return Response
     */
    public function store(CreatePrDetailsReferedHistoryRequest $request)
    {
        $input = $request->all();

        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->create($input);

        Flash::success('Pr Details Refered History saved successfully.');

        return redirect(route('prDetailsReferedHistories.index'));
    }

    /**
     * Display the specified PrDetailsReferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            Flash::error('Pr Details Refered History not found');

            return redirect(route('prDetailsReferedHistories.index'));
        }

        return view('pr_details_refered_histories.show')->with('prDetailsReferedHistory', $prDetailsReferedHistory);
    }

    /**
     * Show the form for editing the specified PrDetailsReferedHistory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            Flash::error('Pr Details Refered History not found');

            return redirect(route('prDetailsReferedHistories.index'));
        }

        return view('pr_details_refered_histories.edit')->with('prDetailsReferedHistory', $prDetailsReferedHistory);
    }

    /**
     * Update the specified PrDetailsReferedHistory in storage.
     *
     * @param  int              $id
     * @param UpdatePrDetailsReferedHistoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePrDetailsReferedHistoryRequest $request)
    {
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            Flash::error('Pr Details Refered History not found');

            return redirect(route('prDetailsReferedHistories.index'));
        }

        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->update($request->all(), $id);

        Flash::success('Pr Details Refered History updated successfully.');

        return redirect(route('prDetailsReferedHistories.index'));
    }

    /**
     * Remove the specified PrDetailsReferedHistory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            Flash::error('Pr Details Refered History not found');

            return redirect(route('prDetailsReferedHistories.index'));
        }

        $this->prDetailsReferedHistoryRepository->delete($id);

        Flash::success('Pr Details Refered History deleted successfully.');

        return redirect(route('prDetailsReferedHistories.index'));
    }
}
