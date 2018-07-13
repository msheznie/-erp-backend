<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockTransferRequest;
use App\Http\Requests\UpdateStockTransferRequest;
use App\Repositories\StockTransferRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockTransferController extends AppBaseController
{
    /** @var  StockTransferRepository */
    private $stockTransferRepository;

    public function __construct(StockTransferRepository $stockTransferRepo)
    {
        $this->stockTransferRepository = $stockTransferRepo;
    }

    /**
     * Display a listing of the StockTransfer.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockTransferRepository->pushCriteria(new RequestCriteria($request));
        $stockTransfers = $this->stockTransferRepository->all();

        return view('stock_transfers.index')
            ->with('stockTransfers', $stockTransfers);
    }

    /**
     * Show the form for creating a new StockTransfer.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_transfers.create');
    }

    /**
     * Store a newly created StockTransfer in storage.
     *
     * @param CreateStockTransferRequest $request
     *
     * @return Response
     */
    public function store(CreateStockTransferRequest $request)
    {
        $input = $request->all();

        $stockTransfer = $this->stockTransferRepository->create($input);

        Flash::success('Stock Transfer saved successfully.');

        return redirect(route('stockTransfers.index'));
    }

    /**
     * Display the specified StockTransfer.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            Flash::error('Stock Transfer not found');

            return redirect(route('stockTransfers.index'));
        }

        return view('stock_transfers.show')->with('stockTransfer', $stockTransfer);
    }

    /**
     * Show the form for editing the specified StockTransfer.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            Flash::error('Stock Transfer not found');

            return redirect(route('stockTransfers.index'));
        }

        return view('stock_transfers.edit')->with('stockTransfer', $stockTransfer);
    }

    /**
     * Update the specified StockTransfer in storage.
     *
     * @param  int              $id
     * @param UpdateStockTransferRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockTransferRequest $request)
    {
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            Flash::error('Stock Transfer not found');

            return redirect(route('stockTransfers.index'));
        }

        $stockTransfer = $this->stockTransferRepository->update($request->all(), $id);

        Flash::success('Stock Transfer updated successfully.');

        return redirect(route('stockTransfers.index'));
    }

    /**
     * Remove the specified StockTransfer from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockTransfer = $this->stockTransferRepository->findWithoutFail($id);

        if (empty($stockTransfer)) {
            Flash::error('Stock Transfer not found');

            return redirect(route('stockTransfers.index'));
        }

        $this->stockTransferRepository->delete($id);

        Flash::success('Stock Transfer deleted successfully.');

        return redirect(route('stockTransfers.index'));
    }
}
