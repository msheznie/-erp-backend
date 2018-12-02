<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockReceiveRefferedBackRequest;
use App\Http\Requests\UpdateStockReceiveRefferedBackRequest;
use App\Repositories\StockReceiveRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockReceiveRefferedBackController extends AppBaseController
{
    /** @var  StockReceiveRefferedBackRepository */
    private $stockReceiveRefferedBackRepository;

    public function __construct(StockReceiveRefferedBackRepository $stockReceiveRefferedBackRepo)
    {
        $this->stockReceiveRefferedBackRepository = $stockReceiveRefferedBackRepo;
    }

    /**
     * Display a listing of the StockReceiveRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockReceiveRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $stockReceiveRefferedBacks = $this->stockReceiveRefferedBackRepository->all();

        return view('stock_receive_reffered_backs.index')
            ->with('stockReceiveRefferedBacks', $stockReceiveRefferedBacks);
    }

    /**
     * Show the form for creating a new StockReceiveRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_receive_reffered_backs.create');
    }

    /**
     * Store a newly created StockReceiveRefferedBack in storage.
     *
     * @param CreateStockReceiveRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateStockReceiveRefferedBackRequest $request)
    {
        $input = $request->all();

        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->create($input);

        Flash::success('Stock Receive Reffered Back saved successfully.');

        return redirect(route('stockReceiveRefferedBacks.index'));
    }

    /**
     * Display the specified StockReceiveRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            Flash::error('Stock Receive Reffered Back not found');

            return redirect(route('stockReceiveRefferedBacks.index'));
        }

        return view('stock_receive_reffered_backs.show')->with('stockReceiveRefferedBack', $stockReceiveRefferedBack);
    }

    /**
     * Show the form for editing the specified StockReceiveRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            Flash::error('Stock Receive Reffered Back not found');

            return redirect(route('stockReceiveRefferedBacks.index'));
        }

        return view('stock_receive_reffered_backs.edit')->with('stockReceiveRefferedBack', $stockReceiveRefferedBack);
    }

    /**
     * Update the specified StockReceiveRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateStockReceiveRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockReceiveRefferedBackRequest $request)
    {
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            Flash::error('Stock Receive Reffered Back not found');

            return redirect(route('stockReceiveRefferedBacks.index'));
        }

        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->update($request->all(), $id);

        Flash::success('Stock Receive Reffered Back updated successfully.');

        return redirect(route('stockReceiveRefferedBacks.index'));
    }

    /**
     * Remove the specified StockReceiveRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockReceiveRefferedBack = $this->stockReceiveRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveRefferedBack)) {
            Flash::error('Stock Receive Reffered Back not found');

            return redirect(route('stockReceiveRefferedBacks.index'));
        }

        $this->stockReceiveRefferedBackRepository->delete($id);

        Flash::success('Stock Receive Reffered Back deleted successfully.');

        return redirect(route('stockReceiveRefferedBacks.index'));
    }
}
