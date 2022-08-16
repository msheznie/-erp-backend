@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            I O U Booking Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'iOUBookingMasters.store']) !!}

                        @include('i_o_u_booking_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
