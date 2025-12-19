<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    @php
        $locale = app()->getLocale();
        $fontFamily = ($locale === 'ar') ? 'Noto Sans Arabic, sans-serif' : 'Poppins, sans-serif';
        $poppinsRegular = asset('fonts/Poppins-Regular.ttf');
        $poppinsBold = asset('fonts/Poppins-Bold.ttf');
        $notoSansRegular = asset('fonts/NotoSansArabic-Regular.ttf');
        $notoSansBold = asset('fonts/NotoSansArabic-Bold.ttf');
        $poppinsRegularWoff = asset('fonts/Poppins-Regular.woff2');
        $poppinsBoldWoff = asset('fonts/Poppins-Bold.woff2');
        $notoSansRegularWoff = asset('fonts/NotoSansArabic-Regular.woff2');
        $notoSansBoldWoff = asset('fonts/NotoSansArabic-Bold.woff2');
    @endphp
</head>
<body style="font-family: {{ $fontFamily }} !important;">
    <style>
        @font-face {
            font-family: 'Poppins';
            src: url('{{ $poppinsRegular }}') format('truetype'), url('{{ $poppinsRegularWoff }}') format('woff2');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url('{{ $poppinsBold }}') format('truetype'), url('{{ $poppinsBoldWoff }}') format('woff2');
            font-weight: 700;
            font-style: normal;
        }
        @font-face {
            font-family: 'Noto Sans Arabic';
            src: url('{{ $notoSansRegular }}') format('truetype'), url('{{ $notoSansRegularWoff }}') format('woff2');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Noto Sans Arabic';
            src: url('{{ $notoSansBold }}') format('truetype'), url('{{ $notoSansBoldWoff }}') format('woff2');
            font-weight: 700;
            font-style: normal;
        }
        body, body *:not(html):not(style):not(br):not(tr):not(code) {
            font-family: {{ $fontFamily }} !important;
        }

        table, td, p, div, span, a, h1, h2, h3, h4, h5, h6 {
            font-family: {{ $fontFamily }} !important;
        }

        .content-cell, .inner-body, .body {
            font-family: {{ $fontFamily }} !important;
        }
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: {{ $fontFamily }} !important;">
        <tr>
            <td align="center" style="font-family: {{ $fontFamily }} !important;">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: {{ $fontFamily }} !important;">
                    {{ $header or '' }}

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: {{ $fontFamily }} !important;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: {{ $fontFamily }} !important;">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell" style="font-family: {{ $fontFamily }} !important;">
                                        {{ Illuminate\Mail\Markdown::parse($slot) }}

                                        {{ $subcopy or '' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{ $footer or '' }}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
