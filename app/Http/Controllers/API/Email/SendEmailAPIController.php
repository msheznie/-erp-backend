<?php

namespace App\Http\Controllers\API\Email;

use App\helper\email;
use App\Http\Controllers\AppBaseController;
use App\Mail\EmailForQueuing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailAPIController extends AppBaseController
{


    public function sendEmail(Request $request)
    {


        $input = $request->all();
        $validator = \Validator::make($input, [
            'data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $count = 0;
        foreach ($input['data'] as $d) {

            if (isset($d['empEmail']) && $d['empEmail']
                && isset($d['alertMessage']) && $d['alertMessage']
                && isset($d['emailAlertMessage']) && $d['emailAlertMessage']) {

                $d['empEmail'] = email::emailAddressFormat($d['empEmail']);
                if($d['empEmail']) {
                    if (!isset($d['attachmentFileName'])) {
                        $d['attachmentFileName'] = '';
                    }
                    Log::info('API Email send start');
                    Log::info('API Email processing');
                    Mail::to($d['empEmail'])->send(new EmailForQueuing($d['alertMessage'], $d['emailAlertMessage'], $d['attachmentFileName']));
                    Log::info('API email sent success fully to :' . $d['empEmail']);
                    Log::info('API Email send end');
                    $count = $count + 1;
                }
            }
        }
        return $this->sendResponse([], trans('custom.successfully_sent'). $count . ' emails');
    }
}
