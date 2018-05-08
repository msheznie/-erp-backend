<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxFormulaMasterAPIRequest;
use App\Http\Requests\API\UpdateTaxFormulaMasterAPIRequest;
use App\Models\Company;
use App\Models\TaxFormulaMaster;
use App\Repositories\TaxFormulaMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxFormulaMasterController
 * @package App\Http\Controllers\API
 */

class TaxFormulaMasterAPIController extends AppBaseController
{
    /** @var  TaxFormulaMasterRepository */
    private $taxFormulaMasterRepository;

    public function __construct(TaxFormulaMasterRepository $taxFormulaMasterRepo)
    {
        $this->taxFormulaMasterRepository = $taxFormulaMasterRepo;
    }

    /**
     * Display a listing of the TaxFormulaMaster.
     * GET|HEAD /taxFormulaMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxFormulaMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->taxFormulaMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxFormulaMasters = $this->taxFormulaMasterRepository->all();

        return $this->sendResponse($taxFormulaMasters->toArray(), 'Tax Formula Masters retrieved successfully');
    }

    /**
     * Store a newly created TaxFormulaMaster in storage.
     * POST /taxFormulaMasters
     *
     * @param CreateTaxFormulaMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxFormulaMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $company = Company::find($input["companySystemID"]);
        $input['companyID'] = $company->CompanyID;

        $taxFormulaMasters = $this->taxFormulaMasterRepository->create($input);

        return $this->sendResponse($taxFormulaMasters->toArray(), 'Tax Formula Master saved successfully');
    }

    /**
     * Display the specified TaxFormulaMaster.
     * GET|HEAD /taxFormulaMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var TaxFormulaMaster $taxFormulaMaster */
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            return $this->sendError('Tax Formula Master not found');
        }

        return $this->sendResponse($taxFormulaMaster->toArray(), 'Tax Formula Master retrieved successfully');
    }

    /**
     * Update the specified TaxFormulaMaster in storage.
     * PUT/PATCH /taxFormulaMasters/{id}
     *
     * @param  int $id
     * @param UpdateTaxFormulaMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxFormulaMasterAPIRequest $request)
    {
        $input = $request->all();
        unset($input['type']);
        $input = $this->convertArrayToValue($input);
        $company = Company::find($input["companySystemID"]);
        $input['companyID'] = $company->CompanyID;
        /** @var TaxFormulaMaster $taxFormulaMaster */
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            return $this->sendError('Tax Formula Master not found');
        }

        $taxFormulaMaster = $this->taxFormulaMasterRepository->update($input, $id);

        return $this->sendResponse($taxFormulaMaster->toArray(), 'TaxFormulaMaster updated successfully');
    }

    /**
     * Remove the specified TaxFormulaMaster from storage.
     * DELETE /taxFormulaMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var TaxFormulaMaster $taxFormulaMaster */
        $taxFormulaMaster = $this->taxFormulaMasterRepository->findWithoutFail($id);

        if (empty($taxFormulaMaster)) {
            return $this->sendError('Tax Formula Master not found');
        }

        $taxFormulaMaster->delete();

        return $this->sendResponse($id, 'Tax Formula Master deleted successfully');
    }

    public function getTaxFormulaMasterDatatable(Request $request)
    {
        $input = $request->all();
        $formula = TaxFormulaMaster::with('type');
        $companiesByGroup = "";
        if(array_key_exists ('selectedCompanyID' , $input)){
            if($input['selectedCompanyID'] > 0){
                $formula = $formula->where('companySystemID', $input['selectedCompanyID']);
            }
        }else {
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $companiesByGroup = $input['globalCompanyId'];
                $formula = $formula->where('companySystemID', $companiesByGroup);
            }
        }

        return \DataTables::eloquent($formula)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('taxCalculationformulaID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);
    }
}
