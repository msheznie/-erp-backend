<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDirectReceiptDetailRequest;
use App\Http\Requests\UpdateDirectReceiptDetailRequest;
use App\Repositories\DirectReceiptDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DirectReceiptDetailController extends AppBaseController
{
    /** @var  DirectReceiptDetailRepository */
    private $directReceiptDetailRepository;

    public function __construct(DirectReceiptDetailRepository $directReceiptDetailRepo)
    {
        $this->directReceiptDetailRepository = $directReceiptDetailRepo;
    }

    /**
     * Display a listing of the DirectReceiptDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->directReceiptDetailRepository->pushCriteria(new RequestCriteria($request));
        $directReceiptDetails = $this->directReceiptDetailRepository->all();

        return view('direct_receipt_details.index')
            ->with('directReceiptDetails', $directReceiptDetails);
    }

    /**
     * Show the form for creating a new DirectReceiptDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('direct_receipt_details.create');
    }

    /**
     * Store a newly created DirectReceiptDetail in storage.
     *
     * @param CreateDirectReceiptDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateDirectReceiptDetailRequest $request)
    {
        $input = $request->all();

        $directReceiptDetail = $this->directReceiptDetailRepository->create($input);

        Flash::success('Direct Receipt Detail saved successfully.');

        return redirect(route('directReceiptDetails.index'));
    }

    /**
     * Display the specified DirectReceiptDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            Flash::error('Direct Receipt Detail not found');

            return redirect(route('directReceiptDetails.index'));
        }

        return view('direct_receipt_details.show')->with('directReceiptDetail', $directReceiptDetail);
    }

    /**
     * Show the form for editing the specified DirectReceiptDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            Flash::error('Direct Receipt Detail not found');

            return redirect(route('directReceiptDetails.index'));
        }

        return view('direct_receipt_details.edit')->with('directReceiptDetail', $directReceiptDetail);
    }

    /**
     * Update the specified DirectReceiptDetail in storage.
     *
     * @param  int              $id
     * @param UpdateDirectReceiptDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDirectReceiptDetailRequest $request)
    {
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            Flash::error('Direct Receipt Detail not found');

            return redirect(route('directReceiptDetails.index'));
        }

        $directReceiptDetail = $this->directReceiptDetailRepository->update($request->all(), $id);

        Flash::success('Direct Receipt Detail updated successfully.');

        return redirect(route('directReceiptDetails.index'));
    }

    /**
     * Remove the specified DirectReceiptDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $directReceiptDetail = $this->directReceiptDetailRepository->findWithoutFail($id);

        if (empty($directReceiptDetail)) {
            Flash::error('Direct Receipt Detail not found');

            return redirect(route('directReceiptDetails.index'));
        }

        $this->directReceiptDetailRepository->delete($id);

        Flash::success('Direct Receipt Detail deleted successfully.');

        return redirect(route('directReceiptDetails.index'));
    }
}
