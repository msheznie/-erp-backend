<?php

namespace App\Repositories;

use App\Models\CreditNote;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\helper\StatusService;

/**
 * Class CreditNoteRepository
 * @package App\Repositories
 * @version August 21, 2018, 9:53 am UTC
 *
 * @method CreditNote findWithoutFail($id, $columns = ['*'])
 * @method CreditNote find($id, $columns = ['*'])
 * @method CreditNote first($columns = ['*'])
*/
class CreditNoteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemiD',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'creditNoteCode',
        'creditNoteDate',
        'comments',
        'customerID',
        'customerGLCodeSystemID',
        'customerGLCode',
        'customerCurrencyID',
        'customerCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'creditAmountTrans',
        'creditAmountLocal',
        'creditAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'secondaryLogoCompID',
        'secondaryLogo',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'documentType',
        'timesReferred',
        'RollLevForApp_curr',
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
        return CreditNote::class;
    }

    function getAudit($id)
    {
        $creditNote = $this->with(['company', 'customer', 'createduser','currency', 'local_currency','approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 19);
        }, 'details'=>function($query){
            $query->with('segment');
        }
        ])->findWithoutFail($id);

        return $creditNote;

    }

    public function creditNoteListQuery($request, $input, $search = '', $customerID, $projectID) {

        $master = DB::table('erp_creditnote')
            ->leftjoin('currencymaster', 'customerCurrencyID', '=', 'currencyID')
            ->leftjoin('employees', 'erp_creditnote.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftjoin('erp_projectmaster', 'erp_creditnote.projectID', '=', 'erp_projectmaster.id')
            ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_creditnote.customerID')
            ->leftjoin('document_system_mapping', 'document_system_mapping.documentId', '=', 'erp_creditnote.creditNoteAutoID')
            ->where('erp_creditnote.companySystemID', $input['companyId'])
            ->where('erp_creditnote.documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $master->where('erp_creditnote.confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if (($input['customerID'] != '')) {
                $master->whereIn('erp_creditnote.customerID', $customerID);
            }
        }

        if (array_key_exists('projectID', $input)) {
            if ($input['projectID'] && !is_null($input['projectID'])) {
                $master->whereIn('projectID', $projectID);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $master->where('erp_creditnote.approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $master->whereMonth('creditNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $master->whereYear('creditNoteDate', '=', $input['year']);
            }
        }

        /*   if (array_key_exists('year', $input)) {
               if ($input['year'] && !is_null($input['year'])) {
                   $creditNoteDate = $input['year'] . '-12-31';
                   if (array_key_exists('month', $input)) {
                       if ($input['month'] && !is_null($input['month'])) {
                           $creditNoteDate = $input['year'] . '-' . $input['month'] . '-31';
                       }
                   }

                   $master->where('creditNoteDate', '<=', $creditNoteDate);

               }
           }*/



        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $master = $master->where(function ($query) use ($search, $search_without_comma) {
                $query->Where('creditNoteCode', 'LIKE', "%{$search}%")
                    ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CutomerCode', 'LIKE', "%{$search}%")
                    ->orwhere('erp_projectmaster.description', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%")
                    ->orWhere('creditAmountTrans', 'LIKE', "%{$search_without_comma}%");
            });
        }
        $request->request->remove('search.value');
        $master->select('creditNoteCode','erp_creditnote.postedDate' ,'CurrencyCode', 'erp_creditnote.approvedDate', 'creditNoteDate', 'erp_creditnote.comments', 'empName', 'DecimalPlaces', 'erp_creditnote.confirmedYN', 'erp_creditnote.approved', 'erp_creditnote.refferedBackYN', 'creditNoteAutoID', 'customermaster.CutomerCode', 'customermaster.CustomerName', 'creditAmountTrans', 'erp_projectmaster.description as project_description', DB::raw('CASE WHEN document_system_mapping.documentId IS NOT NULL THEN 1 ELSE 0 END as AutoGenerated'));


        return $master;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.cn_date')] = \Helper::dateFormat($val->creditNoteDate);
                $data[$x][trans('custom.credit_note_code')] = $val->creditNoteCode;
                $data[$x][trans('custom.customer')] = $val->CutomerCode;
                $data[$x][trans('custom.comments')] = $val->comments;
                $data[$x][trans('custom.created_by')] = $val->empName;
                $data[$x][trans('custom.currency')] =$val->CurrencyCode? $val->CurrencyCode : '';
                $data[$x][trans('custom.amount')] = number_format($val->creditAmountTrans, $val->DecimalPlaces? $val->DecimalPlaces : '', ".", "");
                $data[$x][trans('custom.status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
