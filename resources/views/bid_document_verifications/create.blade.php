@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bid Document Verification
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'bidDocumentVerifications.store']) !!}

                        @include('bid_document_verifications.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
