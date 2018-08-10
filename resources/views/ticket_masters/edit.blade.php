@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Ticket Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($ticketMaster, ['route' => ['ticketMasters.update', $ticketMaster->id], 'method' => 'patch']) !!}

                        @include('ticket_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection