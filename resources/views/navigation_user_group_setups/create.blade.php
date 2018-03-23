@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Navigation User Group Setup
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'navigationUserGroupSetups.store']) !!}

                        @include('navigation_user_group_setups.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
