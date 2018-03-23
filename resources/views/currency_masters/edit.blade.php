@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Currency Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($currencyMaster, ['route' => ['currencyMasters.update', $currencyMaster->id], 'method' => 'patch']) !!}

                        @include('currency_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection