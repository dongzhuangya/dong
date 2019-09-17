<center>
    <table border="1">
        <tr>

            <td>id</td>
            <td>openid</td>
            <td>名称</td>
            <td>操作</td>
        </tr>
        @foreach($info as $v)
        <tr>

            <td>{{$v->id}}</td>
            <td>{{$v->openid}}</td>
            <td>{{$v->name}}</td>
            <td><a href="{{url('song/yan')}}?id={{$v->id}}">留言</a></td>
        </tr>
        @endforeach
    </table>
</center>