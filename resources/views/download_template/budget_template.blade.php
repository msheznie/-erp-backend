<div class="row">
    <table class="table table-sm table-striped hover table-bordered">
        <tr>
        <td><b>Template Name :</b></td> <td> {{ $templateMaster->description }} </td>
        <td><b>Financial Year :</b></td> <td>{{ $beginDate }} - {{ $endDate }}</td>
        </tr>
        <tr>
        <td><b>Currency :</b> </td> <td> {{ $company->reportingcurrency->CurrencyCode }} </td>
        <td><b>Send Notification at % :</b> </td> <td>  {{ $sentNotificationAt }}</td>
        </tr>
    </table>
</div>
<div class="row">
    <div class="col-md-12">
        <p><b>Note<span class="p-l-10"></span> Please enter Expense GL Amounts in negative value. Eg : -1200.00 <br/><b> Do not amend/modify the Template Description columns or rows. If amended the upload may not be successful.</b> <br/> Delete segment columns which are not applicable for the upload</b></p>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-striped hover table-bordered">
                <thead>
                <tr>
                    <th>Template Description 1</th>
                    <th>Template Description 2</th>
                    <th>GL Code</th>
                    <th>GL Description</th>
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
