<table class="table table-responsive" id="taxFormulaDetails-table">
    <thead>
        <tr>
            <th>Taxcalculationformulaid</th>
        <th>Taxmasterautoid</th>
        <th>Description</th>
        <th>Taxmasters</th>
        <th>Sortorder</th>
        <th>Formula</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($taxFormulaDetails as $taxFormulaDetail)
        <tr>
            <td>{!! $taxFormulaDetail->taxCalculationformulaID !!}</td>
            <td>{!! $taxFormulaDetail->taxMasterAutoID !!}</td>
            <td>{!! $taxFormulaDetail->description !!}</td>
            <td>{!! $taxFormulaDetail->taxMasters !!}</td>
            <td>{!! $taxFormulaDetail->sortOrder !!}</td>
            <td>{!! $taxFormulaDetail->formula !!}</td>
            <td>{!! $taxFormulaDetail->companySystemID !!}</td>
            <td>{!! $taxFormulaDetail->companyID !!}</td>
            <td>{!! $taxFormulaDetail->createdUserGroup !!}</td>
            <td>{!! $taxFormulaDetail->createdPCID !!}</td>
            <td>{!! $taxFormulaDetail->createdUserID !!}</td>
            <td>{!! $taxFormulaDetail->createdDateTime !!}</td>
            <td>{!! $taxFormulaDetail->createdUserName !!}</td>
            <td>{!! $taxFormulaDetail->modifiedPCID !!}</td>
            <td>{!! $taxFormulaDetail->modifiedUserID !!}</td>
            <td>{!! $taxFormulaDetail->modifiedDateTime !!}</td>
            <td>{!! $taxFormulaDetail->modifiedUserName !!}</td>
            <td>{!! $taxFormulaDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['taxFormulaDetails.destroy', $taxFormulaDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('taxFormulaDetails.show', [$taxFormulaDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('taxFormulaDetails.edit', [$taxFormulaDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>