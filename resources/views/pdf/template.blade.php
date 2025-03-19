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
    <table style="border: 1.5px black dotted;">
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
                Chalan No   : <b>{{$chalan_no}}</b><br>
                Client Name : <b>{{$client['client_name']}}</b> <br>
                Mobile NO.  : <b>{{$client['mobile_no']}}</b><br>
                Address     : <b>{{$client['address']}}</b>
            </td>
            <td colspan="2"></td>
            <td colspan="2">
                Date            : <b> {{$chalan_date}}</b><br>
                Auto Name       : <b>{{$auto['auto_name']??"N/A"}}</b><br>
                Auto No         : <b>{{$auto['auto_no']??"N/A"}}</b><br>
                Auto Mobile No  : <b>{{$auto['mobile_no']??"----------"}}</b> <br>
                @if(!$is_local)
                    @if($bus_no)
                        Bus Name          : <b>{{$transposer['transporter_name']??"N/A"}}</b><br>
                        Bus No            : <b>{{$bus_no??"N/A"}}</b><br>
                    @else
                        Transporter Name  : <b>{{$transposer['transporter_name']??"N/A"}}</b><br>
                        GST No            : <b>{{$transposer['gst_no']??"N/A"}}</b><br>
                    @endif
                @endif
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
                                    <td rowspan="{{$index==0 ? $item['count'] :''}}">{{$index==0 ? $item['bag_type']:""}}</td>
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
                            <td>{{$item['total_weight']}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2">Grand Total</th>
                        <th>{{$table["grand_total"]["total"] }}</th>
                        <th>{{$table["grand_total"]["total_weight"] }}</th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
