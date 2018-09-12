<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLogisticDetailsRequest;
use App\Http\Requests\UpdateLogisticDetailsRequest;
use App\Repositories\LogisticDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LogisticDetailsController extends AppBaseController
{
    /** @var  LogisticDetailsRepository */
    private $logisticDetailsRepository;

    public function __construct(LogisticDetailsRepository $logisticDetailsRepo)
    {
        $this->logisticDetailsRepository = $logisticDetailsRepo;
    }

    /**
     * Display a listing of the LogisticDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logisticDetailsRepository->pushCriteria(new RequestCriteria($request));
        $logisticDetails = $this->logisticDetailsRepository->all();

        return view('logistic_details.index')
            ->with('logisticDetails', $logisticDetails);
    }

    /**
     * Show the form for creating a new LogisticDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistic_details.create');
    }

    /**
     * Store a newly created LogisticDetails in storage.
     *
     * @param CreateLogisticDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateLogisticDetailsRequest $request)
    {
        $input = $request->all();

        $logisticDetails = $this->logisticDetailsRepository->create($input);

        Flash::success('Logistic Details saved successfully.');

        return redirect(route('logisticDetails.index'));
    }

    /**
     * Display the specified LogisticDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            Flash::error('Logistic Details not found');

            return redirect(route('logisticDetails.index'));
        }

        return view('logistic_details.show')->with('logisticDetails', $logisticDetails);
    }

    /**
     * Show the form for editing the specified LogisticDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            Flash::error('Logistic Details not found');

            return redirect(route('logisticDetails.index'));
        }

        return view('logistic_details.edit')->with('logisticDetails', $logisticDetails);
    }

    /**
     * Update the specified LogisticDetails in storage.
     *
     * @param  int              $id
     * @param UpdateLogisticDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogisticDetailsRequest $request)
    {
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            Flash::error('Logistic Details not found');

            return redirect(route('logisticDetails.index'));
        }

        $logisticDetails = $this->logisticDetailsRepository->update($request->all(), $id);

        Flash::success('Logistic Details updated successfully.');

        return redirect(route('logisticDetails.index'));
    }

    /**
     * Remove the specified LogisticDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logisticDetails = $this->logisticDetailsRepository->findWithoutFail($id);

        if (empty($logisticDetails)) {
            Flash::error('Logistic Details not found');

            return redirect(route('logisticDetails.index'));
        }

        $this->logisticDetailsRepository->delete($id);

        Flash::success('Logistic Details deleted successfully.');

        return redirect(route('logisticDetails.index'));
    }
}
