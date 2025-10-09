<div class="row">
    <table class="table table-sm table-striped hover table-bordered">
        <tr>
        <td><b>{{ trans('custom.template_name') }} :</b></td> <td> {{ $templateMaster->description }} </td>
        <td><b>{{ trans('custom.financial_year') }} :</b></td> <td>{{ $beginDate }} - {{ $endDate }}</td>
        </tr>
        <tr>
        <td><b>{{ trans('custom.currency') }} :</b> </td> <td> {{ $company->reportingcurrency->CurrencyCode }} </td>
        <td><b>{{ trans('custom.send_notification_at_percent') }} :</b> </td> <td>  {{ $sentNotificationAt }}</td>
        </tr>
    </table>
</div>
<div class="row">
    <div class="col-md-12">
        <p><b>{{ trans('custom.note') }}<span class="p-l-10"></span> {{ trans('custom.expense_gl_amounts_negative_value') }} <br/><b> {{ trans('custom.do_not_amend_template_description') }}</b> <br/> {{ trans('custom.delete_segment_columns_not_applicable') }}</b></p>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-striped hover table-bordered">
                <thead>
                <tr>
                    <th>{{ trans('custom.template_description_1') }}</th>
                    <th>{{ trans('custom.template_description_2') }}</th>
                    <th>{{ trans('custom.gl_code') }}</th>
                    <th>{{ trans('custom.gl_description') }}</th>
                    @foreach($segments as $segment)
                        <th>{{ $segment->ServiceLineDes }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($glMasters as $glMaster)
                @foreach ($templateDetails as $rowLevel)

                    @if($glMaster->detID == \App\helper\Helper::getMasterLevelOfReportTemplate($rowLevel['masterID']))

                        @foreach ($rowLevel['gllink'] as $row)
                        <tr>
                            <td>
                                {{\App\helper\Helper::headerCategoryOfReportTemplate($row['templateDetailID'])['description'] }}
                            </td>
                            <td>
                                {{$rowLevel['description']}}
                            </td>
                            <td>
                                {{$row['glCode']}}
                            </td>
                            <td>
                                {{$row['glDescription']}}
                            </td>
                            @foreach($segments as $segment)
                            <td>0</td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
