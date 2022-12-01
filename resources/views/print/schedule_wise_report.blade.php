<html>
<head>
    <title>Item - Supplier</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .sup-item-summary-report {
            font-size: 12px;
        }

        .sup-item-summary-report-body {
            font-size: 12px;
        }

        .table thead th {
            border-bottom: none !important;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }

    </style>
</head>

<table style="width:100%;" class="sup-item-summary-report">
   <tr>
    <td>Tender ID</td>
    <td>{{$tender->id}}</td>
   </tr>
   <tr>
    <td>Tender Title</td>
    <td>{{$tender->title}}</td>
   </tr>
</table>

<table style="width:100%; margin-top:10%;" class="sup-item-summary-report">
   <tr>
    <th>Supplier Name</th>
    <th>Grand Sum</th>
    <th>Ranking</th>
   </tr>

   @foreach($items as $item)
   <tr>
    <td>{{$supplier}}</td>
    <td>{{$item['total']}}</td>
    <td>{{$item['ranking']}}</td>
   </tr>
   @endforeach

</table>



</html>

