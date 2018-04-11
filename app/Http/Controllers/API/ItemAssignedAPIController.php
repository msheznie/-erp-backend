<?php
/**
=============================================
-- File Name : ItemAssignedAPIController.php
-- Project Name : ERP
-- Module Name :  Item Assigned
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Item Assigned
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemAssignedAPIRequest;
use App\Http\Requests\API\UpdateItemAssignedAPIRequest;
use App\Models\ItemAssigned;
use App\Models\Company;
use App\Repositories\ItemAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemAssignedController
 * @package App\Http\Controllers\API
 */
class ItemAssignedAPIController extends AppBaseController
{
    /** @var  ItemAssignedRepository */
    private $itemAssignedRepository;

    public function __construct(ItemAssignedRepository $itemAssignedRepo)
    {
        $this->itemAssignedRepository = $itemAssignedRepo;
    }

    /**
     * Display a listing of the ItemAssigned.
     * GET|HEAD /itemAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->itemAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->itemAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemAssigneds = $this->itemAssignedRepository->all();

        return $this->sendResponse($itemAssigneds->toArray(), 'Item Assigneds retrieved successfully');
    }

    /**
     * Store a newly created ItemAssigned in storage.
     * POST /itemAssigneds
     *
     * @param CreateItemAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateItemAssignedAPIRequest $request)
    {
        $input = $request->all();

        unset($input['company']);

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                if (count($input[$key]) > 0) {
                    $input[$key] = $input[$key][0];
                } else {
                    $input[$key] = 0;
                }
            }
        }

        if (array_key_exists("idItemAssigned", $input)) {
            $itemAssigneds = ItemAssigned::where('idItemAssigned', $input['idItemAssigned'])->first();
            $itemAssigneds->isActive = $input['isActive'];

            if($input['isAssigned'] == 1 || $input['isAssigned'] == true){
                $input['isAssigned'] = -1;
            }

            $itemAssigneds->isAssigned = $input['isAssigned'];
            $itemAssigneds->save();
        } else {
            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            $input['wacValueReportingCurrencyID'] = $company->reportingCurrency;
            $input['wacValueLocalCurrencyID'] = $company->localCurrencyID;
            $input['companyID'] = $company->CompanyID;
            $input['isActive'] = 1;
            $input['isAssigned'] = -1;
            $input['itemPrimaryCode'] = $input['primaryCode'];
            $input['itemUnitOfMeasure'] = $input['unit'];
            $itemAssigneds = $this->itemAssignedRepository->create($input);
        }

        return $this->sendResponse($itemAssigneds->toArray(), 'Item Assigned saved successfully');
    }

    /**
     * Display the specified ItemAssigned.
     * GET|HEAD /itemAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        return $this->sendResponse($itemAssigned->toArray(), 'Item Assigned retrieved successfully');
    }

    /**
     * Update the specified ItemAssigned in storage.
     * PUT/PATCH /itemAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateItemAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateItemAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        $itemAssigned = $this->itemAssignedRepository->update($input, $id);

        return $this->sendResponse($itemAssigned->toArray(), 'ItemAssigned updated successfully');
    }

    /**
     * Remove the specified ItemAssigned from storage.
     * DELETE /itemAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ItemAssigned $itemAssigned */
        $itemAssigned = $this->itemAssignedRepository->findWithoutFail($id);

        if (empty($itemAssigned)) {
            return $this->sendError('Item Assigned not found');
        }

        $itemAssigned->delete();

        return $this->sendResponse($id, 'Item Assigned deleted successfully');
    }
}
