@extends('layut.comm')
@section('login')
	<!-- login -->
	<div class="pages section">
		<div class="container">
			<div class="pages-head">
				<h3>LOGIN</h3>
			</div>
			<div class="login">
				<div class="row">

					<form class="col s12" method="post" action="{{url('login/dong')}}">
					@csrf
						<div class="input-field">
							<input type="text" class="validate"name="name" placeholder="USERNAME" required>
						</div>
						<div class="input-field">
							<input type="password" class="validate" name="pwd" placeholder="PASSWORD" required>
						</div>
						<a href=""><h6>Forgot Password ?</h6></a>
						<input type="submit" value="提交">
						<!-- <a href="java" class="btn button-default">LOGIN</a> -->
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- end login -->
	
	<!-- loader -->
	<div id="fakeLoader"></div>
	<!-- end loader -->
	
	<!-- footer -->
	
@endsection
