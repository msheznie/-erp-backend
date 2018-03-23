<table class="table table-responsive" id="companyNavigationMenuses-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Companyid</th>
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
    @foreach($companyNavigationMenuses as $companyNavigationMenus)
        <tr>
            <td>{!! $companyNavigationMenus->description !!}</td>
            <td>{!! $companyNavigationMenus->companyID !!}</td>
            <td>{!! $companyNavigationMenus->masterID !!}</td>
            <td>{!! $companyNavigationMenus->languageID !!}</td>
            <td>{!! $companyNavigationMenus->url !!}</td>
            <td>{!! $companyNavigationMenus->pageID !!}</td>
            <td>{!! $companyNavigationMenus->pageTitle !!}</td>
            <td>{!! $companyNavigationMenus->pageIcon !!}</td>
            <td>{!! $companyNavigationMenus->levelNo !!}</td>
            <td>{!! $companyNavigationMenus->sortOrder !!}</td>
            <td>{!! $companyNavigationMenus->isSubExist !!}</td>
            <td>{!! $companyNavigationMenus->timestamp !!}</td>
            <td>{!! $companyNavigationMenus->isAddon !!}</td>
            <td>{!! $companyNavigationMenus->addonDescription !!}</td>
            <td>{!! $companyNavigationMenus->addonDetails !!}</td>
            <td>{!! $companyNavigationMenus->isCoreModule !!}</td>
            <td>{!! $companyNavigationMenus->isGroup !!}</td>
            <td>
                {!! Form::open(['route' => ['companyNavigationMenuses.destroy', $companyNavigationMenus->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyNavigationMenuses.show', [$companyNavigationMenus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyNavigationMenuses.edit', [$companyNavigationMenus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>