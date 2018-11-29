@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Stock Transfer Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($stockTransferRefferedBack, ['route' => ['stockTransferRefferedBacks.update', $stockTransferRefferedBack->id], 'method' => 'patch']) !!}

                        @include('stock_transfer_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection