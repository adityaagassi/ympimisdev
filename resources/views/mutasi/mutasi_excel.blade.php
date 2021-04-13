<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($resumes))
    <table>
        <thead>
            <tr>
                <th>EmployeeNO</th>
                <th>FirstName</th>
                <th>MiddleName</th>
                <th>LastName</th>
                <th>NO</th>
                <th>CareerTransition</th>
                <th>CareerTransType</th>
                <th>EffectiveDate</th>
                <th>EmploymentEnddate</th>
                <th>PositionCode</th>
                <th>Position</th>
                <th>Grade</th>
                <th>EmployeeStatus</th>
                <th>Company</th>
                <!-- <th>Resign_Reason</th> -->
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $mjo)
            
            <?php
                $namaLengkap = $mjo->nama;
                $nama = explode(" ", $namaLengkap);
                $jumlah_nama = count($nama);
            ?>

            <tr>
                <td>{{ $mjo->nik }}</td>
                <?php 
                    if($jumlah_nama == 1){
                        $first = $nama[0];
                        $middle = "";
                        $last = "";
                    }
                    else if($jumlah_nama == 2){
                        $first = $nama[0];
                        $middle = "";
                        $last = $nama[1];
                    }
                    else if($jumlah_nama == 3){
                        $first = $nama[0];
                        $middle = $nama[1];
                        $last = $nama[2];
                    }
                ?>
                <td>{{ $first }}</td>
                <td>{{ $middle }}</td>
                <td>{{ $last }}</td>
                <td>{{ '1' }}</td>
                <td>{{ 'Movement' }}</td>
                <td>{{ 'Mutation' }}</td>
                <td>{{ $mjo->tanggal }}</td>
                <td>{{ '' }}</td>
                <td>{{ $mjo->position_code }}</td>
                <td>{{ $mjo->posisi }}</td>
                <td>{{ $mjo->grade }}</td>
                <td>{{ $mjo->pegawai }}</td>
                <td>{{ 'PT. Yamaha Musical Product Indonesia' }}</td>
                <!-- <td>{{ $mjo->alasan }}</td> -->
            </tr>
            
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>