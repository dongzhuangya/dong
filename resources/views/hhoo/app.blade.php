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
        <form action="{{url('hhoo/appTo')}}" method="get">
            <input type="hidden" name="id" value="{{$data->id}}">
            <h1>我要竞猜</h1>
            <input type="text" name="name"value="{{$data->name}}"> VS <input type="text"name="namm" value="{{$data->namm}}"><br>
            <input type="radio" name="1" value="胜">胜<input type="radio" name="sex" value="2">平<input type="radio" name="sex" value="3">负<br>
            <input type="submit" value="提交">
        </form>
    </center>
</body>
</html>
