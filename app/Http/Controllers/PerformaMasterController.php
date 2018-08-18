<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePerformaMasterRequest;
use App\Http\Requests\UpdatePerformaMasterRequest;
use App\Repositories\PerformaMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PerformaMasterController extends AppBaseController
{
    /** @var  PerformaMasterRepository */
    private $performaMasterRepository;

    public function __construct(PerformaMasterRepository $performaMasterRepo)
    {
        $this->performaMasterRepository = $performaMasterRepo;
    }

    /**
     * Display a listing of the PerformaMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->performaMasterRepository->pushCriteria(new RequestCriteria($request));
        $performaMasters = $this->performaMasterRepository->all();

        return view('performa_masters.index')
            ->with('performaMasters', $performaMasters);
    }

    /**
     * Show the form for creating a new PerformaMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('performa_masters.create');
    }

    /**
     * Store a newly created PerformaMaster in storage.
     *
     * @param CreatePerformaMasterRequest $request
     *
     * @return Response
     */
    public function store(CreatePerformaMasterRequest $request)
    {
        $input = $request->all();

        $performaMaster = $this->performaMasterRepository->create($input);

        Flash::success('Performa Master saved successfully.');

        return redirect(route('performaMasters.index'));
    }

    /**
     * Display the specified PerformaMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            Flash::error('Performa Master not found');

            return redirect(route('performaMasters.index'));
        }

        return view('performa_masters.show')->with('performaMaster', $performaMaster);
    }

    /**
     * Show the form for editing the specified PerformaMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            Flash::error('Performa Master not found');

            return redirect(route('performaMasters.index'));
        }

        return view('performa_masters.edit')->with('performaMaster', $performaMaster);
    }

    /**
     * Update the specified PerformaMaster in storage.
     *
     * @param  int              $id
     * @param UpdatePerformaMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePerformaMasterRequest $request)
    {
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            Flash::error('Performa Master not found');

            return redirect(route('performaMasters.index'));
        }

        $performaMaster = $this->performaMasterRepository->update($request->all(), $id);

        Flash::success('Performa Master updated successfully.');

        return redirect(route('performaMasters.index'));
    }

    /**
     * Remove the specified PerformaMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $performaMaster = $this->performaMasterRepository->findWithoutFail($id);

        if (empty($performaMaster)) {
            Flash::error('Performa Master not found');

            return redirect(route('performaMasters.index'));
        }

        $this->performaMasterRepository->delete($id);

        Flash::success('Performa Master deleted successfully.');

        return redirect(route('performaMasters.index'));
    }
}
