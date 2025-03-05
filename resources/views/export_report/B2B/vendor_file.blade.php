
<!--------------------------- header part !------------------------------------------------>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-striped hover table-bordered" width="100%">
                <thead>
                <tr>
                    @foreach($header['title'] as $title)
                        <th>{{$title}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                        @foreach($header['data'] as $data)
                            <tr>
                                @foreach($data as $dt)
                                    <th>{{$dt}}</th>
                                @endforeach
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
        <br>
    </div>
</div>


<!--------------------------- details part !------------------------------------------------>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-sm table-striped hover table-bordered" width="100%">
                <thead style="border: 1px solid black;">
                    <tr>
                        @foreach($detail['title'] as $title)
                            <th>{{$title}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($detail['data'] as $detail)
                        <tr>
                            @foreach($detail as $dt)
                                <th>{{$dt}}</th>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
        <br>
    </div>
</div>




<!--------------------------- footer part !------------------------------------------------>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">

            <table class="table table-sm table-striped hover table-bordered" width="100%">
                <thead>
                    <tr>
                        @foreach($footer['title'] as $footerTitle)
                            <th>{{$footerTitle}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                        @foreach($footer['data'] as $data)
                            <tr>
                                @foreach($data as $dt)
                                    <th>{{$dt}}</th>
                                @endforeach
                            </tr>
                        @endforeach
                </tbody>

            </table>
        </div>
        <br>
    </div>
</div>


