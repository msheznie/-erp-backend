<table class="table table-responsive" id="addonCostCategories-table">
    <thead>
        <tr>
            <th>Costcatdes</th>
        <th>Glcode</th>
        <th>Timesstamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($addonCostCategories as $addonCostCategories)
        <tr>
            <td>{!! $addonCostCategories->costCatDes !!}</td>
            <td>{!! $addonCostCategories->glCode !!}</td>
            <td>{!! $addonCostCategories->timesStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['addonCostCategories.destroy', $addonCostCategories->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('addonCostCategories.show', [$addonCostCategories->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('addonCostCategories.edit', [$addonCostCategories->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>