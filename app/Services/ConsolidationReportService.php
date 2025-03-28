<?php

namespace App\Services;

use App\Models\GroupParents;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConsolidationReportService
{
    public static function getCompanyOwnershipPeriods($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs, $groupTypes) {
        return GroupParents::select('company_system_id', 'holding_percentage', 'start_date', 'group_type')
            ->selectRaw("COALESCE(end_date, ?) as end_date", [$toDate])
            ->whereIn('company_system_id', $childCompanyIDs)
            ->where('parent_company_system_id', $parentCompanySystemID)
            ->whereIn('group_type', $groupTypes)
            ->whereBetween('start_date', [$fromDate, $toDate])
            ->whereBetween(DB::raw("COALESCE(end_date, '{$toDate}')"), [$fromDate, $toDate])
            ->orderBy('company_system_id')
            ->orderBy('start_date')
            ->get();
    }

    public static function getColumnKeys($consolidationKeys, $search) {
        foreach ($consolidationKeys as $item) {
            if (strpos($item, $search) !== false) {
                return $item;
            }
        }
        return $search;
    }

    public static function generateFilterQuery($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs, $currencyColumn, $groupTypes, $isCombinedFilter, $alias, $isDropDown): string
    {
        // Get periods
        $periods = self::getCompanyOwnershipPeriods($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs,$groupTypes);

        $companyPeriodsFilterData = [];

        $companyIDs = $periods->pluck("company_system_id")->unique()->values();
        foreach ($companyIDs as $companyID) {
            $filter = "WHEN " . $alias . ".companySystemID = " . $companyID;

            $periodDates = $periods->where('company_system_id',$companyID);
            $periodConditions = [];
            foreach ($periodDates as $periodDate) {
                $periodConditions[] = $alias . ".documentDate BETWEEN '" . $periodDate->start_date . "' AND '" . $periodDate->end_date . "'";
            }

            $filter .= " AND (" . join(" OR ", $periodConditions) . ") THEN ";
            if($isCombinedFilter) {
                $filter .= $alias . "." . $currencyColumn . " * -1";
            }
            else {
                $filter .= $alias . "." . $currencyColumn;
            }

            $companyPeriodsFilterData[] = $filter;
        }

        // Add parent company
        if($isCombinedFilter) {
            $companyPeriodsFilterData[] = "WHEN " . $alias . ".companySystemID = " . $parentCompanySystemID . " AND (" . $alias . ".documentDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "') THEN " . $alias . "." . $currencyColumn . " * -1";
        }

        // Generate filter query
        if(!empty($companyPeriodsFilterData)) {
            if($isDropDown) {
                return "IFNULL(CASE " . join(" ", $companyPeriodsFilterData) . " ELSE 0 END,0)";
            }
            else {
                return "IFNULL(SUM(CASE " . join(" ", $companyPeriodsFilterData) . " ELSE 0 END),0)";
            }
        }
        else {
            return "0";
        }
    }

    public static function generateConsolidationReportData($request, $consolidationKeys) {
        $fromDate = Carbon::parse($request->fromDate)->format("Y-m-d");
        $toDate = Carbon::parse($request->toDate)->format("Y-m-d");

        $servicelineIDs = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $parentCompanySystemID = collect($request->groupCompanySystemID)->first()['companySystemID'];
        // Remove parent company from companySystemID list
        $allCompanyIDs = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $childCompanyIDs = array_values(array_diff($allCompanyIDs,[$parentCompanySystemID]));

        if(empty($childCompanyIDs)) $childCompanyIDs[] = 0;

        $currencyColumn = $request->currency == 1 ? "documentLocalAmount" : "documentRptAmount";

        // Generate Query Data
        $columnNames = array_map(function($term) use ($consolidationKeys) {
            return self::getColumnKeys($consolidationKeys, $term);
        }, ["CMB","ELMN","CONS"]);

        [$cmbColumnName, $elmnColumnName, $consColumnName] = $columnNames;

        $cmbQuery = "IFNULL(gl.`" . $cmbColumnName . "`, 0)";
        $elmnQuery = "IFNULL(CASE WHEN rtd.controlAccountType = 2 THEN IF(ABS(el.`" . $elmnColumnName . "`) != 0, el.`" . $elmnColumnName . "` * - 1, 0) ELSE el.`" . $elmnColumnName . "` END, 0)";
        $consQuery = $cmbQuery . " - " . $elmnQuery;

        $cmbQuery .= " AS `" . $cmbColumnName . "`";
        $elmnQuery .= " AS `" . $elmnColumnName . "`";
        $consQuery .= " AS `" . $consColumnName . "`";

        $serviceLineIDsCondition = "serviceLineSystemID IN (" . join(',', $servicelineIDs) . ")";
        $glAccountTypeIDCondition = "glAccountTypeID = " . 2;
        $templateIDCondition = "templateMasterID = " . $request->templateType;

        $zeroRemoveConditions = [];
        foreach ($columnNames as $columnName) {
            $zeroRemoveConditions[] = "final.`" . $columnName . "` != 0";
        }

        $subsidiaryCompanyPeriodsFilterForGL = self::generateFilterQuery($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs, $currencyColumn, [1], true, "gl", false);
        $subsidiaryCompanyPeriodsFilterForEL = self::generateFilterQuery($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs, $currencyColumn, [1], false, "el", false);

        $sql = "SELECT *
                FROM
                    (
                        SELECT
                            " . $cmbQuery . ",
                            " . $elmnQuery . ",
                            " . $consQuery . ",
                            rtl.glAutoID,
                            rtl.glCode,
                            rtl.glDescription,
                            rtl.templateDetailID,
                            rtd.controlAccountType AS controlAccountType,
                            rtl.categoryType AS linkCatType,
                            rtd.categoryType AS templateCatType,
                            rtl.sortOrder
                        FROM
                            erp_companyreporttemplatelinks AS rtl
                        INNER JOIN erp_companyreporttemplatedetails AS rtd ON rtl.templateDetailID = rtd.detID
                        LEFT JOIN (
                            SELECT
                                " . $subsidiaryCompanyPeriodsFilterForGL . " AS `" . $cmbColumnName . "`,
                                0 AS `" . $elmnColumnName . "`,
                                0 AS `" . $consColumnName . "`,
                                gl.chartOfAccountSystemID
                            FROM
                                erp_generalledger AS gl
                            WHERE
                                gl.companySystemID IN (" . join(",", $allCompanyIDs) . ")
                                AND gl." . $serviceLineIDsCondition . "
                                AND gl." . $glAccountTypeIDCondition . "
                            GROUP BY gl.chartOfAccountSystemID
                    ) AS gl ON rtl.glAutoID = gl.chartOfAccountSystemID
                    LEFT JOIN (
                        SELECT
                            el.chartOfAccountSystemID,
                            " . $subsidiaryCompanyPeriodsFilterForEL . " AS `" . $elmnColumnName . "`
                        FROM
                            erp_elimination_ledger AS el
                        WHERE
                            el.companySystemID IN (" . join(",", $childCompanyIDs) . ")
                            AND el." . $serviceLineIDsCondition . "
                            AND el." . $glAccountTypeIDCondition . "
                        GROUP BY el.chartOfAccountSystemID
                    ) AS el ON el.chartOfAccountSystemID = rtl.glAutoID
                    WHERE 
                        rtl." . $templateIDCondition . "
                        AND rtl.glAutoID IS NOT NULL
                ) AS final
                WHERE " . join(" OR ", $zeroRemoveConditions) . "
                ORDER BY final.glAutoID";

        return DB::select($sql);
    }
}
