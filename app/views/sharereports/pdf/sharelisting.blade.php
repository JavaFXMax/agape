<html>
  <head>     
   <style>
     @page { margin: 170px 30px; }
     .header { position: fixed; left: 0px; top: -150px; right: 0px; height: 150px;  text-align: center; }
     .footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 50px;  }
     .footer .page:after { content: counter(page, upper-roman); }
     .content { margin-top: -70px; margin-bottom:-25px; }      
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
          <strong><h3>MEMBERS CONTRIBUTIONS AND SHARES LISTING</h3></strong>
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
      <table class="table table-bordered" style="width:100%">
          <tr style="font-weight:bold">
              <td>#</td>
              <td>Member</td>
              <td>Member #</td>
              <td>Total Contributions</td>
              <td>Accrued Dividends</td>                         
          </tr>
          <tbody>
           <?php $i =1; ?>
            @foreach($members as $member)
             <tr>
                <td>{{$i}}</td>
                <td>{{ucwords($member->name)}}</td>
                <td>{{ucwords($member->membership_no)}}</td>
                <?php
                  $contributions=Savingtransaction::where('savingaccount_id','=',$member->id)
                          ->where('type','=', 'credit')->sum('amount');
                ?>
                <td>{{asMoney($contributions)}}</td>
                <td>{{asMoney($contributions/40)}}</td>                 
              </tr>
               <?php $i++; ?>
            @endforeach                     
          </tbody>
      </table>          
   </div>
 </body>
 </html>