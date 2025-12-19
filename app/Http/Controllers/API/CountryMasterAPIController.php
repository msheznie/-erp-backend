<?php
/**
=============================================
-- File Name : CountryMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Country Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Country Master.
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCountryMasterAPIRequest;
use App\Http\Requests\API\UpdateCountryMasterAPIRequest;
use App\Models\CountryMaster;
use App\Repositories\CountryMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\SupplierMaster;
use App\Models\SupplierCurrency;

use Response;

/**
 * Class CountryMasterController
 * @package App\Http\Controllers\API
 */

class CountryMasterAPIController extends AppBaseController
{
    /** @var  CountryMasterRepository */
    private $countryMasterRepository;

    public function __construct(CountryMasterRepository $countryMasterRepo)
    {
        $this->countryMasterRepository = $countryMasterRepo;
    }

    /**
     * Display a listing of the CountryMaster.
     * GET|HEAD /countryMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->countryMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->countryMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $countryMasters = $this->countryMasterRepository->all();

        return $this->sendResponse($countryMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.country_masters')]));
    }


    /**
     * Store a newly created CountryMaster in storage.
     * POST /countryMasters
     *
     * @param CreateCountryMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCountryMasterAPIRequest $request)
    {
        $input = $request->all();

        $countryMasters = $this->countryMasterRepository->create($input);

        return $this->sendResponse($countryMasters->toArray(), trans('custom.save', ['attribute' => trans('custom.country_masters')]));
    }

    /**
     * Display the specified CountryMaster.
     * GET|HEAD /countryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CountryMaster $countryMaster */
        $countryMaster = $this->countryMasterRepository->findWithoutFail($id);

        if (empty($countryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.country_masters')]));
        }

        return $this->sendResponse($countryMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.country_masters')]));
    }

    /**
     * Update the specified CountryMaster in storage.
     * PUT/PATCH /countryMasters/{id}
     *
     * @param  int $id
     * @param UpdateCountryMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCountryMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CountryMaster $countryMaster */
        $countryMaster = $this->countryMasterRepository->findWithoutFail($id);

        if (empty($countryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.country_masters')]));
        }

        $countryMaster = $this->countryMasterRepository->update($input, $id);

        return $this->sendResponse($countryMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.country_masters')]));
    }

    /**
     * Remove the specified CountryMaster from storage.
     * DELETE /countryMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CountryMaster $countryMaster */
        $countryMaster = $this->countryMasterRepository->findWithoutFail($id);

        if (empty($countryMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.country_masters')]));
        }

        $countryMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.country_masters')]));
    }
}
