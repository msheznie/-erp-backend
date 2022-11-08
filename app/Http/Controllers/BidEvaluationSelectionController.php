<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBidEvaluationSelectionRequest;
use App\Http\Requests\UpdateBidEvaluationSelectionRequest;
use App\Repositories\BidEvaluationSelectionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BidEvaluationSelectionController extends AppBaseController
{
    /** @var  BidEvaluationSelectionRepository */
    private $bidEvaluationSelectionRepository;

    public function __construct(BidEvaluationSelectionRepository $bidEvaluationSelectionRepo)
    {
        $this->bidEvaluationSelectionRepository = $bidEvaluationSelectionRepo;
    }

    /**
     * Display a listing of the BidEvaluationSelection.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bidEvaluationSelectionRepository->pushCriteria(new RequestCriteria($request));
        $bidEvaluationSelections = $this->bidEvaluationSelectionRepository->all();

        return view('bid_evaluation_selections.index')
            ->with('bidEvaluationSelections', $bidEvaluationSelections);
    }

    /**
     * Show the form for creating a new BidEvaluationSelection.
     *
     * @return Response
     */
    public function create()
    {
        return view('bid_evaluation_selections.create');
    }

    /**
     * Store a newly created BidEvaluationSelection in storage.
     *
     * @param CreateBidEvaluationSelectionRequest $request
     *
     * @return Response
     */
    public function store(CreateBidEvaluationSelectionRequest $request)
    {
        $input = $request->all();

        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->create($input);

        Flash::success('Bid Evaluation Selection saved successfully.');

        return redirect(route('bidEvaluationSelections.index'));
    }

    /**
     * Display the specified BidEvaluationSelection.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            Flash::error('Bid Evaluation Selection not found');

            return redirect(route('bidEvaluationSelections.index'));
        }

        return view('bid_evaluation_selections.show')->with('bidEvaluationSelection', $bidEvaluationSelection);
    }

    /**
     * Show the form for editing the specified BidEvaluationSelection.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            Flash::error('Bid Evaluation Selection not found');

            return redirect(route('bidEvaluationSelections.index'));
        }

        return view('bid_evaluation_selections.edit')->with('bidEvaluationSelection', $bidEvaluationSelection);
    }

    /**
     * Update the specified BidEvaluationSelection in storage.
     *
     * @param  int              $id
     * @param UpdateBidEvaluationSelectionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBidEvaluationSelectionRequest $request)
    {
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            Flash::error('Bid Evaluation Selection not found');

            return redirect(route('bidEvaluationSelections.index'));
        }

        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->update($request->all(), $id);

        Flash::success('Bid Evaluation Selection updated successfully.');

        return redirect(route('bidEvaluationSelections.index'));
    }

    /**
     * Remove the specified BidEvaluationSelection from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bidEvaluationSelection = $this->bidEvaluationSelectionRepository->findWithoutFail($id);

        if (empty($bidEvaluationSelection)) {
            Flash::error('Bid Evaluation Selection not found');

            return redirect(route('bidEvaluationSelections.index'));
        }

        $this->bidEvaluationSelectionRepository->delete($id);

        Flash::success('Bid Evaluation Selection deleted successfully.');

        return redirect(route('bidEvaluationSelections.index'));
    }
}
