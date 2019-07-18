<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<center>		
		<form action="{{url('goods/updateTo')}}" method="post" enctype="multipart/form-data">
			<h1>商品修改</h1>
			<input type="hidden" name="goods_id" value="{{$data->goods_id}}">
			@csrf
			<table>
				<tr>
					<td>商品名称</td>
					<td><input type="text" name="goods_name" value="{{$data->goods_name}}"></td>
				</tr>
				<tr>
					<td>商品库存</td>
					<td><input type="text" name="goods_kc" value="{{$data->goods_kc}}"></td>
				</tr>
				<tr>
					<td>商品图片</td>
					<td><img src="{{$data->pic}}" alt=""width="100" height="100"><input type="file" name="pic"></td>
				</tr>
				<tr>
					<td>商品价格</td>
					<td><input type="text" name="goods_price" value="{{$data->goods_price}}"></td>
				</tr>
				<tr>
					<td><input type="submit" name="" value="修改"></td>
				</tr>
			</table>
		</form>
	</center>
</body>
</html>