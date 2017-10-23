<?php

class sharereportsController extends \BaseController{
	//The index page for share reports
	public function index(){
		return View::make('sharereports.index');
	}
	//Contribution Listing Report
	public function c_listing(){
		$members=Member::all();
		$organization=Organization::find(1);
		$pdf = PDF::loadView('sharereports.pdf.contributionlisting', compact('organization','members'))
		->setPaper('a5')->setOrientation('potrait');
		return $pdf->stream('Members Contributions Report.pdf');
	}
	//Shares Listing Report
	public function s_listing(){
		$members=Member::all();
		$organization=Organization::find(1);
		$pdf = PDF::loadView('sharereports.pdf.sharelisting', compact('organization','members'))
		->setPaper('a5')->setOrientation('potrait');
		return $pdf->stream('Members Shares Report.pdf');
	}
	//Individual Contribution Report
	public function show(){
		$members=Member::all();
		return View::make('sharereports.individualcontribution',compact('members'));
	}

	//View Individual Contribution
	public function individual(){
		//Obtain member ID selected
		$id=Input::get('memberid');
		$member=Member::where('id','=',$id)->get()->first();
		$contributions=Savingtransaction::where('savingaccount_id','=',$id)
					  ->where('type','=', 'credit')->sum('amount');
		/*$value=Share::count();
		if($value>0){
			$var=share::where('id','=',1)->pluck('value');
			if($var<=0){
				$sharevalue=0.00000009;
			}else{
				$sharevalue=$var;
			}
		$shares=$contributions/$sharevalue;
        */
		$transactions=Savingtransaction::where('savingaccount_id','=',$id)->get();
		$organization=Organization::find(1);
		$pdf = PDF::loadView('sharereports.pdf.individualcontribution', compact('organization','member','contributions','transactions'))->setPaper('a5')->setOrientation('potrait');
		return $pdf->stream('Individual Member Contribution Report.pdf');
		/*}else if($value<=0){
			return Redirect::back();
		}*/
	}
}