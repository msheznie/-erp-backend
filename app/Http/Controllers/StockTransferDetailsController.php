<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockTransferDetailsRequest;
use App\Http\Requests\UpdateStockTransferDetailsRequest;
use App\Repositories\StockTransferDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockTransferDetailsController extends AppBaseController
{
    /** @var  StockTransferDetailsRepository */
    private $stockTransferDetailsRepository;

    public function __construct(StockTransferDetailsRepository $stockTransferDetailsRepo)
    {
        $this->stockTransferDetailsRepository = $stockTransferDetailsRepo;
    }

    /**
     * Display a listing of the StockTransferDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockTransferDetailsRepository->pushCriteria(new RequestCriteria($request));
        $stockTransferDetails = $this->stockTransferDetailsRepository->all();

        return view('stock_transfer_details.index')
            ->with('stockTransferDetails', $stockTransferDetails);
    }

    /**
     * Show the form for creating a new StockTransferDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_transfer_details.create');
    }

    /**
     * Store a newly created StockTransferDetails in storage.
     *
     * @param CreateStockTransferDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateStockTransferDetailsRequest $request)
    {
        $input = $request->all();

        $stockTransferDetails = $this->stockTransferDetailsRepository->create($input);

        Flash::success('Stock Transfer Details saved successfully.');

        return redirect(route('stockTransferDetails.index'));
    }

    /**
     * Display the specified StockTransferDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            Flash::error('Stock Transfer Details not found');

            return redirect(route('stockTransferDetails.index'));
        }

        return view('stock_transfer_details.show')->with('stockTransferDetails', $stockTransferDetails);
    }

    /**
     * Show the form for editing the specified StockTransferDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            Flash::error('Stock Transfer Details not found');

            return redirect(route('stockTransferDetails.index'));
        }

        return view('stock_transfer_details.edit')->with('stockTransferDetails', $stockTransferDetails);
    }

    /**
     * Update the specified StockTransferDetails in storage.
     *
     * @param  int              $id
     * @param UpdateStockTransferDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockTransferDetailsRequest $request)
    {
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            Flash::error('Stock Transfer Details not found');

            return redirect(route('stockTransferDetails.index'));
        }

        $stockTransferDetails = $this->stockTransferDetailsRepository->update($request->all(), $id);

        Flash::success('Stock Transfer Details updated successfully.');

        return redirect(route('stockTransferDetails.index'));
    }

    /**
     * Remove the specified StockTransferDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            Flash::error('Stock Transfer Details not found');

            return redirect(route('stockTransferDetails.index'));
        }

        $this->stockTransferDetailsRepository->delete($id);

        Flash::success('Stock Transfer Details deleted successfully.');

        return redirect(route('stockTransferDetails.index'));
    }
}
