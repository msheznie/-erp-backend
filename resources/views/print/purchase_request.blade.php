<html>
<head>
    <title>Purchase Request</title>
    <style>
        h3 {
            font-size: 1.53125rem;
        }

        h6 {
            font-size: 0.875rem;
        }

        h6, h3 {
            margin-bottom: 0.5rem;
            font-family: inherit;
            font-weight: 500;
            line-height: 1.2;
            color: inherit;
        }

        table > tbody > tr > td {
            font-size: 11.5px;
        }

        .theme-tr-head {
            background-color: #DEDEDE !important;
        }

        .text-left {
            text-align: left;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        tr td {
            padding: 5px 0;
        }
    </style>
</head>
<body>
<div class="card-body" id="print-section">
    <table>
        <tr style="width:100%">
            <td style="width: 30%">
                <h6>
                    @if($request->company)
                        {{$request->company->CompanyName}}
                    @endif
                </h6>
                <h6>
                    @if($request->company)
                        {{$request->company->CompanyAddress}}
                    @endif
                </h6>
                <table>
                    <tr>
                        <td width="100px">
                            <span class="font-weight-bold">Priority </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if($request->priority)
                                {{$request->priority}}
                                {{--{{$request->priority->priorityDescription}}--}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="170px">
                            <span class="font-weight-bold">Requisioner</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if($request->created_by)
                                {{$request->created_by->empName}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="170px">
                            <span class="font-weight-bold">Location</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            @if($request->location)
                                {{$request->location}}
                                {{--{{$request->location->locationName}}--}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="170px">
                            <span class="font-weight-bold">Comments </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$request->comments}}</span>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;text-align: center">
                <h3>
                    @if($request->documentSystemID == 1)
                        Purchase
                    @endif
                    @if($request->documentSystemID == 50)
                        Work
                    @endif
                    @if($request->documentSystemID == 51)
                        Direct
                    @endif
                    Requisition
                </h3>
            </td>
            <td style="width: 30%">
                <table>
                    <tr>
                        <td width="170px">
                            <span class="font-weight-bold">Document No</span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$request->purchaseRequestCode}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="170px">
                            <span class="font-weight-bold">Date </span>
                        </td>
                        <td width="10px">
                            <span class="font-weight-bold">:</span>
                        </td>
                        <td>
                            <span>{{$request->createdDateTime}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td rowspan="3" colspan="3" width="300px">
                                <span class="font-weight-bold">
                                    <h6 class="text-muted">
                                    @if($request->cancelledYN == 0 && $request->PRConfirmedYN == 1)
                                            Confirmed
                                        @endif
                                        @if($request->cancelledYN == 0 && $request->PRConfirmedYN == 1 && $request->approved == 0)
                                            & Not Approved
                                        @endif
                                        @if($request->cancelledYN == 0 && $request->PRConfirmedYN == 1 && $request->approved == -1)
                                            & Approved
                                        @endif
                                        @if($request->cancelledYN == -1)
                                            Cancelled
                                        @endif
                                                              </h6>
                                </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <div class="row">
        <table class="table" style="width: 100%">
            <thead>
            <tr class="theme-tr-head">
                <th class="text-left">Item Code</th>
                <th class="text-left">Item Description</th>
                <th class="text-left">Part Number</th>
                <th class="text-left">UOM</th>
                <th class="text-left">QTY Requested</th>
                <th class="text-left">QTY On Order</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($request->details as $item)
                <tr>
                    <td>{{$item->itemPrimaryCode}}</td>
                    <td>{{$item->itemDescription}}</td>
                    <td> {{$item->partNumber}}</td>
                    <td>
                        @if($item->uom)
                            {{$item->uom->UnitShortCode}}
                        @endif
                    </td>
                    <td class="text-md-right">{{$item->quantityRequested}}</td>
                    <td class="text-md-right">{{$item->quantityOnOrder}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <hr>
    <div class="row">
        <table>
            <tr style="width:100%">
                <td style="width:60%">
                    <table>
                        <tr>
                            <td width="140px">
                                <span class="font-weight-bold">Confirmed By </span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                @if($request->confirmed_by)
                                    {{$request->confirmed_by->empName}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td width="80px">
                                <span class="font-weight-bold">Review By </span>
                            </td>
                            <td width="10px">
                                <span class="font-weight-bold">:</span>
                            </td>
                            <td>
                                <div style="border-bottom: 1px solid black;width: 200px;margin-top: 7px;"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <span class="font-weight-bold">Electronically Approved By :</span>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <table>
                    <tr>
                        @foreach ($request->approved_by as $det)
                            <td style="padding-right: 25px">
                                <div>
                                    @if($det->employee)
                                        {{$det->employee->empFullName }}
                                    @endif
                                </div>
                                <div><span>{{$det->approvedDate }}</span></div>
                                <div style="width: 3px"></div>
                            </td>
                        @endforeach
                    </tr>
                </table>

            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 30px">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <span class="font-weight-bold">
                        <span class="white-space-pre-line">{{$request->docRefNo}}</span>
                    </span>
        </div>
    </div>
</div>
</body>
</html>