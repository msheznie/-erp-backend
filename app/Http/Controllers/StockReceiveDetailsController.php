<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockReceiveDetailsRequest;
use App\Http\Requests\UpdateStockReceiveDetailsRequest;
use App\Repositories\StockReceiveDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class StockReceiveDetailsController extends AppBaseController
{
    /** @var  StockReceiveDetailsRepository */
    private $stockReceiveDetailsRepository;

    public function __construct(StockReceiveDetailsRepository $stockReceiveDetailsRepo)
    {
        $this->stockReceiveDetailsRepository = $stockReceiveDetailsRepo;
    }

    /**
     * Display a listing of the StockReceiveDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->stockReceiveDetailsRepository->pushCriteria(new RequestCriteria($request));
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->all();

        return view('stock_receive_details.index')
            ->with('stockReceiveDetails', $stockReceiveDetails);
    }

    /**
     * Show the form for creating a new StockReceiveDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('stock_receive_details.create');
    }

    /**
     * Store a newly created StockReceiveDetails in storage.
     *
     * @param CreateStockReceiveDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateStockReceiveDetailsRequest $request)
    {
        $input = $request->all();

        $stockReceiveDetails = $this->stockReceiveDetailsRepository->create($input);

        Flash::success('Stock Receive Details saved successfully.');

        return redirect(route('stockReceiveDetails.index'));
    }

    /**
     * Display the specified StockReceiveDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            Flash::error('Stock Receive Details not found');

            return redirect(route('stockReceiveDetails.index'));
        }

        return view('stock_receive_details.show')->with('stockReceiveDetails', $stockReceiveDetails);
    }

    /**
     * Show the form for editing the specified StockReceiveDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            Flash::error('Stock Receive Details not found');

            return redirect(route('stockReceiveDetails.index'));
        }

        return view('stock_receive_details.edit')->with('stockReceiveDetails', $stockReceiveDetails);
    }

    /**
     * Update the specified StockReceiveDetails in storage.
     *
     * @param  int              $id
     * @param UpdateStockReceiveDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockReceiveDetailsRequest $request)
    {
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            Flash::error('Stock Receive Details not found');

            return redirect(route('stockReceiveDetails.index'));
        }

        $stockReceiveDetails = $this->stockReceiveDetailsRepository->update($request->all(), $id);

        Flash::success('Stock Receive Details updated successfully.');

        return redirect(route('stockReceiveDetails.index'));
    }

    /**
     * Remove the specified StockReceiveDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stockReceiveDetails = $this->stockReceiveDetailsRepository->findWithoutFail($id);

        if (empty($stockReceiveDetails)) {
            Flash::error('Stock Receive Details not found');

            return redirect(route('stockReceiveDetails.index'));
        }

        $this->stockReceiveDetailsRepository->delete($id);

        Flash::success('Stock Receive Details deleted successfully.');

        return redirect(route('stockReceiveDetails.index'));
    }
}
