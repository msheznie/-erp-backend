{{--@extends('layouts.app')--}}

<div class="card-body" id="print-section">
    <div class="row">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <h6><span *ngIf="request.company">{{$request->company->CompanyName}}</span></h6>
            <h6><span *ngIf="request.company">{{$request->company->CompanyAddress}}</span></h6>
            <table>
                <tr>
                    <td width="100px"><span class="font-weight-bold">Priority </span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td>
                        @if($request->priority)

                            hiii
                            {{$request->priorityD->priorityDescription}}
                            @endif
                    </td>
                </tr>
                <tr>
                    <td width="170px"><span class="font-weight-bold">Requisioner</span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td><span *ngIf="request.created_by">{{$request->created_by->empName}}</span></td>
                </tr>
                <tr>
                    <td width="170px"><span class="font-weight-bold">Location</span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td>
                        {{--<span *ngIf="request.location">
                            {{$request->location->locationName}}
                        </span>--}}
                    </td>
                </tr>
                <tr>
                    <td width="170px"><span class="font-weight-bold">Comments </span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td><span>{{$request->comments}}</span></td>
                </tr>
            </table>
        </div>
        {{--<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <h3 style="bottom: 0;position: absolute;"><span *ngIf="request.documentSystemID == 1">Purchase</span><span *ngIf="request.documentSystemID == 50">Work</span><span *ngIf="request.documentSystemID == 51">Direct</span> Requisition </h3>
        </div>
        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
            <table>
                <tr>
                    <td width="170px"><span class="font-weight-bold">Document No</span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td><span>{{request.purchaseRequestCode}}</span></td>
                </tr>
                <tr>
                    <td width="170px"><span class="font-weight-bold">Date </span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td><span>{{request.createdDateTime}}</span></td>
                </tr>
                <tr>
                    <td rowspan="3" colspan="3" width="300px" style="bottom: 0;position: absolute;">
                            <span class="font-weight-bold">
                            <h6 class="text-muted">
                                <span *ngIf="request.cancelledYN == 0 && request.PRConfirmedYN == 1">Confirmed</span>
                                <span *ngIf="request.cancelledYN == 0 && request.PRConfirmedYN == 1 && request.approved == 0">& Not Approved</span>
                                <span *ngIf="request.cancelledYN == 0 && request.PRConfirmedYN == 1 && request.approved == -1">& Approved</span>
                                 <span *ngIf="request.cancelledYN == -1">Cancelled</span>
                            </h6>
                            </span>
                    </td>
                </tr>

            </table>
        </div>--}}
    </div>
    <hr>
  {{--  <div class="row">
        <div class="col-xs-72 col-sm-12 col-md-12 col-lg-12">
            <table class="table">
                <thead>
                <tr class="theme-tr-head">
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th>Part Number</th>
                    <th>UOM</th>
                    <th style="width:10%">QTY Requested</th>
                    <th style="width:10%">QTY On Order</th>
                </tr>
                </thead>
                <tbody>
                <tr *ngFor="let item of request.details">
                    <td>{{item.itemPrimaryCode}}</td>
                    <td>{{item.itemDescription}}</td>
                    <td> {{item.partNumber}}</td>
                    <td><span *ngIf="item.uom">{{item.uom.UnitShortCode}}</span></td>
                    <td class="text-md-right">{{item.quantityRequested}}</td>
                    <td class="text-md-right">{{item.quantityOnOrder}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <table>
                <tr>
                    <td width="140px"><span class="font-weight-bold">Confirmed By </span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td><span *ngIf="request.confirmed_by">{{request.confirmed_by.empName}}</span></td>
                </tr>
            </table>
        </div>
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <table>
                <tr>
                    <td width="80px"><span class="font-weight-bold">Review By </span></td>
                    <td width="10px"><span class="font-weight-bold">:</span></td>
                    <td><div style="border-bottom: 1px solid black;width: 200px;margin-top: 7px;"></div></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <span class="font-weight-bold">Electronically Approved By :</span>
        </div>
    </div>
    <div class="row" style="margin-top: 10px">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" *ngFor="let det of request.approved_by; let i = index">
                    <div><span *ngIf="det.employee">{{ det.employee.empFullName }}</span></div>
                    <div><span>{{ det.approvedDate | date:'dd/MM/yyyy' }}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 30px">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <span class="font-weight-bold"><span [innerHTML]="request.docRefNo" class="white-space-pre-line"></span></span>
        </div>
    </div>--}}
</div>
