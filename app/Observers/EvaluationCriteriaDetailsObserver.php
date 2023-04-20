<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\DocumentEditValidate;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaDetailsEditLog;


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
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $obj = DocumentEditValidate::process($tenderObj->getOriginal('bid_submission_opening_date'),$tender->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
      
        if($obj)
        {
            $parentId = null;
            $parentObj = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('parent_id'))->orderBy('id','desc')->first();
            if(isset($parentObj))
            {
                $parentId = $parentObj->getOriginal('id');
            }
            $result = $this->process($tenderObj,$tender,$empId,2,null,$parentId);
            if($result)
            {
                Log::info('created succesfully');
            }
        }
    }

    public function updated(EvaluationCriteriaDetails $tender)
    {
        

        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $obj = DocumentEditValidate::process($tenderObj->getOriginal('bid_submission_opening_date'),$tender->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        if($obj)
        {
            
        $result = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->first();
        $parentId = $tender->getAttribute('parent_id');
        if(isset($result))
        {

            $modify_type_val = 3;
            $modify_type = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->where('tender_version_id',$tenderObj->getAttribute('tender_edit_version_id'))->first();
            if(isset($modify_type))
            {
                $modify_type_val = 4;
            }


            $reflog_id = null;
            $output = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
            if(isset($output))
            {
               $reflog_id = $output->getAttribute('id');
            }


            
            $result = $this->process($tenderObj,$tender,$empId,$modify_type_val,$reflog_id,$parentId);
        }
        else
        {   
            $result = $this->process($tenderObj,$tender,$empId,2,null,$parentId);
            if($result)
            {
                Log::info('created succesfully');
            }
        }
        }


    }


    public function deleted(EvaluationCriteriaDetails $tender)
    {


        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();

        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));

        if($obj)
        {   
            $obj1 = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first(); //1
            $parentId = $tender->getAttribute('parent_id');
            $result1 = $this->process($tenderObj,$tender,$empId,1,$obj1->getAttribute('id'),$parentId);
            if($result1)
            {
                $obj2 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj1->getAttribute('id'))->orderBy('id','desc')->first(); //2
                if(isset($obj2))
                {
                    $parent2Id = $obj2->getAttribute('parent_id');
                    $result2 = $this->process($tenderObj,$obj2,$empId,1,$obj2->getAttribute('id'),$parent2Id);
        
                    if($result2)
                    {
                        $obj3 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj2->getAttribute('id'))->orderBy('id','desc')->first(); //3
    
                        if(isset($obj3))
                        {   
                            $parent3Id = $obj3->getAttribute('parent_id');
                            $result3 = $this->process($tenderObj,$obj3,$empId,1,$obj3->getAttribute('id'),$parent3Id);
            
                            if($result3)
                            {
                                $obj4 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj3->getAttribute('id'))->orderBy('id','desc')->first(); //3
                                if(isset($obj4))
                                {
                                    $parent4Id = $obj4->getAttribute('parent_id');
                                    $result3 = $this->process($tenderObj,$obj4,$empId,1,$obj4->getAttribute('id'),$parent4Id);
                                }
                   
                            }
                        }
                
                    }
                }
         
            }
        }




    }


    public function process($tenderObj,$tender,$emp_id,$type,$reflog_id,$parentId)
    {
        $data['description'] =$tender->getAttribute('description');
        $data['tender_id'] = $tender->getAttribute('tender_id');
        $data['parent_id'] = $parentId;
        $data['level'] =$tender->getAttribute('level');
        $data['critera_type_id'] = $tender->getAttribute('critera_type_id');
        $data['answer_type_id'] = $tender->getAttribute('answer_type_id');
        $data['weightage'] = $tender->getAttribute('weightage');
        $data['passing_weightage'] = $tender->getAttribute('passing_weightage');
        $data['is_final_level'] = $tender->getAttribute('is_final_level');
        $data['sort_order'] = $tender->getAttribute('sort_order');
        $data['created_by'] = $emp_id;
        $data['max_value'] = $tender->getAttribute('max_value');
        $data['min_value'] = $tender->getAttribute('min_value');
        $data['modify_type'] = $type;
        $data['ref_log_id'] = $reflog_id;
        $data['master_id'] = $tender->getAttribute('id');
        $data['tender_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');

        $result = EvaluationCriteriaDetailsEditLog::create($data);

        if($result)
        {
            return true;
        }

    }
}