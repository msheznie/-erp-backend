<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommercialBidRankingItemsRequest;
use App\Http\Requests\UpdateCommercialBidRankingItemsRequest;
use App\Repositories\CommercialBidRankingItemsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CommercialBidRankingItemsController extends AppBaseController
{
    /** @var  CommercialBidRankingItemsRepository */
    private $commercialBidRankingItemsRepository;

    public function __construct(CommercialBidRankingItemsRepository $commercialBidRankingItemsRepo)
    {
        $this->commercialBidRankingItemsRepository = $commercialBidRankingItemsRepo;
    }

    /**
     * Display a listing of the CommercialBidRankingItems.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->commercialBidRankingItemsRepository->pushCriteria(new RequestCriteria($request));
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->all();

        return view('commercial_bid_ranking_items.index')
            ->with('commercialBidRankingItems', $commercialBidRankingItems);
    }

    /**
     * Show the form for creating a new CommercialBidRankingItems.
     *
     * @return Response
     */
    public function create()
    {
        return view('commercial_bid_ranking_items.create');
    }

    /**
     * Store a newly created CommercialBidRankingItems in storage.
     *
     * @param CreateCommercialBidRankingItemsRequest $request
     *
     * @return Response
     */
    public function store(CreateCommercialBidRankingItemsRequest $request)
    {
        $input = $request->all();

        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->create($input);

        Flash::success('Commercial Bid Ranking Items saved successfully.');

        return redirect(route('commercialBidRankingItems.index'));
    }

    /**
     * Display the specified CommercialBidRankingItems.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            Flash::error('Commercial Bid Ranking Items not found');

            return redirect(route('commercialBidRankingItems.index'));
        }

        return view('commercial_bid_ranking_items.show')->with('commercialBidRankingItems', $commercialBidRankingItems);
    }

    /**
     * Show the form for editing the specified CommercialBidRankingItems.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            Flash::error('Commercial Bid Ranking Items not found');

            return redirect(route('commercialBidRankingItems.index'));
        }

        return view('commercial_bid_ranking_items.edit')->with('commercialBidRankingItems', $commercialBidRankingItems);
    }

    /**
     * Update the specified CommercialBidRankingItems in storage.
     *
     * @param  int              $id
     * @param UpdateCommercialBidRankingItemsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCommercialBidRankingItemsRequest $request)
    {
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            Flash::error('Commercial Bid Ranking Items not found');

            return redirect(route('commercialBidRankingItems.index'));
        }

        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->update($request->all(), $id);

        Flash::success('Commercial Bid Ranking Items updated successfully.');

        return redirect(route('commercialBidRankingItems.index'));
    }

    /**
     * Remove the specified CommercialBidRankingItems from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            Flash::error('Commercial Bid Ranking Items not found');

            return redirect(route('commercialBidRankingItems.index'));
        }

        $this->commercialBidRankingItemsRepository->delete($id);

        Flash::success('Commercial Bid Ranking Items deleted successfully.');

        return redirect(route('commercialBidRankingItems.index'));
    }
}
