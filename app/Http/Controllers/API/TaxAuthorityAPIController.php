<?php
/**
 * =============================================
 * -- File Name : TaxAuthorityAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains all the CRUD for tax authority
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxAuthorityAPIRequest;
use App\Http\Requests\API\UpdateTaxAuthorityAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\TaxAuthority;
use App\Repositories\TaxAuthorityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxAuthorityController
 * @package App\Http\Controllers\API
 */
class TaxAuthorityAPIController extends AppBaseController
{
    /** @var  TaxAuthorityRepository */
    private $taxAuthorityRepository;

    public function __construct(TaxAuthorityRepository $taxAuthorityRepo)
    {
        $this->taxAuthorityRepository = $taxAuthorityRepo;
    }

    /**
     * Display a listing of the TaxAuthority.
     * GET|HEAD /taxAuthorities
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxAuthorityRepository->pushCriteria(new RequestCriteria($request));
        $this->taxAuthorityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxAuthorities = $this->taxAuthorityRepository->all();

        return $this->sendResponse($taxAuthorities->toArray(), trans('custom.tax_authorities_retrieved_successfully'));
    }

    /**
     * Store a newly created TaxAuthority in storage.
     * POST /taxAuthorities
     *
     * @param CreateTaxAuthorityAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxAuthorityAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $lastSerial = TaxAuthority::max('serialNo');

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = $lastSerial + 1;
        }
        $company = Company::find($input["companySystemID"]);
        $authoritySystemCode = ($company->CompanyID . '\\' . 'AUT' . str_pad($lastSerialNumber + 1, 6, '0', STR_PAD_LEFT));
        $input['authoritySystemCode'] = $authoritySystemCode;
        $input['serialNo'] = $lastSerialNumber;
        $input['companyID'] = $company->CompanyID;

        $taxAuthorities = $this->taxAuthorityRepository->create($input);

        return $this->sendResponse($taxAuthorities->toArray(), trans('custom.tax_authority_saved_successfully'));
    }

    /**
     * Display the specified TaxAuthority.
     * GET|HEAD /taxAuthorities/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var TaxAuthority $taxAuthority */
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            return $this->sendError(trans('custom.tax_authority_not_found'));
        }

        return $this->sendResponse($taxAuthority->toArray(), trans('custom.tax_authority_retrieved_successfully'));
    }

    /**
     * Update the specified TaxAuthority in storage.
     * PUT/PATCH /taxAuthorities/{id}
     *
     * @param  int $id
     * @param UpdateTaxAuthorityAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxAuthorityAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var TaxAuthority $taxAuthority */
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            return $this->sendError(trans('custom.tax_authority_not_found'));
        }

        $taxAuthority = $this->taxAuthorityRepository->update($input, $id);

        return $this->sendResponse($taxAuthority->toArray(), trans('custom.taxauthority_updated_successfully'));
    }

    /**
     * Remove the specified TaxAuthority from storage.
     * DELETE /taxAuthorities/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var TaxAuthority $taxAuthority */
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            return $this->sendError(trans('custom.tax_authority_not_found'));
        }

        $isAssigned = TaxAuthority::where('taxAuthourityMasterID',$id)->whereHas('tax')->exists();

        if($isAssigned){
            return $this->sendError(trans('custom.cannot_delete_tax_authority_is_assigned_to_a_tax'));
        }

        $taxAuthority->delete();

        return $this->sendResponse($id, trans('custom.tax_authority_deleted_successfully'));
    }

    public function getTaxAuthorityDatatable(Request $request)
    {
        $input = $request->all();
        $authority = TaxAuthority::select('*');
        $companiesByGroup = "";
        if(array_key_exists ('selectedCompanyID' , $input)){
            if($input['selectedCompanyID'] > 0){
                $authority = $authority->where('companySystemID', $input['selectedCompanyID']);
            }
        }else {
            if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                $companiesByGroup = $input['globalCompanyId'];
                $authority = $authority->where('companySystemID', $companiesByGroup);
            } else {
                $subCompanies = \Helper::getGroupCompany($input['globalCompanyId']);
                $authority = $authority->whereIn('companySystemID', $subCompanies);
            }
        }

        return \DataTables::eloquent($authority)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('taxAuthourityMasterID', $input['order'][0]['dir']);
                    }
                }
            })
             ->filter(function ($query) use ($request) {
                  if ($request->has('search') && !is_null($request->get('search')['value'])) {
                      $regex = str_replace("\\", "\\\\", $request->get('search')['value']);
                      return $query->where(function ($query) use($regex) {
                          $query->where('authoritySystemCode', 'LIKE', "%{$regex}%")
                              ->orWhere('authoritySecondaryCode', 'LIKE', "%{$regex}%");
                      });
                  }
              })
            ->addIndexColumn()
            ->make(true);
    }

    public function getTaxAuthorityFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companies = "";
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companies = [$selectedCompanyId];
        }
        $companiesByGroup = Company::whereIn('companySystemID', $companies)->get();

        $currency = CurrencyMaster::all();

        $chartOfAccount = ChartOfAccount::where('isApproved', 1)->where('controllAccountYN', 1)
                                        ->whereHas('chartofaccount_assigned', function($query) use ($companies){
                                            $query->whereIn('companySystemID', $companies)
                                                  ->where('isAssigned', -1);
                                        })->get();

        $output = array('companies' => $companiesByGroup,
            'currency' => $currency,
            'chartOfAccount' => $chartOfAccount,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getAuthorityByCompany(Request $request)
    {
        $output = TaxAuthority::where('companySystemID', $request->companySystemID)->get();
        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getAccountByAuthority(Request $request)
    {
        $output = TaxAuthority::where('taxAuthourityMasterID', $request->taxAuthourityMasterID)->first();
        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
