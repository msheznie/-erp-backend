@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Document Restriction Policy
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('document_restriction_policies.show_fields')
                    <a href="{!! route('documentRestrictionPolicies.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
