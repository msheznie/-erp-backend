<?php

namespace App\Repositories;

use App\Models\BankReconciliationDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankReconciliationDocumentsRepository
 * @package App\Repositories
 * @version November 19, 2024, 9:42 am +04
 *
 * @method BankReconciliationDocuments findWithoutFail($id, $columns = ['*'])
 * @method BankReconciliationDocuments find($id, $columns = ['*'])
 * @method BankReconciliationDocuments first($columns = ['*'])
*/
class BankReconciliationDocumentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankRecAutoID',
        'documentSystemID',
        'documentAutoId',
        'statementId'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankReconciliationDocuments::class;
    }

    public static function getAdditionalEntryView(Request $request)
    {
        $companyId = $request['companyId'];
        $bankRecAutoID = $request['bankRecAutoID'];

        $bindings = [
            'companyId' => $companyId,
            'bankRecAutoID' => $bankRecAutoID,
            'BPVcompanyId' => $companyId,
            'BPVbankRecAutoID' => $bankRecAutoID,
        ];

        $search = $request->input('search.value');

        $searchWherePV = $searchWhereRV = '';
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $searchWherePV = ' AND (
                BPVcode LIKE :searchPV1 OR
                BPVNarration LIKE :searchPV2 OR
                directPaymentPayee LIKE :searchPV3 OR
                netAmount LIKE :searchPV4
            )';

            $searchWhereRV = ' AND (
                custPaymentReceiveCode LIKE :searchRV1 OR
                PayeeName LIKE :searchRV2 OR
                narration LIKE :searchRV3 OR
                netAmount LIKE :searchRV4
            )';

            $bindings['searchPV1'] = "%{$search}%";
            $bindings['searchPV2'] = "%{$search}%";
            $bindings['searchPV3'] = "%{$search}%";
            $bindings['searchPV4'] = "%{$search}%";

            $bindings['searchRV1'] = "%{$search}%";
            $bindings['searchRV2'] = "%{$search}%";
            $bindings['searchRV3'] = "%{$search}%";
            $bindings['searchRV4'] = "%{$search}%";
        }

        $query = "
            SELECT
                bank_reconciliation_documents.*,
                custPaymentReceiveCode AS documentCode,
                custPaymentReceiveDate AS documentDate,
                narration,
                PayeeName AS payeeName,
                'Receipt' AS documentType,
                netAmount AS documentAmount,
                cancelYN,
                refferedBackYN,
                confirmedYN,
                approved
            FROM
                bank_reconciliation_documents
                JOIN erp_customerreceivepayment 
                ON erp_customerreceivepayment.custReceivePaymentAutoID = bank_reconciliation_documents.documentAutoId 
                AND bank_reconciliation_documents.documentSystemID = erp_customerreceivepayment.documentSystemID 
            WHERE
                companySystemID = :companyId
                AND bankRecAutoID = :bankRecAutoID
                AND approved != -1 {$searchWhereRV}
            UNION ALL
            SELECT
                bank_reconciliation_documents.*,
                BPVcode AS documentCode,
                BPVdate AS documentDate,
                BPVNarration AS narration,
                directPaymentPayee AS payeeName,
                'Payment' AS documentType,
                (netAmount + VATAmount) AS documentAmount,
                cancelYN,
                refferedBackYN,
                confirmedYN,
                approved
            FROM
                bank_reconciliation_documents
                JOIN erp_paysupplierinvoicemaster 
                ON erp_paysupplierinvoicemaster.PayMasterAutoId = bank_reconciliation_documents.documentAutoId 
                AND bank_reconciliation_documents.documentSystemID = erp_paysupplierinvoicemaster.documentSystemID 
            WHERE
                companySystemID = :BPVcompanyId
                AND bankRecAutoID = :BPVbankRecAutoID
                AND approved != -1 {$searchWherePV}
        ";

        return DB::select($query, $bindings);
        // return collect($results);
    }

    public function validateConfirmation($id, $companySystemID)
    {
        return DB::table('bank_reconciliation_documents')
            ->select(
                'bank_reconciliation_documents.*',
                DB::raw("
                    CASE
                        WHEN bank_reconciliation_documents.documentSystemID = 4 THEN erp_paysupplierinvoicemaster.approved
                        ELSE erp_customerreceivepayment.approved
                    END AS approved,
                    CASE
                        WHEN bank_reconciliation_documents.documentSystemID = 4 THEN erp_paysupplierinvoicemaster.cancelYN
                        ELSE erp_customerreceivepayment.cancelYN
                    END AS cancelYN
                ")
            )
            ->leftJoin('erp_paysupplierinvoicemaster', function ($join) use ($companySystemID) {
                $join->on('bank_reconciliation_documents.documentAutoId', '=', 'erp_paysupplierinvoicemaster.PayMasterAutoId')
                    ->where('bank_reconciliation_documents.documentSystemID', '=', 4)
                    ->where('erp_paysupplierinvoicemaster.companySystemID', '=', $companySystemID);
            })
            ->leftJoin('erp_customerreceivepayment', function ($join) use ($companySystemID) {
                $join->on('bank_reconciliation_documents.documentAutoId', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID')
                    ->where('bank_reconciliation_documents.documentSystemID', '=', 21)
                    ->where('erp_customerreceivepayment.companySystemID', '=', $companySystemID);
            })
            ->where('bankRecAutoID', $id)
            ->havingRaw('(approved = 0 AND cancelYN = 0)')
            ->get();
    }
}