<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<center>		
		<form action="{{url('goods/addTo')}}" method="post" enctype="multipart/form-data">
			<h1>商品添加</h1>
			@csrf
			@if ($errors->any())
	            @foreach ($errors->all() as $error)
	                {{ $error }}
	            @endforeach
			@endif
			<table>
				<tr>
					<td>商品名称</td>
					<td><input type="text" name="goods_name"></td>
				</tr>
				<tr>
					<td>商品库存</td>
					<td><input type="text" name="goods_kc"></td>
				</tr>
				<tr>
					<td>商品图片</td>
					<td><input type="file" name="pic"></td>
				</tr>
				<tr>
					<td>商品价格</td>
					<td><input type="text" name="goods_price"></td>
				</tr>
				<tr>
					<td><input type="submit" name="" value="提交"></td>
				</tr>
			</table>
		</form>
	</center>
</body>
</html>