<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePerformaTempRequest;
use App\Http\Requests\UpdatePerformaTempRequest;
use App\Repositories\PerformaTempRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PerformaTempController extends AppBaseController
{
    /** @var  PerformaTempRepository */
    private $performaTempRepository;

    public function __construct(PerformaTempRepository $performaTempRepo)
    {
        $this->performaTempRepository = $performaTempRepo;
    }

    /**
     * Display a listing of the PerformaTemp.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->performaTempRepository->pushCriteria(new RequestCriteria($request));
        $performaTemps = $this->performaTempRepository->all();

        return view('performa_temps.index')
            ->with('performaTemps', $performaTemps);
    }

    /**
     * Show the form for creating a new PerformaTemp.
     *
     * @return Response
     */
    public function create()
    {
        return view('performa_temps.create');
    }

    /**
     * Store a newly created PerformaTemp in storage.
     *
     * @param CreatePerformaTempRequest $request
     *
     * @return Response
     */
    public function store(CreatePerformaTempRequest $request)
    {
        $input = $request->all();

        $performaTemp = $this->performaTempRepository->create($input);

        Flash::success('Performa Temp saved successfully.');

        return redirect(route('performaTemps.index'));
    }

    /**
     * Display the specified PerformaTemp.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            Flash::error('Performa Temp not found');

            return redirect(route('performaTemps.index'));
        }

        return view('performa_temps.show')->with('performaTemp', $performaTemp);
    }

    /**
     * Show the form for editing the specified PerformaTemp.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            Flash::error('Performa Temp not found');

            return redirect(route('performaTemps.index'));
        }

        return view('performa_temps.edit')->with('performaTemp', $performaTemp);
    }

    /**
     * Update the specified PerformaTemp in storage.
     *
     * @param  int              $id
     * @param UpdatePerformaTempRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePerformaTempRequest $request)
    {
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            Flash::error('Performa Temp not found');

            return redirect(route('performaTemps.index'));
        }

        $performaTemp = $this->performaTempRepository->update($request->all(), $id);

        Flash::success('Performa Temp updated successfully.');

        return redirect(route('performaTemps.index'));
    }

    /**
     * Remove the specified PerformaTemp from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $performaTemp = $this->performaTempRepository->findWithoutFail($id);

        if (empty($performaTemp)) {
            Flash::error('Performa Temp not found');

            return redirect(route('performaTemps.index'));
        }

        $this->performaTempRepository->delete($id);

        Flash::success('Performa Temp deleted successfully.');

        return redirect(route('performaTemps.index'));
    }
}
