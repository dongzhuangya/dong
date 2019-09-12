<table border="1">
<center>
    <tr>
        <td>id</td>
        <td>名称</td>
        <td>城市</td>
        <td>头像</td>
    </tr>
    @foreach($info as $v)
        <tr>
            <td>{{$v->id}}</td>
            <td>{{$v->name}}</td>
            <td>{{$v->city}}</td>
            <td><img src="{{$v->img}}" alt=""></td>
            <td><a href="">查看详情</a></td>
        </tr>
    @endforeach
</center>
</table>