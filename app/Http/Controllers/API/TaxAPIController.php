<?php
/**
 * =============================================
 * -- File Name : TaxAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains all the report generation code tex setup module such as tax authority,tax master and tax formula
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxAPIRequest;
use App\Http\Requests\API\UpdateTaxAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Tax;
use App\Models\TaxType;
use App\Repositories\TaxRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxController
 * @package App\Http\Controllers\API
 */
class TaxAPIController extends AppBaseController
{
    /** @var  TaxRepository */
    private $taxRepository;

    public function __construct(TaxRepository $taxRepo)
    {
        $this->taxRepository = $taxRepo;
    }

    /**
     * Display a listing of the Tax.
     * GET|HEAD /taxes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxRepository->pushCriteria(new RequestCriteria($request));
        $this->taxRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxes = $this->taxRepository->all();

        return $this->sendResponse($taxes->toArray(), 'Taxes retrieved successfully');
    }

    /**
     * Store a newly created Tax in storage.
     * POST /taxes
     *
     * @param CreateTaxAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $company = Company::find($input["companySystemID"]);
        $input['companyID'] = $company->CompanyID;

        if (isset($input['effectiveFrom'])) {
            if ($input['effectiveFrom']) {
                $input['effectiveFrom'] = new Carbon($input['effectiveFrom']);
            }
        }

        $taxes = $this->taxRepository->create($input);

        return $this->sendResponse($taxes->toArray(), 'Tax saved successfully');
    }

    /**
     * Display the specified Tax.
     * GET|HEAD /taxes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('Tax not found');
        }

        return $this->sendResponse($tax->toArray(), 'Tax retrieved successfully');
    }

    /**
     * Update the specified Tax in storage.
     * PUT/PATCH /taxes/{id}
     *
     * @param  int $id
     * @param UpdateTaxAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $company = Company::find($input["companySystemID"]);
        $input['companyID'] = $company->CompanyID;
        if (isset($input['effectiveFrom'])) {
            if ($input['effectiveFrom']) {
                $input['effectiveFrom'] = new Carbon($input['effectiveFrom']);
            }
        }
        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('Tax not found');
        }

        $tax = $this->taxRepository->update($input, $id);

        return $this->sendResponse($tax->toArray(), 'Tax updated successfully');
    }

    /**
     * Remove the specified Tax from storage.
     * DELETE /taxes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('Tax not found');
        }

        $tax->delete();

        return $this->sendResponse($id, 'Tax deleted successfully');
    }


    public function getTaxMasterDatatable(Request $request)
    {
        $input = $request->all();
        $tax = Tax::with(['authority', 'type']);
        $companiesByGroup = "";

        if (array_key_exists('selectedCompanyID', $input)) {
            $tax = $tax->where('companySystemID', $input["selectedCompanyID"]);
        } else {

            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $companiesByGroup = $input['globalCompanyId'];
                $tax = $tax->where('companySystemID', $companiesByGroup);
            }
        }

        return \DataTables::eloquent($tax)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('erp_taxmaster_new.taxMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getTaxMasterFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companies = "";
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companies = [$selectedCompanyId];
        }
        $companiesByGroup = Company::whereIn('companySystemID',$companies)->get();
        $chartOfAccount = ChartOfAccount::where('isApproved', 1)->where('controllAccountYN', 1)->get();

        $taxType = TaxType::all();

        $output = array('companies' => $companiesByGroup,
            'taxType' => $taxType,
            'chartOfAccount' => $chartOfAccount
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
