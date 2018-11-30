<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockReceiveDetailsRefferedBackRequest;
use App\Http\Requests\UpdateStockReceiveDetailsRefferedBackRequest;
use App\Repositories\StockReceiveDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockReceiveDetailsRefferedBackController extends AppBaseController
{
    /** @var  StockReceiveDetailsRefferedBackRepository */
    private $stockReceiveDetailsRefferedBackRepository;

    public function __construct(StockReceiveDetailsRefferedBackRepository $stockReceiveDetailsRefferedBackRepo)
    {
        $this->stockReceiveDetailsRefferedBackRepository = $stockReceiveDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the StockReceiveDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockReceiveDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $stockReceiveDetailsRefferedBacks = $this->stockReceiveDetailsRefferedBackRepository->all();

        return view('stock_receive_details_reffered_backs.index')
            ->with('stockReceiveDetailsRefferedBacks', $stockReceiveDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new StockReceiveDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_receive_details_reffered_backs.create');
    }

    /**
     * Store a newly created StockReceiveDetailsRefferedBack in storage.
     *
     * @param CreateStockReceiveDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateStockReceiveDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->create($input);

        Flash::success('Stock Receive Details Reffered Back saved successfully.');

        return redirect(route('stockReceiveDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified StockReceiveDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            Flash::error('Stock Receive Details Reffered Back not found');

            return redirect(route('stockReceiveDetailsRefferedBacks.index'));
        }

        return view('stock_receive_details_reffered_backs.show')->with('stockReceiveDetailsRefferedBack', $stockReceiveDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified StockReceiveDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            Flash::error('Stock Receive Details Reffered Back not found');

            return redirect(route('stockReceiveDetailsRefferedBacks.index'));
        }

        return view('stock_receive_details_reffered_backs.edit')->with('stockReceiveDetailsRefferedBack', $stockReceiveDetailsRefferedBack);
    }

    /**
     * Update the specified StockReceiveDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateStockReceiveDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockReceiveDetailsRefferedBackRequest $request)
    {
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            Flash::error('Stock Receive Details Reffered Back not found');

            return redirect(route('stockReceiveDetailsRefferedBacks.index'));
        }

        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Stock Receive Details Reffered Back updated successfully.');

        return redirect(route('stockReceiveDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified StockReceiveDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockReceiveDetailsRefferedBack)) {
            Flash::error('Stock Receive Details Reffered Back not found');

            return redirect(route('stockReceiveDetailsRefferedBacks.index'));
        }

        $this->stockReceiveDetailsRefferedBackRepository->delete($id);

        Flash::success('Stock Receive Details Reffered Back deleted successfully.');

        return redirect(route('stockReceiveDetailsRefferedBacks.index'));
    }
}
