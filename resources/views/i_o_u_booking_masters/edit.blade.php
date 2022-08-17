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
                   {!! Form::model($iOUBookingMaster, ['route' => ['iOUBookingMasters.update', $iOUBookingMaster->id], 'method' => 'patch']) !!}

                        @include('i_o_u_booking_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection