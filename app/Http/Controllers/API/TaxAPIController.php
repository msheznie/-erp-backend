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
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Tax;
use App\Models\TaxLedger;
use App\Models\TaxType;
use App\Repositories\TaxRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\VatReturnFillingMaster;

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

        return $this->sendResponse($taxes->toArray(), trans('custom.vates_retrieved_successfully'));
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
            return $this->sendError(trans('custom.tax_category_is_required'));
        }
        if($taxCategory == 2) {
            $messages = [
                'companySystemID.required' => trans('custom.company_field_is_required'),
                'authorityAutoID.required' => trans('custom.authority_field_is_required'),
                'inputVatGLAccountAutoID.required' => trans('custom.input_vat_transfer_gl_account_is_required'),
                'outputVatTransferGLAccountAutoID.required' => trans('custom.output_vat_transfer_gl_account_is_required'),
                'outputVatGLAccountAutoID.required' => trans('custom.output_vat_gl_account_is_required'),
                'inputVatTransferGLAccountAutoID.required' => trans('custom.input_vat_transfer_gl_account_field_is_required'),
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
                'companySystemID.required' => trans('custom.company_field_is_required'),
                'authorityAutoID.required' => trans('custom.authority_field_is_required'),
                'inputVatGLAccountAutoID.required' => trans('custom.wht_expense_gl_account_field_is_required'),
                'whtPercentage.required' => trans('custom.wht_percentage_field_is_required'),
                'whtType.required' => trans('custom.wht_type_field_is_required'),
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
                'companySystemID.required' => trans('custom.company_field_is_required'),
                'authorityAutoID.required' => trans('custom.authority_field_is_required'),
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
                    return $this->sendError(trans('custom.vat_is_already_defined_you_cannot_create_more_than'), 500);
                }
            }
        }

        if($taxCategory == 3){
            $isTaxExists = Tax::where('companySystemID',$input['companySystemID'])
                                ->where('taxDescription',$input['taxDescription'])
                                ->exists();
            if($isTaxExists){
                return $this->sendError(trans('custom.tax_description_already_exists'), 500);
            }

            $isWhtTypeExists = Tax::where('taxCategory',3)
                                    ->where('companySystemID',$input['companySystemID'])
                                    ->where('whtType',$input['whtType'])
                                    ->exists();

            if($isWhtTypeExists)
            {
                $whtType = $input['whtType'] == 0 ? trans('custom.wht_on_gross_amount') : trans('custom.wht_on_net_amount');
                return $this->sendError(trans('custom.wht_type_already_defined_cannot_create_more', ['type' => $whtType]), 500);
            }

            if(($input['isDefault'] == 1) && ($input['isActive'] == 0)){
                return $this->sendError(trans('custom.default_wht_cannot_inactive'), 500);
            }

            if($input['isDefault'] == 1){
                $defaultTax = Tax::where('taxCategory',3)
                                    ->where('isDefault',1)
                                    ->where('companySystemID', $input['companySystemID'])
                                    ->first();
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

        return $this->sendResponse($taxes->toArray(), trans('custom.vat_saved_successfully'));
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
            return $this->sendError(trans('custom.vat_not_found'));
        }

        return $this->sendResponse($tax->toArray(), trans('custom.vat_retrieved_successfully'));
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
            return $this->sendError(trans('custom.tax_master_not_found_1'));
        }

        $taxCategory = isset($input['taxCategory'])?$input['taxCategory']:0;

        if($taxCategory==0){
            return $this->sendError(trans('custom.tax_category_is_required'));
        }
        if($taxCategory == 2) {
            $messages = [
                'companySystemID.required' => trans('custom.company_field_is_required'),
                'authorityAutoID.required' => trans('custom.authority_field_is_required'),
                'inputVatGLAccountAutoID.required' => trans('custom.input_vat_transfer_gl_account_is_required'),
                'outputVatGLAccountAutoID.required' => trans('custom.output_vat_gl_account_is_required'),
                'inputVatTransferGLAccountAutoID.required' => trans('custom.input_vat_transfer_gl_account_field_is_required'),
                'outputVatTransferGLAccountAutoID.required' => trans('custom.output_vat_transfer_gl_account_is_required'),
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
                'companySystemID.required' => trans('custom.company_field_is_required'),
                'authorityAutoID.required' => trans('custom.authority_field_is_required'),
                'inputVatGLAccountAutoID.required' => trans('custom.wht_expense_gl_account_field_is_required'),
                'whtPercentage.required' => trans('custom.wht_percentage_field_is_required'),
                'whtType.required' => trans('custom.wht_type_field_is_required')
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
                'companySystemID.required' => trans('custom.company_field_is_required'),
                'authorityAutoID.required' => trans('custom.authority_field_is_required'),
                'currencyID.required' => trans('custom.currency_field_is_required'),
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
            $alreadyTaxDefined = Tax::where('taxCategory',$taxCategory)->where('companySystemID', $input['companySystemID'])->where('taxMasterAutoID','!=',$id)->exists();
            $keys = ['taxDescription', 'identification_no', 'isActive','authorityAutoID','companySystemID'];
            $inputData = array_intersect_key($input, array_flip($keys));
            $newAuthorityAutoID = $inputData['authorityAutoID'];
            $vatFilling = VatReturnFillingMaster::where('companySystemID', $input['companySystemID'])->exists();
            $authorityRecord  = Tax::where('taxCategory',$taxCategory)->where('companySystemID', $input['companySystemID'])->where('taxMasterAutoID',$id)->first();

            if($vatFilling && $authorityRecord && isset($newAuthorityAutoID))
            {
                if ($authorityRecord->authorityAutoID !== $newAuthorityAutoID) {
                    return $this->sendError(trans('custom.a_vat_return_filing_document_has_been_created_chan'), 500);

                }
            }
            if($alreadyTaxDefined){
                if($taxCategory == 2){
                    return $this->sendError(trans('custom.vat_is_already_defined_you_cannot_create_more_than'), 500);
                }
            }
            $input = $inputData;

        }

        if($taxCategory == 3){
            $isWhtTypeExists = Tax::where('taxCategory',3)
                ->where('companySystemID',$input['companySystemID'])
                ->where('whtType',$input['whtType'])
                ->where('isActive',true)
                ->where('taxMasterAutoID','!=',$input['taxMasterAutoID'])
                ->exists();

            if($isWhtTypeExists)
            {
                $whtType = $input['whtType'] == 0 ? trans('custom.wht_on_gross_amount') : trans('custom.wht_on_net_amount');
                if(($tax->isActive != true) && ($input['isActive'] != false))
                {
                    return $this->sendError(trans('custom.wht_type_already_defined_cannot_create_more', ['type' => $whtType]), 500);
                }
            }

            $isTaxExists = Tax::where('companySystemID',$input['companySystemID'])->where('taxMasterAutoID', '!=' , $id)->where('taxDescription',$input['taxDescription'])->exists();
            if($isTaxExists){
                return $this->sendError(trans('custom.tax_description_already_exists'), 500);
            }

            if ($input['isActive'] == 0){
                $isPullSupplier = SupplierMaster::where('primaryCompanySystemID',$tax->companySystemID)->where('whtType',$id)->exists();
                if($isPullSupplier){
                    return $this->sendError(trans('custom.tax_already_use_in_supplier_master_cannot_inactive'));
                }
            }

            if(($input['isDefault'] == 1) && ($input['isActive'] == 0)){
                return $this->sendError(trans('custom.default_wht_cannot_inactive'), 500);
            }

            if(($tax->isDefault == 1) && ($input['isActive'] == 0)){
                return $this->sendError(trans('custom.default_wht_cannot_change_inactive'), 500);
            }

            if(($tax->isDefault == 0) && ($input['isDefault'] == 1)){
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

        return $this->sendResponse($tax->toArray(), trans('custom.tax_updated_successfully'));
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
            return $this->sendError(trans('custom.vat_not_found'));
        }

        if($tax->taxCategory == 3){
            $isPullSupplier = SupplierMaster::where('primaryCompanySystemID',$tax->companySystemID)->where('whtType',$id)->exists();
            if($isPullSupplier){
                return $this->sendError(trans('custom.tax_already_use_in_supplier_master_cannot_delete'));
            }
        }

        $isAssigned = Tax::where('taxMasterAutoID',$id)->whereHas('formula_detail')->exists();

        if($isAssigned){
            return $this->sendError(trans('custom.cannot_delete_tax_master_is_added_to_a_tax_formula'));
        }

        $tax->delete();

        return $this->sendResponse($id, trans('custom.vat_deleted_successfully'));
    }


    public function getTaxMasterDatatable(Request $request)
    {
        $input = $request->all();
        $tax = Tax::with(['authority', 'type'])->where('taxCategory','!=',1);
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
//        $taxCategory = array(array('value' => 1, 'label' => 'Other'), array('value' => 2, 'label' => 'VAT'), array('value' => 3, 'label' => 'WHT'));
        $taxCategory = array(array('value' => 2, 'label' => trans('custom.vat')), array('value' => 3, 'label' => trans('custom.wht')));

        $suppliers = SupplierAssigned::where('companySystemID',$selectedCompanyId)
            ->where('isAssigned',-1)
            ->whereHas('master', function($query) use ($companies){
                $query->where('approvedYN',1)
                    ->where('isActive',1);
            })
            ->get();

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

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
}
