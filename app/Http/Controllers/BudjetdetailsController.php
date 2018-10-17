<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudjetdetailsRequest;
use App\Http\Requests\UpdateBudjetdetailsRequest;
use App\Repositories\BudjetdetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BudjetdetailsController extends AppBaseController
{
    /** @var  BudjetdetailsRepository */
    private $budjetdetailsRepository;

    public function __construct(BudjetdetailsRepository $budjetdetailsRepo)
    {
        $this->budjetdetailsRepository = $budjetdetailsRepo;
    }

    /**
     * Display a listing of the Budjetdetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budjetdetailsRepository->pushCriteria(new RequestCriteria($request));
        $budjetdetails = $this->budjetdetailsRepository->all();

        return view('budjetdetails.index')
            ->with('budjetdetails', $budjetdetails);
    }

    /**
     * Show the form for creating a new Budjetdetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('budjetdetails.create');
    }

    /**
     * Store a newly created Budjetdetails in storage.
     *
     * @param CreateBudjetdetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateBudjetdetailsRequest $request)
    {
        $input = $request->all();

        $budjetdetails = $this->budjetdetailsRepository->create($input);

        Flash::success('Budjetdetails saved successfully.');

        return redirect(route('budjetdetails.index'));
    }

    /**
     * Display the specified Budjetdetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            Flash::error('Budjetdetails not found');

            return redirect(route('budjetdetails.index'));
        }

        return view('budjetdetails.show')->with('budjetdetails', $budjetdetails);
    }

    /**
     * Show the form for editing the specified Budjetdetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            Flash::error('Budjetdetails not found');

            return redirect(route('budjetdetails.index'));
        }

        return view('budjetdetails.edit')->with('budjetdetails', $budjetdetails);
    }

    /**
     * Update the specified Budjetdetails in storage.
     *
     * @param  int              $id
     * @param UpdateBudjetdetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudjetdetailsRequest $request)
    {
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            Flash::error('Budjetdetails not found');

            return redirect(route('budjetdetails.index'));
        }

        $budjetdetails = $this->budjetdetailsRepository->update($request->all(), $id);

        Flash::success('Budjetdetails updated successfully.');

        return redirect(route('budjetdetails.index'));
    }

    /**
     * Remove the specified Budjetdetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $budjetdetails = $this->budjetdetailsRepository->findWithoutFail($id);

        if (empty($budjetdetails)) {
            Flash::error('Budjetdetails not found');

            return redirect(route('budjetdetails.index'));
        }

        $this->budjetdetailsRepository->delete($id);

        Flash::success('Budjetdetails deleted successfully.');

        return redirect(route('budjetdetails.index'));
    }
}
