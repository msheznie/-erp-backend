@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Expense Claim Categories
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($expenseClaimCategories, ['route' => ['expenseClaimCategories.update', $expenseClaimCategories->id], 'method' => 'patch']) !!}

                        @include('expense_claim_categories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection