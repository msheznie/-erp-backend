
<html>
<head>
    <title>Supplier Evaluation Template</title>
    <style>
        @page {
            margin-left: 30px;
            margin-right: 30px;
            margin-top: 30px;
            margin-bottom: 0px;
        }

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

        table > tbody > tr > td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: #DEDEDE !important;
        }

        .text-left {
            text-align: left;

        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        tr td {
            padding: 5px 0;
        }

        .table thead th {
            border-bottom: none !important;
        }

        .white-space-pre-line {
            white-space: pre-line;
            white-space: pre;
            word-wrap: normal;
        }

        .text-muted {
            color: #dedede !important;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

        table.table-bordered {
            border: 1px solid #000;
        }

        .table th, .table td {
            padding: 6.4px !important;
        }

        table.table-bordered {
            border-collapse: collapse;
        }

        table.table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid black;
        }

        table > thead > tr > th {
            font-size: 11.5px;
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

        .header,
        .footer {
            width: 100%;
            text-align: left;
            position: fixed;
        }

        .header {
            top: 0px;
        }

        .footer {
            bottom: 40px;
        }

        .pagenum:before {
            content: counter(page);
        }
        #watermark { position: fixed; bottom: 0px; right: 0px; width: 200px; height: 200px; opacity: .1; }
        .content {
            margin-bottom: 45px;
        }
    </style>
</head>
<body>
    <div id="watermark"></div>
    <div class="card-body content" id="print-section">
        <table style="width: 100%" class="table_height">
            <tr style="width: 100%">
                <td valign="top" style="width: 50%">
                    @if($evaluationTemplate->company)
                        <img src="{{$evaluationTemplate->company->logo_url}}" width="100" class="container">
                    @endif
                </td>
                <td valign="top" style="width: 50%">
                    <br>
                    <span style="font-weight: bold"> {{ $evaluationTemplate->user_text?$evaluationTemplate->user_text:'' }}</span>
                    <br>
                </td>
            </tr>
        </table>
    

        <table style="width: 100%" class="table_height">
            <tr style="width: 100%">
                <td valign="top" style="width: 40%">
                    <br>
                    <span> <b>Supplier Code:</b> </span>
                    <br>
                </td>
                <td valign="top" style="width: 50%">
                    <br>
                    <span> <b>Supplier Name:</b> </span>
                    <br>
                </td>
            </tr>
        </table>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <br>
              <br>
              <div style="text-align: justify;">
                {{ $evaluationTemplate->initial_instruction }}
              </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <br>
                <br>
                @foreach($evaluationTemplateSection as $sectionIndex => $sectionsData)
                    <div class="table-responsive section-table">
                        @if($sectionsData['table'] && $sectionsData['table']['isConfirmed'])
                            <span class="section-name">
                                <b>{{ $sectionsData['table']['table_name'] }}</b>
                            </span>
                            <table class="table table-hover table-striped custom-table table-bordered" cellpadding="5px" autosize="1" width="100%" style="overflow: wrap">
                                <thead>
                                    <tr class="table-header">
                                        @foreach($sectionsData['table']['column'] as $column)
                                            <th style="min-width: 128px; !important">{{ $column['column_header'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sectionsData['table']['row'] as $rowIndex => $row)
                                        <tr>
                                            @foreach($row['rowData'] as $colIndex => $cell)
                                                @if(isset($sectionsData['table']['column'][$colIndex]))
                                                    <td style="min-width: 128px; !important">
                                                        @foreach($cell as $key => $value)
                                                            @switch($sectionsData['table']['column'][$colIndex]['column_type'])
                                                                @case(1)
                                                                    <span> &nbsp;{{ $sectionsData['table']['column'][$colIndex]->autoIncrementStart + $rowIndex}}</span>
                                                                    @break
        
                                                                @case(2)
                                                                    <span> &nbsp;{{ $value }}</span>
                                                                    @break
        
                                                                @case(3)
                                                                    <span></span>
                                                                    @break
        
                                                                @case(5)
                                                                    <span></span>
                                                                    @break
                                                            @endswitch
                                                        @endforeach
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(isset($sectionsData['table']['formula']))
                                <div class="container mt-5">

                                    @foreach($sectionsData['table']['formula'] as $data)
                                                <table class="table table-borderless" style="width: 100%; table-layout: fixed;">
                                                        <tr>
                                                            <th style="text-align: left;">@if($data->label) {{ $data->label->labelName}} @endif</th>
                                                            <th style="text-align: left;">@if($data->label)  @endif</th>
                                                        </tr>
                                                    
                                                </table>
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
                @foreach ($evaluationTemplateComment as $item)
                
                    <div class="label"><strong>{{ $item->label }}</strong></div>
                    <div class="instruction-content">
                        {{ $item->comment }}
                    </div>
                @endforeach
            </div>
          </div>

    </div>
</body>

</html>