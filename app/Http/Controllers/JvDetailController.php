<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJvDetailRequest;
use App\Http\Requests\UpdateJvDetailRequest;
use App\Repositories\JvDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class JvDetailController extends AppBaseController
{
    /** @var  JvDetailRepository */
    private $jvDetailRepository;

    public function __construct(JvDetailRepository $jvDetailRepo)
    {
        $this->jvDetailRepository = $jvDetailRepo;
    }

    /**
     * Display a listing of the JvDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->jvDetailRepository->pushCriteria(new RequestCriteria($request));
        $jvDetails = $this->jvDetailRepository->all();

        return view('jv_details.index')
            ->with('jvDetails', $jvDetails);
    }

    /**
     * Show the form for creating a new JvDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('jv_details.create');
    }

    /**
     * Store a newly created JvDetail in storage.
     *
     * @param CreateJvDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateJvDetailRequest $request)
    {
        $input = $request->all();

        $jvDetail = $this->jvDetailRepository->create($input);

        Flash::success('Jv Detail saved successfully.');

        return redirect(route('jvDetails.index'));
    }

    /**
     * Display the specified JvDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            Flash::error('Jv Detail not found');

            return redirect(route('jvDetails.index'));
        }

        return view('jv_details.show')->with('jvDetail', $jvDetail);
    }

    /**
     * Show the form for editing the specified JvDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            Flash::error('Jv Detail not found');

            return redirect(route('jvDetails.index'));
        }

        return view('jv_details.edit')->with('jvDetail', $jvDetail);
    }

    /**
     * Update the specified JvDetail in storage.
     *
     * @param  int              $id
     * @param UpdateJvDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateJvDetailRequest $request)
    {
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            Flash::error('Jv Detail not found');

            return redirect(route('jvDetails.index'));
        }

        $jvDetail = $this->jvDetailRepository->update($request->all(), $id);

        Flash::success('Jv Detail updated successfully.');

        return redirect(route('jvDetails.index'));
    }

    /**
     * Remove the specified JvDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $jvDetail = $this->jvDetailRepository->findWithoutFail($id);

        if (empty($jvDetail)) {
            Flash::error('Jv Detail not found');

            return redirect(route('jvDetails.index'));
        }

        $this->jvDetailRepository->delete($id);

        Flash::success('Jv Detail deleted successfully.');

        return redirect(route('jvDetails.index'));
    }
}
