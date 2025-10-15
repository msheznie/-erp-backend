<?php

namespace App\Services\Procument\Report;

use App\Exports\Procument\CompanyWisePoAnalysisReport;
use App\Exports\Procument\ItemwisePoAnalysisReport;
use App\Exports\Procument\PoWiseAnalysisReport;
use App\Exports\Procument\SupplierWisePoAnalysisReport;
use App\helper\CreateExcel;
use App\Models\Company;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PoAnalysisService
{

    public function getPOAExportData($request,$exportReportToExcelService)
    {


        $startDate = new Carbon($request->fromDate);
        $startDate = $startDate->format('Y-m-d');

        $endDate = new Carbon($request->toDate);
        $endDate = $endDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }
        $type = $request->type;

        $suppliers = (array)$request->suppliers;
        $suppliers = collect($suppliers)->pluck('supplierCodeSytem');

        $controlAccountsSystemID = collect($request->controlAccountsSystemID)->pluck('id')->toArray();
        $controlAccountsSystemID = array_unique($controlAccountsSystemID);

        $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $company_name = $companyMaster->CompanyName;
        $cur = null;
        if (isset($request->selectedSupplier)) {
            if (!empty($request->selectedSupplier)) {
                $suppliers = collect($request->selectedSupplier);
            }
        }

        if ($request->reportType == 1) {
            $output = $this->getDataFromQuery($request,$controlAccountsSystemID, $startDate,$endDate,$companyID,$suppliers);

            if (isset($request->grvStatus)) {
                if (!empty($request->grvStatus)) {
                    $output = $output->having('receivedStatus', $request->grvStatus);
                }
            }

            if (isset($request->lcc)) {
                if (!empty($request->lcc)) {
                    $output = $output->having('isLcc', $request->lcc);
                }
            }

            if (isset($request->sme)) {
                if (!empty($request->sme)) {
                    $output = $output->having('isSme', $request->sme);
                }
            }

            if (isset($request->segment)) {
                if (!empty($request->segment) && is_array($request->segment)) {
                    $segmentIds = array_column($request->segment, 'id');
                    $output = $output->whereIN('erp_purchaseorderdetails.serviceLineSystemID', $segmentIds);
                }
            }
            $output = $output->get();
            $data = array();
            if(empty($data))
            {
                $reportDataHeader = new ItemwisePoAnalysisReport();
                array_push($data,collect($reportDataHeader->getHeader())->toArray());
            }
            foreach ($output as $val) {

                $reportData = new ItemwisePoAnalysisReport();
                $reportData->setCompanyID($val->companyID);
                $reportData->setPostingYear($val->postingYear);
                $reportData->setApprovedDate($val->orderDate);
                $reportData->setCreatedDate($val->createdDateTime);
                $reportData->setPoCode($val->purchaseOrderCode);
                $reportData->setStatus($val->STATUS);
                $reportData->setLocation($val->location);
                $reportData->setSupplierCode($val->supplierPrimaryCode);
                $reportData->setSupplierName($val->supplierName);
                $reportData->setSupplierCountry($val->countryName);
                $reportData->setLcc($val->isLcc);
                $reportData->setSme($val->isSme);
                $reportData->setIcvCategory($val->icvMasterDes);
                $reportData->setIcvSubCategory($val->icvSubDes);
                $reportData->setCreditPeriod($val->creditPeriod);
                $reportData->setDeliveryTerms($val->deliveryTerms);
                $reportData->setPaymentTerms($val->paymentTerms);
                $reportData->setExpectedDeliveryDate($val->expectedDeliveryDate);
                $reportData->setNarration($val->narration);
                $reportData->setSegment($val->segment);
                $reportData->setItemCode($val->itemPrimaryCode);
                $reportData->setItemDescription($val->itemDescription);
                $reportData->setIsLocallyMade($val->isLocalMade);
                $reportData->setUnit($val->unitShortCode);
                $reportData->setPartNoRefNumber($val->supplierPartNumber);
                $reportData->setFinanceCategory($val->financecategory);
                $reportData->setFinanceCategorySub($val->financecategorysub);
                $reportData->setAccountCode($val->AccountCode);
                $reportData->setAccountDescription($val->finance_gl_code_pl);
                $reportData->setPoQty($val->noQty);
                $reportData->setUnitCostWithoutDiscount(CurrencyService::convertNumberFormatToNumber(number_format($val->unitCostWithOutDiscount, 2)));
                $reportData->setUnitCostWithDiscount(CurrencyService::convertNumberFormatToNumber(number_format($val->unitCostWithDiscount, 2)));
                $reportData->setDiscountPercentage($val->discountPercentage);
                $reportData->setDiscountAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->discountAmount, 2)));
                $reportData->setTotal(CurrencyService::convertNumberFormatToNumber(number_format($val->total, 2)));
                $reportData->setQtyReceived($val->qtyReceived);
                $reportData->setQtyToReceive($val->qtyToReceive);
                $reportData->setPoStatus($val->detManuallyClosed);
                $reportData->setReceivedStatus($val->receivedStatus);
                $reportData->setReceiptDate($val->lastOfgrvDate);


                array_push($data,collect($reportData)->toArray());
            }


            $fileName = trans('custom.po_analysis');
            $title = __('custom.po_analysis_item_detail_report');
            $path = 'procurement/report/po_analysis/excel/';
            $itemWisePoAnalysisReport = new ItemwisePoAnalysisReport();
            $excelColumnFormat = $itemWisePoAnalysisReport->getColumnFormat();

        }
        else if ($request->reportType == 2) { //PO Wise Analysis Report
            $output = DB::table('erp_purchaseordermaster')
                ->selectRaw('erp_purchaseordermaster.companyID,
                            erp_purchaseordermaster.purchaseOrderCode,
                            IF( erp_purchaseordermaster.manuallyClosed = 1, "YES", "NO" ) AS manuallyClosed,
                            erp_purchaseordermaster.narration,
                            erp_purchaseordermaster.approvedDate as orderDate,
                            erp_purchaseordermaster.createdDateTime,
                            erp_purchaseordermaster.serviceLine,
                            erp_purchaseordermaster.supplierPrimaryCode,
                            erp_purchaseordermaster.supplierName,
                            erp_purchaseordermaster.expectedDeliveryDate,
                            erp_purchaseordermaster.budgetYear,
                            erp_purchaseordermaster.purchaseOrderID,
                            IFNULL(suppliercategoryicvmaster.categoryDescription,"-") as icvMasterDes,
                            IFNULL(suppliercategoryicvsub.categoryDescription,"-") as icvSubDes,
                            IF( suppliermaster.isLCCYN = 1, "YES", "NO" ) AS isLcc,
                            IF( suppliermaster.isSMEYN = 1, "YES", "NO" ) AS isSme,
                            supCont.countryName,
                            IF( erp_purchaseordermaster.manuallyClosed = 1, IFNULL(grvdet.TotalGRVValue,0), IFNULL(podet.TotalPOVal,0) ) AS TotalPOVal,
                            /*IFNULL(podet.TotalPOVal,0) as TotalPOVal,*/
                            IFNULL(podet.POQty,0) as POQty,
                            podet.Type,
                            IFNULL(podet.POCapex,0) as POCapex,
                            IFNULL(podet.POOpex,0) as POOpex,
                            IFNULL(grvdet.GRVQty,0) as GRVQty,
                            IFNULL(grvdet.TotalGRVValue,0) as TotalGRVValue,
                            IFNULL(grvdet.GRVCapex,0) as GRVCapex,
                            IFNULL(grvdet.GRVOpex,0) as GRVOpex,
                            (IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            (IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance,
                            ServiceLineDes as segment,
                            IFNULL(adv.AdvanceReleased,0) as advanceReleased,
                            IFNULL(adv.LogisticAdvanceReleased,0) as logisticAdvanceReleased,
                            IFNULL(lg.logisticAmount,0) as logisticAmount,
                            IFNULL(pr.paymentComRptAmount,0) as paymentReleased,
                            (IFNULL(podet.TotalPOVal,0) - IFNULL(pr.paymentComRptAmount,0) - IFNULL(adv.AdvanceReleased,0)) as balanceToBePaid'
                )
                ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                    $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                })
                ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1 AND erp_grvmaster.grvCancelledYN = 0
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                    $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                })
                ->leftJoin(DB::raw('(SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont'), function ($join) use ($companyID) {
                    $join->on('erp_purchaseordermaster.supplierID', '=', 'supCont.supplierCodeSystem');
                })
                ->leftJoin(DB::raw('(SELECT
                                erp_paysupplierinvoicemaster.companySystemID,
                                erp_paysupplierinvoicemaster.companyID,
                                erp_advancepaymentdetails.purchaseOrderID,
                                sum( erp_advancepaymentdetails.comRptAmount ),
                            IF
                                ( poTermID = 0, 0, ( sum( comRptAmount ) ) ) AS AdvanceReleased,
                            IF
                                ( poTermID = 0, ( sum( comRptAmount ) ), 0 ) AS LogisticAdvanceReleased 
                            FROM
                                erp_paysupplierinvoicemaster
                                INNER JOIN erp_advancepaymentdetails ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_advancepaymentdetails.PayMasterAutoId
                                INNER JOIN erp_purchaseorderadvpayment ON erp_advancepaymentdetails.poAdvPaymentID = erp_purchaseorderadvpayment.poAdvPaymentID 
                            WHERE
                                erp_advancepaymentdetails.purchaseOrderID > 0 
                                AND erp_paysupplierinvoicemaster.approved =- 1 
                                AND erp_paysupplierinvoicemaster.cancelYN = 0 
                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                            GROUP BY
                                purchaseOrderID,companySystemID) adv'), function ($join) use ($companyID) {
                    $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'adv.purchaseOrderID');
                    $join->on('erp_purchaseordermaster.companySystemID', '=', 'adv.companySystemID');
                })
                ->leftJoin(DB::raw('(select
                                            purchaseOrderID,
                                            sum(totRptAmount) as paymentComRptAmount
                                            from
                                            (SELECT
                                                erp_bookinvsuppdet.purchaseOrderID,
                                                erp_bookinvsuppdet.totRptAmount,
                                                qry.bookingInvSystemCode 
                                            FROM
                                                (
                                            SELECT
                                                erp_paysupplierinvoicemaster.companySystemID,
                                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                                erp_paysupplierinvoicemaster.BPVcode,
                                                erp_paysupplierinvoicedetail.bookingInvSystemCode 
                                            FROM
                                                erp_paysupplierinvoicemaster
                                                INNER JOIN erp_paysupplierinvoicedetail ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId 
                                            WHERE
                                                erp_paysupplierinvoicemaster.approved = - 1 
                                                AND erp_paysupplierinvoicedetail.addedDocumentSystemID = 11 
                                                AND erp_paysupplierinvoicedetail.matchingDocID = 0 
                                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                                ) AS qry
                                                INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppdet.bookingSuppMasInvAutoID = qry.bookingInvSystemCode 
                                            GROUP BY
                                                qry.companySystemID,
                                                qry.bookingInvSystemCode,
                                                erp_bookinvsuppdet.purchaseOrderID,
                                                erp_bookinvsuppdet.totRptAmount
                                                ) as pr1  GROUP BY purchaseOrderID) pr '), function ($join) use ($companyID) {
                    $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'pr.purchaseOrderID');
                })
                ->leftJoin(DB::raw('(SELECT
                                                poID,
                                                SUM( reqAmountInPORptCur ) as logisticAmount
                                            FROM
                                                `erp_purchaseorderadvpayment` 
                                            WHERE
                                                poTermID = 0 
                                                AND confirmedYN = 1 
                                                AND isAdvancePaymentYN = 1 
                                                AND approvedYN = - 1 
                                            GROUP BY
                                                poID) lg '), function ($join) use ($companyID) {
                    $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'lg.poID');
                })
                ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                ->leftJoin('suppliermaster', 'erp_purchaseordermaster.supplierID', '=', 'suppliermaster.supplierCodeSystem')
                ->leftJoin('suppliercategoryicvmaster', 'erp_purchaseordermaster.supCategoryICVMasterID', '=', 'suppliercategoryicvmaster.supCategoryICVMasterID')
                ->leftJoin('suppliercategoryicvsub', 'erp_purchaseordermaster.supCategorySubICVID', '=', 'suppliercategoryicvsub.supCategorySubICVID')
                ->whereIn('liabilityAccountSysemID', $controlAccountsSystemID)
                ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)
                ->where('poCancelledYN', 0)
                ->where('erp_purchaseordermaster.poType_N', '<>', 5)
                ->where('erp_purchaseordermaster.approved', '=', -1)
                ->where('erp_purchaseordermaster.poCancelledYN', '=', 0)
                ->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))
                ->whereBetween(DB::raw("DATE(erp_purchaseordermaster.approvedDate)"), array($startDate, $endDate));


            $search = $request->input('search.value');
            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $output = $output->where(function ($q) use($search){
                    $q->where('erp_purchaseordermaster.purchaseOrderCode', 'LIKE', "%{$search}%")
                        ->orWhere('erp_purchaseordermaster.supplierPrimaryCode', 'LIKE', "%{$search}%")
                        ->orWhere('erp_purchaseordermaster.supplierName', 'LIKE', "%{$search}%");
                });
            }

            $output = $output->orderBy('erp_purchaseordermaster.approvedDate', 'ASC')
                ->get();
            $data = array();

            if(empty($data)) {
                $poWiseAnalysisReportHeader = new PoWiseAnalysisReport();
                array_push($data,collect($poWiseAnalysisReportHeader->getHeader())->toArray());
            }

            foreach ($output as $val) {
                $poWiseAnalysisReport = new PoWiseAnalysisReport();
                $poWiseAnalysisReport->setCompanyId($val->companyID);
                $poWiseAnalysisReport->setPoCode($val->purchaseOrderCode);
                $poWiseAnalysisReport->setSegment($val->segment);
                $poWiseAnalysisReport->setNarration($val->narration);
                $poWiseAnalysisReport->setApprovedDate($val->orderDate);
                $poWiseAnalysisReport->setCreatedDate($val->createdDateTime);
                $poWiseAnalysisReport->setExpectedDeliveryDate($val->expectedDeliveryDate);
                $poWiseAnalysisReport->setSupplierCode($val->supplierPrimaryCode);
                $poWiseAnalysisReport->setSupplierName($val->supplierName);
                $poWiseAnalysisReport->setSupplierCountry($val->countryName);
                $poWiseAnalysisReport->setLcc($val->isLcc);
                $poWiseAnalysisReport->setSme($val->isSme);
                $poWiseAnalysisReport->setIcvCategory($val->icvMasterDes);
                $poWiseAnalysisReport->setIcvSubCategory($val->icvSubDes);
                $poWiseAnalysisReport->setBudgetYear($val->budgetYear);
                $poWiseAnalysisReport->setPoCapexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->POCapex,2)));
                $poWiseAnalysisReport->setPoOpexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->POOpex,2)));
                $poWiseAnalysisReport->setTotalPoAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->TotalPOVal,2)));
                $poWiseAnalysisReport->setLogisticAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->logisticAmount,2)));
                $poWiseAnalysisReport->setGrvCapexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->GRVCapex,2)));
                $poWiseAnalysisReport->setGrvOpexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->GRVOpex,2)));
                $poWiseAnalysisReport->setTotalGrvAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->TotalGRVValue,2)));
                $poWiseAnalysisReport->setCapexBalance(CurrencyService::convertNumberFormatToNumber(number_format($val->capexBalance,2)));
                $poWiseAnalysisReport->setOpexBalance(CurrencyService::convertNumberFormatToNumber(number_format($val->opexBalance,2)));
                $poWiseAnalysisReport->setAdvanceReleased(CurrencyService::convertNumberFormatToNumber(number_format($val->advanceReleased,2)));
                $poWiseAnalysisReport->setLogisticAdvanceReleased(CurrencyService::convertNumberFormatToNumber(number_format($val->logisticAdvanceReleased,2)));
                $poWiseAnalysisReport->setPaymentReleasedFromInvoice(CurrencyService::convertNumberFormatToNumber(number_format($val->paymentReleased,2)));
                $poWiseAnalysisReport->setBalanceToBePaid(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceToBePaid,2)));
                $poWiseAnalysisReport->setIsManuallyClosed($val->manuallyClosed);

                array_push($data,collect($poWiseAnalysisReport)->toArray());
            }



            $fileName = trans('custom.po_wise_analysis_report');
            $title = __('custom.po_wise_analysis_report');
            $path = 'procurement/report/po_wise_analysis/excel/';
            $poWiseAnalysisReport = new PoWiseAnalysisReport();
            $excelColumnFormat = $poWiseAnalysisReport->getColumnFormat();

        }
        else if ($request->reportType == 3) {
            $output = DB::table('erp_purchaseordermaster')
                ->selectRaw('
                            companymaster.CompanyID,                      
                            companymaster.CompanyName,                      
                            SUM(IF(erp_purchaseordermaster.manuallyClosed =1,IFNULL(grvdet.TotalGRVValue,0),IFNULL(podet.TotalPOVal,0))) as TotalPOVal,
                            SUM(IFNULL(podet.POQty,0)) as POQty, 
                            SUM(IFNULL(podet.POCapex,0)) as POCapex,
                            SUM(IFNULL(podet.POOpex,0)) as POOpex,
                            SUM(IFNULL(grvdet.GRVQty,0)) as GRVQty,
                            SUM(IFNULL(grvdet.TotalGRVValue,0)) as TotalGRVValue,
                            SUM(IFNULL(grvdet.GRVCapex,0)) as GRVCapex,
                            SUM(IFNULL(grvdet.GRVOpex,0)) as GRVOpex,
                            SUM(IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            SUM(IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance'
                )
                ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                    $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                })
                ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                    $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                })
                ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                ->leftJoin('companymaster', 'erp_purchaseordermaster.companySystemID', '=', 'companymaster.companySystemID')
                ->leftJoin('suppliermaster', 'erp_purchaseordermaster.supplierID', '=', 'suppliermaster.supplierCodeSystem')
                ->whereIN('liabilityAccountSysemID', $controlAccountsSystemID)
                ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)
                ->where('poCancelledYN', 0)
                ->where('erp_purchaseordermaster.poType_N', '<>', 5)
                ->where('erp_purchaseordermaster.approved', '=', -1)
                ->where('erp_purchaseordermaster.poCancelledYN', '=', 0)
                ->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))
                ->whereBetween(DB::raw("DATE(erp_purchaseordermaster.approvedDate)"), array($startDate, $endDate))
                ->groupBy('erp_purchaseordermaster.companySystemID');


            $search = $request->input('search.value');
            $search = str_replace("\\", "\\\\", $search);
            if ($search) {
                $output = $output->where('companymaster.CompanyName', 'LIKE', "%{$search}%");
            }

            $output = $output->orderBy('CompanyName', 'ASC')
                ->get();
            $data = array();
            if(empty($data))
            {
                $companyWisePoAnalysisReportHeader = new CompanyWisePoAnalysisReport();
                array_push($data,collect($companyWisePoAnalysisReportHeader->getHeaders())->toArray());
            }
            foreach ($output as $val) {
                $companyWisePoAnalysisReport = new CompanyWisePoAnalysisReport();
                $companyWisePoAnalysisReport->setCompanyID($val->CompanyID);
                $companyWisePoAnalysisReport->setCompanyName($val->CompanyName);
                $companyWisePoAnalysisReport->setPoCapexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->POCapex,2)));
                $companyWisePoAnalysisReport->setPoOpexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->POOpex,2)));
                $companyWisePoAnalysisReport->setTotalPoAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->TotalPOVal,2)));
                $companyWisePoAnalysisReport->setGrvCapexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->GRVCapex,2)));
                $companyWisePoAnalysisReport->setGrvOpexAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->GRVOpex,2)));
                $companyWisePoAnalysisReport->setTotalGrvAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->TotalGRVValue,2)));
                $companyWisePoAnalysisReport->setCapexBalance(CurrencyService::convertNumberFormatToNumber(number_format($val->capexBalance,2)));
                $companyWisePoAnalysisReport->setOpexBalance(CurrencyService::convertNumberFormatToNumber(number_format($val->opexBalance,2)));

                array_push($data, collect($companyWisePoAnalysisReport)->toArray());
            }

            $fileName = trans('custom.po_wise_analysis_company_report');
            $path = 'procurement/report/po_wise_analysis_company/excel/';
            $title = __('custom.po_wise_analysis_company_report');
            $companyWisePoAnalysisReport = new CompanyWisePoAnalysisReport();
            $excelColumnFormat = $companyWisePoAnalysisReport->getColumnFormat();
        }
        else if ($request->reportType == 4) {
            $output = DB::table('erp_purchaseordermaster')
                ->selectRaw('
                            companymaster.CompanyID,                      
                            companymaster.CompanyName, 
                            supplierPrimaryCode as supplierID,
                            erp_purchaseordermaster.supplierName,                  
                            supCont.countryName,                     
                            SUM(IF(erp_purchaseordermaster.manuallyClosed =1,IFNULL(grvdet.TotalGRVValue,0),IFNULL(podet.TotalPOVal,0))) as TotalPOVal,
                            SUM(IFNULL(podet.POQty,0)) as POQty, 
                            SUM(IFNULL(podet.POCapex,0)) as POCapex,
                            SUM(IFNULL(podet.POOpex,0)) as POOpex,
                            SUM(IFNULL(grvdet.GRVQty,0)) as GRVQty,
                            SUM(IFNULL(grvdet.TotalGRVValue,0)) as TotalGRVValue,
                            SUM(IFNULL(grvdet.GRVCapex,0)) as GRVCapex,
                            SUM(IFNULL(grvdet.GRVOpex,0)) as GRVOpex,
                            SUM(IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            SUM(IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance'
                )
                ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                    $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                })
                ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                    $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                })
                ->leftJoin(DB::raw('(SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont'), function ($join) use ($companyID) {
                    $join->on('erp_purchaseordermaster.supplierID', '=', 'supCont.supplierCodeSystem');
                })
                ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                ->leftJoin('companymaster', 'erp_purchaseordermaster.companySystemID', '=', 'companymaster.companySystemID')
                ->leftJoin('suppliermaster', 'erp_purchaseordermaster.supplierID', '=', 'suppliermaster.supplierCodeSystem')
                ->whereIN('liabilityAccountSysemID', $controlAccountsSystemID)
                ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)
                ->where('poCancelledYN', 0)
                ->where('erp_purchaseordermaster.poType_N', '<>', 5)
                ->where('erp_purchaseordermaster.approved', '=', -1)
                ->where('erp_purchaseordermaster.poCancelledYN', '=', 0)
                ->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))
                ->whereBetween(DB::raw("DATE(erp_purchaseordermaster.approvedDate)"), array($startDate, $endDate))
                ->groupBy('supplierID');


            $search = $request->input('search.value');
            if ($search) {
                $search = str_replace("\\", "\\\\", $search);
                $output = $output->where(function ($q) use($search){
                    $q->where('erp_purchaseordermaster.supplierName', 'LIKE', "%{$search}%")
                        ->orWhere('erp_purchaseordermaster.supplierPrimaryCode', 'LIKE', "%{$search}%");
                });

            }

            $output = $output->orderBy('supplierPrimaryCode', 'ASC')
                ->get();
            $data = array();

            if(empty($data)) {
                $reportHeader = new SupplierWisePoAnalysisReport();
                array_push($data,collect($reportHeader->getHeaders())->toArray());
            }

            foreach ($output as $val) {
                $report = new SupplierWisePoAnalysisReport();
                $report->setSupplierID($val->supplierID);
                $report->setSupplierName($val->supplierName);
                $report->setSupplierCountry($val->countryName);
                $report->setPOCapexAmount($val->POCapex);
                $report->setPOOpexAmount($val->POOpex);
                $report->setTotalPOAmount($val->TotalPOVal);
                $report->setGRVCapexAmount($val->GRVCapex);
                $report->setGRVOpexAmount($val->GRVOpex);
                $report->setTotalGRVAmount($val->TotalGRVValue);
                $report->setCapexBalance($val->capexBalance);
                $report->setOpexBalance($val->opexBalance);
                array_push($data, collect($report)->toArray());
            }
            $title = __('custom.po_wise_analysis_supplier_report');
            $fileName = trans('custom.po_wise_analysis_supplier_report');
            $path = 'procurement/report/po_wise_analysis_supplier/excel/';
            $supplierWisePoAnalysisReport = new SupplierWisePoAnalysisReport();
            $excelColumnFormat = $supplierWisePoAnalysisReport->getColumnFormat();

        }


        $exportToExcel = $exportReportToExcelService
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName($company_name)
            ->setFromDate($startDate)
            ->setToDate($endDate)
            ->setReportType(1)
            ->setData($data)
            ->setType('xls')
            ->setDateType(2)
            ->setExcelFormat($excelColumnFormat)
            ->setCurrency($cur)
            ->setDetails()
            ->generateExcel();

        return $exportToExcel;
    }


    public function getDataFromQuery($request, $controlAccountsSystemID, $startDate,$endDate,$companyID,$suppliers) {
        $output = DB::table('erp_purchaseorderdetails')
                    ->join(DB::raw('(SELECT locationName,
                                manuallyClosed,
                            ServiceLineDes as segment,
                            purchaseOrderID,
                            erp_purchaseordermaster.companyID,
                            locationName as location,
                            approved,
                            YEAR ( approvedDate ) AS postingYear,
                            approvedDate AS orderDate,
                            erp_purchaseordermaster.createdDateTime,
                            purchaseOrderCode,IF( sentToSupplier = 0, "Not Released", "Released" ) AS STATUS,
                            supplierID,
                            supplierPrimaryCode,
                            supplierName,
                            creditPeriod,
                            deliveryTerms,
                            paymentTerms,
                            expectedDeliveryDate,
                            narration,
                            approvedDate,
                            erp_purchaseordermaster.companySystemID,
                            supCont.countryName,
                            IFNULL(suppliercategoryicvmaster.categoryDescription,"-") as icvMasterDes,
                            IFNULL(suppliercategoryicvsub.categoryDescription,"-") as icvSubDes,
                            IF( supCont.isLCCYN = 1, "YES", "NO" ) AS isLcc,
                            IF( supCont.isSMEYN = 1, "YES", "NO" ) AS isSme
                             FROM erp_purchaseordermaster 
                             LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID
                               LEFT JOIN suppliercategoryicvmaster ON erp_purchaseordermaster.supCategoryICVMasterID = suppliercategoryicvmaster.supCategoryICVMasterID
                            LEFT JOIN suppliercategoryicvsub ON erp_purchaseordermaster.supCategorySubICVID = suppliercategoryicvsub.supCategorySubICVID
                             INNER JOIN (SELECT supplierCodeSystem FROM suppliermaster WHERE liabilityAccountSysemID IN (' . join(',', $controlAccountsSystemID) . ') ) supp ON erp_purchaseordermaster.supplierID = supp.supplierCodeSystem 
                             LEFT JOIN (SELECT countrymaster.countryName,supplierCodeSystem,isLCCYN,isSMEYN FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont ON  supCont.supplierCodeSystem = erp_purchaseordermaster.supplierID
                             LEFT JOIN erp_location ON poLocation = erp_location.locationID WHERE poCancelledYN=0 AND approved = -1 AND poType_N <>5 AND (approvedDate BETWEEN "' . $startDate . '" AND "' . $endDate . '") AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ') AND erp_purchaseordermaster.supplierID IN (' . join(',', json_decode($suppliers)) . ')) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                        $query->on('purchaseOrderMasterID', '=', 'podet.purchaseOrderID');
                    })->leftJoin('financeitemcategorymaster', function ($query) {
                        $query->on('itemFinanceCategoryID', '=', 'itemCategoryID');
                        $query->select('categoryDescription');
                    })->leftJoin(DB::raw('(SELECT categoryDescription as financecategorysub,AccountDescription AS finance_gl_code_pl,AccountCode,itemCategorySubID FROM financeitemcategorysub LEFT JOIN chartofaccounts ON financeGLcodePLSystemID = chartOfAccountSystemID) as catSub'), function ($query) {
                        $query->on('itemFinanceCategorySubID', '=', 'catSub.itemCategorySubID');
                    })
                    ->leftJoin('units', 'unitOfMeasure', '=', 'UnitID')
                    ->leftJoin(DB::raw('(SELECT SUM(noQty) as noQty,purchaseOrderDetailsID FROM erp_grvdetails WHERE erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderDetailsID) as gdet'), function ($join) use ($companyID) {
                        $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet.purchaseOrderDetailsID');
                    })->leftJoin(
                        DB::raw('(SELECT
            max(erp_grvmaster.grvDate) AS lastOfgrvDate,
            erp_grvdetails.purchaseOrderDetailsID 
        FROM
            (
            erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
            ) 
        WHERE
            purchaseOrderDetailsID>0 AND erp_grvmaster.companySystemID IN (' . join(',', $companyID) . ') GROUP BY erp_grvdetails.purchaseOrderMastertID,erp_grvdetails.purchaseOrderDetailsID,erp_grvdetails.itemCode) as gdet2'),
                        function ($join) use ($companyID) {
                            $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet2.purchaseOrderDetailsID');
                        })->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,
                                  erp_purchaseorderdetails.purchaseOrderDetailsID,
                                gdet2.lastOfgrvDate,
                            erp_purchaseorderdetails.unitOfMeasure,
                            IF((IF(podet.manuallyClosed = 1,IFNULL(gdet.noQty,0),IFNULL(erp_purchaseorderdetails.noQty,0))-IFNULL(gdet.noQty,0)) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Received","Partially Received")) as receivedStatus,
                            /*IFNULL((erp_purchaseorderdetails.noQty-gdet.noQty),0) as qtyToReceive,*/
                            IF(podet.manuallyClosed = 1,0,(IFNULL((erp_purchaseorderdetails.noQty-IFNULL(gdet.noQty,0)),0))) as qtyToReceive,
                            IF(podet.manuallyClosed = 1,IFNULL(gdet.noQty,0),IFNULL(erp_purchaseorderdetails.noQty,0)) as noQty,
                            IFNULL(gdet.noQty,0) as qtyReceived,
                            erp_purchaseorderdetails.itemFinanceCategoryID,
                            erp_purchaseorderdetails.itemFinanceCategorySubID,
                            erp_purchaseorderdetails.itemCode,
                            erp_purchaseorderdetails.itemPrimaryCode,
                            erp_purchaseorderdetails.itemDescription,
                            erp_purchaseorderdetails.supplierPartNumber,
                            IF( erp_purchaseorderdetails.manuallyClosed = 0, " ", "Manually Closed" ) AS detManuallyClosed,
                            IF( erp_purchaseorderdetails.madeLocallyYN = -1, "YES", "NO" ) AS isLocalMade,
                            /*erp_purchaseorderdetails.noQty,*/
                            ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) AS unitCostWithOutDiscount,
                            erp_purchaseorderdetails.GRVcostPerUnitComRptCur as unitCostWithDiscount,
                            erp_purchaseorderdetails.discountPercentage,
                            ( ( ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) ) - erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS discountAmount,
                            ( IF(podet.manuallyClosed = 1,IFNULL(gdet.noQty,0),IFNULL(erp_purchaseorderdetails.noQty,0)) * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS total,
                            financeitemcategorymaster.categoryDescription as financecategory,
                            catSub.*,
                            units.UnitShortCode AS unitShortCode,
                            podet.*')
                    ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID)->orderBy('podet.approvedDate', 'ASC');



        $globalSearch = $request->input('search.value');
        if ($globalSearch) {
            $globalSearch = str_replace("\\", "\\\\", $globalSearch);
            $output = $output->where(function ($query) use ($globalSearch) {
                $query->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$globalSearch}%")
                    ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$globalSearch}%")
                    ->orWhere('supplierName', 'LIKE', "%{$globalSearch}%")
                    ->orWhere('supplierPrimaryCode', 'LIKE', "%{$globalSearch}%")
                    ->orWhere('purchaseOrderCode', 'LIKE', "%{$globalSearch}%");
            });
        }

        if (isset($request->searchText)) {
            if (!empty($request->searchText)) {
                $search = str_replace("\\", "\\\\", $request->searchText);
                $output = $output->where(function ($query) use ($search) {
                    $query->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$search}%")
                        ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$search}%")
                        ->orWhere('supplierName', 'LIKE', "%{$search}%")
                        ->orWhere('purchaseOrderCode', 'LIKE', "%{$search}%");
                });
            }
        }

        return $output;

    }


}
