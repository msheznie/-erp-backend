<table class="table table-responsive" id="navigationMenuses-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Masterid</th>
        <th>Languageid</th>
        <th>Url</th>
        <th>Pageid</th>
        <th>Pagetitle</th>
        <th>Pageicon</th>
        <th>Levelno</th>
        <th>Sortorder</th>
        <th>Issubexist</th>
        <th>Timestamp</th>
        <th>Isaddon</th>
        <th>Addondescription</th>
        <th>Addondetails</th>
        <th>Iscoremodule</th>
        <th>Isgroup</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($navigationMenuses as $navigationMenus)
        <tr>
            <td>{!! $navigationMenus->description !!}</td>
            <td>{!! $navigationMenus->masterID !!}</td>
            <td>{!! $navigationMenus->languageID !!}</td>
            <td>{!! $navigationMenus->url !!}</td>
            <td>{!! $navigationMenus->pageID !!}</td>
            <td>{!! $navigationMenus->pageTitle !!}</td>
            <td>{!! $navigationMenus->pageIcon !!}</td>
            <td>{!! $navigationMenus->levelNo !!}</td>
            <td>{!! $navigationMenus->sortOrder !!}</td>
            <td>{!! $navigationMenus->isSubExist !!}</td>
            <td>{!! $navigationMenus->timestamp !!}</td>
            <td>{!! $navigationMenus->isAddon !!}</td>
            <td>{!! $navigationMenus->addonDescription !!}</td>
            <td>{!! $navigationMenus->addonDetails !!}</td>
            <td>{!! $navigationMenus->isCoreModule !!}</td>
            <td>{!! $navigationMenus->isGroup !!}</td>
            <td>
                {!! Form::open(['route' => ['navigationMenuses.destroy', $navigationMenus->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('navigationMenuses.show', [$navigationMenus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('navigationMenuses.edit', [$navigationMenus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>