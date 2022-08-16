@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            I O U Booking Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('i_o_u_booking_masters.show_fields')
                    <a href="{{ route('iOUBookingMasters.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
