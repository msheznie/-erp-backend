<div class="row">
    <div>
        <div class="table">
            <table>
                <head>
                    <tr></tr>
                    <tr>
                        <th colspan="15">
                            {{__('custom.instructions_to_populate_data')}}
                        </th>
                    </tr>
                </head>
                <tbody>

                    <tr>
                        <td colspan="15">
                            {{__('custom.m_refers_to_mandatory_field')}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="15">
                            {{__('custom.do_not_modify')}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="15">
                            {{__('custom.to_add_multiple_details')}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="15">
                            {{__('custom.the_invoice_header_details_will_be_extracted_from_initial_invoice')}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="15">
                            {{__('custom.system_will_automatically_use_uploader_name')}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="15">
                            {{__('custom.both_cr_number_fields_cannot_be_blank')}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
<div class="row">
    <div>
        <div class="table-responsive">
            @if ($isProjectBase && $isVATEligible)
                {{$detailColumns = 9}}
            @elseif ((!$isProjectBase && $isVATEligible) || ($isProjectBase && !$isVATEligible))
                {{$detailColumns = 8}}
            @else 
                {{$detailColumns = 7}}
            @endif
            <table class="table table-sm table-striped hover table-bordered">
                <thead>
                <tr>
                    <th colspan="11" style="text-align: center">{{__('custom.header')}}</th>
                    <th colspan="{{$detailColumns}}" style="text-align: center">{{__('custom.details')}}</th>
                </tr>
                <tr>
                    <th>M</th>
                    <th></th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th></th>
                    <th></th>
                    <th>M</th>
                    @if ($isProjectBase)
                        <th></th>
                    @endif
                    <th>M</th>
                    <th></th>
                    <th>M</th>
                    <th>M</th>
                    <th>M</th>
                    <th></th>
                    @if ($isVATEligible)
                        <th></th>
                    @endif
                </tr>
                <tr>
                    <th>{{__('custom.customer_code')}}</th>
                    <th>{{__('custom.cr_number')}}</th>
                    <th>{{__('custom.currency')}}</th>
                    <th>{{__('custom.comments')}}</th>
                    <th>{{__('custom.document_date')}}</th>
                    <th>{{__('custom.invoice_due_date')}}</th>
                    <th>{{__('custom.customer_invoice_no')}}</th>
                    <th>{{__('custom.bank')}}</th>
                    <th>{{__('custom.account_no')}}</th>
                    <th>{{__('custom.confirmed_by')}}</th>
                    <th>{{__('custom.approved_by')}}</th>
                    <th>{{__('custom.gl_account')}}</th>
                    @if ($isProjectBase)
                        <th>{{__('custom.project')}}</th>
                    @endif
                    <th>{{__('custom.segment')}}</th>
                    <th>{{__('custom.comments')}}</th>
                    <th>{{__('custom.uom')}}</th>
                    <th>{{__('custom.qty')}}</th>
                    <th>{{__('custom.sales_price')}}</th>
                    <th>{{__('custom.discount_amount')}}</th>
                    @if ($isVATEligible)
                        <th>{{__('custom.vat_amount')}}</th>
                    @endif
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
