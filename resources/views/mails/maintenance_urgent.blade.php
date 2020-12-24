<!DOCTYPE html>
<html>
<head>
  <style type="text/css">
    table {
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid black;
    }
    td {
      padding: 3px;
    }
  </style>
</head>
<body>
  <div style="width: 700px;">
    <center>
      <img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
      <p style="font-size: 18px;">Urgent SPK Information (Last Update: {{ date('d-M-Y H:i:s') }})</p>
      This is an automatic notification. Please do not reply to this address.
      <br>
      <table style="border-color: black">
        <thead style="background-color: rgb(126,86,134);">
          <tr>
            <th colspan="6" style="background-color: #9f84a7">Urgent SPK Open List</th>
          </tr>
          <tr style="color: white; background-color: #7e5686">
            <th style="width: 2%; border:1px solid black;">#</th>
            <th style="width: 2%; border:1px solid black;">Order no</th>
            <th style="width: 2%; border:1px solid black;">Dept</th>
            <th style="width: 15%; border:1px solid black;">Request Date</th>
            <th style="width: 40%; border:1px solid black;">Description</th>
            <th style="width: 5%; border:1px solid black;">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          for ($i=0; $i < count($data['datas']); $i++) { 
            print_r ('<tr>
              <td>'.($i+1).'</td>
              <td>'.$data['datas'][$i]->order_no.'</td>
              <td>'.$data['datas'][$i]->department.'</td> 
              <td>'.$data['datas'][$i]->req_date.'</td>
              <td>'.$data['datas'][$i]->description.'</td>
              <td>'.$data['datas'][$i]->process_name.'</td>
              </tr>');
              ?>
              <tr>
                <td></td>
                <td colspan="5">
                  <table style="width: 100%">
                    <tr style="background-color: #f5eb33">
                      <th style="width: 10%">PIC</th>
                      <th style="width: 15%">Start Time</th>
                      <th style="width: 15%">Finish Time</th>
                      <th>Cause</th>
                      <th>Handling</th>
                    </tr>
                    <?php 
                    for ($z=0; $z < count($data['data_details']); $z++) 
                    { 
                      if ($data['datas'][$i]->order_no == $data['data_details'][$z]->order_no) {
                        print_r("<tr>
                          <td>".$data['data_details'][$z]->name."</td>
                          <td>".$data['data_details'][$z]->start_actual."</td>
                          <td>".$data['data_details'][$z]->finish_actual."</td>
                          <td>".$data['data_details'][$z]->cause."</td>
                          <td>".$data['data_details'][$z]->handling."</td>
                          </tr>");
                      }
                      ?>
                    <?php } ?>
                  </table>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>


        <br>
      <!-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br>
        <a href="http://172.17.128.4/mirai/public/index/report/overtime_monthly_fq">Overtime Monitoring</a><br> -->
      </center>
    </div>
  </body>
  </html>