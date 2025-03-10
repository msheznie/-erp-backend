<html>

<!--------------------------- header part !------------------------------------------------>

            <table>
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



<!--------------------------- details part !------------------------------------------------>

            <table>
                <thead>
                <tr>
                    @foreach($detail['title'] as $key => $title)
                        <th>{{$title}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                    @foreach($detail['data'] as $detail)
                        <tr>
                        @foreach($detail as $key => $dt)
                            @if($key != "payment_voucher_code")
                                <th>{{$dt}}</th>
                            @endif
                        @endforeach
                        </tr>
                    @endforeach
                </tbody>

            </table>




<!--------------------------- footer part !------------------------------------------------>

            <table>
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




</html>
