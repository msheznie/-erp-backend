<?php

namespace App\Repositories;

use App\Models\ChartOfAccount;
use App\Repositories\BaseRepository;

/**
 * Class ChartOfAccountRepository
 * @package App\Repositories
 * @version February 27, 2018, 9:57 am UTC
 *
 * @method ChartOfAccount findWithoutFail($id, $columns = ['*'])
 * @method ChartOfAccount find($id, $columns = ['*'])
 * @method ChartOfAccount first($columns = ['*'])
*/
class ChartOfAccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccounts',
        'isApproved',
        'approvedBy',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'interCompanyID',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ChartOfAccount::class;
    }

    /**
     * Get chart of accounts for the pull API with all filters applied.
     *
     * @param array $filters
     * @param bool $usePagination
     * @param int $page
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getPullChartOfAccounts(array $filters, bool $usePagination, int $page, int $perPage)
    {
        $companySystemID = $filters['company_id'];
        $categoryFilter = $filters['categoryFilter'] ?? null;
        $controlAccountCodes = $filters['controlAccountCodes'] ?? [];
        $accountCodeFilter = $filters['accountCodeFilter'] ?? [];
        $controlAccountYNFilter = $filters['controlAccountYNFilter'] ?? null;
        $isBankFilter = $filters['isBankFilter'] ?? null;
        $defaultTemplateCategoryDescriptions = $filters['defaultTemplateCategoryDescriptions'] ?? [];

        $query = ChartOfAccount::select(
            'chartOfAccountSystemID',
            'AccountCode',
            'AccountDescription',
            'catogaryBLorPLID',
            'controlAccountsSystemID',
            'controllAccountYN',
            'isActive',
            'isBank',
            'AllocationID',
            'relatedPartyYN',
            'reportTemplateCategory'
        )
            ->where('primaryCompanySystemID', $companySystemID)
            ->where('isApproved', 1)
            ->where('isActive', 1)
            ->whereHas('chartofaccount_assigned', function ($query) {
                $query->where('isActive', 1)
                    ->where('isAssigned', -1);
            });

        if ($categoryFilter !== null) {
            $query->where('catogaryBLorPL', $categoryFilter);
        }

        if (!empty($controlAccountCodes)) {
            $query->whereIn('controlAccounts', $controlAccountCodes);
        }

        if (!empty($accountCodeFilter)) {
            $query->whereIn('AccountCode', $accountCodeFilter);
        }

        if ($controlAccountYNFilter !== null) {
            if ($controlAccountYNFilter === 1) {
                $query->where('controllAccountYN', 1);
            } else {
                $query->where('controllAccountYN', '!=', 1);
            }
        }

        if ($isBankFilter !== null) {
            if ($isBankFilter === 1) {
                $query->where('isBank', 1);
            } else {
                $query->where('isBank', '!=', 1);
            }
        }

        if (!empty($defaultTemplateCategoryDescriptions)) {
            $query->whereHas('templateCategoryDetails', function ($q) use ($defaultTemplateCategoryDescriptions) {
                $q->whereIn('description', $defaultTemplateCategoryDescriptions);
            });
        }

        $query->with([
            'chartofaccount_assigned' => function ($q) use ($companySystemID) {
                $q->select('chartOfAccountSystemID', 'companySystemID')
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->with('company:companySystemID,CompanyName');
            },
            'controlAccount' => function ($q) {
                $q->select('controlAccountsSystemID', 'description');
            },
            'accountType' => function ($q) {
                $q->select('accountsType', 'description');
            },
            'allocation' => function ($q) {
                $q->select('AutoID', 'Desciption');
            },
            'templateCategoryDetails' => function ($q) {
                $q->select('detID', 'description');
            },
        ])
            ->orderBy('chartOfAccountSystemID', 'asc');

        return $usePagination
            ? $query->paginate($perPage, ['*'], 'page', $page)
            : $query->get();
    }
}
