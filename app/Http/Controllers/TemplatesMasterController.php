<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTemplatesMasterRequest;
use App\Http\Requests\UpdateTemplatesMasterRequest;
use App\Repositories\TemplatesMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TemplatesMasterController extends AppBaseController
{
    /** @var  TemplatesMasterRepository */
    private $templatesMasterRepository;

    public function __construct(TemplatesMasterRepository $templatesMasterRepo)
    {
        $this->templatesMasterRepository = $templatesMasterRepo;
    }

    /**
     * Display a listing of the TemplatesMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->templatesMasterRepository->pushCriteria(new RequestCriteria($request));
        $templatesMasters = $this->templatesMasterRepository->all();

        return view('templates_masters.index')
            ->with('templatesMasters', $templatesMasters);
    }

    /**
     * Show the form for creating a new TemplatesMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('templates_masters.create');
    }

    /**
     * Store a newly created TemplatesMaster in storage.
     *
     * @param CreateTemplatesMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateTemplatesMasterRequest $request)
    {
        $input = $request->all();

        $templatesMaster = $this->templatesMasterRepository->create($input);

        Flash::success('Templates Master saved successfully.');

        return redirect(route('templatesMasters.index'));
    }

    /**
     * Display the specified TemplatesMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            Flash::error('Templates Master not found');

            return redirect(route('templatesMasters.index'));
        }

        return view('templates_masters.show')->with('templatesMaster', $templatesMaster);
    }

    /**
     * Show the form for editing the specified TemplatesMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            Flash::error('Templates Master not found');

            return redirect(route('templatesMasters.index'));
        }

        return view('templates_masters.edit')->with('templatesMaster', $templatesMaster);
    }

    /**
     * Update the specified TemplatesMaster in storage.
     *
     * @param  int              $id
     * @param UpdateTemplatesMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTemplatesMasterRequest $request)
    {
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            Flash::error('Templates Master not found');

            return redirect(route('templatesMasters.index'));
        }

        $templatesMaster = $this->templatesMasterRepository->update($request->all(), $id);

        Flash::success('Templates Master updated successfully.');

        return redirect(route('templatesMasters.index'));
    }

    /**
     * Remove the specified TemplatesMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $templatesMaster = $this->templatesMasterRepository->findWithoutFail($id);

        if (empty($templatesMaster)) {
            Flash::error('Templates Master not found');

            return redirect(route('templatesMasters.index'));
        }

        $this->templatesMasterRepository->delete($id);

        Flash::success('Templates Master deleted successfully.');

        return redirect(route('templatesMasters.index'));
    }
}
