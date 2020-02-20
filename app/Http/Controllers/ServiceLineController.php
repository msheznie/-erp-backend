<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateServiceLineRequest;
use App\Http\Requests\UpdateServiceLineRequest;
use App\Repositories\ServiceLineRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ServiceLineController extends AppBaseController
{
    /** @var  ServiceLineRepository */
    private $serviceLineRepository;

    public function __construct(ServiceLineRepository $serviceLineRepo)
    {
        $this->serviceLineRepository = $serviceLineRepo;
    }

    /**
     * Display a listing of the ServiceLine.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->serviceLineRepository->pushCriteria(new RequestCriteria($request));
        $serviceLines = $this->serviceLineRepository->all();

        return view('service_lines.index')
            ->with('serviceLines', $serviceLines);
    }

    /**
     * Show the form for creating a new ServiceLine.
     *
     * @return Response
     */
    public function create()
    {
        return view('service_lines.create');
    }

    /**
     * Store a newly created ServiceLine in storage.
     *
     * @param CreateServiceLineRequest $request
     *
     * @return Response
     */
    public function store(CreateServiceLineRequest $request)
    {
        $input = $request->all();

        $serviceLine = $this->serviceLineRepository->create($input);

        Flash::success('Service Line saved successfully.');

        return redirect(route('serviceLines.index'));
    }

    /**
     * Display the specified ServiceLine.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            Flash::error('Service Line not found');

            return redirect(route('serviceLines.index'));
        }

        return view('service_lines.show')->with('serviceLine', $serviceLine);
    }

    /**
     * Show the form for editing the specified ServiceLine.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            Flash::error('Service Line not found');

            return redirect(route('serviceLines.index'));
        }

        return view('service_lines.edit')->with('serviceLine', $serviceLine);
    }

    /**
     * Update the specified ServiceLine in storage.
     *
     * @param  int              $id
     * @param UpdateServiceLineRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateServiceLineRequest $request)
    {
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            Flash::error('Service Line not found');

            return redirect(route('serviceLines.index'));
        }

        $serviceLine = $this->serviceLineRepository->update($request->all(), $id);

        Flash::success('Service Line updated successfully.');

        return redirect(route('serviceLines.index'));
    }

    /**
     * Remove the specified ServiceLine from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $serviceLine = $this->serviceLineRepository->findWithoutFail($id);

        if (empty($serviceLine)) {
            Flash::error('Service Line not found');

            return redirect(route('serviceLines.index'));
        }

        $this->serviceLineRepository->delete($id);

        Flash::success('Service Line deleted successfully.');

        return redirect(route('serviceLines.index'));
    }
}
