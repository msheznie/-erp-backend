<table class="table table-responsive" id="navigationUserGroupSetups-table">
    <thead>
        <tr>
            <th>Usergroupid</th>
        <th>Companyid</th>
        <th>Navigationmenuid</th>
        <th>Description</th>
        <th>Masterid</th>
        <th>Url</th>
        <th>Pageid</th>
        <th>Pagetitle</th>
        <th>Pageicon</th>
        <th>Levelno</th>
        <th>Sortorder</th>
        <th>Issubexist</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($navigationUserGroupSetups as $navigationUserGroupSetup)
        <tr>
            <td>{!! $navigationUserGroupSetup->userGroupID !!}</td>
            <td>{!! $navigationUserGroupSetup->companyID !!}</td>
            <td>{!! $navigationUserGroupSetup->navigationMenuID !!}</td>
            <td>{!! $navigationUserGroupSetup->description !!}</td>
            <td>{!! $navigationUserGroupSetup->masterID !!}</td>
            <td>{!! $navigationUserGroupSetup->url !!}</td>
            <td>{!! $navigationUserGroupSetup->pageID !!}</td>
            <td>{!! $navigationUserGroupSetup->pageTitle !!}</td>
            <td>{!! $navigationUserGroupSetup->pageIcon !!}</td>
            <td>{!! $navigationUserGroupSetup->levelNo !!}</td>
            <td>{!! $navigationUserGroupSetup->sortOrder !!}</td>
            <td>{!! $navigationUserGroupSetup->isSubExist !!}</td>
            <td>{!! $navigationUserGroupSetup->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['navigationUserGroupSetups.destroy', $navigationUserGroupSetup->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('navigationUserGroupSetups.show', [$navigationUserGroupSetup->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('navigationUserGroupSetups.edit', [$navigationUserGroupSetup->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>