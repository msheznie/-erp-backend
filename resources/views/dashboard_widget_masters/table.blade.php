<div class="table-responsive">
    <table class="table" id="dashboardWidgetMasters-table">
        <thead>
            <tr>
                <th>Widgetmastername</th>
        <th>Departmentid</th>
        <th>Sortorder</th>
        <th>Widgetmastericon</th>
        <th>Isactive</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($dashboardWidgetMasters as $dashboardWidgetMaster)
            <tr>
                <td>{!! $dashboardWidgetMaster->WidgetMasterName !!}</td>
            <td>{!! $dashboardWidgetMaster->departmentID !!}</td>
            <td>{!! $dashboardWidgetMaster->sortOrder !!}</td>
            <td>{!! $dashboardWidgetMaster->widgetMasterIcon !!}</td>
            <td>{!! $dashboardWidgetMaster->isActive !!}</td>
            <td>{!! $dashboardWidgetMaster->timestamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['dashboardWidgetMasters.destroy', $dashboardWidgetMaster->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('dashboardWidgetMasters.show', [$dashboardWidgetMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('dashboardWidgetMasters.edit', [$dashboardWidgetMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
