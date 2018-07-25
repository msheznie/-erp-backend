<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUnbilledGrvGroupByRequest;
use App\Http\Requests\UpdateUnbilledGrvGroupByRequest;
use App\Repositories\UnbilledGrvGroupByRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class UnbilledGrvGroupByController extends AppBaseController
{
    /** @var  UnbilledGrvGroupByRepository */
    private $unbilledGrvGroupByRepository;

    public function __construct(UnbilledGrvGroupByRepository $unbilledGrvGroupByRepo)
    {
        $this->unbilledGrvGroupByRepository = $unbilledGrvGroupByRepo;
    }

    /**
     * Display a listing of the UnbilledGrvGroupBy.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unbilledGrvGroupByRepository->pushCriteria(new RequestCriteria($request));
        $unbilledGrvGroupBies = $this->unbilledGrvGroupByRepository->all();

        return view('unbilled_grv_group_bies.index')
            ->with('unbilledGrvGroupBies', $unbilledGrvGroupBies);
    }

    /**
     * Show the form for creating a new UnbilledGrvGroupBy.
     *
     * @return Response
     */
    public function create()
    {
        return view('unbilled_grv_group_bies.create');
    }

    /**
     * Store a newly created UnbilledGrvGroupBy in storage.
     *
     * @param CreateUnbilledGrvGroupByRequest $request
     *
     * @return Response
     */
    public function store(CreateUnbilledGrvGroupByRequest $request)
    {
        $input = $request->all();

        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->create($input);

        Flash::success('Unbilled Grv Group By saved successfully.');

        return redirect(route('unbilledGrvGroupBies.index'));
    }

    /**
     * Display the specified UnbilledGrvGroupBy.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            Flash::error('Unbilled Grv Group By not found');

            return redirect(route('unbilledGrvGroupBies.index'));
        }

        return view('unbilled_grv_group_bies.show')->with('unbilledGrvGroupBy', $unbilledGrvGroupBy);
    }

    /**
     * Show the form for editing the specified UnbilledGrvGroupBy.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            Flash::error('Unbilled Grv Group By not found');

            return redirect(route('unbilledGrvGroupBies.index'));
        }

        return view('unbilled_grv_group_bies.edit')->with('unbilledGrvGroupBy', $unbilledGrvGroupBy);
    }

    /**
     * Update the specified UnbilledGrvGroupBy in storage.
     *
     * @param  int              $id
     * @param UpdateUnbilledGrvGroupByRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnbilledGrvGroupByRequest $request)
    {
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            Flash::error('Unbilled Grv Group By not found');

            return redirect(route('unbilledGrvGroupBies.index'));
        }

        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->update($request->all(), $id);

        Flash::success('Unbilled Grv Group By updated successfully.');

        return redirect(route('unbilledGrvGroupBies.index'));
    }

    /**
     * Remove the specified UnbilledGrvGroupBy from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $unbilledGrvGroupBy = $this->unbilledGrvGroupByRepository->findWithoutFail($id);

        if (empty($unbilledGrvGroupBy)) {
            Flash::error('Unbilled Grv Group By not found');

            return redirect(route('unbilledGrvGroupBies.index'));
        }

        $this->unbilledGrvGroupByRepository->delete($id);

        Flash::success('Unbilled Grv Group By deleted successfully.');

        return redirect(route('unbilledGrvGroupBies.index'));
    }
}
