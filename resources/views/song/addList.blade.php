<center>
    <table border="1">
        <tr>
            <td>id</td>
            <td>uid</td>
            <td>openid</td>
            <td>name</td>
            <td>二维码</td>
            <td>操作</td>
        </tr>
        @foreach($data as $v)
        <tr>
            <td>{{$v->id}}</td>
            <td>{{$v->uid}}</td>
            <td>{{$v->openid}}</td>
            <td>{{$v->name}}</td>
            <td><img src="{{$v->img}}" alt="" ></td>
            <td><a href="{{url('song/mm')}}?uid={{$v->uid}}">生成专属二维码</a></td>
        </tr>
            @endforeach
    </table>
</center>