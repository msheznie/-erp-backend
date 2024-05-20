<?php
/**
 * =============================================
 * -- File Name : TaxAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains all CRUD for tax master
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxAPIRequest;
use App\Http\Requests\API\UpdateTaxAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\SupplierMaster;
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

        return $this->sendResponse($taxes->toArray(), 'VATes retrieved successfully');
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

        $taxCategory = isset($input['taxCategory'])?$input['taxCategory']:0;

        if($taxCategory==0){
            return $this->sendError('Tax Category is required');
        }
        if($taxCategory == 2) {
            $messages = [
                'companySystemID.required' => 'Company field is required.',
                'authorityAutoID.required' => 'Authority field is required.',
                'inputVatGLAccountAutoID.required' => 'Input Vat Transfer GL Account is required.',
                'outputVatTransferGLAccountAutoID.required' => 'Output Vat Transfer GL Account is required.',
                'outputVatGLAccountAutoID.required' => 'Output Vat GL Account  is required.',
                'inputVatTransferGLAccountAutoID.required' => 'Input Vat Transfer GL Account field is required.',
            ];
            $validator = \Validator::make($input, [
                'companySystemID' => 'required|numeric|min:1',
                'taxDescription' => 'required',
               // 'taxShortCode' => 'required',
                'authorityAutoID' => 'required|numeric|min:1',
                'inputVatGLAccountAutoID' => 'required|numeric|min:1',
                'outputVatGLAccountAutoID' => 'required|numeric|min:1',
                'inputVatTransferGLAccountAutoID' => 'required|numeric|min:1',
                'outputVatTransferGLAccountAutoID' => 'required|numeric|min:1',
                //'taxReferenceNo' => 'required'

            ], $messages);
        }elseif ($taxCategory == 3){
            $messages = [
                'companySystemID.required' => 'Company field is required.',
                'authorityAutoID.required' => 'Authority field is required.',
                'inputVatGLAccountAutoID.required' => 'WHT Expense GL Account field is required.',
                'whtPercentage.required' => 'WHT Percentage field is required.',
                'whtType.required' => 'WHT Type field is required.',
            ];
            $validator = \Validator::make($input, [
                'companySystemID' => 'required|numeric|min:1',
                'taxDescription' => 'required',
               // 'taxShortCode' => 'required',
                'authorityAutoID' => 'required|numeric|min:1',
                'inputVatGLAccountAutoID' => 'required|numeric|min:1',
                'whtPercentage' => 'required',
                'whtType' => 'required'
                //'taxReferenceNo' => 'required'

            ], $messages);
        }else{
            $messages = [
                'companySystemID.required' => 'Company field is required.',
                'authorityAutoID.required' => 'Authority field is required.',
            ];
            $validator = \Validator::make($input, [
                'companySystemID' => 'required|numeric|min:1',
                'taxDescription' => 'required',
                //'taxShortCode' => 'required',
                'authorityAutoID' => 'required|numeric|min:1',
            ], $messages);
        }

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if($taxCategory == 2){
            $input['isDefault'] = 1;
            $alreadyTaxDefined = Tax::where('taxCategory',$taxCategory)
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->exists();

            if(!empty($alreadyTaxDefined)){
                if($taxCategory == 2){
                    return $this->sendError('VAT is already defined. You cannot create more than one active VAT', 500);
                }
            }
        }

        if($taxCategory == 3){
            if(($input['isDefault'] == 1) && ($input['isActive'] == 0)){
                return $this->sendError('Default WHT cannot inactive', 500);
            }

            if($input['isDefault'] == 1){
                $defaultTax = Tax::where('taxCategory',3)->where('isDefault',1)->where('companySystemID', $input['companySystemID'])->first();
                if($defaultTax){
                    $defaultTax->isDefault = 0;
                    $defaultTax->save();
                }
            }
        }

        $company = Company::find($input["companySystemID"]);
        $input['companyID'] = $company->CompanyID;

        if (isset($input['effectiveFrom'])) {
            if ($input['effectiveFrom']) {
                $input['effectiveFrom'] = new Carbon($input['effectiveFrom']);
            }
        }

        if(isset($input['inputVatGLAccountAutoID']) && $input['inputVatGLAccountAutoID']>0){
            $glinput = ChartOfAccount::find($input['inputVatGLAccountAutoID']);
            $input['inputVatGLAccount'] = $glinput->AccountCode;
        }

        if(isset($input['outputVatGLAccountAutoID']) && $input['outputVatGLAccountAutoID']>0){
            $gloutput = ChartOfAccount::find($input['outputVatGLAccountAutoID']);
            $input['outputVatGLAccount'] = $gloutput->AccountCode;
        }

        if(isset($input['inputVatTransferGLAccountAutoID']) && $input['inputVatTransferGLAccountAutoID']>0){
            $gltrans = ChartOfAccount::find($input['inputVatTransferGLAccountAutoID']);
            $input['inputVatTransferGLAccount'] = $gltrans->AccountCode;
        }

        if(isset($input['outputVatTransferGLAccountAutoID']) && $input['outputVatTransferGLAccountAutoID']>0){
            $gloutputtrans = ChartOfAccount::find($input['outputVatTransferGLAccountAutoID']);
            $input['outputVatTransferGLAccount'] = $gloutputtrans->AccountCode;
        }


        $taxes = $this->taxRepository->create($input);

        return $this->sendResponse($taxes->toArray(), 'VAT saved successfully');
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
            return $this->sendError('VAT not found');
        }

        return $this->sendResponse($tax->toArray(), 'VAT retrieved successfully');
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

        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('tax master not found');
        }

        $taxCategory = isset($input['taxCategory'])?$input['taxCategory']:0;

        if($taxCategory==0){
            return $this->sendError('Tax Category is required');
        }
        if($taxCategory == 2) {
            $messages = [
                'companySystemID.required' => 'Company field is required.',
                'authorityAutoID.required' => 'Authority field is required.',
                'inputVatGLAccountAutoID.required' => 'Input Vat Transfer GL Account is required.',
                'outputVatGLAccountAutoID.required' => 'Output Vat GL Account  is required.',
                'inputVatTransferGLAccountAutoID.required' => 'Input Vat Transfer GL Account field is required.',
                'outputVatTransferGLAccountAutoID.required' => 'Output Vat Transfer GL Account field is required.',
            ];
            $validator = \Validator::make($input, [
                'companySystemID' => 'required|numeric|min:1',
                'taxDescription' => 'required',
                //'taxShortCode' => 'required',
                'authorityAutoID' => 'required|numeric|min:1',
                'inputVatGLAccountAutoID' => 'required|numeric|min:1',
                'outputVatGLAccountAutoID' => 'required|numeric|min:1',
                'inputVatTransferGLAccountAutoID' => 'required|numeric|min:1',
                //'taxReferenceNo' => 'required'

            ], $messages);
        }elseif ($taxCategory == 3){
            $messages = [
                'companySystemID.required' => 'Company field is required.',
                'authorityAutoID.required' => 'Authority field is required.',
                'inputVatGLAccountAutoID.required' => 'WHT Expense GL Account field is required.',
                'whtPercentage.required' => 'WHT Percentage field is required.',
                'whtType.required' => 'WHT Type field is required.'
            ];
            $validator = \Validator::make($input, [
                'companySystemID' => 'required|numeric|min:1',
                'taxDescription' => 'required',
                //'taxShortCode' => 'required',
                'authorityAutoID' => 'required|numeric|min:1',
                'inputVatGLAccountAutoID' => 'required|numeric|min:1',
                'whtPercentage' => 'required',
                'whtType' => 'required'
                //'taxReferenceNo' => 'required'

            ], $messages);
        }else{
            $messages = [
                'companySystemID.required' => 'Company field is required.',
                'authorityAutoID.required' => 'Authority field is required.',
                'currencyID.required' => 'Currency field is required.',
            ];
            $validator = \Validator::make($input, [
                'companySystemID' => 'required|numeric|min:1',
                'taxDescription' => 'required',
                //'taxShortCode' => 'required',
                'authorityAutoID' => 'required|numeric|min:1',
            ], $messages);
        }
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if($taxCategory == 2){
            $input['isDefault'] = 1;
            $alreadyTaxDefined = Tax::where('taxCategory',$taxCategory)->where('taxMasterAutoID','!=',$id)->exists();
            if($alreadyTaxDefined){
                if($taxCategory == 2){
                    return $this->sendError('VAT is already defined. You cannot create more than one active VAT', 500);
                }
            }
        }

        if($taxCategory == 3){
            if(($tax->isDefault == 1) && ($input['isActive'] == 0)){
                return $this->sendError('Default WHT cannot change inactive', 500);
            }
            if(($tax->isDefault == 0) && ($input['isDefault'] == 1)){
                $defaultTax = Tax::where('taxCategory',3)->where('isDefault',1)->where('companySystemID', $input['companySystemID'])->first();
                $defaultTax->isDefault = 0;
                $defaultTax->save();
            }
        }

        $company = Company::find($input["companySystemID"]);
        $input['companyID'] = $company->CompanyID;
        if (isset($input['effectiveFrom'])) {
            if ($input['effectiveFrom']) {
                $input['effectiveFrom'] = new Carbon($input['effectiveFrom']);
            }
        }

        if(isset($input['inputVatGLAccountAutoID']) && $input['inputVatGLAccountAutoID']>0  && $tax->inputVatGLAccountAutoID != $input['inputVatGLAccountAutoID']){
            $glinput = ChartOfAccount::find($input['inputVatGLAccountAutoID']);
            $input['inputVatGLAccount'] = $glinput->AccountCode;
        }

        if(isset($input['outputVatGLAccountAutoID']) && $input['outputVatGLAccountAutoID']>0  && $tax->outputVatGLAccountAutoID != $input['outputVatGLAccountAutoID']){
            $gloutput = ChartOfAccount::find($input['outputVatGLAccountAutoID']);
            $input['outputVatGLAccount'] = $gloutput->AccountCode;
        }

        if(isset($input['inputVatTransferGLAccountAutoID']) && $input['inputVatTransferGLAccountAutoID']>0 && $tax->inputVatTransferGLAccountAutoID != $input['inputVatTransferGLAccountAutoID']){
            $gltrans = ChartOfAccount::find($input['inputVatTransferGLAccountAutoID']);
            $input['inputVatTransferGLAccount'] = $gltrans->AccountCode;
        }

        if(isset($input['outputVatTransferGLAccountAutoID']) && $input['outputVatTransferGLAccountAutoID']>0){
            $gloutputtrans = ChartOfAccount::find($input['outputVatTransferGLAccountAutoID']);
            $input['outputVatTransferGLAccount'] = $gloutputtrans->AccountCode;
        }


        $tax = $this->taxRepository->update($input, $id);

        return $this->sendResponse($tax->toArray(), 'TAX updated successfully');
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
            return $this->sendError('VAT not found');
        }

        $isAssigned = Tax::where('taxMasterAutoID',$id)->whereHas('formula_detail')->exists();

        if($isAssigned){
            return $this->sendError('Cannot delete. Tax master is added to a tax formula.');
        }

        $tax->delete();

        return $this->sendResponse($id, 'VAT deleted successfully');
    }


    public function getTaxMasterDatatable(Request $request)
    {
        $input = $request->all();
        $tax = Tax::with(['authority', 'type']);
        $companiesByGroup = "";

        if (array_key_exists('selectedCompanyID', $input)) {
            $tax = $tax->where('companySystemID', $input["selectedCompanyID"]);
        } else {
            if(array_key_exists ('selectedCompanyID' , $input)){
                if($input['selectedCompanyID'] > 0){
                    $tax = $tax->where('companySystemID', $input['selectedCompanyID']);
                }
            }else {
                if (!\Helper::checkIsCompanyGroup($input['globalCompanyId'])) {
                    $companiesByGroup = $input['globalCompanyId'];
                    $tax = $tax->where('companySystemID', $companiesByGroup);
                } else {
                    $subCompanies = \Helper::getGroupCompany($input['globalCompanyId']);
                    $tax = $tax->whereIn('companySystemID', $subCompanies);
                }
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
        $chartOfAccount = ChartOfAccount::where('isApproved', 1)->whereIn('controlAccountsSystemID', [3,4])
                                        ->whereHas('chartofaccount_assigned', function($query) use ($companies){
                                            $query->whereIn('companySystemID', $companies)
                                                  ->where('isAssigned', -1);
                                        })->get();

        $taxType = TaxType::all();
        $taxCategory = array(array('value' => 1, 'label' => 'Other'), array('value' => 2, 'label' => 'VAT'), array('value' => 3, 'label' => 'WHT'));

        $suppliers = SupplierMaster::where('isActive',1)->get();

        $isActiveState = Tax::where('taxCategory',3)->where('isActive',1)->where('companySystemID', $selectedCompanyId)->exists();

        $isDefaultState = Tax::where('taxCategory',3)->where('companySystemID', $selectedCompanyId)->exists();

        $output = array(
            'companies' => $companiesByGroup,
            'taxType' => $taxType,
            'chartOfAccount' => $chartOfAccount,
            'taxCategory' => $taxCategory,
            'suppliers' => $suppliers,
            'activeState' => $isActiveState ? 0 : 1,
            'defaultState' => $isDefaultState ? 0 : 1
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
