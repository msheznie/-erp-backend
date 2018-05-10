<?php
/**
 * =============================================
 * -- File Name : GRVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for GRV Master
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVMasterAPIRequest;
use App\Http\Requests\API\UpdateGRVMasterAPIRequest;
use App\Models\GRVMaster;
use App\Repositories\GRVMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GRVMasterController
 * @package App\Http\Controllers\API
 */

class GRVMasterAPIController extends AppBaseController
{
    /** @var  GRVMasterRepository */
    private $gRVMasterRepository;

    public function __construct(GRVMasterRepository $gRVMasterRepo)
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
    }

    /**
     * Display a listing of the GRVMaster.
     * GET|HEAD /gRVMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->gRVMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gRVMasters = $this->gRVMasterRepository->all();

        return $this->sendResponse($gRVMasters->toArray(), 'G R V Masters retrieved successfully');
    }

    /**
     * Store a newly created GRVMaster in storage.
     * POST /gRVMasters
     *
     * @param CreateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        $gRVMasters = $this->gRVMasterRepository->create($input);

        return $this->sendResponse($gRVMasters->toArray(), 'G R V Master saved successfully');
    }

    /**
     * Display the specified GRVMaster.
     * GET|HEAD /gRVMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('G R V Master not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'G R V Master retrieved successfully');
    }

    /**
     * Update the specified GRVMaster in storage.
     * PUT/PATCH /gRVMasters/{id}
     *
     * @param  int $id
     * @param UpdateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('G R V Master not found');
        }

        $gRVMaster = $this->gRVMasterRepository->update($input, $id);

        return $this->sendResponse($gRVMaster->toArray(), 'GRVMaster updated successfully');
    }

    /**
     * Remove the specified GRVMaster from storage.
     * DELETE /gRVMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('G R V Master not found');
        }

        $gRVMaster->delete();

        return $this->sendResponse($id, 'G R V Master deleted successfully');
    }
}
