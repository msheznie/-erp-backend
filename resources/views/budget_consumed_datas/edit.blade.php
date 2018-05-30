@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Budget Consumed Data
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($budgetConsumedData, ['route' => ['budgetConsumedDatas.update', $budgetConsumedData->id], 'method' => 'patch']) !!}

                        @include('budget_consumed_datas.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection