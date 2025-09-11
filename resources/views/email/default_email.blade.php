<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="{{ ($locale ?? ($locale ?? app()->getLocale())) === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    @if(($locale ?? app()->getLocale()) === 'ar')
    <meta name="text-direction" content="rtl"/>
    @endif
    <title>ERP Email</title>
    <style type="text/css">
        img {
            max-width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6em;
        }

        body {
            background-color: #f6f6f6;
        }

        /* RTL Support for Arabic */
        [dir="rtl"] {
            direction: rtl !important;
            text-align: right !important;
            font-family: 'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif !important;
        }

        [dir="rtl"] .content-block {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="rtl"] .aligncenter {
            text-align: center !important;
        }

        [dir="rtl"] table {
            direction: rtl !important;
        }

        [dir="rtl"] td {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="rtl"] .aligncenter td {
            text-align: center !important;
        }

        [dir="rtl"] .content-wrap {
            text-align: right !important;
            direction: rtl !important;
        }

        [dir="rtl"] .main {
            direction: rtl !important;
        }

        [dir="rtl"] .main td {
            text-align: right !important;
            direction: rtl !important;
        }

        /* Arabic font support */
        .arabic-text {
            font-family: 'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif !important;
            direction: rtl !important;
            text-align: right !important;
        }

        /* Force RTL for Arabic emails */
        @media screen {
            [dir="rtl"] * {
                direction: rtl !important;
                text-align: right !important;
            }
            
            [dir="rtl"] .aligncenter,
            [dir="rtl"] .aligncenter * {
                text-align: center !important;
            }
        }

        /* Additional RTL enforcement */
        body[dir="rtl"] * {
            direction: rtl !important;
            text-align: right !important;
        }
        
        body[dir="rtl"] .aligncenter,
        body[dir="rtl"] .aligncenter * {
            text-align: center !important;
        }

        /* Fallback RTL enforcement for email clients */
        .rtl-force {
            direction: rtl !important;
            text-align: right !important;
        }

        /* Force center alignment for header regardless of RTL */
        .header-center {
            text-align: center !important;
            direction: ltr !important;
        }

        [dir="rtl"] .header-center {
            text-align: center !important;
            direction: ltr !important;
        }

        /* More aggressive header centering */
        .alert.alert-warning.header-center {
            text-align: center !important;
            direction: ltr !important;
        }

        [dir="rtl"] .alert.alert-warning.header-center {
            text-align: center !important;
            direction: ltr !important;
        }

        /* Override any RTL inheritance for header */
        body[dir="rtl"] .header-center,
        body[dir="rtl"] .alert.alert-warning.header-center {
            text-align: center !important;
            direction: ltr !important;
        }

        /* Force center for all header elements */
        .header-center * {
            text-align: center !important;
            direction: ltr !important;
        }

        /* Ultimate header centering - override everything */
        table[dir="rtl"] .header-center,
        table[dir="rtl"] .alert.alert-warning.header-center,
        [dir="rtl"] table .header-center,
        [dir="rtl"] table .alert.alert-warning.header-center {
            text-align: center !important;
            direction: ltr !important;
        }

        /* Force center alignment on the strong tag inside header */
        .header-center strong {
            text-align: center !important;
            direction: ltr !important;
            display: block !important;
        }
    
        @media only screen and (max-width: 640px) {
            body {
                padding: 0 !important;
            }

            h1 {
                font-weight: 800 !important;
                margin: 20px 0 5px !important;
            }

            h2 {
                font-weight: 800 !important;
                margin: 20px 0 5px !important;
            }

            h3 {
                font-weight: 800 !important;
                margin: 20px 0 5px !important;
            }

            h4 {
                font-weight: 800 !important;
                margin: 20px 0 5px !important;
            }

            h1 {
                font-size: 22px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            h3 {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }};"
      bgcolor="#f6f6f6" dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">

<table class="body-wrap"
       style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
       bgcolor="#f6f6f6" dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">
    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
        <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
            valign="top"></td>
        <td class="container" width="600"
            style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
            valign="top" dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">
            <div class="content {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl-force' : '' }}"
                 style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                 dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">
                <table class="main {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl-force' : '' }}" width="100%" cellpadding="0" cellspacing="0"
                       style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                       bgcolor="#fff" dir="{{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}">
                    <tr style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <td class="alert alert-warning header-center"
                            style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; border: 1px solid #e9e9e9; box-sizing: border-box; font-size: 16px; vertical-align: top; color: #ffffff; font-weight: 500; text-align: center !important; border-radius: 3px 3px 0 0; background-color:{{ $color }}; margin: 0; padding: 20px; direction: ltr !important;"
                            align="center" bgcolor="#fff" valign="top" dir="ltr">
                            <strong style="text-align: center !important; direction: ltr !important; display: block !important;"> {{ $text }} </strong>
                        </td>
                    </tr>
                    <tr style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};">
                        <td class="content-wrap"
                            style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }}; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                            valign="top">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};">
                                <tr style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }}; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};">
                                    <td class="content-block"
                                        style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }}; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                                        valign="top">
                                        {{--{{$arr->to}}--}}
                                    </td>
                                </tr>
                                <tr style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }}; text-justify: inter-word; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};">
                                    <td class="content-block"
                                        style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }}; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                                        valign="top">
                                        {{--{!! html_entity_decode(nl2br($content)) !!}--}}
                                        {!! $content !!}
                                    </td>
                                </tr>
                                <tr style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'justify' }}; text-justify: inter-word; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};">
                                    <td class="content-block"
                                        style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'justify' }}; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                                        valign="top">
                                        <span> </span>
                                    </td>
                                </tr>
                                <tr style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; margin: 0; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'justify' : 'justify' }}; text-justify: inter-word; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};">
                                    <td class="content-block"
                                        style="font-family: {{ ($locale ?? app()->getLocale()) === 'ar' ? "'Tahoma', 'Arial', 'Helvetica Neue', Helvetica, sans-serif" : "'Helvetica Neue',Helvetica,Arial,sans-serif" }}; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'justify' : 'justify' }}; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }};"
                                        valign="top">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class="footer"
                     style="height:50px;background-color: white;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 10px; direction: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'rtl' : 'ltr' }}; text-align: {{ ($locale ?? app()->getLocale()) === 'ar' ? 'right' : 'left' }};">
                    <table width="100%"
                           style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <td class="aligncenter content-block"
                                style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;"
                                align="center" valign="top">
                                <span style="color:black">Copyright © {{ date('Y') }} OSOS Tech. All rights reserved. Powered by <span><a href="https://www.osos.om/" target=”_blank” style="color: #F55431 !important;text-decoration: none;">OSOS</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
            valign="top"></td>
    </tr>
</table>
</body>
</html>
