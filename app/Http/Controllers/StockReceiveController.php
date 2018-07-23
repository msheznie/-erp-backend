<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockReceiveRequest;
use App\Http\Requests\UpdateStockReceiveRequest;
use App\Repositories\StockReceiveRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockReceiveController extends AppBaseController
{
    /** @var  StockReceiveRepository */
    private $stockReceiveRepository;

    public function __construct(StockReceiveRepository $stockReceiveRepo)
    {
        $this->stockReceiveRepository = $stockReceiveRepo;
    }

    /**
     * Display a listing of the StockReceive.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockReceiveRepository->pushCriteria(new RequestCriteria($request));
        $stockReceives = $this->stockReceiveRepository->all();

        return view('stock_receives.index')
            ->with('stockReceives', $stockReceives);
    }

    /**
     * Show the form for creating a new StockReceive.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_receives.create');
    }

    /**
     * Store a newly created StockReceive in storage.
     *
     * @param CreateStockReceiveRequest $request
     *
     * @return Response
     */
    public function store(CreateStockReceiveRequest $request)
    {
        $input = $request->all();

        $stockReceive = $this->stockReceiveRepository->create($input);

        Flash::success('Stock Receive saved successfully.');

        return redirect(route('stockReceives.index'));
    }

    /**
     * Display the specified StockReceive.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            Flash::error('Stock Receive not found');

            return redirect(route('stockReceives.index'));
        }

        return view('stock_receives.show')->with('stockReceive', $stockReceive);
    }

    /**
     * Show the form for editing the specified StockReceive.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            Flash::error('Stock Receive not found');

            return redirect(route('stockReceives.index'));
        }

        return view('stock_receives.edit')->with('stockReceive', $stockReceive);
    }

    /**
     * Update the specified StockReceive in storage.
     *
     * @param  int              $id
     * @param UpdateStockReceiveRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockReceiveRequest $request)
    {
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            Flash::error('Stock Receive not found');

            return redirect(route('stockReceives.index'));
        }

        $stockReceive = $this->stockReceiveRepository->update($request->all(), $id);

        Flash::success('Stock Receive updated successfully.');

        return redirect(route('stockReceives.index'));
    }

    /**
     * Remove the specified StockReceive from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockReceive = $this->stockReceiveRepository->findWithoutFail($id);

        if (empty($stockReceive)) {
            Flash::error('Stock Receive not found');

            return redirect(route('stockReceives.index'));
        }

        $this->stockReceiveRepository->delete($id);

        Flash::success('Stock Receive deleted successfully.');

        return redirect(route('stockReceives.index'));
    }
}
