<table class="table table-responsive" id="reportTemplateLinks-table">
    <thead>
        <tr>
            <th>Templatemasterid</th>
        <th>Templatedetailid</th>
        <th>Sortorder</th>
        <th>Glautoid</th>
        <th>Subcategory</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportTemplateLinks as $reportTemplateLinks)
        <tr>
            <td>{!! $reportTemplateLinks->templateMasterID !!}</td>
            <td>{!! $reportTemplateLinks->templateDetailID !!}</td>
            <td>{!! $reportTemplateLinks->sortOrder !!}</td>
            <td>{!! $reportTemplateLinks->glAutoID !!}</td>
            <td>{!! $reportTemplateLinks->subCategory !!}</td>
            <td>{!! $reportTemplateLinks->companySystemID !!}</td>
            <td>{!! $reportTemplateLinks->companyID !!}</td>
            <td>{!! $reportTemplateLinks->createdPCID !!}</td>
            <td>{!! $reportTemplateLinks->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateLinks->createdUserID !!}</td>
            <td>{!! $reportTemplateLinks->createdDateTime !!}</td>
            <td>{!! $reportTemplateLinks->modifiedPCID !!}</td>
            <td>{!! $reportTemplateLinks->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateLinks->modifiedUserID !!}</td>
            <td>{!! $reportTemplateLinks->modifiedDateTime !!}</td>
            <td>{!! $reportTemplateLinks->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateLinks.destroy', $reportTemplateLinks->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateLinks.show', [$reportTemplateLinks->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateLinks.edit', [$reportTemplateLinks->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>