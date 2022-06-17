@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Third Party Integration Keys
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('third_party_integration_keys.show_fields')
                    <a href="{{ route('thirdPartyIntegrationKeys.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
