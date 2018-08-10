<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFieldMasterRequest;
use App\Http\Requests\UpdateFieldMasterRequest;
use App\Repositories\FieldMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FieldMasterController extends AppBaseController
{
    /** @var  FieldMasterRepository */
    private $fieldMasterRepository;

    public function __construct(FieldMasterRepository $fieldMasterRepo)
    {
        $this->fieldMasterRepository = $fieldMasterRepo;
    }

    /**
     * Display a listing of the FieldMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fieldMasterRepository->pushCriteria(new RequestCriteria($request));
        $fieldMasters = $this->fieldMasterRepository->all();

        return view('field_masters.index')
            ->with('fieldMasters', $fieldMasters);
    }

    /**
     * Show the form for creating a new FieldMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('field_masters.create');
    }

    /**
     * Store a newly created FieldMaster in storage.
     *
     * @param CreateFieldMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateFieldMasterRequest $request)
    {
        $input = $request->all();

        $fieldMaster = $this->fieldMasterRepository->create($input);

        Flash::success('Field Master saved successfully.');

        return redirect(route('fieldMasters.index'));
    }

    /**
     * Display the specified FieldMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            Flash::error('Field Master not found');

            return redirect(route('fieldMasters.index'));
        }

        return view('field_masters.show')->with('fieldMaster', $fieldMaster);
    }

    /**
     * Show the form for editing the specified FieldMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            Flash::error('Field Master not found');

            return redirect(route('fieldMasters.index'));
        }

        return view('field_masters.edit')->with('fieldMaster', $fieldMaster);
    }

    /**
     * Update the specified FieldMaster in storage.
     *
     * @param  int              $id
     * @param UpdateFieldMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFieldMasterRequest $request)
    {
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            Flash::error('Field Master not found');

            return redirect(route('fieldMasters.index'));
        }

        $fieldMaster = $this->fieldMasterRepository->update($request->all(), $id);

        Flash::success('Field Master updated successfully.');

        return redirect(route('fieldMasters.index'));
    }

    /**
     * Remove the specified FieldMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fieldMaster = $this->fieldMasterRepository->findWithoutFail($id);

        if (empty($fieldMaster)) {
            Flash::error('Field Master not found');

            return redirect(route('fieldMasters.index'));
        }

        $this->fieldMasterRepository->delete($id);

        Flash::success('Field Master deleted successfully.');

        return redirect(route('fieldMasters.index'));
    }
}
