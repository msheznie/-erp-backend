@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Budget Transfer Form Detail
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'budgetTransferFormDetails.store']) !!}

                        @include('budget_transfer_form_details.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
