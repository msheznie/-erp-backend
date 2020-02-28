<div class="table-responsive">
    <table class="table" id="chartOfAccountAllocationDetailHistories-table">
        <thead>
            <tr>
                <th>Jvmasterautoid</th>
        <th>Timestamp</th>
        <th>Percentage</th>
        <th>Productlineid</th>
        <th>Productlinecode</th>
        <th>Allocationmaid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Chartofaccountallocationmasterid</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($chartOfAccountAllocationDetailHistories as $chartOfAccountAllocationDetailHistory)
            <tr>
                <td>{!! $chartOfAccountAllocationDetailHistory->jvMasterAutoId !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->timestamp !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->percentage !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->productLineID !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->productLineCode !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->allocationmaid !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->companySystemID !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->companyid !!}</td>
            <td>{!! $chartOfAccountAllocationDetailHistory->chartOfAccountAllocationMasterID !!}</td>
                <td>
                    {!! Form::open(['route' => ['chartOfAccountAllocationDetailHistories.destroy', $chartOfAccountAllocationDetailHistory->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('chartOfAccountAllocationDetailHistories.show', [$chartOfAccountAllocationDetailHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('chartOfAccountAllocationDetailHistories.edit', [$chartOfAccountAllocationDetailHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
