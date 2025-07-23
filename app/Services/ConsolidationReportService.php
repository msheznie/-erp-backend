<?php

namespace App\Services;

use App\Models\GeneralLedger;
use App\Models\GroupParents;
use App\Models\ReportTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConsolidationReportService
{
    public static function getCompanyOwnershipPeriods($parentCompanySystemID, $childCompanyIDs, $groupTypes) {
        return GroupParents::with('companyMaster')
            ->whereIn('company_system_id', $childCompanyIDs)
            ->where('parent_company_system_id', $parentCompanySystemID)
            ->whereIn('group_type', $groupTypes)
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
        $periods = self::getCompanyOwnershipPeriods($parentCompanySystemID, $childCompanyIDs,$groupTypes);

        $companyPeriodsFilterData = [];

        $companyIDs = $periods->pluck("company_system_id")->unique()->values();
        foreach ($companyIDs as $companyID) {
            $filter = "WHEN " . $alias . ".companySystemID = " . $companyID;

            $periodDates = $periods->where('company_system_id',$companyID)->where('group_type', 1);
            $periodConditions = [];
            foreach ($periodDates as $periodDate) {

                $rowStartDate = Carbon::parse($periodDate->start_date);
                $rowEndDate = ($periodDate->end_date == null) ? $toDate : Carbon::parse($periodDate->end_date);

                if($rowStartDate->isBetween($fromDate, $toDate) || $rowEndDate->isBetween($fromDate, $toDate)) {
                    $queryStartDate = ($fromDate >= $rowStartDate) ? $fromDate : $rowStartDate;
                    $queryEndDate = ($toDate <= $rowEndDate) ? $toDate : $rowEndDate;

                    $periodConditions[] = "DATE(" . $alias . ".documentDate) BETWEEN '" . $queryStartDate->format("Y-m-d") . "' AND '" . $queryEndDate->format("Y-m-d") . "'";
                }
            }

            if (!empty($periodConditions)) {
                $filter .= " AND (" . join(" OR ", $periodConditions) . ") THEN ";
                if($isCombinedFilter) {
                    $filter .= $alias . "." . $currencyColumn . " * -1";
                }
                else {
                    $filter .= $alias . "." . $currencyColumn;
                }

                $companyPeriodsFilterData[] = $filter;
            }
        }

        if($isCombinedFilter) {
            // Add parent company
            $companyPeriodsFilterData[] = "WHEN " . $alias . ".companySystemID = " . $parentCompanySystemID . " AND (DATE(" . $alias . ".documentDate) BETWEEN '" . $fromDate->format("Y-m-d") . "' AND '" . $toDate->format("Y-m-d") . "') THEN " . $alias . "." . $currencyColumn . " * -1";

            // Add Joint venture and Associate company periods data for BS report CMB column
            $periodDates = $periods->whereIn('group_type', [2,3]);
            foreach ($periodDates as $periodDate) {

                $rowStartDate = Carbon::parse($periodDate->start_date);
                $rowEndDate = ($periodDate->end_date == null) ? $toDate : Carbon::parse($periodDate->end_date);

                if($rowStartDate->isBetween($fromDate, $toDate) || $rowEndDate->isBetween($fromDate, $toDate)) {
                    $queryStartDate = ($fromDate >= $rowStartDate) ? $fromDate : $rowStartDate;
                    $queryEndDate = ($toDate <= $rowEndDate) ? $toDate : $rowEndDate;

                    $filter = "WHEN " . $alias . ".companySystemID = " . $periodDate->company_system_id;
                    $filter .= " AND DATE(" . $alias . ".documentDate) BETWEEN '" . $queryStartDate->format("Y-m-d") . "' AND '" . $queryEndDate->format("Y-m-d") . "'";
                    $filter .= " THEN (" . $alias . "." . $currencyColumn . " * -1) * " . ($periodDate->holding_percentage / 100);

                    $companyPeriodsFilterData[] = $filter;
                }
            }
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

    public static function getTotalProfit($serviceLineIDs, $company, $fromDate, $toDate, $amountColumn, $glType = 2, $isOpeningBalance = false) {
        $totalProfit = GeneralLedger::selectRaw('SUM(documentLocalAmount) as documentLocalAmount, SUM(documentRptAmount) as documentRptAmount')
            ->whereIn('serviceLineSystemID', $serviceLineIDs)
            ->where('companySystemID', $company)
            ->where('glAccountTypeID', $glType)
            ->when($isOpeningBalance, function ($query) use ($toDate) {
                return $query->where(DB::raw('DATE(documentDate)'), '<', $toDate);
            }, function ($query) use ($fromDate, $toDate) {
                return $query->whereBetween(DB::raw('DATE(documentDate)'), [$fromDate, $toDate]);
            })
            ->first();

        return $totalProfit->$amountColumn * -1;
    }

    public static function getCompanyType($companyType): ?string {
        $type = null;
        switch ($companyType) {
            case 1;
                $type = "Subsidary";
                break;
            case 2;
                $type = "Associate";
                break;
            case 3;
                $type = "Joint venture";
                break;
        }
        return $type;
    }

    public static function processConsolidationDataForDrillDownAndReport($input): array {
        $dataType = $input['selectedRow'];
        $fromDate = Carbon::parse($input['fromDate']);
        $toDate = Carbon::parse($input['toDate']);

        $currency = $input['currency'][0] ?? $input['currency'];
        $amountColumn = ($currency == 1) ? 'documentLocalAmount' : 'documentRptAmount';

        // selected sub companies
        $companySystemIDs = collect($input['companySystemID']);
        // selected group company
        $groupCompanySystemID = collect($input['groupCompanySystemID'])->pluck('companySystemID')->toArray();
        $serviceLineIDs = collect($input['serviceLineSystemID'])->pluck('serviceLineSystemID')->toArray();

        // check the selected item
        $dataType = explode('-',$dataType);

        // remove group company from sub companies
        $childCompanies = array_values(
            $companySystemIDs->pluck('companySystemID')->diff($groupCompanySystemID)->toArray()
        );

        $data = [];

        if (in_array($dataType[0],['CMB','CONS'])) {

            // Joint venture & associate types
            $groupTypes = [2,3];
            $periods = self::getCompanyOwnershipPeriods($groupCompanySystemID, $childCompanies, $groupTypes);

            foreach ($periods as $period) {
                $rowStartDate = Carbon::parse($period->start_date);
                $rowEndDate = ($period->end_date == null) ? $toDate : Carbon::parse($period->end_date);

                if ($rowStartDate->isBetween($fromDate, $toDate) || $rowEndDate->isBetween($fromDate, $toDate)) {
                    $queryStartDate = ($fromDate >= $rowStartDate) ? $fromDate : $rowStartDate;
                    $queryEndDate = ($toDate <= $rowEndDate) ? $toDate : $rowEndDate;

                    $totalProfit = self::getTotalProfit($serviceLineIDs, $period->company_system_id, $queryStartDate->format("Y-m-d"), $queryEndDate->format("Y-m-d"), $amountColumn);

                    if (abs($totalProfit) != 0) {
                        $parentPortion = ($totalProfit * $period->holding_percentage) / 100;

                        $data[] = [
                            'company' => $period->companyMaster->CompanyName,
                            'type' => self::getCompanyType($period->group_type),
                            'holdingPercentage' => $period->holding_percentage,
                            'companyProfit' => $totalProfit,
                            'parentPortion' => $parentPortion
                        ];
                    }
                }
            }
        }
        else {

            // Subsidiary types
            $groupTypes = [1];
            $periods = self::getCompanyOwnershipPeriods($groupCompanySystemID, $childCompanies, $groupTypes);

            foreach ($periods as $period) {
                $rowStartDate = Carbon::parse($period->start_date);
                $rowEndDate = ($period->end_date == null) ? $toDate : Carbon::parse($period->end_date);

                if ($rowStartDate->isBetween($fromDate, $toDate) || $rowEndDate->isBetween($fromDate, $toDate)) {
                    $queryStartDate = ($fromDate >= $rowStartDate) ? $fromDate : $rowStartDate;
                    $queryEndDate = ($toDate <= $rowEndDate) ? $toDate : $rowEndDate;

                    $totalProfit = self::getTotalProfit($serviceLineIDs, $period->company_system_id, $queryStartDate->format("Y-m-d"), $queryEndDate->format("Y-m-d"), $amountColumn);

                    if (abs($totalProfit) != 0) {
                        // calculate NCI percentage
                        $nciPercentage = 100 - $period->holding_percentage;
                        $parentPortion = ($totalProfit * $nciPercentage) / 100;

                        $data[] = [
                            'company' => $period->companyMaster->CompanyName,
                            'type' => self::getCompanyType(1),
                            'holdingPercentage' => $nciPercentage,
                            'companyProfit' => $totalProfit,
                            'parentPortion' => $parentPortion
                        ];
                    }
                }
            }
        }

        // calculate total amount
        $total = collect($data)->sum('parentPortion');

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    public static function generateConsolidationReportData($request, $consolidationKeys) {
        $fromDate = Carbon::parse($request->fromDate);
        $toDate = Carbon::parse($request->toDate);

        $showZeroGL = $request->showZeroGL ?? false;

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
        $templateIDCondition = "templateMasterID = " . $request->templateType;

        $containsCMB = array_filter($consolidationKeys, function ($item) {
            return strpos($item, "CMB") !== false;
        });

        $zeroRemoveCondition = "";
        // Check if hide zero value condition checked
        if (!$showZeroGL) {
            $zeroRemoveConditions = [];

            foreach ($columnNames as $columnName) {
                $zeroRemoveConditions[] = "final.`" . $columnName . "` != 0";
            }

            $zeroRemoveCondition = "WHERE (" . join(" OR ", $zeroRemoveConditions) . ")";
        }

        if (($request->accountType == 1) && ($request->type == 2) && (count($containsCMB) > 0)) {
            // If the report is balance sheet, retrieve GL in subsidiary, associate & joint venture types
            $groupTypes = [1,2,3];
        }
        else {
            // If the report is not balance sheet, retrieve GL in subsidiary
            $groupTypes = [1];
        }

        $combinedColumnCompanyPeriods = self::generateFilterQuery($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs, $currencyColumn, $groupTypes, true, "gl", false);
        $eliminationColumnCompanyPeriods = self::generateFilterQuery($fromDate, $toDate, $parentCompanySystemID, $childCompanyIDs, $currencyColumn, [1], false, "el", false);

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
                                " . $combinedColumnCompanyPeriods . " AS `" . $cmbColumnName . "`,
                                0 AS `" . $elmnColumnName . "`,
                                0 AS `" . $consColumnName . "`,
                                gl.chartOfAccountSystemID
                            FROM
                                erp_generalledger AS gl
                            WHERE
                                gl.companySystemID IN (" . join(",", $allCompanyIDs) . ")
                                AND gl." . $serviceLineIDsCondition . "
                            GROUP BY gl.chartOfAccountSystemID
                    ) AS gl ON rtl.glAutoID = gl.chartOfAccountSystemID
                    LEFT JOIN (
                        SELECT
                            el.chartOfAccountSystemID,
                            " . $eliminationColumnCompanyPeriods . " AS `" . $elmnColumnName . "`
                        FROM
                            erp_elimination_ledger AS el
                        WHERE
                            el.companySystemID IN (" . join(",", $childCompanyIDs) . ")
                            AND el." . $serviceLineIDsCondition . "
                        GROUP BY el.chartOfAccountSystemID
                    ) AS el ON el.chartOfAccountSystemID = rtl.glAutoID
                    WHERE 
                        rtl." . $templateIDCondition . "
                        AND rtl.glAutoID IS NOT NULL
                ) AS final
                " . $zeroRemoveCondition . "
                ORDER BY final.glAutoID";

        return DB::select($sql);
    }

    public static function generateBSConsolidationOpeningBalance($request, $consolidationKeys) {
        $openingBalanceLastDate = Carbon::parse($request->fromDate)->subDay();
        $reportEndDate = Carbon::parse($request->toDate);

        $servicelineIDs = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $parentCompanySystemID = collect($request->groupCompanySystemID)->first()['companySystemID'];
        // Remove parent company from companySystemID list
        $allCompanyIDs = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $childCompanyIDs = array_values(array_diff($allCompanyIDs,[$parentCompanySystemID]));

        if(empty($childCompanyIDs)) $childCompanyIDs[] = 0;

        $currencyColumn = $request->currency == 1 ? "documentLocalAmount" : "documentRptAmount";

        $openingBalance = 0;

        // Parent company
        $openingBalance += self::getTotalProfit($servicelineIDs, $parentCompanySystemID, null, $openingBalanceLastDate->format('Y-m-d'), $currencyColumn, 1, true);

        $periods = self::getCompanyOwnershipPeriods($parentCompanySystemID, $childCompanyIDs, [1,2,3]);

        foreach ($childCompanyIDs as $companyID) {
            // Get first transaction date each company
            $firstTransactionDate = GeneralLedger::whereIn('serviceLineSystemID', $servicelineIDs)
                ->where('companySystemID', $companyID)
                ->where('glAccountTypeID', 1)
                ->min('documentDate');

            if ($firstTransactionDate) {
                $firstTransactionDate = Carbon::parse(explode(" ", $firstTransactionDate)[0]);
                $selectedCompanyPeriods = $periods->where('company_system_id', $companyID);

                if (count($selectedCompanyPeriods) == 1) {
                    if ($firstTransactionDate <= $openingBalanceLastDate) {
                        $openingBalanceSelectedCompany = self::getTotalProfit($servicelineIDs, $companyID, null, $openingBalanceLastDate->format('Y-m-d'), $currencyColumn, 1, true);
                        if ($selectedCompanyPeriods->first()->group_type == 1) {
                            $openingBalance += $openingBalanceSelectedCompany;
                        }
                        else {
                            $openingBalance += (($openingBalanceSelectedCompany * $selectedCompanyPeriods->first()->holding_percentage) / 100);
                        }
                    }
                }
                else {
                    $openingStart = $firstTransactionDate;
                    $openingEnd = $openingBalanceLastDate;

                    $companyRanges= [];
                    foreach ($selectedCompanyPeriods as $period) {
                        $rowEndDate = ($period->end_date == null) ? $reportEndDate : Carbon::parse($period->end_date);

                        $companyRanges[] = [
                            'start' => Carbon::parse($period->start_date),
                            'end' => $rowEndDate,
                            'groupType' => $period->group_type,
                            'holdingPercentage' => $period->holding_percentage
                        ];
                    }

                    // Sort ranges by start date
                    usort($companyRanges, function ($a, $b) {
                        return $a['start'] <=> $b['start'];
                    });

                    $availableRanges = [];
                    $currentStart = $openingStart->copy();

                    foreach ($companyRanges as $range) {
                        if ($range['start'] > $currentStart) {
                            $availableRanges[] = [
                                'start' => $currentStart->copy(),
                                'end' => ($range['start']->copy())->subDay(),
                            ];
                        }

                        // Move currentStart forward if needed
                        if ($range['end'] >= $currentStart) {
                            $currentStart = ($range['end']->copy())->addDay();
                        }
                    }

                    // Add the final range if it goes till end of main range
                    if ($currentStart <= $openingEnd) {
                        $availableRanges[] = [
                            'start' => $currentStart->copy(),
                            'end' => $openingEnd,
                        ];
                    }

                    // Opening Balance Periods
                    foreach ($availableRanges as $range) {
                        $openingBalanceSelectedCompany = self::getTotalProfit($servicelineIDs, $companyID, $range['start']->format('Y-m-d'), $range['end']->format('Y-m-d'), $currencyColumn, 1, false);
                        $openingBalance += $openingBalanceSelectedCompany;
                    }

                    // Structure History Periods
                    foreach ($companyRanges as $range) {
                        if ($range['start'] < $openingBalanceLastDate) {
                            $endDate = $range['end'] <= $openingBalanceLastDate ? $range['end'] : $openingBalanceLastDate;
                            $openingBalanceSelectedCompany = self::getTotalProfit($servicelineIDs, $companyID, $range['start']->format('Y-m-d'), $endDate->format('Y-m-d'), $currencyColumn, 1, false);
                            if ($range['groupType'] == 1) {
                                $openingBalance += $openingBalanceSelectedCompany;
                            }
                            else {
                                $openingBalance += (($openingBalanceSelectedCompany * $range['holdingPercentage']) / 100);
                            }
                        }
                    }
                }
            }
        }

        return $openingBalance;
    }
}
