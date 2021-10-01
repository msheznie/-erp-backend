<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChequeTemplateMasterRequest;
use App\Http\Requests\UpdateChequeTemplateMasterRequest;
use App\Repositories\ChequeTemplateMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ChequeTemplateMasterController extends AppBaseController
{
    /** @var  ChequeTemplateMasterRepository */
    private $chequeTemplateMasterRepository;

    public function __construct(ChequeTemplateMasterRepository $chequeTemplateMasterRepo)
    {
        $this->chequeTemplateMasterRepository = $chequeTemplateMasterRepo;
    }

    /**
     * Display a listing of the ChequeTemplateMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->chequeTemplateMasterRepository->pushCriteria(new RequestCriteria($request));
        $chequeTemplateMasters = $this->chequeTemplateMasterRepository->all();

        return view('cheque_template_masters.index')
            ->with('chequeTemplateMasters', $chequeTemplateMasters);
    }

    /**
     * Show the form for creating a new ChequeTemplateMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('cheque_template_masters.create');
    }

    /**
     * Store a newly created ChequeTemplateMaster in storage.
     *
     * @param CreateChequeTemplateMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateChequeTemplateMasterRequest $request)
    {
        $input = $request->all();

        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->create($input);

        Flash::success('Cheque Template Master saved successfully.');

        return redirect(route('chequeTemplateMasters.index'));
    }

    /**
     * Display the specified ChequeTemplateMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            Flash::error('Cheque Template Master not found');

            return redirect(route('chequeTemplateMasters.index'));
        }

        return view('cheque_template_masters.show')->with('chequeTemplateMaster', $chequeTemplateMaster);
    }

    /**
     * Show the form for editing the specified ChequeTemplateMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            Flash::error('Cheque Template Master not found');

            return redirect(route('chequeTemplateMasters.index'));
        }

        return view('cheque_template_masters.edit')->with('chequeTemplateMaster', $chequeTemplateMaster);
    }

    /**
     * Update the specified ChequeTemplateMaster in storage.
     *
     * @param  int              $id
     * @param UpdateChequeTemplateMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateChequeTemplateMasterRequest $request)
    {
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            Flash::error('Cheque Template Master not found');

            return redirect(route('chequeTemplateMasters.index'));
        }

        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->update($request->all(), $id);

        Flash::success('Cheque Template Master updated successfully.');

        return redirect(route('chequeTemplateMasters.index'));
    }

    /**
     * Remove the specified ChequeTemplateMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            Flash::error('Cheque Template Master not found');

            return redirect(route('chequeTemplateMasters.index'));
        }

        $this->chequeTemplateMasterRepository->delete($id);

        Flash::success('Cheque Template Master deleted successfully.');

        return redirect(route('chequeTemplateMasters.index'));
    }
}
