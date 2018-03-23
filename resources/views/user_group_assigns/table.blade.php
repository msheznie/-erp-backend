<table class="table table-responsive" id="userGroupAssigns-table">
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
        <th>Readonly</th>
        <th>Create</th>
        <th>Update</th>
        <th>Delete</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($userGroupAssigns as $userGroupAssign)
        <tr>
            <td>{!! $userGroupAssign->userGroupID !!}</td>
            <td>{!! $userGroupAssign->companyID !!}</td>
            <td>{!! $userGroupAssign->navigationMenuID !!}</td>
            <td>{!! $userGroupAssign->description !!}</td>
            <td>{!! $userGroupAssign->masterID !!}</td>
            <td>{!! $userGroupAssign->url !!}</td>
            <td>{!! $userGroupAssign->pageID !!}</td>
            <td>{!! $userGroupAssign->pageTitle !!}</td>
            <td>{!! $userGroupAssign->pageIcon !!}</td>
            <td>{!! $userGroupAssign->levelNo !!}</td>
            <td>{!! $userGroupAssign->sortOrder !!}</td>
            <td>{!! $userGroupAssign->isSubExist !!}</td>
            <td>{!! $userGroupAssign->readonly !!}</td>
            <td>{!! $userGroupAssign->create !!}</td>
            <td>{!! $userGroupAssign->update !!}</td>
            <td>{!! $userGroupAssign->delete !!}</td>
            <td>{!! $userGroupAssign->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['userGroupAssigns.destroy', $userGroupAssign->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('userGroupAssigns.show', [$userGroupAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('userGroupAssigns.edit', [$userGroupAssign->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>