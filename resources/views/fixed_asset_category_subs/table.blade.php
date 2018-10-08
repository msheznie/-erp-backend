<table class="table table-responsive" id="fixedAssetCategorySubs-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Catdescription</th>
        <th>Facatid</th>
        <th>Maincatdescription</th>
        <th>Sucatlevel</th>
        <th>Isactive</th>
        <th>Createdpcid</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetCategorySubs as $fixedAssetCategorySub)
        <tr>
            <td>{!! $fixedAssetCategorySub->companySystemID !!}</td>
            <td>{!! $fixedAssetCategorySub->companyID !!}</td>
            <td>{!! $fixedAssetCategorySub->catDescription !!}</td>
            <td>{!! $fixedAssetCategorySub->faCatID !!}</td>
            <td>{!! $fixedAssetCategorySub->mainCatDescription !!}</td>
            <td>{!! $fixedAssetCategorySub->suCatLevel !!}</td>
            <td>{!! $fixedAssetCategorySub->isActive !!}</td>
            <td>{!! $fixedAssetCategorySub->createdPcID !!}</td>
            <td>{!! $fixedAssetCategorySub->createdUserGroup !!}</td>
            <td>{!! $fixedAssetCategorySub->createdUserID !!}</td>
            <td>{!! $fixedAssetCategorySub->createdDateTime !!}</td>
            <td>{!! $fixedAssetCategorySub->modifiedPc !!}</td>
            <td>{!! $fixedAssetCategorySub->modifiedUser !!}</td>
            <td>{!! $fixedAssetCategorySub->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetCategorySubs.destroy', $fixedAssetCategorySub->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetCategorySubs.show', [$fixedAssetCategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetCategorySubs.edit', [$fixedAssetCategorySub->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>