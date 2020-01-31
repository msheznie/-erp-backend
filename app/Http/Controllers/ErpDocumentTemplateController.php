<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateErpDocumentTemplateRequest;
use App\Http\Requests\UpdateErpDocumentTemplateRequest;
use App\Repositories\ErpDocumentTemplateRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ErpDocumentTemplateController extends AppBaseController
{
    /** @var  ErpDocumentTemplateRepository */
    private $erpDocumentTemplateRepository;

    public function __construct(ErpDocumentTemplateRepository $erpDocumentTemplateRepo)
    {
        $this->erpDocumentTemplateRepository = $erpDocumentTemplateRepo;
    }

    /**
     * Display a listing of the ErpDocumentTemplate.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpDocumentTemplateRepository->pushCriteria(new RequestCriteria($request));
        $erpDocumentTemplates = $this->erpDocumentTemplateRepository->all();

        return view('erp_document_templates.index')
            ->with('erpDocumentTemplates', $erpDocumentTemplates);
    }

    /**
     * Show the form for creating a new ErpDocumentTemplate.
     *
     * @return Response
     */
    public function create()
    {
        return view('erp_document_templates.create');
    }

    /**
     * Store a newly created ErpDocumentTemplate in storage.
     *
     * @param CreateErpDocumentTemplateRequest $request
     *
     * @return Response
     */
    public function store(CreateErpDocumentTemplateRequest $request)
    {
        $input = $request->all();

        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->create($input);

        Flash::success('Erp Document Template saved successfully.');

        return redirect(route('erpDocumentTemplates.index'));
    }

    /**
     * Display the specified ErpDocumentTemplate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            Flash::error('Erp Document Template not found');

            return redirect(route('erpDocumentTemplates.index'));
        }

        return view('erp_document_templates.show')->with('erpDocumentTemplate', $erpDocumentTemplate);
    }

    /**
     * Show the form for editing the specified ErpDocumentTemplate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            Flash::error('Erp Document Template not found');

            return redirect(route('erpDocumentTemplates.index'));
        }

        return view('erp_document_templates.edit')->with('erpDocumentTemplate', $erpDocumentTemplate);
    }

    /**
     * Update the specified ErpDocumentTemplate in storage.
     *
     * @param  int              $id
     * @param UpdateErpDocumentTemplateRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpDocumentTemplateRequest $request)
    {
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            Flash::error('Erp Document Template not found');

            return redirect(route('erpDocumentTemplates.index'));
        }

        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->update($request->all(), $id);

        Flash::success('Erp Document Template updated successfully.');

        return redirect(route('erpDocumentTemplates.index'));
    }

    /**
     * Remove the specified ErpDocumentTemplate from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $erpDocumentTemplate = $this->erpDocumentTemplateRepository->findWithoutFail($id);

        if (empty($erpDocumentTemplate)) {
            Flash::error('Erp Document Template not found');

            return redirect(route('erpDocumentTemplates.index'));
        }

        $this->erpDocumentTemplateRepository->delete($id);

        Flash::success('Erp Document Template deleted successfully.');

        return redirect(route('erpDocumentTemplates.index'));
    }
}
