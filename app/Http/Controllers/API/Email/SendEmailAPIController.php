<?php

namespace App\Http\Controllers\API\Email;

use App\Http\Controllers\AppBaseController;
use App\Mail\EmailForQueuing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailAPIController extends AppBaseController
{


    public function sendEmail(Request $request){


        $input = $request->all();
        $validator = \Validator::make($input, [
            'data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        foreach ($input['data'] as $d){

            if(isset($d['empEmail']) && $d['empEmail']
                && isset($d['alertMessage']) && $d['alertMessage']
                && isset($d['emailAlertMessage']) && $d['emailAlertMessage']){

                if(!isset($d['attachmentFileName'])){
                    $d['attachmentFileName'] = '';
                }
                Log::info('API Email send start');
                if(isset($data['empEmail']) && $d['empEmail']){
                    Mail::to($d['empEmail'])->send(new EmailForQueuing($d['alertMessage'], $d['emailAlertMessage'],$d['attachmentFileName']));
                }
                Log::info('API email sent success fully to :' . $d['empEmail']);
            }
        }
        return $this->sendResponse([],'successfully sent the emails');
    }
}
