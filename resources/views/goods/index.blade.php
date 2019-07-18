<center>
<form action="{{url('goods/index')}}" method="get">
	<input type="text" name="ss" value="{{$ss}}">
	<input type="submit" value="搜索">
</form>
<table border="1">
	<tr>
		<td>商品id</td>
		<td>商品名称</td>
		<td>商品价格</td>
		<td>商品图片</td>
		<td>商品库存</td>
		<td>添加时间</td>
	</tr>
	@foreach($data as $v)
	<tr>
		<td>{{$v->id}}</td>
		<td>{{$v->goods_name}}</td>
		<td>{{$v->goods_price}}</td>
		<td><img src="{{$v->goods_pic}}" width="50" alt=""></td>
		<td>{{$v->goods_kc}}</td>
		<td><?php echo date('Y-m-d',$v->add_time) ?></td>
		<td><a href="{{url('goods/update')}}?id={{$v->id}}">修改</a>|<a href="{{url('goods/delete')}}?id={{$v->id}}">删除</a></td>
	
	</tr>
	@endforeach
	{{$data->appends(['ss'=>$ss])->links()}}
</table>
</center>
