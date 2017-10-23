@extends('layouts.ports')
@section('content')
<br/>
<div class="row">
	<div class="col-lg-12">
  <h3> Share Reports</h3>
<hr>
</div>	
</div>
<div class="row">
	<div class="col-lg-12">
    <ul>
    <!--
      <li>
        <a href="{{ URL::to('sharereports/contributionlisting') }}" target="_blank">
          Contributions Listing Report
        </a>
      </li>
      -->
       <li>
        <a href="sharereports/sharelisting" target="_blank"> 
          Contributions and Shares Listing
        </a>
      </li>
      <li>
        <a href="sharereports/individualcontribution" target="_blank">
          Individual Contribution 
        </a>
      </li>      
    </ul>
  </div>
</div>
@stop