@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Insurance Policy Type
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('insurance_policy_types.show_fields')
                    <a href="{!! route('insurancePolicyTypes.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
