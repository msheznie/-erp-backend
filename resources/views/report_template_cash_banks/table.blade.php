<table class="table table-responsive" id="reportTemplateCashBanks-table">
    <thead>
        <tr>
            <th>Chartofaccountsystemid</th>
        <th>Glcode</th>
        <th>Glcodedescription</th>
        <th>Isactive</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportTemplateCashBanks as $reportTemplateCashBank)
        <tr>
            <td>{!! $reportTemplateCashBank->chartOfAccountSystemID !!}</td>
            <td>{!! $reportTemplateCashBank->glCode !!}</td>
            <td>{!! $reportTemplateCashBank->glCodeDescription !!}</td>
            <td>{!! $reportTemplateCashBank->isActive !!}</td>
            <td>{!! $reportTemplateCashBank->companySystemID !!}</td>
            <td>{!! $reportTemplateCashBank->companyID !!}</td>
            <td>{!! $reportTemplateCashBank->createdUserGroup !!}</td>
            <td>{!! $reportTemplateCashBank->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateCashBank->createdUserID !!}</td>
            <td>{!! $reportTemplateCashBank->createdPcID !!}</td>
            <td>{!! $reportTemplateCashBank->createdDateTime !!}</td>
            <td>{!! $reportTemplateCashBank->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateCashBank->modifiedUser !!}</td>
            <td>{!! $reportTemplateCashBank->modifiedPc !!}</td>
            <td>{!! $reportTemplateCashBank->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateCashBanks.destroy', $reportTemplateCashBank->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateCashBanks.show', [$reportTemplateCashBank->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateCashBanks.edit', [$reportTemplateCashBank->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>