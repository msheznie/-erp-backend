@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Secondary Company
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($secondaryCompany, ['route' => ['secondaryCompanies.update', $secondaryCompany->id], 'method' => 'patch']) !!}

                        @include('secondary_companies.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection