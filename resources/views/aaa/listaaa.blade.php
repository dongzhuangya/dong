<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <center>
        <a href="{{url('aaa/add')}}">添加</a>
        <table border="1">
            <tr>
                <td>id</td>
                <td>新闻标题</td>
                <td>新闻作者</td>
                <td>新闻详情</td>
                <td>新闻图片</td>
                <td>添加时间</td>
                <td>操作</td>
            </tr>
            @foreach($data as $v)
            <tr>
                <td>{{$v->id}}</td>
                <td>{{$v->title}}</td>
                <td>{{$v->author}}</td>
                <td>{{$v->details}}</td>
                <td><img src="{{$v->pic}}" alt=""></td>
                <td><?php echo date('Y-m-d',$v->time) ?></td>
                <td><a href="{{url('aaa/delect')}}?id={{$v->id}}">删除</a>|<a href="{{url('aaa/adc')}}?id={{$v->id}}">前往详情页</a></td>
            </tr>
            @endforeach
            {{$data->links()}}
        </table>
    </center>
</body>
</html>