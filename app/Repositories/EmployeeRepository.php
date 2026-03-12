<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeNavigation;
use App\Models\NavigationUserGroupSetup;
use App\Repositories\BaseRepository;

/**
 * Class EmployeeRepository
 * @package App\Repositories
 * @version February 13, 2018, 8:41 am UTC
 *
 * @method Employee findWithoutFail($id, $columns = ['*'])
 * @method Employee find($id, $columns = ['*'])
 * @method Employee first($columns = ['*'])
*/
class EmployeeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empID',
        'serial',
        'empLeadingText',
        'empUserName',
        'empTitle',
        'empInitial',
        'empName',
        'empName_O',
        'empFullName',
        'empSurname',
        'empSurname_O',
        'empFirstName',
        'empFirstName_O',
        'empFamilyName',
        'empFamilyName_O',
        'empFatherName',
        'empFatherName_O',
        'empManagerAttached',
        'empDateRegistered',
        'empTelOffice',
        'empTelMobile',
        'empLandLineNo',
        'extNo',
        'empFax',
        'empEmail',
        'empLocation',
        'empDateTerminated',
        'empLoginActive',
        'empActive',
        'userGroupID',
        'empCompanyID',
        'religion',
        'isLoggedIn',
        'isLoggedOutFailYN',
        'logingFlag',
        'isSuperAdmin',
        'discharegedYN',
        'hrusergroupID',
        'isConsultant',
        'isTrainee',
        'is3rdParty',
        '3rdPartyCompanyName',
        'gender',
        'designation',
        'nationality',
        'isManager',
        'isApproval',
        'isDashBoard',
        'isAdmin',
        'isBasicUser',
        'ActivationCode',
        'ActivationFlag',
        'isHR_admin',
        'isLock',
        'opRptManagerAccess',
        'isSupportAdmin',
        'isHSEadmin',
        'excludeObjectivesYN',
        'machineID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Employee::class;
    }

   
    public function getProductNames(): array
    {
        return [
            'ERP',
            'POS',
            'HRMS',
            'OBP',
            'Manufacturing',
            'Self Service',
            'Club Management',
            'Help Desk',
            'Operation & PMS',
            'Survey Management',
            'Contract Management',
            'QHSE',
        ];
    }

   
    public function getUserTypeAliases(): array
    {
        return [
            'ESS user'         => 'ESS User',
            'Super admin'      => 'Super Admin',
            'Enterprise User'  => 'Enterprise User',
            'Functional user'  => 'Functional User',
            'External user'    => 'External Portal User',
        ];
    }

    public function normalizeMultiValue($input)
    {
        if (!is_array($input)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', $input)));
    }

    public function buildUserResponseItem($employee, $accessMap = [])
    {
        $user = $employee->user_data;

        return [
            'registeredCompany' => $employee->emp_company->CompanyName ?? '',
            'empID'             => $employee->empID,
            'secondaryCode'     => $employee->hr_emp->EmpSecondaryCode ?? '',
            'name'               => $employee->empName,
            'userName'           => $employee->empUserName,
            'email'              => $employee->empEmail,
            'designation'        => $employee->erp_designation->designation ?? '',
            'userType'          => $this->mapUserTypeToAlias(($user && $user->user_type) ? $user->user_type->userType : ''),
            'empLoginActive'   => $employee->empLoginActive == 1,
            'dischargedYN'     => $employee->discharegedYN != 0,
            'accessDetails'     => $accessMap[$employee->employeeSystemID] ?? [],
        ];
    }

    public function mapUserTypeToAlias($dbUserType)
    {
        if (empty($dbUserType)) {
            return '';
        }

        $aliases = $this->getUserTypeAliases();
        $reversed = array_flip($aliases);

        return $reversed[$dbUserType] ?? $dbUserType;
    }

    public function batchLoadAccessDetails(array $employeeIds)
    {
        if (empty($employeeIds)) {
            return [];
        }

        $navigations = EmployeeNavigation::with([
            'company' => function ($query) {
                $query->select('companySystemID', 'CompanyID', 'CompanyName');
            },
            'usergroup' => function ($query) {
                $query->select('userGroupID', 'description');
            },
        ])
            ->select('employeeSystemID', 'userGroupID', 'companyID')
            ->whereIn('employeeSystemID', $employeeIds)
            ->get();

        $pairs = $navigations->unique(fn($n) => $n->userGroupID . '_' . $n->companyID);

        $productMap = [];
        if ($pairs->isNotEmpty()) {
            $productRecords = NavigationUserGroupSetup::where(function ($query) use ($pairs) {
                    $pairs->each(function ($nav) use ($query) {
                        $query->orWhere(function ($q) use ($nav) {
                            $q->where('userGroupID', $nav->userGroupID)
                              ->where('companyID', $nav->companyID);
                        });
                    });
                })
                ->where('isPortalYN', true)
                ->where('levelNo', 0)
                ->whereIn('description', $this->getProductNames())
                ->distinct()
                ->get(['userGroupID', 'companyID', 'description']);

            foreach ($productRecords as $rec) {
                $key = $rec->userGroupID . '_' . $rec->companyID;
                $productMap[$key][] = $rec->description;
            }
        }

        $portalPairs = [];
        if ($pairs->isNotEmpty()) {
            $portalRecords = NavigationUserGroupSetup::where(function ($query) use ($pairs) {
                    $pairs->each(function ($nav) use ($query) {
                        $query->orWhere(function ($q) use ($nav) {
                            $q->where('userGroupID', $nav->userGroupID)
                              ->where('companyID', $nav->companyID);
                        });
                    });
                })
                ->where('isPortalYN', true)
                ->distinct()
                ->get(['userGroupID', 'companyID']);

            foreach ($portalRecords as $rec) {
                $portalPairs[$rec->userGroupID . '_' . $rec->companyID] = true;
            }
        }

        $accessMap = [];
        foreach ($navigations as $nav) {
            if (!$nav->company || !$nav->usergroup) {
                continue;
            }

            $key = $nav->userGroupID . '_' . $nav->companyID;

            if (!isset($portalPairs[$key])) {
                continue;
            }

            $products = $productMap[$key] ?? ['Self Service'];

            $accessMap[$nav->employeeSystemID][] = [
                'company'        => $nav->company->CompanyName ?? '',
                'companyID'     => $nav->company->CompanyID ?? '',
                'userGroup'     => $nav->usergroup->description ?? '',
                'productAccess' => implode(', ', array_unique($products)),
            ];
        }

        return $accessMap;
    }

    public function getEmployeeIDsByProductAccess($productNameOrNames)
    {
        $productNames = is_array($productNameOrNames)
            ? array_values(array_filter(array_map('trim', $productNameOrNames)))
            : [trim((string) $productNameOrNames)];
        $productNames = array_filter($productNames);
        if (empty($productNames)) {
            return [];
        }

        $validProducts = $this->getProductNames();
        $productMap = collect($validProducts)->mapWithKeys(fn($p) => [strtolower($p) => $p])->all();

        if (count($productNames) === 1) {
            $productName = $productMap[strtolower($productNames[0])] ?? null;
            if ($productName === null) {
                return [];
            }
            if (strtolower($productName) === 'self service') {
                $otherProducts = array_diff($this->getProductNames(), ['Self Service']);
                $employeesWithProducts = EmployeeNavigation::withProducts($otherProducts)
                    ->distinct()
                    ->pluck('srp_erp_employeenavigation.employeeSystemID')
                    ->toArray();
                $allPortalEmployees = EmployeeNavigation::portalUsers()
                    ->distinct()
                    ->pluck('srp_erp_employeenavigation.employeeSystemID')
                    ->toArray();
                return array_values(array_diff($allPortalEmployees, $employeesWithProducts));
            }
            return EmployeeNavigation::withProduct($productName)
                ->distinct()
                ->pluck('srp_erp_employeenavigation.employeeSystemID')
                ->toArray();
        }

        $normalized = [];
        foreach ($productNames as $name) {
            $matched = $productMap[strtolower($name)] ?? null;
            if ($matched !== null) {
                $normalized[] = $matched;
            }
        }
        $normalized = array_unique($normalized);
        if (empty($normalized)) {
            return [];
        }

        $hasSelfService = in_array('Self Service', $normalized);
        $others = array_values(array_diff($normalized, ['Self Service']));

        $employeeIDs = [];
        if (!empty($others)) {
            $employeeIDs = EmployeeNavigation::withProducts($others)
                ->distinct()
                ->pluck('srp_erp_employeenavigation.employeeSystemID')
                ->toArray();
        }
        if ($hasSelfService) {
            $selfServiceIds = $this->getEmployeeIDsByProductAccess('Self Service');
            $employeeIDs = array_values(array_unique(array_merge($employeeIDs, $selfServiceIds)));
        }
        return $employeeIDs;
    }
}
