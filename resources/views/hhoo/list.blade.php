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

            <h1>竞猜列表</h1>
    @foreach($data as $v)
        <h3>{{$v->name}}VS{{$v->namm}}@if($v->atime <time())
                <a href="{{url('hhoo/ass')}}?id={{$v->id}}">查看结果</a>
            @else
                <a href="{{url('hhoo/app')}}?id={{$v->id}}"> 竞猜</a>
            @endif</h3>
    @endforeach
    </center>
</body>
</html>