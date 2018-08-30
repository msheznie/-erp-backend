<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUnbilledGRVRequest;
use App\Http\Requests\UpdateUnbilledGRVRequest;
use App\Repositories\UnbilledGRVRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UnbilledGRVController extends AppBaseController
{
    /** @var  UnbilledGRVRepository */
    private $unbilledGRVRepository;

    public function __construct(UnbilledGRVRepository $unbilledGRVRepo)
    {
        $this->unbilledGRVRepository = $unbilledGRVRepo;
    }

    /**
     * Display a listing of the UnbilledGRV.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unbilledGRVRepository->pushCriteria(new RequestCriteria($request));
        $unbilledGRVs = $this->unbilledGRVRepository->all();

        return view('unbilled_g_r_vs.index')
            ->with('unbilledGRVs', $unbilledGRVs);
    }

    /**
     * Show the form for creating a new UnbilledGRV.
     *
     * @return Response
     */
    public function create()
    {
        return view('unbilled_g_r_vs.create');
    }

    /**
     * Store a newly created UnbilledGRV in storage.
     *
     * @param CreateUnbilledGRVRequest $request
     *
     * @return Response
     */
    public function store(CreateUnbilledGRVRequest $request)
    {
        $input = $request->all();

        $unbilledGRV = $this->unbilledGRVRepository->create($input);

        Flash::success('Unbilled G R V saved successfully.');

        return redirect(route('unbilledGRVs.index'));
    }

    /**
     * Display the specified UnbilledGRV.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            Flash::error('Unbilled G R V not found');

            return redirect(route('unbilledGRVs.index'));
        }

        return view('unbilled_g_r_vs.show')->with('unbilledGRV', $unbilledGRV);
    }

    /**
     * Show the form for editing the specified UnbilledGRV.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            Flash::error('Unbilled G R V not found');

            return redirect(route('unbilledGRVs.index'));
        }

        return view('unbilled_g_r_vs.edit')->with('unbilledGRV', $unbilledGRV);
    }

    /**
     * Update the specified UnbilledGRV in storage.
     *
     * @param  int              $id
     * @param UpdateUnbilledGRVRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnbilledGRVRequest $request)
    {
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            Flash::error('Unbilled G R V not found');

            return redirect(route('unbilledGRVs.index'));
        }

        $unbilledGRV = $this->unbilledGRVRepository->update($request->all(), $id);

        Flash::success('Unbilled G R V updated successfully.');

        return redirect(route('unbilledGRVs.index'));
    }

    /**
     * Remove the specified UnbilledGRV from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $unbilledGRV = $this->unbilledGRVRepository->findWithoutFail($id);

        if (empty($unbilledGRV)) {
            Flash::error('Unbilled G R V not found');

            return redirect(route('unbilledGRVs.index'));
        }

        $this->unbilledGRVRepository->delete($id);

        Flash::success('Unbilled G R V deleted successfully.');

        return redirect(route('unbilledGRVs.index'));
    }
}
