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
        <form action="{{url('aaa/list')}}" method="post">
            @csrf
            账号：<input type="text" name="name"><br>
            密码：<input type="password" name="pwd"><br>
            <input type="submit" value="登录">
        </form>
    </center>
</body>
</html>