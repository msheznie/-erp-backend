<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateErpPrintTemplateMasterRequest;
use App\Http\Requests\UpdateErpPrintTemplateMasterRequest;
use App\Repositories\ErpPrintTemplateMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ErpPrintTemplateMasterController extends AppBaseController
{
    /** @var  ErpPrintTemplateMasterRepository */
    private $erpPrintTemplateMasterRepository;

    public function __construct(ErpPrintTemplateMasterRepository $erpPrintTemplateMasterRepo)
    {
        $this->erpPrintTemplateMasterRepository = $erpPrintTemplateMasterRepo;
    }

    /**
     * Display a listing of the ErpPrintTemplateMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpPrintTemplateMasterRepository->pushCriteria(new RequestCriteria($request));
        $erpPrintTemplateMasters = $this->erpPrintTemplateMasterRepository->all();

        return view('erp_print_template_masters.index')
            ->with('erpPrintTemplateMasters', $erpPrintTemplateMasters);
    }

    /**
     * Show the form for creating a new ErpPrintTemplateMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('erp_print_template_masters.create');
    }

    /**
     * Store a newly created ErpPrintTemplateMaster in storage.
     *
     * @param CreateErpPrintTemplateMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateErpPrintTemplateMasterRequest $request)
    {
        $input = $request->all();

        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->create($input);

        Flash::success('Erp Print Template Master saved successfully.');

        return redirect(route('erpPrintTemplateMasters.index'));
    }

    /**
     * Display the specified ErpPrintTemplateMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            Flash::error('Erp Print Template Master not found');

            return redirect(route('erpPrintTemplateMasters.index'));
        }

        return view('erp_print_template_masters.show')->with('erpPrintTemplateMaster', $erpPrintTemplateMaster);
    }

    /**
     * Show the form for editing the specified ErpPrintTemplateMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            Flash::error('Erp Print Template Master not found');

            return redirect(route('erpPrintTemplateMasters.index'));
        }

        return view('erp_print_template_masters.edit')->with('erpPrintTemplateMaster', $erpPrintTemplateMaster);
    }

    /**
     * Update the specified ErpPrintTemplateMaster in storage.
     *
     * @param  int              $id
     * @param UpdateErpPrintTemplateMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpPrintTemplateMasterRequest $request)
    {
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            Flash::error('Erp Print Template Master not found');

            return redirect(route('erpPrintTemplateMasters.index'));
        }

        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->update($request->all(), $id);

        Flash::success('Erp Print Template Master updated successfully.');

        return redirect(route('erpPrintTemplateMasters.index'));
    }

    /**
     * Remove the specified ErpPrintTemplateMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $erpPrintTemplateMaster = $this->erpPrintTemplateMasterRepository->findWithoutFail($id);

        if (empty($erpPrintTemplateMaster)) {
            Flash::error('Erp Print Template Master not found');

            return redirect(route('erpPrintTemplateMasters.index'));
        }

        $this->erpPrintTemplateMasterRepository->delete($id);

        Flash::success('Erp Print Template Master deleted successfully.');

        return redirect(route('erpPrintTemplateMasters.index'));
    }
}
