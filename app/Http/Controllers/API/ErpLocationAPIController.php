<?php
/**
=============================================
-- File Name : BankAssignController.php
-- Project Name : ERP
-- Module Name :  System Admin
-- Author : Pasan Madhuranga
-- Create date : 21 - March 2018
-- Description : This file contains the all CRUD for Bank Assigned
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpLocationAPIRequest;
use App\Http\Requests\API\UpdateErpLocationAPIRequest;
use App\Models\ErpLocation;
use App\Repositories\ErpLocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\WarehouseMaster;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpLocationController
 * @package App\Http\Controllers\API
 */

class ErpLocationAPIController extends AppBaseController
{
    /** @var  ErpLocationRepository */
    private $erpLocationRepository;

    public function __construct(ErpLocationRepository $erpLocationRepo)
    {
        $this->erpLocationRepository = $erpLocationRepo;
    }

    /**
     * Display a listing of the ErpLocation.
     * GET|HEAD /erpLocations
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpLocationRepository->pushCriteria(new RequestCriteria($request));
        $this->erpLocationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpLocations = $this->erpLocationRepository->all();

        return $this->sendResponse($erpLocations->toArray(), 'Erp Locations retrieved successfully');
    }

    /**
     * Store a newly created ErpLocation in storage.
     * POST /erpLocations
     *
     * @param CreateErpLocationAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateErpLocationAPIRequest $request)
    {
        $input = $request->all();

        $erpLocations = $this->erpLocationRepository->create($input);

        return $this->sendResponse($erpLocations->toArray(), 'Erp Location saved successfully');
    }

    /**
     * Display the specified ErpLocation.
     * GET|HEAD /erpLocations/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ErpLocation $erpLocation */
        $erpLocation = $this->erpLocationRepository->findWithoutFail($id);

        if (empty($erpLocation)) {
            return $this->sendError('Erp Location not found');
        }

        return $this->sendResponse($erpLocation->toArray(), 'Erp Location retrieved successfully');
    }

    /**
     * Update the specified ErpLocation in storage.
     * PUT/PATCH /erpLocations/{id}
     *
     * @param  int $id
     * @param UpdateErpLocationAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpLocationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpLocation $erpLocation */
        $erpLocation = $this->erpLocationRepository->findWithoutFail($id);

        if (empty($erpLocation)) {
            return $this->sendError('Erp Location not found');
        }

        $erpLocation = $this->erpLocationRepository->update($input, $id);

        return $this->sendResponse($erpLocation->toArray(), 'ErpLocation updated successfully');
    }

    /**
     * Remove the specified ErpLocation from storage.
     * DELETE /erpLocations/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ErpLocation $erpLocation */
        $erpLocation = $this->erpLocationRepository->findWithoutFail($id);

        if (empty($erpLocation)) {
            return $this->sendError('Erp Location not found');
        }

        $erpLocation->delete();

        return $this->sendResponse($id, 'Erp Location deleted successfully');
    }

    public function getAllLocation(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $locations= ErpLocation::where('is_deleted', 0)->get();
        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $locations = $locations->where(function ($query) use ($search) {
                $query->where('locationName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($locations)
        ->addIndexColumn()
        ->with('orderCondition', $sort)
        ->addColumn('Actions', 'Actions', "Actions")
        //->addColumn('Index', 'Index', "Index")
        ->make(true);
    }

    public function createLocation(Request $request){
        $input = $request->all();
        $masterData = ['locationName'=>$input['locationName'] ];

        if(isset($input['locationID'])){
            $location = ErpLocation::where('locationID', $input['locationID'])->update($masterData);
            return $this->sendResponse($location, 'Erp Location updated successfully');
        }

        $location = ErpLocation::create($masterData);
        return $this->sendResponse($location, 'Erp Location Created successfully');
    }

    public function deleteLocation(Request $request){
        $input = $request->all();
        $isLocationUsed = WarehouseMaster::where('wareHouseLocation', $input['locationID'])->first();
        
        if($isLocationUsed){
            return $this->sendError('Location cannot be deleted - Location is already selected for a warehouse');
        }
            $deleteData = ['is_deleted'=>1];
            $location = ErpLocation::where('locationID', $input['locationID'])->update($deleteData);
            return $this->sendResponse($location, 'Erp Location deleted successfully');
    }
}
