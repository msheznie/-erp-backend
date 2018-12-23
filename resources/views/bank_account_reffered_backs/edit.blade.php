@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Account Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankAccountRefferedBack, ['route' => ['bankAccountRefferedBacks.update', $bankAccountRefferedBack->id], 'method' => 'patch']) !!}

                        @include('bank_account_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection