@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Third Party Systems
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('third_party_systems.show_fields')
                    <a href="{{ route('thirdPartySystems.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
