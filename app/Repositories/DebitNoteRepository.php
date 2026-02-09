<?php

namespace App\Repositories;

use App\Models\DebitNote;
use Illuminate\Support\Facades\Log;
use http\Exception\InvalidArgumentException;
use App\Repositories\BaseRepository;
use App\Models\DebitNoteDetails;
use App\helper\Helper;

/**
 * Class DebitNoteRepository
 * @package App\Repositories
 * @version August 16, 2018, 10:12 am UTC
 *
 * @method DebitNote findWithoutFail($id, $columns = ['*'])
 * @method DebitNote find($id, $columns = ['*'])
 * @method DebitNote first($columns = ['*'])
*/
class DebitNoteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'debitNoteCode',
        'debitNoteDate',
        'comments',
        'supplierID',
        'supplierGLCode',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'debitAmountTrans',
        'debitAmountLocal',
        'debitAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'documentType',
        'timesReferred',
        'RollLevForApp_curr',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DebitNote::class;
    }

    public function store(DebitNote $debitNote)
    {
        if($debitNote instanceof  DebitNote)
        {
            return DebitNote::create($debitNote->toArray());
        }

        if(is_array($debitNote))
        {
            return DebitNote::create($debitNote);
        }

        throw new InvalidArgumentException("Invalid Arguements");
    }

    public function getAudit($id)
    {
        return $this->with(['detail' => function ($query) {
            $query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 15);
        }, 'company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier', 'confirmed_by', 'created_by', 'modified_by','audit_trial.modified_by','employee'])
            ->findWithoutFail($id);
    }

    /**
     * Apply master document exchange rates to all detail items
     *
     * @param int $id Master document ID (debitNoteAutoID)
     * @return bool Success status
     */
    public function applyMasterExchangeRatesToDetails($id)
    {
        try {
            $masterDocument = $this->find($id);

            if (!$masterDocument) {
                return false;
            }

            $localCurrencyER = $masterDocument->localCurrencyER ?? 1;
            $companyReportingER = $masterDocument->companyReportingER ?? 1;

            $details = DebitNoteDetails::where('debitNoteAutoID', $id)->get();

            // Update each detail item with master exchange rates and recalculate amounts
            foreach ($details as $item) {
                $localAmount = Helper::roundValue($item->debitAmount / $localCurrencyER);
                $VATAmountLocal = Helper::roundValue($item->VATAmount / $localCurrencyER);
                $netAmountLocal = Helper::roundValue($item->netAmount / $localCurrencyER);

                $comRptAmount = Helper::roundValue($item->debitAmount / $companyReportingER);
                $VATAmountRpt = Helper::roundValue($item->VATAmount / $companyReportingER);
                $netAmountRpt = Helper::roundValue($item->netAmount / $companyReportingER);

                $item->update([
                    'localCurrencyER' => $localCurrencyER,
                    'comRptCurrencyER' => $companyReportingER,
                    'localAmount' => $localAmount,
                    'comRptAmount' => $comRptAmount,
                    'VATAmountLocal' => $VATAmountLocal,
                    'VATAmountRpt' => $VATAmountRpt,
                    'netAmountLocal' => $netAmountLocal,
                    'netAmountRpt' => $netAmountRpt
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error applying master exchange rates to details: ' . $e->getMessage());
            return false;
        }
    }
}
