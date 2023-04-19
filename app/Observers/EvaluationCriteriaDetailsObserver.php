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
        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $obj = DocumentEditValidate::process($tender_obj->getOriginal('bid_submission_opening_date'),$tender->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();
      
        if($obj)
        {
            $parent_id = null;
            $parent_id_obj = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('parent_id'))->orderBy('id','desc')->first();
            if(isset($parent_id_obj))
            {
                $parent_id = $parent_id_obj->getOriginal('id');
            }
            $result = $this->process($tender_obj,$tender,$employee->employeeSystemID,2,null,$parent_id);
            if($result)
            {
                Log::info('created succesfully');
            }
        }
    }

    public function updated(EvaluationCriteriaDetails $tender)
    {
        

        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $obj = DocumentEditValidate::process($tender_obj->getOriginal('bid_submission_opening_date'),$tender->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();

        if($obj)
        {
            
        $result = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->first();
        $parent_id = $tender->getAttribute('parent_id');
        if(isset($result))
        {

            $modify_type_val = 3;
            $modify_type = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->where('tender_version_id',$tender_obj->getAttribute('tender_edit_version_id'))->first();
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


            
            $result = $this->process($tender_obj,$tender,$employee->employeeSystemID,$modify_type_val,$reflog_id,$parent_id);
        }
        else
        {   
            $result = $this->process($tender_obj,$tender,$employee->employeeSystemID,2,null,$parent_id);
            if($result)
            {
                Log::info('created succesfully');
            }
        }
        }


    }


    public function deleted(EvaluationCriteriaDetails $tender)
    {

        Log::info(print_r($tender, true));

        $employee = \Helper::getEmployeeInfo();
        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();

        $obj1 = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first(); //1
        $parent_id = $tender->getAttribute('parent_id');
        $result1 = $this->process($tender_obj,$tender,$employee->employeeSystemID,1,$obj1->getAttribute('id'),$parent_id);
        if($result1)
        {
            $obj2 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj1->getAttribute('id'))->orderBy('id','desc')->first(); //2
            if(isset($obj2))
            {
                $parent2_id = $obj2->getAttribute('parent_id');
                $result2 = $this->process($tender_obj,$obj2,$employee->employeeSystemID,1,$obj2->getAttribute('id'),$parent2_id);
    
                if($result2)
                {
                    $obj3 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj2->getAttribute('id'))->orderBy('id','desc')->first(); //3

                    if(isset($obj3))
                    {   
                        $parent3_id = $obj3->getAttribute('parent_id');
                        $result3 = $this->process($tender_obj,$obj3,$employee->employeeSystemID,1,$obj3->getAttribute('id'),$parent3_id);
        
                        if($result3)
                        {
                            $obj4 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj3->getAttribute('id'))->orderBy('id','desc')->first(); //3
                            if(isset($obj4))
                            {
                                $parent3_id = $obj4->getAttribute('parent_id');
                                $result3 = $this->process($tender_obj,$obj4,$employee->employeeSystemID,1,$obj4->getAttribute('id'),$parent3_id);
                            }
               
                        }
                    }
            
                }
            }
     
        }


    }


    public function process($tender_obj,$tender,$emp_id,$type,$reflog_id,$parent_id)
    {
        $data['description'] =$tender->getAttribute('description');
        $data['tender_id'] = $tender->getAttribute('tender_id');
        $data['parent_id'] = $parent_id;
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
        $data['tender_version_id'] = $tender_obj->getAttribute('tender_edit_version_id');

        $result = EvaluationCriteriaDetailsEditLog::create($data);

        if($result)
        {
            return true;
        }

    }
}