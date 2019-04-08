@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
				
				
				@if(@$left_hours || @$left_minutes)
					<b>Осталось {{@$left_hours}}ч. {{@$left_minutes}}мин.</b><br>
				@endif
				
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
					@if(@$pay == TRUE)
						 
						<form id="payment" name="payment" method="post" action="https://sci.interkassa.com/" enctype="utf-8">
							<input type="hidden" name="ik_co_id" value="{{@$idKassa}}" />
							<input type="hidden" name="ik_pm_no" value="ID_{{@$idOrder}}" />
							<input type="hidden" name="ik_am" value="{{@$amount}}" />
							<input type="hidden" name="ik_cur" value="RUB" />
							<input type="hidden" name="ik_time" value="{{@payHours}}" /> 
							<input type="hidden" name="ik_desc" value="{{@$desc}}" />
							<button id="but" class="btn-disable submit">{{@$buttonText}}</button>

						</form><br>
					@endif
					
					Выберите подписку 
					<form id="payment" name="payment" method="POST" action="{{URL('send_interkassa')}}" enctype="utf-8">
					<select name="selectHours">
						<option>Выбрать</option>
						<option value="1">На 1 час</option>
						<option value="2">На 2 час</option>
						<option value="3">На 3 час</option>
						<option value="4">На 4 час</option>
						<option value="5">На 5 час</option>
					</select>
					
						{{csrf_field()}}
					<button id="but" class="btn-disable submit">Перейти к оплате</button>

					</form>
					
					@if(Auth::user()->time_access > time())
						Просмотр разрешен
					@endif
					
				
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
