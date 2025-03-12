<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>

</head>
<body>
    <table>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">
                Chalan No   : <b>{{$chalanNo}}</b><br>
                Client Name : <b>{{$client->client_name}}</b> <br>
                Mobile NO.  : <b>{{$client->mobile_no}}</b><br>
                Address     : <b>{{$client->address}}</b>
            </td>
            <td colspan="2"></td>
            <td colspan="2">
                Date            : <b> {{$chalanDate}}</b><br>
                Auto Name       : <b>{{$auto->auto_name??"N/A"}}</b><br>
                Auto Mobile No  : <b>{{$auto->mobile_no??"----------"}}</b> <br>
                Transposer Name : <b>{{$transposer->name??"N/A"}}</b><br>
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <table>
                    <tr>
                        <th>Bag Type</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Qtr</th>
                    </tr>
                    
                    @foreach($table["row"] as $item)
                        @php $index = 0; @endphp

                        @foreach($item["bags"] as $bags)                                 
                            <tr>
                                @if($index==0)
                                    <td rowspan="{{$index==0 ? $item['count'] :''}}">{{$index==0 ? $item['bagType']:""}}</td>
                                @endif                                
                                <td >{{$bags['bag_size']}}</td>
                                <td >{{$bags['bag_color']}}</td>
                                <td >{{$bags['packing_weight']}}</td>
                                @php 
                                    $index++; 
                                @endphp
                            </tr>
                        @endforeach
                        <tr>
                            <td  colspan="3"></td>
                            <td>{{$item['totalWeight']}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2">Grand Total</th>
                        <th>{{$table["grandTotal"]["total"] }}</th>
                        <th>{{$table["grandTotal"]["totalWeight"] }}</th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
