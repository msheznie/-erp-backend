@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Memo Types
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bankMemoTypes, ['route' => ['bankMemoTypes.update', $bankMemoTypes->id], 'method' => 'patch']) !!}

                        @include('bank_memo_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection