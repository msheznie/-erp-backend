@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Approval Level
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'approvalLevels.store']) !!}

                        @include('approval_levels.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
