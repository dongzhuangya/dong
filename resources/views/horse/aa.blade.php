<h1>登录页面</h1>
<form action="{{url('horse/aaTo')}}" method="post">
@csrf
	<p>账号<input type="text" name="name"></p>
	<p>密码<input type="password" name="pwd"></p>
	<input type="submit" value="登录">
</form>