@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Expense Claim
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($expenseClaim, ['route' => ['expenseClaims.update', $expenseClaim->id], 'method' => 'patch']) !!}

                        @include('expense_claims.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection