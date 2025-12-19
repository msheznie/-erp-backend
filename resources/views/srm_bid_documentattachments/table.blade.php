<div class="table-responsive">
    <table class="table" id="srmBidDocumentattachments-table">
        <thead>
            <tr>
                <th>Tender Id</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Attachmentdescription</th>
        <th>Originalfilename</th>
        <th>Myfilename</th>
        <th>Path</th>
        <th>Sizeinkbs</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($srmBidDocumentattachments as $srmBidDocumentattachments)
            <tr>
                <td>{{ $srmBidDocumentattachments->tender_id }}</td>
            <td>{{ $srmBidDocumentattachments->companySystemID }}</td>
            <td>{{ $srmBidDocumentattachments->companyID }}</td>
            <td>{{ $srmBidDocumentattachments->documentSystemID }}</td>
            <td>{{ $srmBidDocumentattachments->documentID }}</td>
            <td>{{ $srmBidDocumentattachments->documentSystemCode }}</td>
            <td>{{ $srmBidDocumentattachments->attachmentDescription }}</td>
            <td>{{ $srmBidDocumentattachments->originalFileName }}</td>
            <td>{{ $srmBidDocumentattachments->myFileName }}</td>
            <td>{{ $srmBidDocumentattachments->path }}</td>
            <td>{{ $srmBidDocumentattachments->sizeInKbs }}</td>
                <td>
                    {!! Form::open(['route' => ['srmBidDocumentattachments.destroy', $srmBidDocumentattachments->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('srmBidDocumentattachments.show', [$srmBidDocumentattachments->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('srmBidDocumentattachments.edit', [$srmBidDocumentattachments->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
