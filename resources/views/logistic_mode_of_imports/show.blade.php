@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Logistic Mode Of Import
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('logistic_mode_of_imports.show_fields')
                    <a href="{!! route('logisticModeOfImports.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
