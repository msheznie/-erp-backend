<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLogisticModeOfImportRequest;
use App\Http\Requests\UpdateLogisticModeOfImportRequest;
use App\Repositories\LogisticModeOfImportRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LogisticModeOfImportController extends AppBaseController
{
    /** @var  LogisticModeOfImportRepository */
    private $logisticModeOfImportRepository;

    public function __construct(LogisticModeOfImportRepository $logisticModeOfImportRepo)
    {
        $this->logisticModeOfImportRepository = $logisticModeOfImportRepo;
    }

    /**
     * Display a listing of the LogisticModeOfImport.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->logisticModeOfImportRepository->pushCriteria(new RequestCriteria($request));
        $logisticModeOfImports = $this->logisticModeOfImportRepository->all();

        return view('logistic_mode_of_imports.index')
            ->with('logisticModeOfImports', $logisticModeOfImports);
    }

    /**
     * Show the form for creating a new LogisticModeOfImport.
     *
     * @return Response
     */
    public function create()
    {
        return view('logistic_mode_of_imports.create');
    }

    /**
     * Store a newly created LogisticModeOfImport in storage.
     *
     * @param CreateLogisticModeOfImportRequest $request
     *
     * @return Response
     */
    public function store(CreateLogisticModeOfImportRequest $request)
    {
        $input = $request->all();

        $logisticModeOfImport = $this->logisticModeOfImportRepository->create($input);

        Flash::success('Logistic Mode Of Import saved successfully.');

        return redirect(route('logisticModeOfImports.index'));
    }

    /**
     * Display the specified LogisticModeOfImport.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            Flash::error('Logistic Mode Of Import not found');

            return redirect(route('logisticModeOfImports.index'));
        }

        return view('logistic_mode_of_imports.show')->with('logisticModeOfImport', $logisticModeOfImport);
    }

    /**
     * Show the form for editing the specified LogisticModeOfImport.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            Flash::error('Logistic Mode Of Import not found');

            return redirect(route('logisticModeOfImports.index'));
        }

        return view('logistic_mode_of_imports.edit')->with('logisticModeOfImport', $logisticModeOfImport);
    }

    /**
     * Update the specified LogisticModeOfImport in storage.
     *
     * @param  int              $id
     * @param UpdateLogisticModeOfImportRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLogisticModeOfImportRequest $request)
    {
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            Flash::error('Logistic Mode Of Import not found');

            return redirect(route('logisticModeOfImports.index'));
        }

        $logisticModeOfImport = $this->logisticModeOfImportRepository->update($request->all(), $id);

        Flash::success('Logistic Mode Of Import updated successfully.');

        return redirect(route('logisticModeOfImports.index'));
    }

    /**
     * Remove the specified LogisticModeOfImport from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $logisticModeOfImport = $this->logisticModeOfImportRepository->findWithoutFail($id);

        if (empty($logisticModeOfImport)) {
            Flash::error('Logistic Mode Of Import not found');

            return redirect(route('logisticModeOfImports.index'));
        }

        $this->logisticModeOfImportRepository->delete($id);

        Flash::success('Logistic Mode Of Import deleted successfully.');

        return redirect(route('logisticModeOfImports.index'));
    }
}
