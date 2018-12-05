<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJvDetailsReferredbackRequest;
use App\Http\Requests\UpdateJvDetailsReferredbackRequest;
use App\Repositories\JvDetailsReferredbackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class JvDetailsReferredbackController extends AppBaseController
{
    /** @var  JvDetailsReferredbackRepository */
    private $jvDetailsReferredbackRepository;

    public function __construct(JvDetailsReferredbackRepository $jvDetailsReferredbackRepo)
    {
        $this->jvDetailsReferredbackRepository = $jvDetailsReferredbackRepo;
    }

    /**
     * Display a listing of the JvDetailsReferredback.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->jvDetailsReferredbackRepository->pushCriteria(new RequestCriteria($request));
        $jvDetailsReferredbacks = $this->jvDetailsReferredbackRepository->all();

        return view('jv_details_referredbacks.index')
            ->with('jvDetailsReferredbacks', $jvDetailsReferredbacks);
    }

    /**
     * Show the form for creating a new JvDetailsReferredback.
     *
     * @return Response
     */
    public function create()
    {
        return view('jv_details_referredbacks.create');
    }

    /**
     * Store a newly created JvDetailsReferredback in storage.
     *
     * @param CreateJvDetailsReferredbackRequest $request
     *
     * @return Response
     */
    public function store(CreateJvDetailsReferredbackRequest $request)
    {
        $input = $request->all();

        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->create($input);

        Flash::success('Jv Details Referredback saved successfully.');

        return redirect(route('jvDetailsReferredbacks.index'));
    }

    /**
     * Display the specified JvDetailsReferredback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            Flash::error('Jv Details Referredback not found');

            return redirect(route('jvDetailsReferredbacks.index'));
        }

        return view('jv_details_referredbacks.show')->with('jvDetailsReferredback', $jvDetailsReferredback);
    }

    /**
     * Show the form for editing the specified JvDetailsReferredback.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            Flash::error('Jv Details Referredback not found');

            return redirect(route('jvDetailsReferredbacks.index'));
        }

        return view('jv_details_referredbacks.edit')->with('jvDetailsReferredback', $jvDetailsReferredback);
    }

    /**
     * Update the specified JvDetailsReferredback in storage.
     *
     * @param  int              $id
     * @param UpdateJvDetailsReferredbackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateJvDetailsReferredbackRequest $request)
    {
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            Flash::error('Jv Details Referredback not found');

            return redirect(route('jvDetailsReferredbacks.index'));
        }

        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->update($request->all(), $id);

        Flash::success('Jv Details Referredback updated successfully.');

        return redirect(route('jvDetailsReferredbacks.index'));
    }

    /**
     * Remove the specified JvDetailsReferredback from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $jvDetailsReferredback = $this->jvDetailsReferredbackRepository->findWithoutFail($id);

        if (empty($jvDetailsReferredback)) {
            Flash::error('Jv Details Referredback not found');

            return redirect(route('jvDetailsReferredbacks.index'));
        }

        $this->jvDetailsReferredbackRepository->delete($id);

        Flash::success('Jv Details Referredback deleted successfully.');

        return redirect(route('jvDetailsReferredbacks.index'));
    }
}
