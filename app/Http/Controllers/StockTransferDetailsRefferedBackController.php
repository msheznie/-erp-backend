<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockTransferDetailsRefferedBackRequest;
use App\Http\Requests\UpdateStockTransferDetailsRefferedBackRequest;
use App\Repositories\StockTransferDetailsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockTransferDetailsRefferedBackController extends AppBaseController
{
    /** @var  StockTransferDetailsRefferedBackRepository */
    private $stockTransferDetailsRefferedBackRepository;

    public function __construct(StockTransferDetailsRefferedBackRepository $stockTransferDetailsRefferedBackRepo)
    {
        $this->stockTransferDetailsRefferedBackRepository = $stockTransferDetailsRefferedBackRepo;
    }

    /**
     * Display a listing of the StockTransferDetailsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockTransferDetailsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $stockTransferDetailsRefferedBacks = $this->stockTransferDetailsRefferedBackRepository->all();

        return view('stock_transfer_details_reffered_backs.index')
            ->with('stockTransferDetailsRefferedBacks', $stockTransferDetailsRefferedBacks);
    }

    /**
     * Show the form for creating a new StockTransferDetailsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_transfer_details_reffered_backs.create');
    }

    /**
     * Store a newly created StockTransferDetailsRefferedBack in storage.
     *
     * @param CreateStockTransferDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateStockTransferDetailsRefferedBackRequest $request)
    {
        $input = $request->all();

        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->create($input);

        Flash::success('Stock Transfer Details Reffered Back saved successfully.');

        return redirect(route('stockTransferDetailsRefferedBacks.index'));
    }

    /**
     * Display the specified StockTransferDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            Flash::error('Stock Transfer Details Reffered Back not found');

            return redirect(route('stockTransferDetailsRefferedBacks.index'));
        }

        return view('stock_transfer_details_reffered_backs.show')->with('stockTransferDetailsRefferedBack', $stockTransferDetailsRefferedBack);
    }

    /**
     * Show the form for editing the specified StockTransferDetailsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            Flash::error('Stock Transfer Details Reffered Back not found');

            return redirect(route('stockTransferDetailsRefferedBacks.index'));
        }

        return view('stock_transfer_details_reffered_backs.edit')->with('stockTransferDetailsRefferedBack', $stockTransferDetailsRefferedBack);
    }

    /**
     * Update the specified StockTransferDetailsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateStockTransferDetailsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockTransferDetailsRefferedBackRequest $request)
    {
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            Flash::error('Stock Transfer Details Reffered Back not found');

            return redirect(route('stockTransferDetailsRefferedBacks.index'));
        }

        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Stock Transfer Details Reffered Back updated successfully.');

        return redirect(route('stockTransferDetailsRefferedBacks.index'));
    }

    /**
     * Remove the specified StockTransferDetailsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepository->findWithoutFail($id);

        if (empty($stockTransferDetailsRefferedBack)) {
            Flash::error('Stock Transfer Details Reffered Back not found');

            return redirect(route('stockTransferDetailsRefferedBacks.index'));
        }

        $this->stockTransferDetailsRefferedBackRepository->delete($id);

        Flash::success('Stock Transfer Details Reffered Back deleted successfully.');

        return redirect(route('stockTransferDetailsRefferedBacks.index'));
    }
}
