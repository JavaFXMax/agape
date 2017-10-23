<?php

class Loanrepayment extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = [];
	public function loanaccount(){

		return $this->belongsTo('Loanaccount');
	}

	public static function getPrincipalPaid($loanaccount){
			$paid = DB::table('loanrepayments')->where('loanaccount_id', '=', $loanaccount->id)->sum('principal_paid');
			return $paid;
	}

	public static function getInterestPaid($loanaccount){
			$paid = DB::table('loanrepayments')->where('loanaccount_id', '=', $loanaccount->id)->sum('interest_paid');		
			return $paid;
	}

	public static function repayLoan($data){

		$loanaccount_id = array_get($data, 'loanaccount_id');

		$loanaccount = Loanaccount::findorfail($loanaccount_id);

		$amount = array_get($data, 'amount');
		$date = array_get($data, 'date');
        //Create a transaction record
        $transaction = new Loantransaction;
		$transaction->loanaccount()->associate($loanaccount);
		$transaction->date = $date;
		$transaction->description = 'loan repayment';
		$transaction->amount = $amount;
		$transaction->type = 'credit';
		$transaction->save();
		Audit::logAudit($date, Confide::user()->username, 'loan repayment', 'Loans', $amount);

		$principal_due = Loantransaction::getPrincipalDue($loanaccount);
		$interest_due = Loantransaction::getInterestDue($loanaccount);
        $insurance_due= Loantransaction::getInsuranceDue($loanaccount);

		$total_due = $principal_due + $interest_due + $insurance_due;

		$payamount = $amount;

 	    if($payamount < $total_due){
			//pay interest first
			Loanrepayment::payInterest($loanaccount, $date, $interest_due,$transaction);
            Loanrepayment::payInsurance($loanaccount, $date, $insurance_due,$transaction);
			$payamount = $payamount - ($interest_due + $insurance_due);
			if($payamount > 0){
				Loanrepayment::payPrincipal($loanaccount, $date, $payamount,$transaction);
			}
		}


		if($payamount >= $total_due){
			//pay interest first 
			Loanrepayment::payInterest($loanaccount, $date, $interest_due,$transaction);
			Loanrepayment::payInsurance($loanaccount, $date, $insurance_due,$transaction);
			$payamount = $payamount - ($interest_due + $insurance_due);
			//pay principal with the remaining amount
			Loanrepayment::payPrincipal($loanaccount, $date, $payamount,$transaction);
		}
	}

	public static function offsetLoan($data){
        $loanaccount_id = array_get($data, 'loanaccount_id');
		$loanaccount = Loanaccount::findorfail($loanaccount_id);
		$amount = array_get($data, 'amount');
		$date = array_get($data, 'date');
        //Create a transaction record
        $transaction = new Loantransaction;
		$transaction->loanaccount()->associate($loanaccount);
		$transaction->date = $date;
		$transaction->description = 'loan repayment';
		$transaction->amount = $amount;
		$transaction->type = 'credit';
		$transaction->save();
		$amount = array_get($data, 'amount');
		$date = array_get($data, 'date');
		$principal_bal = Loanaccount::getPrincipalBal($loanaccount);
		$interest_bal = Loanaccount::getInterestBal($loanaccount);
		//pay principal
 		Loanrepayment::payPrincipal($loanaccount, $date, $principal_bal,$transaction);
 		//pay interest
 		Loanrepayment::payInterest($loanaccount, $date, $interest_bal,$transaction);
		///Loantransaction::repayLoan($loanaccount, $amount, $date);
	}

	public static function payPrincipal($loanaccount, $date, $principal_due,$transaction){
		$repayment = new Loanrepayment;
		$repayment->loanaccount()->associate($loanaccount);
		$repayment->date = $date;
		$repayment->principal_paid = $principal_due;	
        $repayment->transaction_id=$transaction->id;
		$repayment->save();
		$account = Loanposting::getPostingAccount($loanaccount->loanproduct, 'principal_repayment');
		$data = array(
			'credit_account' =>$account['credit'] , 
			'debit_account' =>$account['debit'] ,
			'date' => $date,
			'amount' => $principal_due,
			'initiated_by' => 'system',
			'description' => 'principal repayment'
			);
		$journal = new Journal;
		$journal->journal_entry($data);
	}

	public static function payInterest($loanaccount, $date, $interest_due,$transaction){
		$repayment = new Loanrepayment;
		$repayment->loanaccount()->associate($loanaccount);
		$repayment->date = $date;
		$repayment->interest_paid = $interest_due;
        $repayment->transaction_id=$transaction->id;
		$repayment->save();
		$account = Loanposting::getPostingAccount($loanaccount->loanproduct, 'interest_repayment');
		$data = array(
			'credit_account' =>$account['credit'] , 
			'debit_account' =>$account['debit'] ,
			'date' => $date,
			'amount' => $interest_due,
			'initiated_by' => 'system',
			'description' => 'interest repayment'
			);
		$journal = new Journal;
		$journal->journal_entry($data);
	}
    
    public static function payInsurance($loanaccount, $date, $insurance_due,$transaction){
		$repayment = new Loanrepayment;
		$repayment->loanaccount()->associate($loanaccount);
		$repayment->date = $date;
		$repayment->insurance_paid = $insurance_due;
        $repayment->transaction_id=$transaction->id;
		$repayment->save();
		$account = Loanposting::getPostingAccount($loanaccount->loanproduct, 'principal_repayment');
		$data = array(
			'credit_account' =>$account['credit'] , 
			'debit_account' =>$account['debit'] ,
			'date' => $date,
			'amount' => $insurance_due,
			'initiated_by' => 'system',
			'description' => 'insurance payment'
			);
		$journal = new Journal;
		$journal->journal_entry($data);
	}
}