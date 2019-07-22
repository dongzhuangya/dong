<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<center>
		<form action="{{url('horse/index')}}" method="get">
			<input type="text" name="sss" value="{{$sss}}">
			<input type="submit" value="搜索">
		</form>
		<table border="1">
			<tr>
				<td>车次</td>
				<td>出发地->到达地</td>
				
				<td>价钱</td>
				<td>张数</td>
				<td>备注</td>
			</tr>
		@foreach($data as $v)
			<tr>
				<td>{{$v->train}}</td>
				<td>{{$v->place}}->{{$v->arrival}}</td>
				
				<td>￥{{$v->price}}</td>
				<td>
					@if($v->amount == 0)
					    无
					@elseif ($v->amount > 100)
					    	有
					@else
					   {{$v->amount}}
					@endif

				</td>
				<td>
					@if($v->amount==0)
						<a>购票</a>
					@else($v->amount>1)
						<a href="#">购票</a>
					@endif
				</td>
			</tr>
		@endforeach
		</table>
	</center>
</body>
</html>
