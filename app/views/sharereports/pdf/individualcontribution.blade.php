<html>
  <head>     
   <style>
     @page { margin: 170px 30px; }
     .header { position: fixed; left: 0px; top: -150px; right: 0px; height: 150px;  text-align: center; }
     .footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 50px;  }
     .footer .page:after { content: counter(page, upper-roman); }
     .content { margin-top: -70px;  }      
   </style>
  <body style="font-size:10px">
    <?php
    function asMoney($value) {
      return number_format($value, 2);
    }
    ?>
   <div class="header">
     <table >
      <tr>
        <td style="width:150px">
            <img src="{{ asset('../images/logo.png') }}" alt="{{ $organization->logo }}" width="150px"/>
        </td>
        <td>
        <strong>
          {{ strtoupper($organization->name)}}<br>
          </strong>
          {{ $organization->phone}} |
          {{ $organization->email}} |
          {{ $organization->website}}<br>
          {{ $organization->address}}
        </td>
        <td>
          <strong><h3>MEMBER INDIVIDUAL CONTRIBUTION </h3></strong>
        </td>
      </tr>
      <tr>
      <hr>
      </tr>
    </table>
   </div>
   <div class="footer">
     <p class="page">Page <?php $PAGE_NUM ?></p>
   </div>
   <div class="content">
      <table class="table table-bordered">
          <tr>
            <td style="width:17%">Member</td><td>{{ucwords($member->name)}}</td>
          </tr>
          <tr>
            <td>Member #</td><td>{{ucwords($member->membership_no)}}</td>
          </tr>  
          <tr>
            <td>Total Contributions</td><td>{{asMoney($contributions)}}</td>
          </tr>   
          <tr>
            <td>Total Shares</td><td>{{asMoney($contributions)}}</td>
          </tr> 
          <tr>
            <td>Accrued Dividends</td><td>{{asMoney($contributions/40)}}</td>
          </tr>  
      </table>
      <br><br>
      <table class="table table-bordered" style="width:100%">          
          <tr style="font-weight:bold">
              <td>Date</td>
              <td>Amount</td>
              <td>Type</td>
              <td>Description</td>                         
          </tr>
          <tbody>
          <?php
            $total=0;
          ?>
            @foreach($transactions as $transact)
             <tr>
                <td>
                  <?php 
                   $date = date("d-M-Y", strtotime($transact->date));
                  ?>
                  {{$date}}
                </td>
                <td>{{asMoney($amount=$transact->amount)}}</td>
                <td>{{$transact->type}}</td>
                <td>{{$transact->description}}</td>                 
              </tr> 
              <?php
                $total+=$amount;
              ?>
              @endforeach                       
          </tbody>
      </table>      
        <p style="margin-top:3%; margin-left: 26%;">
          <strong>
              Total Contributions: &emsp; {{ asMoney($total)}}
          </strong>
        </p>        
   </div>
 </body>
 </html>