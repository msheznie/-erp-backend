<?php
/**
 * Created by PhpStorm.
 * User: Fayaz
 * Date: 2/19/2018
 * Time: 12:36 PM
 */

namespace App\Http\Controllers;
use App\helper\Helper;
use App\Utils\ResponseUtil;
use Illuminate\Routing\Controller as BaseController;
use Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;


class AppBaseController extends BaseController
{

    public function sendResponse($result, $message, int $code = HttpResponse::HTTP_OK)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result), $code);
    }
    
    public function sendError($error, $code = HttpResponse::HTTP_NOT_FOUND, $errorType = [])
    {
        return Response::json(ResponseUtil::makeError($error,$errorType), $code);
    }

    public function sendAPIError($error, $code = 422,$data = [])
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($data)) {
            $res['errors'] = $data;
        }

        return Response::json($res, $code);
    }

    public function sendReponseWithDetails($data,$message,$type,$detail,$additionalData = null)
    {
        $res = [
            'success' => true,
            'data' => $data,
            'message' => $message,
            'type' => 1,
            'detail' => $detail,
            'additionalData' => $additionalData
        ];

        return Response::json($res);
    }



    public function convertArrayToValue ($input){
        foreach ($input as $key => $value) {
            if (is_array($input[$key])){
                if(count($input[$key]) > 0){
                    $input[$key] = $input[$key][0];
                }else{
                    $input[$key] = null;
                }
            }
        }
        return $input;
    }

    public function convertArrayToSelectedValue ($input,$params){
        foreach ($input as $key => $value) {
            if(in_array($key,$params)){
                if (is_array($input[$key])){
                    if(count($input[$key]) > 0){
                        $input[$key] = $input[$key][0];
                    }
                }
            }
        }
        return $input;
    }

    /**
     * Set the application locale based on user's language preference
     */
    protected function setUserLocale()
    {
        try {
            $employeeSystemID = \App\helper\Helper::getEmployeeSystemID();
            
            if ($employeeSystemID) {
                $employeeLanguage = \App\Models\EmployeeLanguage::where('employeeID', $employeeSystemID)
                    ->with(['language'])
                    ->first();
                
                if ($employeeLanguage && $employeeLanguage->language) {
                    $userLanguageCode = $employeeLanguage->language->languageShortCode;
                    
                    if ($userLanguageCode) {
                        app()->setLocale($userLanguageCode);
                        return;
                    }
                }
            }
            
            // Fallback to Accept-Language header
            $acceptLanguage = request()->header('Accept-Language');
            if ($acceptLanguage) {
                app()->setLocale($acceptLanguage);
                return;
            }
            
            // Final fallback to English
            app()->setLocale('en');
            
        } catch (\Exception $e) {
            // If there's an error, fall back to English
            app()->setLocale('en');
            \Log::warning('Error setting user locale: ' . $e->getMessage());
        }
    }


}
