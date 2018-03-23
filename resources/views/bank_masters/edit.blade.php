@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankMaster, ['route' => ['bankMasters.update', $bankMaster->id], 'method' => 'patch']) !!}

                        @include('bank_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection