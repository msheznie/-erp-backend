<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockTransferRefferedBackRequest;
use App\Http\Requests\UpdateStockTransferRefferedBackRequest;
use App\Repositories\StockTransferRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockTransferRefferedBackController extends AppBaseController
{
    /** @var  StockTransferRefferedBackRepository */
    private $stockTransferRefferedBackRepository;

    public function __construct(StockTransferRefferedBackRepository $stockTransferRefferedBackRepo)
    {
        $this->stockTransferRefferedBackRepository = $stockTransferRefferedBackRepo;
    }

    /**
     * Display a listing of the StockTransferRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockTransferRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $stockTransferRefferedBacks = $this->stockTransferRefferedBackRepository->all();

        return view('stock_transfer_reffered_backs.index')
            ->with('stockTransferRefferedBacks', $stockTransferRefferedBacks);
    }

    /**
     * Show the form for creating a new StockTransferRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_transfer_reffered_backs.create');
    }

    /**
     * Store a newly created StockTransferRefferedBack in storage.
     *
     * @param CreateStockTransferRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateStockTransferRefferedBackRequest $request)
    {
        $input = $request->all();

        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->create($input);

        Flash::success('Stock Transfer Reffered Back saved successfully.');

        return redirect(route('stockTransferRefferedBacks.index'));
    }

    /**
     * Display the specified StockTransferRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            Flash::error('Stock Transfer Reffered Back not found');

            return redirect(route('stockTransferRefferedBacks.index'));
        }

        return view('stock_transfer_reffered_backs.show')->with('stockTransferRefferedBack', $stockTransferRefferedBack);
    }

    /**
     * Show the form for editing the specified StockTransferRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            Flash::error('Stock Transfer Reffered Back not found');

            return redirect(route('stockTransferRefferedBacks.index'));
        }

        return view('stock_transfer_reffered_backs.edit')->with('stockTransferRefferedBack', $stockTransferRefferedBack);
    }

    /**
     * Update the specified StockTransferRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateStockTransferRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockTransferRefferedBackRequest $request)
    {
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            Flash::error('Stock Transfer Reffered Back not found');

            return redirect(route('stockTransferRefferedBacks.index'));
        }

        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->update($request->all(), $id);

        Flash::success('Stock Transfer Reffered Back updated successfully.');

        return redirect(route('stockTransferRefferedBacks.index'));
    }

    /**
     * Remove the specified StockTransferRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockTransferRefferedBack = $this->stockTransferRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferRefferedBack)) {
            Flash::error('Stock Transfer Reffered Back not found');

            return redirect(route('stockTransferRefferedBacks.index'));
        }

        $this->stockTransferRefferedBackRepository->delete($id);

        Flash::success('Stock Transfer Reffered Back deleted successfully.');

        return redirect(route('stockTransferRefferedBacks.index'));
    }
}
