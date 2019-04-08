<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

use App\Payment;
use App\User;


class InterkasaController extends Controller
{
	protected $arrAmount = array(1=>50, 2=>100, 3=>150, 4=>180);
	protected $arrButtonText = array(1=>"Оплатить 1 час", 
									 2=>"Оплатить 2 часа", 
									 3=>"Оплатить 3 часа", 
									 4=>"Оплатить 4 часа"
									);
	protected $arrDesc = array(1=>"Оплата за 1 час", 
									 2=>"Оплата за 2 часа", 
									 3=>"Оплата за 3 часа", 
									 4=>"Оплата за 4 часа"
									);
	public function sendRequest(Request $request){
		
		$time_access = \Auth::user()->time_access;
		$left_hours = 0;
		$left_minutes = 0;
		if($time_access > time()){
			
			$left_hours = floor(($time_access-time())/3600);
			if($left_hours) $left_minutes = floor((($time_access - (time()+($left_hours*3600)))/3600)*60);
			else $left_minutes = floor(($time_access-time())/60); 
		}
		$desc = $this->arrDesc[$request->selectHours];
		$buttonText = $this->arrButtonText[$request->selectHours];
		$buttonText = $this->arrButtonText[$request->selectHours];
		$amount = $this->arrAmount[$request->selectHours];
		if(!$amount) return redirect('home');
		$id =  \Auth::user()->id;
		$payment = new Payment;
		$payment->user_id = $id;
		$payment->hours = $request->selectHours;
		$payment->amount = $amount;
		$payment->save();
		return view('home')->with(['pay'=>TRUE, 'idKassa'=>env('ID_INTERKASSA'),'payHours' => $request->selectHours, 'idOrder'=>$payment->id, 'amount'=>$amount, 'buttonText'=>$buttonText, 'desc'=>$desc, 'left_hours' => $left_hours, 'left_minutes' =>$left_minutes]);
		
		//return Redirect::away('https://sci.interkassa.com/');
	}
	public function success(Request $request){
		
		$id_order = str_replace("ID_", "", $request->ik_pm_no);
		$order = Payment::find($id_order);
		return view('success')->with(['hours' => $order->hours]);
		
	}
	public function interaction(Request $request){
		$id = str_replace("ID_", "", $request->ik_pm_no);
		
		$order = Payment::find($id);
		$user_id = $order->user_id;
		
		if(!$order){ 
			$order->text = 'Такого заказа не существует';
			$order->save();
			return 'Такого заказа не существует';
			
		}
		if($order->amount != $request->ik_am){
			$order->text = 'Суммы не совпадают'.$id0.'='.$request->ik_am;
			$order->save();
			return 'Суммы не совпадают';
			
		}
		if($request->ik_inv_st != 'success'){
			$order->text = 'Оплата не поведена';
			$order->save();
			return 'Оплата не проведена';
			
		}
		
		$sign = $this->getSign($_POST);
		if($sign != $request->ik_sign){
			$order->text = 'Цифровые подписи не совпадают'.$sign.'-'.$request->ik_sign;
			$order->save();
			return 'Цифовая подпись не совпадает';
			
		}
		$order->status = 1;
		
		$order->save();
		$user = User::find($user_id);
		
		if($user->time_access > time())
			$user->time_access = $user->time_access + $order->hours*60*60;
		else 
			$user->time_access = time()+$order->hours*60*60;
		
		$user->save();
		return 'OK';
	}
	
	public function getSign($dataSet){
		unset($dataSet['ik_sign']); 
		ksort($dataSet, SORT_STRING); 
		array_push($dataSet, env('KEY_TEST_INTERKASSA')); 
		$signString = implode(':', $dataSet); 
		$sign = base64_encode(md5($signString, true)); 
		return $sign;
	}
	
	
}
