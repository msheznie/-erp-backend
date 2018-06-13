<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGRVTypesRequest;
use App\Http\Requests\UpdateGRVTypesRequest;
use App\Repositories\GRVTypesRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class GRVTypesController extends AppBaseController
{
    /** @var  GRVTypesRepository */
    private $gRVTypesRepository;

    public function __construct(GRVTypesRepository $gRVTypesRepo)
    {
        $this->gRVTypesRepository = $gRVTypesRepo;
    }

    /**
     * Display a listing of the GRVTypes.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVTypesRepository->pushCriteria(new RequestCriteria($request));
        $gRVTypes = $this->gRVTypesRepository->all();

        return view('g_r_v_types.index')
            ->with('gRVTypes', $gRVTypes);
    }

    /**
     * Show the form for creating a new GRVTypes.
     *
     * @return Response
     */
    public function create()
    {
        return view('g_r_v_types.create');
    }

    /**
     * Store a newly created GRVTypes in storage.
     *
     * @param CreateGRVTypesRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVTypesRequest $request)
    {
        $input = $request->all();

        $gRVTypes = $this->gRVTypesRepository->create($input);

        Flash::success('G R V Types saved successfully.');

        return redirect(route('gRVTypes.index'));
    }

    /**
     * Display the specified GRVTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            Flash::error('G R V Types not found');

            return redirect(route('gRVTypes.index'));
        }

        return view('g_r_v_types.show')->with('gRVTypes', $gRVTypes);
    }

    /**
     * Show the form for editing the specified GRVTypes.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            Flash::error('G R V Types not found');

            return redirect(route('gRVTypes.index'));
        }

        return view('g_r_v_types.edit')->with('gRVTypes', $gRVTypes);
    }

    /**
     * Update the specified GRVTypes in storage.
     *
     * @param  int              $id
     * @param UpdateGRVTypesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVTypesRequest $request)
    {
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            Flash::error('G R V Types not found');

            return redirect(route('gRVTypes.index'));
        }

        $gRVTypes = $this->gRVTypesRepository->update($request->all(), $id);

        Flash::success('G R V Types updated successfully.');

        return redirect(route('gRVTypes.index'));
    }

    /**
     * Remove the specified GRVTypes from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $gRVTypes = $this->gRVTypesRepository->findWithoutFail($id);

        if (empty($gRVTypes)) {
            Flash::error('G R V Types not found');

            return redirect(route('gRVTypes.index'));
        }

        $this->gRVTypesRepository->delete($id);

        Flash::success('G R V Types deleted successfully.');

        return redirect(route('gRVTypes.index'));
    }
}
