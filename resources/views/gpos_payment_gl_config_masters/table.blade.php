<table class="table table-responsive" id="gposPaymentGlConfigMasters-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Glaccounttype</th>
        <th>Querystring</th>
        <th>Image</th>
        <th>Isactive</th>
        <th>Sortorder</th>
        <th>Selectboxname</th>
        <th>Timesstamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gposPaymentGlConfigMasters as $gposPaymentGlConfigMaster)
        <tr>
            <td>{!! $gposPaymentGlConfigMaster->description !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->glAccountType !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->queryString !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->image !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->isActive !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->sortOrder !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->selectBoxName !!}</td>
            <td>{!! $gposPaymentGlConfigMaster->timesstamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gposPaymentGlConfigMasters.destroy', $gposPaymentGlConfigMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gposPaymentGlConfigMasters.show', [$gposPaymentGlConfigMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gposPaymentGlConfigMasters.edit', [$gposPaymentGlConfigMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>