@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Budjetdetails
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($budjetdetails, ['route' => ['budjetdetails.update', $budjetdetails->id], 'method' => 'patch']) !!}

                        @include('budjetdetails.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection