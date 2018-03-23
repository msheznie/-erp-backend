@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Company Navigation Menus
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'companyNavigationMenuses.store']) !!}

                        @include('company_navigation_menuses.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
