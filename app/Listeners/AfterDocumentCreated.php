<?php

namespace App\Listeners;

use App\Models\Alert;
use App\Models\DocumentMaster;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AfterDocumentCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $document = $event->document;

        Log::useFiles(storage_path() . '/logs/after_document_created.log');
        Log::info('Successfully start  after_document_created' . date('H:i:s'));

        if (!empty($document)) {
            Log::info($document);
            $documentArray = array(
                'modelName' => '',
                'primaryKey' => '',
                'documentCodeColumnName' => '',
                'companyFinanceYearID' => '',
                'documentExist' => 0,
              );

            switch ($document["documentSystemID"]) { // check the document id and set relevant parameters
                case 8: // material issue
                    $documentArray["modelName"] = 'ItemIssueMaster';
                    $documentArray["primaryKey"] = 'itemIssueAutoID';
                    $documentArray["documentCodeColumnName"] = 'itemIssueCode';
                    $documentArray["companyFinanceYearID"] = 'companyFinanceYearID';
                    $documentArray['documentExist'] = 1;
                    break;
                default:
                    Log::info('Document ID Not Found' . date('H:i:s'));
            }


            if($documentArray['documentExist'] == 1){
                $nameSpacedModel = 'App\Models\\' . $documentArray["modelName"];
                $document = $document->toArray();
                $listOfDoc  = $nameSpacedModel::where('companySystemID',$document['companySystemID'])
                                                ->where($documentArray['companyFinanceYearID'],$document[$documentArray['companyFinanceYearID']])
                                                ->selectRaw($documentArray["primaryKey"].",".$documentArray['documentCodeColumnName'].",RIGHT(".$documentArray['documentCodeColumnName'].",6) as 'serialNo'")
                                                ->orderBy($documentArray['documentCodeColumnName'],'ASC')
                                                ->get();
                                                //->get([$documentArray["primaryKey"],$documentArray["documentCodeColumnName"]]);

                $previousDoc = null;
                $missingRecodes  = array();
                $range  = "";
                foreach ($listOfDoc as $doc){
                    if($previousDoc){
                        if((((int)$doc['serialNo']) - ((int)$previousDoc['serialNo'])) != 1 ){
                            array_push($missingRecodes,array('start' => $previousDoc[$documentArray['documentCodeColumnName']],'end' => $doc[$documentArray['documentCodeColumnName']]));
                            Log::info('Test: ');
                            Log::info($doc[$documentArray['documentCodeColumnName']]);

                            $range = $range.'<br>'.$previousDoc[$documentArray['documentCodeColumnName']].' - '. $doc[$documentArray['documentCodeColumnName']];
                        }
                    }
                    $previousDoc = $doc;
                }
                Log::info('List count: ' . count($listOfDoc));
                Log::info($listOfDoc);


                if($range) {

                    $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" . "<br>This is an auto generated email. Please do not reply to this email because we are not" . "monitoring this inbox.</font>";
                    $email_id = 'm.zahlan@pbs-int.net';
                    $empName  = 'Zahlan';
                    $employeeSystemID = 11;
                    $empID = '8888';

                    $systemDocument = DocumentMaster::find($document["documentSystemID"]);

                    $dataEmail = array();
                    $dataEmail['empName'] = $empName;
                    $dataEmail['empEmail'] = $email_id;
                    $dataEmail['empSystemID'] = $employeeSystemID;
                    $dataEmail['empID'] = $empID;
                    $dataEmail['companySystemID'] = $document['companySystemID'];
                    $dataEmail['companyID'] = $document['companyID'];
                    $dataEmail['docID'] = $systemDocument->documentID;
                    $dataEmail['docSystemID'] = null;
                    $dataEmail['docSystemCode'] = null;
                    $dataEmail['docApprovedYN'] = 0;
                    $dataEmail['docCode'] = null;
                    $dataEmail['ccEmailID'] = $email_id;

                    $temp = "Following documents are missing in the mentioned range for " . $document['CompanyName'] . " " . $systemDocument->documentDescription ."<p>".$range."<p>" . $footer;


                    $dataEmail['isEmailSend'] = 0;
                    $dataEmail['attachmentFileName'] = null;
                    $dataEmail['alertMessage'] = $systemDocument->documentDescription . " - " . $document['CompanyName'] . " (Missing Documents with Range)";
                    $dataEmail['emailAlertMessage'] = $temp;

                    Alert::create($dataEmail);
                    Log::info('Email array:');
                    Log::info($dataEmail);

                }
                Log::info('Mising count: ' . count($missingRecodes));
                Log::info($range);
                Log::info($missingRecodes);
            }

        } else {
            Log::info('Document Not Found' . date('H:i:s'));
        }
        Log::info('Successfully end  after_document_created' . date('H:i:s'));
    }

}
