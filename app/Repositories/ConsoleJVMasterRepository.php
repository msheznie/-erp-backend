<?php

namespace App\Repositories;

use App\Models\ConsoleJVMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;

/**
 * Class ConsoleJVMasterRepository
 * @package App\Repositories
 * @version March 6, 2019, 3:27 pm +04
 *
 * @method ConsoleJVMaster findWithoutFail($id, $columns = ['*'])
 * @method ConsoleJVMaster find($id, $columns = ['*'])
 * @method ConsoleJVMaster first($columns = ['*'])
*/
class ConsoleJVMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'serialNo',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'consoleJVcode',
        'consoleJVdate',
        'consoleJVNarration',
        'currencyID',
        'currencyER',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'localCurrencyID',
        'localCurrencyER',
        'rptCurrencyID',
        'rptCurrencyER',
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
        return ConsoleJVMaster::class;
    }


    public function consoleJVMasterListQuery($request, $input, $search = '') {

        $selectedCompanyId = $request['companyID'];

        $consoleJV = ConsoleJVMaster::with(['created_by'])->ofCompany($selectedCompanyId);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $consoleJV->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $consoleJV->whereMonth('consoleJVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $consoleJV->whereYear('consoleJVdate', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $consoleJV = $consoleJV->where(function ($query) use ($search) {
                $query->where('consoleJVcode', 'LIKE', "%{$search}%");
                $query->orWhere('consoleJVNarration', 'LIKE', "%{$search}%");
            });
        }

        return $consoleJV;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.document_date')] = \Helper::dateFormat($val->consoleJVdate);
                $data[$x][trans('custom.document_code')] = $val->consoleJVcode;
                $data[$x][trans('custom.e_narration')] = $val->consoleJVNarration;
                $data[$x][trans('custom.e_type')] = $val->jvType == 1? 'IFRS' : ($val->jvType == 2? 'GAAP' : '');
                $data[$x][trans('custom.e_created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.confirmed')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, NULL, NULL);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
