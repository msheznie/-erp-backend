<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLogisticRequest;
use App\Http\Requests\UpdateLogisticRequest;
use App\Repositories\LogisticRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LogisticController extends AppBaseController
{
    /** @var  LogisticRepository */
    private $logisticRepository;

    public function __construct(LogisticRepository $logisticRepo)
    {
        $this->logisticRepository = $logisticRepo;
    }

    /**
     * Display a listing of the Logistic.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logisticRepository->pushCriteria(new RequestCriteria($request));
        $logistics = $this->logisticRepository->all();

        return view('logistics.index')
            ->with('logistics', $logistics);
    }

    /**
     * Show the form for creating a new Logistic.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistics.create');
    }

    /**
     * Store a newly created Logistic in storage.
     *
     * @param CreateLogisticRequest $request
     *
     * @return Response
     */
    public function store(CreateLogisticRequest $request)
    {
        $input = $request->all();

        $logistic = $this->logisticRepository->create($input);

        Flash::success('Logistic saved successfully.');

        return redirect(route('logistics.index'));
    }

    /**
     * Display the specified Logistic.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            Flash::error('Logistic not found');

            return redirect(route('logistics.index'));
        }

        return view('logistics.show')->with('logistic', $logistic);
    }

    /**
     * Show the form for editing the specified Logistic.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            Flash::error('Logistic not found');

            return redirect(route('logistics.index'));
        }

        return view('logistics.edit')->with('logistic', $logistic);
    }

    /**
     * Update the specified Logistic in storage.
     *
     * @param  int              $id
     * @param UpdateLogisticRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogisticRequest $request)
    {
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            Flash::error('Logistic not found');

            return redirect(route('logistics.index'));
        }

        $logistic = $this->logisticRepository->update($request->all(), $id);

        Flash::success('Logistic updated successfully.');

        return redirect(route('logistics.index'));
    }

    /**
     * Remove the specified Logistic from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logistic = $this->logisticRepository->findWithoutFail($id);

        if (empty($logistic)) {
            Flash::error('Logistic not found');

            return redirect(route('logistics.index'));
        }

        $this->logisticRepository->delete($id);

        Flash::success('Logistic deleted successfully.');

        return redirect(route('logistics.index'));
    }
}
