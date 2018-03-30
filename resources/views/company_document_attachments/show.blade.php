@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Company Document Attachment
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('company_document_attachments.show_fields')
                    <a href="{!! route('companyDocumentAttachments.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
