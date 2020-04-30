@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Fcm Token
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($fcmToken, ['route' => ['fcmTokens.update', $fcmToken->id], 'method' => 'patch']) !!}

                        @include('fcm_tokens.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection