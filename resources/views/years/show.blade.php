@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Year
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('years.show_fields')
                    <a href="{!! route('years.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
