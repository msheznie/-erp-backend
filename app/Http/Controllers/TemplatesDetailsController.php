<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTemplatesDetailsRequest;
use App\Http\Requests\UpdateTemplatesDetailsRequest;
use App\Repositories\TemplatesDetailsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TemplatesDetailsController extends AppBaseController
{
    /** @var  TemplatesDetailsRepository */
    private $templatesDetailsRepository;

    public function __construct(TemplatesDetailsRepository $templatesDetailsRepo)
    {
        $this->templatesDetailsRepository = $templatesDetailsRepo;
    }

    /**
     * Display a listing of the TemplatesDetails.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->templatesDetailsRepository->pushCriteria(new RequestCriteria($request));
        $templatesDetails = $this->templatesDetailsRepository->all();

        return view('templates_details.index')
            ->with('templatesDetails', $templatesDetails);
    }

    /**
     * Show the form for creating a new TemplatesDetails.
     *
     * @return Response
     */
    public function create()
    {
        return view('templates_details.create');
    }

    /**
     * Store a newly created TemplatesDetails in storage.
     *
     * @param CreateTemplatesDetailsRequest $request
     *
     * @return Response
     */
    public function store(CreateTemplatesDetailsRequest $request)
    {
        $input = $request->all();

        $templatesDetails = $this->templatesDetailsRepository->create($input);

        Flash::success('Templates Details saved successfully.');

        return redirect(route('templatesDetails.index'));
    }

    /**
     * Display the specified TemplatesDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            Flash::error('Templates Details not found');

            return redirect(route('templatesDetails.index'));
        }

        return view('templates_details.show')->with('templatesDetails', $templatesDetails);
    }

    /**
     * Show the form for editing the specified TemplatesDetails.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            Flash::error('Templates Details not found');

            return redirect(route('templatesDetails.index'));
        }

        return view('templates_details.edit')->with('templatesDetails', $templatesDetails);
    }

    /**
     * Update the specified TemplatesDetails in storage.
     *
     * @param  int              $id
     * @param UpdateTemplatesDetailsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTemplatesDetailsRequest $request)
    {
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            Flash::error('Templates Details not found');

            return redirect(route('templatesDetails.index'));
        }

        $templatesDetails = $this->templatesDetailsRepository->update($request->all(), $id);

        Flash::success('Templates Details updated successfully.');

        return redirect(route('templatesDetails.index'));
    }

    /**
     * Remove the specified TemplatesDetails from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $templatesDetails = $this->templatesDetailsRepository->findWithoutFail($id);

        if (empty($templatesDetails)) {
            Flash::error('Templates Details not found');

            return redirect(route('templatesDetails.index'));
        }

        $this->templatesDetailsRepository->delete($id);

        Flash::success('Templates Details deleted successfully.');

        return redirect(route('templatesDetails.index'));
    }
}
