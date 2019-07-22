<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<center>
		<form action="{{url('horse/addTo')}}" method="post">
		@csrf
		<table>
			<h1>添加车次</h1>
			<tr>
				<td>车次</td>
				<td><input type="text" name="train"></td>
			</tr>
			<tr>
				<td>出发地</td>
				<td><input type="text"name="place"></td>
			</tr>
			<tr>
				<td>到达地</td>
				<td><input type="text" name="arrival"></td>
			</tr>
			<tr>
				<td>价钱</td>
				<td><input type="text" name="price"></td>
			</tr>
			<tr>
				<td>张数</td>
				<td><input type="text" name="amount"></td>
			</tr>
			<tr>
				<td>出发时间</td>
				<td><input type="text" name="goofftime"></td>
			</tr>
			<tr>
				<td>到达时间</td>
				<td><input type="text" name="arrivaltime"></td>
			</tr>
			<tr>
				<td><input type="submit" value="提交"></td>
				
			</tr>
		</table>
	</form>
	</center>	
</body>
</html>