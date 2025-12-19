<?php

namespace App\helper;

use App\Models\NavigationMenus;
use App\Models\SrpErpTemplates;
use App\Repositories\SMECompanyPolicyMasterRepository;
use App\Repositories\CurrencyMasterRepository;
use App\Repositories\SMECompanyPolicyRepository;
use App\Repositories\SMECompanyPolicyValueRepository;
use App\Repositories\SMECompanyRepository;
use App\Repositories\SMEDocumentCodeMasterRepository;
use App\Repositories\SMEDocumentCodesRepository;
use App\Repositories\SMEEmpContractTypeRepository;
use App\Repositories\SMELaveGroupRepository;
use App\Repositories\SMELaveTypeRepository;
use App\Repositories\SMEOertimeGroupMasterRepository;
use App\Repositories\SMEReligionRepository;
use App\Repositories\SMESystemEmployeeTypeRepository;
use App\Repositories\SMETitleRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class hrCompany
{
    private $sMECompanyRepository;
    private $currencyRepository;
    private $sMETitleRepository;
    private $sMECompanyPolicyValueRepository;
    private $sMEReligionRepository;
    private $sMESystemEmployeeTypeRepository;
    private $sMEEmpContractTypeRepository;
    private $sMECompanyPolicyMasterRepository;
    private $sMECompanyPolicyRepository;
    private $sMEDocumentCodesRepository;
    private $sMEDocumentCodeMasterRepository;
    private $sMEOertimeGroupMasterRepository;
    private $sMELaveTypeRepository;
    private $sMELaveGroupRepository;

    private $date_time;

    public function __construct(
        SMECompanyRepository $smeCompanyRepo,
        CurrencyMasterRepository $currencyRepo,
        SMETitleRepository $smeTitleRepo,
        SMECompanyPolicyValueRepository $sMECompanyPolicyValueRepo,
        SMEReligionRepository $sMEReligionRepo,
        SMESystemEmployeeTypeRepository $sMESystemEmployeeTypeRepo,
        SMEEmpContractTypeRepository $sMEEmpContractTypeRepo,
        SMECompanyPolicyMasterRepository $sMECompanyPolicyMasterRepo,
        SMECompanyPolicyRepository $sMECompanyPolicyRepo,
        SMEDocumentCodesRepository $sMEDocumentCodesRepo,
        SMEDocumentCodeMasterRepository $sMEDocumentCodeMasterRepo,
        SMEOertimeGroupMasterRepository $sMEOertimeGroupMasterRepo,
        SMELaveTypeRepository $sMELaveTypeRepo,
        SMELaveGroupRepository $sMELaveGroupRepo)
    {        
        $this->sMECompanyRepository = $smeCompanyRepo;
        $this->currencyRepository = $currencyRepo;
        $this->sMETitleRepository = $smeTitleRepo;
        $this->sMECompanyPolicyValueRepository = $sMECompanyPolicyValueRepo;
        $this->sMEReligionRepository = $sMEReligionRepo;
        $this->sMESystemEmployeeTypeRepository = $sMESystemEmployeeTypeRepo;
        $this->sMEEmpContractTypeRepository = $sMEEmpContractTypeRepo;
        $this->sMECompanyPolicyMasterRepository = $sMECompanyPolicyMasterRepo;
        $this->sMECompanyPolicyRepository = $sMECompanyPolicyRepo;
        $this->sMEDocumentCodesRepository = $sMEDocumentCodesRepo;
        $this->sMEDocumentCodeMasterRepository = $sMEDocumentCodeMasterRepo;
        $this->sMEOertimeGroupMasterRepository = $sMEOertimeGroupMasterRepo;
        $this->sMELaveTypeRepository = $sMELaveTypeRepo;
        $this->sMELaveGroupRepository = $sMELaveGroupRepo;
        $this->date_time = Carbon::now();
    }

    public static function isHRSysIntegrated(){ /* Check Standard HR integrated */

        $table_exists = DB::select("SHOW TABLES LIKE 'srp_erp_company'");
        return ($table_exists)? true: false;
        /*
        We can not use Schema::hasTable('srp_erp_company') since we are changing the 
        DB connection based on sub-domain
        */        

        /* following tables get update in company creation
            - srp_titlemaster
            - srp_erp_companypolicymaster_value
            - srp_religion
            - srp_empcontracttypes
            - srp_erp_companypolicy
            - srp_erp_documentcodemaster
            - srp_erp_pay_overtimegroupmaster
            - srp_erp_leavetype
            - srp_erp_leavegroup
        */
    }

    public function store($det){
        if( !self::isHRSysIntegrated() ){//if HR company not integrated
            return true;
        }
        $loc_cur = $this->currencyRepository->find($det['localCurrencyID']);
        $rep_cur = $this->currencyRepository->find($det['reportingCurrency']);

        $company_id = $det['companySystemID'];
        $data = [
            'company_id'=> $company_id, 'company_name'=> $det['CompanyName'], 'company_code'=> $det['CompanyID'], 
            'company_default_currencyID'=> $det['localCurrencyID'],
            'company_default_currency'=> $loc_cur->CurrencyCode, 'company_default_decimal'=> $loc_cur->DecimalPlaces,

            'company_reporting_currencyID'=> $det['reportingCurrency'],
            'company_reporting_currency'=> $rep_cur->CurrencyCode, 'company_reporting_decimal'=> $rep_cur->DecimalPlaces,
                        
            'countryID'=> $det['companyCountry'], 'createdPCID'=> $det['createdPcID'], 'createdUserID'=> $det['createdUserID'],

            'confirmedYN'=> 1, 'timestamp'=> $this->date_time
        ];

        $this->sMECompanyRepository->insert($data); 
    
        $this->update_basic_tables($company_id, $det['CompanyID']);

        $this->add_navigation_templates($company_id);

        return true;
    }

    public function update($id, $input){
        if( !self::isHRSysIntegrated() ){//if HR compnay not integrated
            return true;
        }

        $loc_cur = $this->currencyRepository->find($input['localCurrencyID']);
        $rep_cur = $this->currencyRepository->find($input['reportingCurrency']);

        $data = [
            'company_name'=> $input['CompanyName'], 
            'company_default_currencyID'=> $input['localCurrencyID'],
            'company_default_currency'=> $loc_cur->CurrencyCode, 'company_default_decimal'=> $loc_cur->DecimalPlaces,

            'company_reporting_currencyID'=> $input['reportingCurrency'],
            'company_reporting_currency'=> $rep_cur->CurrencyCode, 'company_reporting_decimal'=> $rep_cur->DecimalPlaces,
            
            'countryID'=> $input['companyCountry'],
            'timestamp'=> Carbon::now()
        ];

        $this->sMECompanyRepository->update($data, $id); 

        return true;
    }

    public function update_basic_tables($company_id, $company_code){

        $this->sMETitleRepository->insert([
            ['TitleDescription'=> 'Mr','Erp_companyID'=> $company_id],
            ['TitleDescription'=> 'Mrs','Erp_companyID'=> $company_id],
            ['TitleDescription'=> 'Miss','Erp_companyID'=> $company_id],
        ]);

        $this->sMECompanyPolicyValueRepository->insert([
            ['companypolicymasterID' => '5', 'value' => 'General', 'systemValue' => '0', 'companyID' => $company_id],
            ['companypolicymasterID' => '6', 'value' => 'General', 'systemValue' => '0', 'companyID' => $company_id]
        ]);

        $this->sMEReligionRepository->insert([
            ['Religion' => 'Christianity', 'ReligionAr' => 'ؤاقهسفهشى','Erp_companyID' => $company_id],
            ['Religion' => 'Islam', 'ReligionAr' => 'ةعسخمهة','Erp_companyID' => $company_id],
            ['Religion' => 'Hinduism', 'ReligionAr' => 'اهىيع','Erp_companyID' => $company_id],
            ['Religion' => 'Buddhism', 'ReligionAr' => 'يبيبسبس','Erp_companyID' => $company_id],
            ['Religion' => 'Others', 'ReligionAr' => '', 'Erp_companyID' => $company_id],
        ]);

        $contractType = $this->sMESystemEmployeeTypeRepository
            ->selectRaw("employeeTypeID AS typeID, employeeType AS Description, {$company_id} AS Erp_CompanyID")
            ->get()->toArray();
        if($contractType){
            $this->sMEEmpContractTypeRepository->insert($contractType);
        }

        $policy = $this->sMECompanyPolicyMasterRepository
            ->selectRaw("companypolicymasterID, '{$company_id}' AS companyID, documentID, 1 AS isYN, defaultValue AS `value`")
            ->get()->toArray();            
        if($policy){
            $this->sMECompanyPolicyRepository->insert($policy);
        }

        $code_mas = $this->sMEDocumentCodesRepository->get()->toArray();            
        $code_arr = [];
        foreach ($code_mas as $i=> $data) {
            $code_arr[$i]['documentID'] = $data['documentID'];
            $code_arr[$i]['document'] = $data['document'];
            $code_arr[$i]['prefix'] = $data['documentID'];
            $code_arr[$i]['startSerialNo'] = 1;
            $code_arr[$i]['serialNo'] = 0;
            $code_arr[$i]['formatLength'] = 6;
            $code_arr[$i]['approvalLevel'] = 3;
            $code_arr[$i]['format_1'] = 'prefix';
            $code_arr[$i]['format_2'] = '/';
            $code_arr[$i]['companyID'] = $company_id;
            $code_arr[$i]['companyCode'] = $company_code; 
        }
        if($code_arr){
            $this->sMEDocumentCodeMasterRepository->insert($code_arr);
        }

        $this->sMEOertimeGroupMasterRepository->insert([
            'description' => "General", 'companyID' => $company_id, 'companyCode' => $company_code
        ]);

        $this->sMELaveTypeRepository->insert([
            ['description' => 'Annual Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $company_code],
            ['description' => 'Sick Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $company_code],
            ['description' => 'Emergency Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $company_code]
        ]);

        $this->sMELaveGroupRepository->insert([
            ['description' => 'Permanent Employees', 'companyID' => $company_id],
            ['description' => 'Temporary Employees', 'companyID' => $company_id]
        ]);

        return true;
    }

    public function add_navigation_templates($company_id){
        $menus = NavigationMenus::selectRaw('navigationMenuID, description')
            ->whereIn('isPortalYN', [3, 4]) /* 3=> HR | 4=> Manufacturing*/
            ->whereHas('form_category', function($q) {
                $q->whereHas('template', function ($q1) {
                    $q1->where('isdefault', 1);
                });
            })
            ->with(['form_category'=> function($q){
                $q->with(['template'=> function($q1){
                    $q1->selectRaw('TempMasterID, FormCatID')
                        ->where('isdefault', 1);
                }]);
            }])
            ->get();

        if(empty($menus)){
            return true;
        }

        $menus = $menus->toArray();


        $tem_det = [];
        foreach ($menus as $menu ){
            $tem_det[] = [
                'companyID'=> $company_id,
                'navigationMenuID'=> $menu['navigationMenuID'],
                'FormCatID'=> $menu['form_category']['FormCatID'],
                'TempMasterID'=> $menu['form_category']['template']['TempMasterID']
            ];
        }

        SrpErpTemplates::insert($tem_det);

        return true;
    }
}
