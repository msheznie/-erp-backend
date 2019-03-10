<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConsoleJVDetailRequest;
use App\Http\Requests\UpdateConsoleJVDetailRequest;
use App\Repositories\ConsoleJVDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ConsoleJVDetailController extends AppBaseController
{
    /** @var  ConsoleJVDetailRepository */
    private $consoleJVDetailRepository;

    public function __construct(ConsoleJVDetailRepository $consoleJVDetailRepo)
    {
        $this->consoleJVDetailRepository = $consoleJVDetailRepo;
    }

    /**
     * Display a listing of the ConsoleJVDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->consoleJVDetailRepository->pushCriteria(new RequestCriteria($request));
        $consoleJVDetails = $this->consoleJVDetailRepository->all();

        return view('console_j_v_details.index')
            ->with('consoleJVDetails', $consoleJVDetails);
    }

    /**
     * Show the form for creating a new ConsoleJVDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('console_j_v_details.create');
    }

    /**
     * Store a newly created ConsoleJVDetail in storage.
     *
     * @param CreateConsoleJVDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateConsoleJVDetailRequest $request)
    {
        $input = $request->all();

        $consoleJVDetail = $this->consoleJVDetailRepository->create($input);

        Flash::success('Console J V Detail saved successfully.');

        return redirect(route('consoleJVDetails.index'));
    }

    /**
     * Display the specified ConsoleJVDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            Flash::error('Console J V Detail not found');

            return redirect(route('consoleJVDetails.index'));
        }

        return view('console_j_v_details.show')->with('consoleJVDetail', $consoleJVDetail);
    }

    /**
     * Show the form for editing the specified ConsoleJVDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            Flash::error('Console J V Detail not found');

            return redirect(route('consoleJVDetails.index'));
        }

        return view('console_j_v_details.edit')->with('consoleJVDetail', $consoleJVDetail);
    }

    /**
     * Update the specified ConsoleJVDetail in storage.
     *
     * @param  int              $id
     * @param UpdateConsoleJVDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateConsoleJVDetailRequest $request)
    {
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            Flash::error('Console J V Detail not found');

            return redirect(route('consoleJVDetails.index'));
        }

        $consoleJVDetail = $this->consoleJVDetailRepository->update($request->all(), $id);

        Flash::success('Console J V Detail updated successfully.');

        return redirect(route('consoleJVDetails.index'));
    }

    /**
     * Remove the specified ConsoleJVDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $consoleJVDetail = $this->consoleJVDetailRepository->findWithoutFail($id);

        if (empty($consoleJVDetail)) {
            Flash::error('Console J V Detail not found');

            return redirect(route('consoleJVDetails.index'));
        }

        $this->consoleJVDetailRepository->delete($id);

        Flash::success('Console J V Detail deleted successfully.');

        return redirect(route('consoleJVDetails.index'));
    }
}
