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
    <form action="{{url('hhoo/assTo')}}" method="get">
        <input type="hidden" name="atime" value="{{$data->aaa}}">
        <h1>比赛结果</h1>
        <center>
            <h1>
                比赛结果：{{$data->name}}VS{{$data->namm}}<br>
                您的竞猜：{{$data->name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                @if($data->sss==null)很遗憾你没参与@elseif($data->sss==1)胜@elseif($data->sss==2)平@elseif($data->sss==3)负@endif
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                {{$data->namm}}<br>
                @if($data->aaa==null)真遗憾！你没参与竞猜@elseif($data->aaa==1)运气真好！你猜对了@elseif($data->aaa==2)平了@elseif($data->aaa==3)猜错了@endif
            </h1>
        </center>
    </form>
</center>
</body>
</html>