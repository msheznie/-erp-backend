<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\helper\TenderDetails;
use App\helper\Helper;

class EvaluationCriteriaDetailsObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  EvaluationCriteriaDetails $tender
     * @return void
     */
    public function created(EvaluationCriteriaDetails $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
       

        if ($obj && isset($employee)) {
            $empId = $employee->employeeSystemID;
            $parentId = null;
            $parentObj = EvaluationCriteriaDetailsEditLog::where('master_id', $tender->getAttribute('parent_id'))->orderBy('id', 'desc')->first();
            if (isset($parentObj)) {
                $parentId = $parentObj->getOriginal('id');
            }
            $master_id = $tender->getAttribute('id');
            $result = $this->process($tenderObj, $tender, $empId, 2, null, $parentId, $master_id);
            if ($result) {
                Log::info('created succesfully 123');
            }
        }
    }

    public function updated(EvaluationCriteriaDetails $tender)
    {

        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
     
        if ($obj && isset($employee)) {
            $empId = $employee->employeeSystemID;
            $parentId = null;
            $result = EvaluationCriteriaDetailsEditLog::where('master_id', $tender->getAttribute('id'))->first();
            if (isset($result)) {
                $parentId = $result->getAttribute('parent_id');
            }

            $masterId = $tender->getAttribute('id');


            $modifyType = 3;
            $evaluationLog = EvaluationCriteriaDetailsEditLog::where('master_id', $tender->getAttribute('id'))->where('tender_version_id', $tenderObj->getAttribute('tender_edit_version_id'))->first();
            if (isset($evaluationLog)) {
                $modifyType = 4;
            }


            $reflogId = null;
            $output = EvaluationCriteriaDetailsEditLog::where('master_id', $tender->getAttribute('id'))->orderBy('id', 'desc')->first();
            if (isset($output)) {
                $reflogId = $output->getAttribute('id');
            }



            $result = $this->process($tenderObj, $tender, $empId, $modifyType, $reflogId, $parentId, $masterId);
        }
    }


    public function deleted(EvaluationCriteriaDetails $tender)
    {

        $employee = Helper::getEmployeeInfo();
       
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

        if ($obj && isset($employee)) {
            $empId = $employee->employeeSystemID;
            $obj1 = EvaluationCriteriaDetailsEditLog::where('master_id', $tender->getAttribute('id'))->orderBy('id', 'desc')->first();
            if (isset($obj1)) {
                $parentId = $obj1->getAttribute('parent_id');
                $masterId = $tender->getAttribute('id');
                $result1 = $this->process($tenderObj, $tender, $empId, 1, $obj1->getAttribute('id'), $parentId, $masterId); //level 1
                if ($result1) {
                    $obj2 = EvaluationCriteriaDetailsEditLog::where('parent_id', $obj1->getAttribute('id'))->orderBy('id', 'desc')->where('modify_type', 2)->get(); //level 2
                    if (isset($obj2)) {
                        foreach ($obj2 as $ob2) {

                            $previuosId = $ob2->getOriginal('id');
                            $deletedRecords = EvaluationCriteriaDetailsEditLog::where('modify_type', 1)->where('ref_log_id', $previuosId)->select('id')->first();

                            if (!isset($deletedRecords)) {
                                $parentLevel2 = $ob2->getOriginal('parent_id');
                                $masterIdLevel2 = $ob2->getOriginal('master_id');
                                $obj3 = EvaluationCriteriaDetailsEditLog::where('parent_id', $ob2->getOriginal('id'))->orderBy('id', 'desc')->where('modify_type', 2)->get(); //level 3
                                if (isset($obj3)) {
                                    foreach ($obj3 as $ob3) {

                                        $previuosId = $ob3->getOriginal('id');
                                        $deletedRecords = EvaluationCriteriaDetailsEditLog::where('modify_type', 1)->where('ref_log_id', $previuosId)->select('id')->first();

                                        if (!isset($deletedRecords)) {
                                            $parentLevel3 = $ob3->getOriginal('parent_id');
                                            $masterIdLevel3 = $ob3->getOriginal('master_id');
                                            $obj4 = EvaluationCriteriaDetailsEditLog::where('parent_id', $ob3->getOriginal('id'))->orderBy('id', 'desc')->where('modify_type', 2)->get(); //level 4
                                            if (isset($obj4)) {
                                                foreach ($obj4 as $ob4) {

                                                    $previuosId = $ob4->getOriginal('id');
                                                    $deletedRecords = EvaluationCriteriaDetailsEditLog::where('modify_type', 1)->where('ref_log_id', $previuosId)->select('id')->first();
                                                    if (!isset($deletedRecords)) {
                                                        $parentLevel4 = $ob4->getOriginal('parent_id');
                                                        $masterIdLevel4 = $ob4->getOriginal('master_id');
                                                        $result3 = $this->process1($tenderObj, $ob4, $empId, 1, $ob4->getOriginal('id'), $parentLevel4, $masterIdLevel4);
                                                    }
                                                }
                                            }
                                            $result3 = $this->process1($tenderObj, $ob3, $empId, 1, $ob3->getOriginal('id'), $parentLevel3, $masterIdLevel3);
                                        }
                                    }
                                }

                                $result2 = $this->process1($tenderObj, $ob2, $empId, 1, $ob2->getOriginal('id'), $parentLevel2, $masterIdLevel2);
                            }
                        }
                    }
                }
            } else {
                $parent = $tender->getAttribute('parent_id');
                $masterId = $tender->getAttribute('id');
                $result1 = $this->process($tenderObj, $tender, $empId, 1, null, $parent, $masterId); //level 1

                if ($result1) {
                    $obj2 = EvaluationCriteriaDetails::where('parent_id', $masterId)->orderBy('id', 'desc')->get(); //level 2
                    if (isset($obj2)) {
                        foreach ($obj2 as $ob2) {
                            $parentLevel2 = $ob2->getOriginal('parent_id');
                            $masterIdLevel2 = $ob2->getOriginal('master_id');
                            $obj3 = EvaluationCriteriaDetails::where('parent_id', $ob2->getOriginal('id'))->orderBy('id', 'desc')->get(); //level 3
                            if (isset($obj3)) {
                                foreach ($obj3 as $ob3) {

                                    $parentLevel3 = $ob3->getOriginal('parent_id');
                                    $masterIdLevel3 = $ob3->getOriginal('master_id');
                                    $obj4 = EvaluationCriteriaDetails::where('parent_id', $ob3->getOriginal('id'))->orderBy('id', 'desc')->get(); //level 4
                                    if (isset($obj4)) {
                                        foreach ($obj4 as $ob4) {
                                            $parentLevel4 = $ob4->getOriginal('parent_id');
                                            $masterIdLevel4 = $ob4->getOriginal('master_id');
                                            $result3 = $this->process($tenderObj, $ob4, $empId, 1, null, $parentLevel4, $masterIdLevel4);
                                        }
                                    }
                                    $result3 = $this->process($tenderObj, $ob3, $empId, 1, null, $parentLevel3, $masterIdLevel3);
                                }
                            }

                            $result2 = $this->process($tenderObj, $ob2, $empId, 1, null, $parentLevel2, $masterIdLevel2);
                        }
                    }
                }
            }
        }
    }


    public function process1($tenderObj, $tender, $emp_id, $type, $reflog_id, $parentId, $master_id)
    {
        $data['description'] = $tender->getOriginal('description');
        $data['tender_id'] = $tender->getOriginal('tender_id');
        $data['parent_id'] = $parentId;
        $data['level'] = $tender->getOriginal('level');
        $data['critera_type_id'] = $tender->getOriginal('critera_type_id');
        $data['answer_type_id'] = $tender->getOriginal('answer_type_id');
        $data['weightage'] = $tender->getOriginal('weightage');
        $data['passing_weightage'] = $tender->getOriginal('passing_weightage');
        $data['is_final_level'] = $tender->getOriginal('is_final_level');
        $data['sort_order'] = $tender->getOriginal('sort_order');
        $data['updated_by'] = $emp_id;
        $data['max_value'] = $tender->getOriginal('max_value');
        $data['min_value'] = $tender->getOriginal('min_value');
        $data['modify_type'] = $type;
        $data['ref_log_id'] = $reflog_id;
        $data['master_id'] = $master_id;
        $data['tender_version_id'] = $tenderObj->getOriginal('tender_edit_version_id');
        $result = EvaluationCriteriaDetailsEditLog::create($data);

        if ($result) {
            return true;
        }
    }

    public function process($tenderObj, $tender, $emp_id, $type, $reflog_id, $parentId, $master_id)
    {
        $data['description'] = $tender->getAttribute('description');
        $data['tender_id'] = $tender->getAttribute('tender_id');
        $data['parent_id'] = $parentId;
        $data['level'] = $tender->getAttribute('level');
        $data['critera_type_id'] = $tender->getAttribute('critera_type_id');
        $data['answer_type_id'] = $tender->getAttribute('answer_type_id');
        $data['weightage'] = $tender->getAttribute('weightage');
        $data['passing_weightage'] = $tender->getAttribute('passing_weightage');
        $data['is_final_level'] = $tender->getAttribute('is_final_level');
        $data['sort_order'] = $tender->getAttribute('sort_order');
        $data['updated_by'] = $emp_id;
        $data['max_value'] = $tender->getAttribute('max_value');
        $data['min_value'] = $tender->getAttribute('min_value');
        $data['modify_type'] = $type;
        $data['ref_log_id'] = $reflog_id;
        $data['master_id'] = $master_id;
        $data['tender_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');
        $result = EvaluationCriteriaDetailsEditLog::create($data);

        if ($result) {
            return true;
        }
    }
}
