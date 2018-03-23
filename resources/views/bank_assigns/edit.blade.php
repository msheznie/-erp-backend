@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Assign
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankAssign, ['route' => ['bankAssigns.update', $bankAssign->id], 'method' => 'patch']) !!}

                        @include('bank_assigns.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection