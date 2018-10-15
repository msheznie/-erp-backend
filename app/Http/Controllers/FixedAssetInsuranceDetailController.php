<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFixedAssetInsuranceDetailRequest;
use App\Http\Requests\UpdateFixedAssetInsuranceDetailRequest;
use App\Repositories\FixedAssetInsuranceDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FixedAssetInsuranceDetailController extends AppBaseController
{
    /** @var  FixedAssetInsuranceDetailRepository */
    private $fixedAssetInsuranceDetailRepository;

    public function __construct(FixedAssetInsuranceDetailRepository $fixedAssetInsuranceDetailRepo)
    {
        $this->fixedAssetInsuranceDetailRepository = $fixedAssetInsuranceDetailRepo;
    }

    /**
     * Display a listing of the FixedAssetInsuranceDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fixedAssetInsuranceDetailRepository->pushCriteria(new RequestCriteria($request));
        $fixedAssetInsuranceDetails = $this->fixedAssetInsuranceDetailRepository->all();

        return view('fixed_asset_insurance_details.index')
            ->with('fixedAssetInsuranceDetails', $fixedAssetInsuranceDetails);
    }

    /**
     * Show the form for creating a new FixedAssetInsuranceDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('fixed_asset_insurance_details.create');
    }

    /**
     * Store a newly created FixedAssetInsuranceDetail in storage.
     *
     * @param CreateFixedAssetInsuranceDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateFixedAssetInsuranceDetailRequest $request)
    {
        $input = $request->all();

        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->create($input);

        Flash::success('Fixed Asset Insurance Detail saved successfully.');

        return redirect(route('fixedAssetInsuranceDetails.index'));
    }

    /**
     * Display the specified FixedAssetInsuranceDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            Flash::error('Fixed Asset Insurance Detail not found');

            return redirect(route('fixedAssetInsuranceDetails.index'));
        }

        return view('fixed_asset_insurance_details.show')->with('fixedAssetInsuranceDetail', $fixedAssetInsuranceDetail);
    }

    /**
     * Show the form for editing the specified FixedAssetInsuranceDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            Flash::error('Fixed Asset Insurance Detail not found');

            return redirect(route('fixedAssetInsuranceDetails.index'));
        }

        return view('fixed_asset_insurance_details.edit')->with('fixedAssetInsuranceDetail', $fixedAssetInsuranceDetail);
    }

    /**
     * Update the specified FixedAssetInsuranceDetail in storage.
     *
     * @param  int              $id
     * @param UpdateFixedAssetInsuranceDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFixedAssetInsuranceDetailRequest $request)
    {
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            Flash::error('Fixed Asset Insurance Detail not found');

            return redirect(route('fixedAssetInsuranceDetails.index'));
        }

        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->update($request->all(), $id);

        Flash::success('Fixed Asset Insurance Detail updated successfully.');

        return redirect(route('fixedAssetInsuranceDetails.index'));
    }

    /**
     * Remove the specified FixedAssetInsuranceDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fixedAssetInsuranceDetail = $this->fixedAssetInsuranceDetailRepository->findWithoutFail($id);

        if (empty($fixedAssetInsuranceDetail)) {
            Flash::error('Fixed Asset Insurance Detail not found');

            return redirect(route('fixedAssetInsuranceDetails.index'));
        }

        $this->fixedAssetInsuranceDetailRepository->delete($id);

        Flash::success('Fixed Asset Insurance Detail deleted successfully.');

        return redirect(route('fixedAssetInsuranceDetails.index'));
    }
}
