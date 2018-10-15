@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fixed Asset Insurance Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fixedAssetInsuranceDetail, ['route' => ['fixedAssetInsuranceDetails.update', $fixedAssetInsuranceDetail->id], 'method' => 'patch']) !!}

                        @include('fixed_asset_insurance_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection