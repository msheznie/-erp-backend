<html>
<head>
    <title>{{ __('custom.supplier_evaluation_template') }}</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
        }

        /* RTL Support for Arabic */
        @if(app()->getLocale() == 'ar')
        body {
            direction: rtl;
            text-align: right;
        }
        
        .rtl-text-left {
            text-align: right !important;
        }
        
        .rtl-text-right {
            text-align: left !important;
        }
        
        .rtl-float-left {
            float: right !important;
        }
        
        .rtl-float-right {
            float: left !important;
        }
        
        .rtl-margin-left {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        .rtl-margin-right {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        
        .rtl-padding-left {
            padding-right: 0 !important;
            padding-left: auto !important;
        }
        
        .rtl-padding-right {
            padding-left: 0 !important;
            padding-right: auto !important;
        }
        
        table {
            direction: rtl;
        }
        
        .table th, .table td {
            text-align: right;
        }
        
        .text-right {
            text-align: left !important;
        }
        
        .text-left {
            text-align: right !important;
        }
        @endif

        body {
            font-size: 12px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }

        h3 {
            font-size: 24.5px;
        }

        h6 {
            font-size: 14px;
        }

        h6, h3 {
            margin-top: 0px;
            margin-bottom: 0px;
            font-family: inherit;
            font-weight: bold;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > th > tr > td {
            font-size: 11px;
        }

        tr td {
            padding: 5px 0;
        }

        table {
            border-collapse: collapse;
        }

        .table th {
            border: 1px solid rgb(127, 127, 127) !important;
        }

        .table th, .table td {
            padding: 0.4rem !important;
            vertical-align: top;
            border: 1px solid rgb(127, 127, 127) !important;
        }

        .table th {
            background-color: #EBEBEB !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        hr {
            margin-top: 16px;
            margin-bottom: 16px;
            border: 0;
            border-top: 1px solid
        }

        hr {
            -webkit-box-sizing: content-box;
            box-sizing: content-box;
            height: 0;
            overflow: visible;
        }
    </style>
</head>
<body>
<div class="card-body content" id="print-section">
    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 40%">
                @if($templateMaster->company)
                    <img src="{{$templateMaster->company->logo_url}}" width="100" class="container">
                @endif
            </td>
            <td valign="top" style="width: 50%">
                <br>
                <div style="font-weight: bold; text-align: justify;"> {{ $templateMaster->user_text?$templateMaster->user_text:'' }}</div>
                <br>
            </td>
        </tr>
    </table>

    <table style="width: 100%" class="table_height">
        <tr style="width: 100%">
            <td valign="top" style="width: 40%">
                <br>
                <span> <b>Supplier Code:</b> {{$evaluationMaster->supplierCode}}</span>
                <br>
            </td>
            <td valign="top" style="width: 50%">
                <br>
                <span> <b>Supplier Name:</b> {{$evaluationMaster->supplierName}}</span>
                <br>
            </td>
        </tr>
    </table>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <br>
            <br>
            <div style="text-align: justify;">
                {{ $templateMaster->initial_instruction }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <br>
            <br>
            @foreach($templateSections as $sectionIndex => $sectionsData)
                <div class="table-responsive section-table">
                    @if($sectionsData['table'])
                        <span class="section-name">
                            <b>{{ $sectionsData['table']['table_name'] }}</b>
                        </span>
                        <table class="table table-bordered table-striped table-sm" style="width: 100%">
                            <thead>
                                <tr class="table-header">
                                    @foreach($sectionsData['table']['column'] as $column)
                                        <th>{{ $column['column_header'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($sectionsData['table']['evaluationDetailRow'] as $rowIndex => $row)
                                <tr>
                                    @foreach($row['rowDetails'] as $colIndex => $cell)
                                        @if(isset($sectionsData['table']['column'][$colIndex]))
                                            @switch($sectionsData['table']['column'][$colIndex]['column_type'])
                                                @case(1)
                                                    <td>
                                                        <span>&nbsp;{{ $rowIndex + 1 }}</span>
                                                    </td>
                                                    @break
                                                @case(2)
                                                    <td>
                                                        <span>&nbsp;{{ $cell[$sectionsData['table']['column'][$colIndex]['column_header']] }}</span>
                                                    </td>
                                                    @break
                                                @case(3)
                                                    <td style="text-align: center;">
                                                        {{ $cell[$sectionsData['table']['column'][$colIndex]['column_header']] }}
                                                    </td>
                                                    @break
                                                @case(4)
                                                    <td style="text-align: right;">
                                                        {{ $cell[$sectionsData['table']['column'][$colIndex]['column_header']] }} &nbsp;&nbsp;
                                                    </td>
                                                    @break
                                                @case(5)
                                                    <td style="text-align: center;">
                                                        {{ $cell[$sectionsData['table']['column'][$colIndex]['column_header']] }} &nbsp;&nbsp;
                                                    </td>
                                                    @break
                                            @endswitch
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if($sectionsData['table']['formula'])
                            <div class="container mt-5">
                                @foreach($sectionsData['table']['formula'] as $formula)
                                    <div class="row">
                                        <table style="width: 100%; table-layout: fixed;">
                                            <tr>
                                                <td style="font-weight: bold; width: 90% !important;">
                                                    {{ $formula['label']['labelName'] }}
                                                </td>
                                                @if($formula['formulaType'] == 1)
                                                    <td style="font-weight: bold; width: 10% !important; text-align: right;">
                                                        {{$sectionsData['table']['totalSelectedScore']}} &nbsp;
                                                    </td>
                                                @endif
                                                @if($formula['formulaType'] == 2)
                                                    <td style="font-weight: bold; width: 10% !important; text-align: right;">
                                                        {{$sectionsData['table']['selectedAverage']}} &nbsp;
                                                    </td>
                                                @endif
                                                @if($formula['formulaType'] == 3)
                                                    <td style="font-weight: bold; width: 10% !important; text-align: right;">
                                                        {{$sectionsData['table']['selectedPercentage']}} &nbsp;
                                                    </td>
                                                @endif
                                            </tr>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <br>
                        <br>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <br>
            <br>
            @foreach ($templateComments as $item)
                <div class="label"><strong>{{ $item->label }}</strong></div>
                <div class="instruction-content" style="text-align: justify;">
                    {{ $item->comment }}
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
