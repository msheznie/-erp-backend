<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePricingScheduleDetailRequest;
use App\Http\Requests\UpdatePricingScheduleDetailRequest;
use App\Repositories\PricingScheduleDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PricingScheduleDetailController extends AppBaseController
{
    /** @var  PricingScheduleDetailRepository */
    private $pricingScheduleDetailRepository;

    public function __construct(PricingScheduleDetailRepository $pricingScheduleDetailRepo)
    {
        $this->pricingScheduleDetailRepository = $pricingScheduleDetailRepo;
    }

    /**
     * Display a listing of the PricingScheduleDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->pricingScheduleDetailRepository->pushCriteria(new RequestCriteria($request));
        $pricingScheduleDetails = $this->pricingScheduleDetailRepository->all();

        return view('pricing_schedule_details.index')
            ->with('pricingScheduleDetails', $pricingScheduleDetails);
    }

    /**
     * Show the form for creating a new PricingScheduleDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('pricing_schedule_details.create');
    }

    /**
     * Store a newly created PricingScheduleDetail in storage.
     *
     * @param CreatePricingScheduleDetailRequest $request
     *
     * @return Response
     */
    public function store(CreatePricingScheduleDetailRequest $request)
    {
        $input = $request->all();

        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->create($input);

        Flash::success('Pricing Schedule Detail saved successfully.');

        return redirect(route('pricingScheduleDetails.index'));
    }

    /**
     * Display the specified PricingScheduleDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            Flash::error('Pricing Schedule Detail not found');

            return redirect(route('pricingScheduleDetails.index'));
        }

        return view('pricing_schedule_details.show')->with('pricingScheduleDetail', $pricingScheduleDetail);
    }

    /**
     * Show the form for editing the specified PricingScheduleDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            Flash::error('Pricing Schedule Detail not found');

            return redirect(route('pricingScheduleDetails.index'));
        }

        return view('pricing_schedule_details.edit')->with('pricingScheduleDetail', $pricingScheduleDetail);
    }

    /**
     * Update the specified PricingScheduleDetail in storage.
     *
     * @param  int              $id
     * @param UpdatePricingScheduleDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePricingScheduleDetailRequest $request)
    {
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            Flash::error('Pricing Schedule Detail not found');

            return redirect(route('pricingScheduleDetails.index'));
        }

        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->update($request->all(), $id);

        Flash::success('Pricing Schedule Detail updated successfully.');

        return redirect(route('pricingScheduleDetails.index'));
    }

    /**
     * Remove the specified PricingScheduleDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $pricingScheduleDetail = $this->pricingScheduleDetailRepository->findWithoutFail($id);

        if (empty($pricingScheduleDetail)) {
            Flash::error('Pricing Schedule Detail not found');

            return redirect(route('pricingScheduleDetails.index'));
        }

        $this->pricingScheduleDetailRepository->delete($id);

        Flash::success('Pricing Schedule Detail deleted successfully.');

        return redirect(route('pricingScheduleDetails.index'));
    }
}
