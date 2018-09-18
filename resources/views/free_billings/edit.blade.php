@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Free Billing
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($freeBilling, ['route' => ['freeBillings.update', $freeBilling->id], 'method' => 'patch']) !!}

                        @include('free_billings.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection