@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            General Ledger
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($generalLedger, ['route' => ['generalLedgers.update', $generalLedger->id], 'method' => 'patch']) !!}

                        @include('general_ledgers.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection