<?php

namespace App\Http\Controllers\API\Verification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerificationController extends Controller
{
    public function sendVerficationEmailToMutipleEmails(Request $request)
    {
        $message = $request->input('message');

        if(!empty($message))
        {
            $dom = new \DOMDocument();
            $dom->loadHTML($message);
            $li_elements = $dom->getElementsByTagName('li');
            $emailLists = [];

            foreach ($li_elements as $li) {
                $emailLists[] = trim($li->textContent);
            }

            $emailLists = array_unique($emailLists);
            dd($emailLists);
        }

    }
}
