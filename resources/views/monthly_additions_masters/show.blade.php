@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Monthly Additions Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('monthly_additions_masters.show_fields')
                    <a href="{!! route('monthlyAdditionsMasters.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
