<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConsoleJVMasterRequest;
use App\Http\Requests\UpdateConsoleJVMasterRequest;
use App\Repositories\ConsoleJVMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ConsoleJVMasterController extends AppBaseController
{
    /** @var  ConsoleJVMasterRepository */
    private $consoleJVMasterRepository;

    public function __construct(ConsoleJVMasterRepository $consoleJVMasterRepo)
    {
        $this->consoleJVMasterRepository = $consoleJVMasterRepo;
    }

    /**
     * Display a listing of the ConsoleJVMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->consoleJVMasterRepository->pushCriteria(new RequestCriteria($request));
        $consoleJVMasters = $this->consoleJVMasterRepository->all();

        return view('console_j_v_masters.index')
            ->with('consoleJVMasters', $consoleJVMasters);
    }

    /**
     * Show the form for creating a new ConsoleJVMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('console_j_v_masters.create');
    }

    /**
     * Store a newly created ConsoleJVMaster in storage.
     *
     * @param CreateConsoleJVMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateConsoleJVMasterRequest $request)
    {
        $input = $request->all();

        $consoleJVMaster = $this->consoleJVMasterRepository->create($input);

        Flash::success('Console J V Master saved successfully.');

        return redirect(route('consoleJVMasters.index'));
    }

    /**
     * Display the specified ConsoleJVMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            Flash::error('Console J V Master not found');

            return redirect(route('consoleJVMasters.index'));
        }

        return view('console_j_v_masters.show')->with('consoleJVMaster', $consoleJVMaster);
    }

    /**
     * Show the form for editing the specified ConsoleJVMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            Flash::error('Console J V Master not found');

            return redirect(route('consoleJVMasters.index'));
        }

        return view('console_j_v_masters.edit')->with('consoleJVMaster', $consoleJVMaster);
    }

    /**
     * Update the specified ConsoleJVMaster in storage.
     *
     * @param  int              $id
     * @param UpdateConsoleJVMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateConsoleJVMasterRequest $request)
    {
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            Flash::error('Console J V Master not found');

            return redirect(route('consoleJVMasters.index'));
        }

        $consoleJVMaster = $this->consoleJVMasterRepository->update($request->all(), $id);

        Flash::success('Console J V Master updated successfully.');

        return redirect(route('consoleJVMasters.index'));
    }

    /**
     * Remove the specified ConsoleJVMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $consoleJVMaster = $this->consoleJVMasterRepository->findWithoutFail($id);

        if (empty($consoleJVMaster)) {
            Flash::error('Console J V Master not found');

            return redirect(route('consoleJVMasters.index'));
        }

        $this->consoleJVMasterRepository->delete($id);

        Flash::success('Console J V Master deleted successfully.');

        return redirect(route('consoleJVMasters.index'));
    }
}
