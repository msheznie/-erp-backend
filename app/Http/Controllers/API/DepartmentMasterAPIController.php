<?php
/**
 * =============================================
 * -- File Name : DepartmentMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Department master.
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentMasterAPIRequest;
use App\Http\Requests\API\UpdateDepartmentMasterAPIRequest;
use App\Models\DepartmentMaster;
use App\Repositories\DepartmentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DepartmentMasterController
 * @package App\Http\Controllers\API
 */

class DepartmentMasterAPIController extends AppBaseController
{
    /** @var  DepartmentMasterRepository */
    private $departmentMasterRepository;

    public function __construct(DepartmentMasterRepository $departmentMasterRepo)
    {
        $this->departmentMasterRepository = $departmentMasterRepo;
    }

    /**
     * Display a listing of the DepartmentMaster.
     * GET|HEAD /departmentMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->departmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->departmentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $departmentMasters = $this->departmentMasterRepository->all();

        return $this->sendResponse($departmentMasters->toArray(), trans('custom.department_masters_retrieved_successfully'));
    }

    /**
     * Store a newly created DepartmentMaster in storage.
     * POST /departmentMasters
     *
     * @param CreateDepartmentMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        $departmentMasters = $this->departmentMasterRepository->create($input);

        return $this->sendResponse($departmentMasters->toArray(), trans('custom.department_master_saved_successfully'));
    }

    /**
     * Display the specified DepartmentMaster.
     * GET|HEAD /departmentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var DepartmentMaster $departmentMaster */
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            return $this->sendError(trans('custom.department_master_not_found'));
        }

        return $this->sendResponse($departmentMaster->toArray(), trans('custom.department_master_retrieved_successfully'));
    }

    /**
     * Update the specified DepartmentMaster in storage.
     * PUT/PATCH /departmentMasters/{id}
     *
     * @param  int $id
     * @param UpdateDepartmentMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepartmentMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepartmentMaster $departmentMaster */
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            return $this->sendError(trans('custom.department_master_not_found'));
        }

        $departmentMaster = $this->departmentMasterRepository->update($input, $id);

        return $this->sendResponse($departmentMaster->toArray(), trans('custom.departmentmaster_updated_successfully'));
    }

    /**
     * Remove the specified DepartmentMaster from storage.
     * DELETE /departmentMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var DepartmentMaster $departmentMaster */
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            return $this->sendError(trans('custom.department_master_not_found'));
        }

        $departmentMaster->delete();

        return $this->sendResponse($id, trans('custom.department_master_deleted_successfully'));
    }
}
