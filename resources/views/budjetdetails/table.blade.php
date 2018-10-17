<table class="table table-responsive" id="budjetdetails-table">
    <thead>
        <tr>
            <th>Budgetmasterid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Companyfinanceyearid</th>
        <th>Servicelinesystemid</th>
        <th>Serviceline</th>
        <th>Templatedetailid</th>
        <th>Chartofaccountid</th>
        <th>Glcode</th>
        <th>Glcodetype</th>
        <th>Year</th>
        <th>Month</th>
        <th>Budjetamtlocal</th>
        <th>Budjetamtrpt</th>
        <th>Createdbyusersystemid</th>
        <th>Createdbyuserid</th>
        <th>Modifiedbyusersystemid</th>
        <th>Modifiedbyuserid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($budjetdetails as $budjetdetails)
        <tr>
            <td>{!! $budjetdetails->budgetmasterID !!}</td>
            <td>{!! $budjetdetails->companySystemID !!}</td>
            <td>{!! $budjetdetails->companyId !!}</td>
            <td>{!! $budjetdetails->companyFinanceYearID !!}</td>
            <td>{!! $budjetdetails->serviceLineSystemID !!}</td>
            <td>{!! $budjetdetails->serviceLine !!}</td>
            <td>{!! $budjetdetails->templateDetailID !!}</td>
            <td>{!! $budjetdetails->chartOfAccountID !!}</td>
            <td>{!! $budjetdetails->glCode !!}</td>
            <td>{!! $budjetdetails->glCodeType !!}</td>
            <td>{!! $budjetdetails->Year !!}</td>
            <td>{!! $budjetdetails->month !!}</td>
            <td>{!! $budjetdetails->budjetAmtLocal !!}</td>
            <td>{!! $budjetdetails->budjetAmtRpt !!}</td>
            <td>{!! $budjetdetails->createdByUserSystemID !!}</td>
            <td>{!! $budjetdetails->createdByUserID !!}</td>
            <td>{!! $budjetdetails->modifiedByUserSystemID !!}</td>
            <td>{!! $budjetdetails->modifiedByUserID !!}</td>
            <td>{!! $budjetdetails->createdDateTime !!}</td>
            <td>{!! $budjetdetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['budjetdetails.destroy', $budjetdetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('budjetdetails.show', [$budjetdetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('budjetdetails.edit', [$budjetdetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>