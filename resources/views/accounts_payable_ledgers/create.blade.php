@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accounts Payable Ledger
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'accountsPayableLedgers.store']) !!}

                        @include('accounts_payable_ledgers.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
