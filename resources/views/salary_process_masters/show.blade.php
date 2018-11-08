@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Salary Process Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('salary_process_masters.show_fields')
                    <a href="{!! route('salaryProcessMasters.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
