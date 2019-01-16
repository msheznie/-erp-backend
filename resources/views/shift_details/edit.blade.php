@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Shift Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($shiftDetails, ['route' => ['shiftDetails.update', $shiftDetails->id], 'method' => 'patch']) !!}

                        @include('shift_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection