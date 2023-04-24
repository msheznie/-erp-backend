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
            $master_id = $tender->getAttribute('id');
            $result = $this->process($tenderObj,$tender,$empId,2,null,$parentId,$master_id);
            if($result)
            {
                Log::info('created succesfully 123');
            }
        }
    }

    public function updated(EvaluationCriteriaDetails $tender)
    {
        
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        if($obj)
        {
            
        $result = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->first();
        $parentId = $tender->getAttribute('parent_id');
        $masterId = $tender->getAttribute('id');
   

            $modifyType = 3;
            $evaluationLog = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->where('tender_version_id',$tenderObj->getAttribute('tender_edit_version_id'))->first();
            if(isset($evaluationLog))
            {
                $modifyType = 4;
            }


            $reflogId = null;
            $output = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
            if(isset($output))
            {
               $reflogId = $output->getAttribute('id');
            }


                
            $result = $this->process($tenderObj,$tender,$empId,$modifyType,$reflogId,$parentId,$masterId);
     
        }


    }


    public function deleted(EvaluationCriteriaDetails $tender)
    {

        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

        if($obj)
        {   
            $obj1 = EvaluationCriteriaDetailsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first(); 
            if(isset($obj1))
            {
                $parentId = $tender->getAttribute('parent_id');
                $masterId = $tender->getAttribute('id');
                $result1 = $this->process($tenderObj,$tender,$empId,1,$obj1->getAttribute('id'),$parentId,$masterId);
                if($result1)
                {
                    $obj2 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj1->getAttribute('id'))->orderBy('id','desc')->first(); 
                    if(isset($obj2))
                    {
                        $parent2Id = $obj2->getAttribute('parent_id');
                        $masterId = $obj2->getAttribute('master_id');
                        $result2 = $this->process($tenderObj,$obj2,$empId,1,$obj2->getAttribute('id'),$parent2Id,$masterId);
            
                        if($result2)
                        {
                            $obj3 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj2->getAttribute('id'))->orderBy('id','desc')->first(); 
        
                            if(isset($obj3))
                            {   
                                $parent3Id = $obj3->getAttribute('parent_id');
                                $masterId = $obj3->getAttribute('master_id');
                                $result3 = $this->process($tenderObj,$obj3,$empId,1,$obj3->getAttribute('id'),$parent3Id,$masterId);
                
                                if($result3)
                                {
                                    $obj4 = EvaluationCriteriaDetailsEditLog::where('parent_id',$obj3->getAttribute('id'))->orderBy('id','desc')->first(); 
                                    if(isset($obj4))
                                    {
                                        $parent4Id = $obj4->getAttribute('parent_id');
                                        $masterId = $obj4->getAttribute('master_id');
                                        $result3 = $this->process($tenderObj,$obj4,$empId,1,$obj4->getAttribute('id'),$parent4Id,$masterId);
                                    }
                              
                                }
                            }
                           
                    
                        }
                    }
 
             
                }
            }
            else
            {   
                $parent = $tender->getAttribute('parent_id');
                $masterId = $tender->getAttribute('id');
                $result = $this->process($tenderObj,$tender,$empId,1,null,$parent,$masterId);
                if($result)
                {
                    $obj1 = EvaluationCriteriaDetails::where('parent_id',$tender->getAttribute('id'))->orderBy('id','desc')->first(); 
                    if($obj1)
                    {
                        $parent = $obj1->getAttribute('parent_id');
                        $masterId = $obj1->getAttribute('id');
                        $result1 = $this->process($tenderObj,$tender,$empId,1,null,$parent,$masterId);
                        if($result1)
                        {
                            $obj2 = EvaluationCriteriaDetails::where('parent_id',$obj1->getAttribute('id'))->orderBy('id','desc')->first(); 
                            if($obj2)
                            {
                                $parent = $obj2->getAttribute('parent_id');
                                $masterId = $obj2->getAttribute('id');
                                $result2 = $this->process($tenderObj,$tender,$empId,1,null,$parent,$masterId);

                                if($result2)
                                {
                                    $obj3 = EvaluationCriteriaDetails::where('parent_id',$obj2->getAttribute('id'))->orderBy('id','desc')->first(); 
                                    if($obj3)
                                    {
                                        $parent = $obj3->getAttribute('parent_id');
                                        $masterId = $obj3->getAttribute('id');
                                        $result3 = $this->process($tenderObj,$tender,$empId,1,null,$parent,$masterId);
        
                                    }
                                }
                            }
                    
                        }
                    }
                }
            }
   
        }




    }
    

    public function process($tenderObj,$tender,$emp_id,$type,$reflog_id,$parentId,$master_id)
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
        $data['master_id'] = $master_id;
        $data['tender_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');

        $result = EvaluationCriteriaDetailsEditLog::create($data);

        if($result)
        {
            return true;
        }

    }
}