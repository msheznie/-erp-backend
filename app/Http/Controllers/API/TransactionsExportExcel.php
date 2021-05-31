<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\GRVMasterRepository;
use App\Repositories\MaterielRequestRepository;
use App\Http\Controllers\AppBaseController;
use Response;

class TransactionsExportExcel extends AppBaseController
{
    private $gRVMasterRepository;
    private $materielRequestRepository;

    public function __construct(GRVMasterRepository $gRVMasterRepo, MaterielRequestRepository $materielRequestRepo)
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
        $this->materielRequestRepository = $materielRequestRepo;
    }

    public function exportRecord(Request $request) { 

        $input = $request->all();
        $type = $input['type'];
        $search = $request->input('search.value');

        switch($input['documentId']) {
            case '3':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvLocation', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'grvTypeID'));
                $dataQry = $this->gRVMasterRepository->grvListQuery($request, $input, $search);
                $data = $this->gRVMasterRepository->setExportExcelData($dataQry );
                break;

            case '9':
                $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'ConfirmedYN', 'approved'));
                $dataQry = $this->materielRequestRepository->materialrequestsListQuery($request, $input, $search);
                $data = $this->materielRequestRepository->setExportExcelData($dataQry);
                break;

            default:
                return $this->sendResponse(array(), 'export failed');
        }

        \Excel::create('po_master', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }
}
