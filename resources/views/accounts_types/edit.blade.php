@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accounts Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($accountsType, ['route' => ['accountsTypes.update', $accountsType->id], 'method' => 'patch']) !!}

                        @include('accounts_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection