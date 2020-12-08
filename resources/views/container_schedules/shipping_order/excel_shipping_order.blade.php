<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        table{
            border: 2px solid black;
            vertical-align: middle;
        }
        table > thead > tr > th{
            border: 2px solid black;
        }
        table > tbody > tr > td{
            border: 1px solid rgb(211,211,211);
        }
        table > tfoot > tr > th{
            border: 1px solid rgb(211,211,211);
        }
    </style>
</head>
<body>
    @if(isset($excel) && count($excel) > 0)
    <table>
        <thead>
            <tr style="background-color: #ddebf7;">
                <th>YCJ Ref. no.</th>
                <th>HELP</th>
                <th>Status</th>
                <th>Shipper</th>
                <th>POL</th>
                <th>POD</th>
                <th>40HC</th>
                <th>40'</th>
                <th>20'</th>
                <th>B/L no. or Booking no.</th>
                <th>Carrier/FWD</th>
                <th>Nomination</th>
                <th>STUFFING</th>
                <th>ETD</th>
                <th>Applied Rate</th>
                <th>Remarks</th>
                <th>Due Date</th>
                <th>I/V#</th>
                <th>Ref #</th>
            </tr>
        </thead>
        <tbody>
            @php $last = ''; @endphp
            @foreach($excel as $tr)
            <tr>
                @php
                if($last != $tr->ycj_ref_number){
                    $span = $resumes[$tr->ycj_ref_number]['qty'];
                    @endphp
                    <td style="vertical-align: middle;" rowspan="{{ $span }}">{{ $tr->ycj_ref_number }}</td>

                    @php
                    $last = $tr->ycj_ref_number;

                }else{
                    @endphp
                    <td>{{ $tr->ycj_ref_number }}</td>
                    @php
                }
                @endphp

                
                @if($tr->help == 'YES')
                <td style="text-align: center;">&#9899;</td>
                @else
                <td style="text-align: center;">-</td>
                @endif

                <td>{{ $tr->status }}</td>
                <td>{{ $tr->shipper }}</td>
                <td>{{ $tr->port_loading }}</td>
                <td>{{ $tr->port_of_delivery }}, {{ $tr->country }}</td>
                <td>{{ $tr->fortyhc }}</td>
                <td>{{ $tr->forty }}</td>
                <td>{{ $tr->twenty }}</td>
                <td>{{ $tr->booking_number }}</td>
                <td>{{ $tr->carier }}</td>
                <td>{{ $tr->nomination }}</td>
                <td>{{ $tr->stuffing_date }}</td>
                <td>{{ $tr->etd_date }}</td>
                <td>{{ $tr->application_rate }}</td>
                <td>{{ $tr->remark }}</td>
                <td>{{ $tr->due_date }}</td>
                <td>{{ $tr->invoice_number }}</td>
                <td>{{ $tr->ref }}</td>


            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>