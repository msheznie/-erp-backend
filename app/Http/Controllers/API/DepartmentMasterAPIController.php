<?php

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

        return $this->sendResponse($departmentMasters->toArray(), 'Department Masters retrieved successfully');
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

        return $this->sendResponse($departmentMasters->toArray(), 'Department Master saved successfully');
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
            return $this->sendError('Department Master not found');
        }

        return $this->sendResponse($departmentMaster->toArray(), 'Department Master retrieved successfully');
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
            return $this->sendError('Department Master not found');
        }

        $departmentMaster = $this->departmentMasterRepository->update($input, $id);

        return $this->sendResponse($departmentMaster->toArray(), 'DepartmentMaster updated successfully');
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
            return $this->sendError('Department Master not found');
        }

        $departmentMaster->delete();

        return $this->sendResponse($id, 'Department Master deleted successfully');
    }
}
