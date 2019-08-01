<form action="{{url('horse/qq')}}" method="post">
@csrf
@if($xxx==1)
	<h4>laravel框架定义中间件关键字是</h4>
	<p><input type="radio" name="a" value="middleware"><input type="text" value="middleware"></p>
	<p><input type="radio" name="a" value="controller"><input type="text" value="controller"></p>
	<p><input type="radio" name="a" value="model"><input type="text" value="model"></p>
	<p><input type="radio" name="a" value="migration"><input type="text" value="migration"></p>
@elseif($xxx==2)
	<h4>面向对象特征是</h4>
	<p><input type="checkbox" name="b[]" value="封装"><input type="text" value="封装"></p>
	<p><input type="checkbox" name="b[]" value="继承"><input type="text" value="继承"></p>
	<p><input type="checkbox" name="b[]" value="多态"><input type="text" value="多态"></p>
	<p><input type="checkbox" name="b[]" value="抽象"><input type="text" value="抽象"></p>
@elseif($xxx==3)
	<h4>laravel只能使用composer安装</h4>
	<p><input type="radio" name="c" value="正确">正确</p>
	<p><input type="radio" name="c" value="错误">错误</p>
@endif
<input type="submit" value="添加">
</form>