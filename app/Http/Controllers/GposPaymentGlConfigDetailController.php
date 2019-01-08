<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGposPaymentGlConfigDetailRequest;
use App\Http\Requests\UpdateGposPaymentGlConfigDetailRequest;
use App\Repositories\GposPaymentGlConfigDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GposPaymentGlConfigDetailController extends AppBaseController
{
    /** @var  GposPaymentGlConfigDetailRepository */
    private $gposPaymentGlConfigDetailRepository;

    public function __construct(GposPaymentGlConfigDetailRepository $gposPaymentGlConfigDetailRepo)
    {
        $this->gposPaymentGlConfigDetailRepository = $gposPaymentGlConfigDetailRepo;
    }

    /**
     * Display a listing of the GposPaymentGlConfigDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gposPaymentGlConfigDetailRepository->pushCriteria(new RequestCriteria($request));
        $gposPaymentGlConfigDetails = $this->gposPaymentGlConfigDetailRepository->all();

        return view('gpos_payment_gl_config_details.index')
            ->with('gposPaymentGlConfigDetails', $gposPaymentGlConfigDetails);
    }

    /**
     * Show the form for creating a new GposPaymentGlConfigDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('gpos_payment_gl_config_details.create');
    }

    /**
     * Store a newly created GposPaymentGlConfigDetail in storage.
     *
     * @param CreateGposPaymentGlConfigDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateGposPaymentGlConfigDetailRequest $request)
    {
        $input = $request->all();

        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->create($input);

        Flash::success('Gpos Payment Gl Config Detail saved successfully.');

        return redirect(route('gposPaymentGlConfigDetails.index'));
    }

    /**
     * Display the specified GposPaymentGlConfigDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            Flash::error('Gpos Payment Gl Config Detail not found');

            return redirect(route('gposPaymentGlConfigDetails.index'));
        }

        return view('gpos_payment_gl_config_details.show')->with('gposPaymentGlConfigDetail', $gposPaymentGlConfigDetail);
    }

    /**
     * Show the form for editing the specified GposPaymentGlConfigDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            Flash::error('Gpos Payment Gl Config Detail not found');

            return redirect(route('gposPaymentGlConfigDetails.index'));
        }

        return view('gpos_payment_gl_config_details.edit')->with('gposPaymentGlConfigDetail', $gposPaymentGlConfigDetail);
    }

    /**
     * Update the specified GposPaymentGlConfigDetail in storage.
     *
     * @param  int              $id
     * @param UpdateGposPaymentGlConfigDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGposPaymentGlConfigDetailRequest $request)
    {
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            Flash::error('Gpos Payment Gl Config Detail not found');

            return redirect(route('gposPaymentGlConfigDetails.index'));
        }

        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->update($request->all(), $id);

        Flash::success('Gpos Payment Gl Config Detail updated successfully.');

        return redirect(route('gposPaymentGlConfigDetails.index'));
    }

    /**
     * Remove the specified GposPaymentGlConfigDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gposPaymentGlConfigDetail = $this->gposPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigDetail)) {
            Flash::error('Gpos Payment Gl Config Detail not found');

            return redirect(route('gposPaymentGlConfigDetails.index'));
        }

        $this->gposPaymentGlConfigDetailRepository->delete($id);

        Flash::success('Gpos Payment Gl Config Detail deleted successfully.');

        return redirect(route('gposPaymentGlConfigDetails.index'));
    }
}
