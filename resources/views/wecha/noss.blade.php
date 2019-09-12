<form action="{{url('wecha/do_noss')}}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file_name" value="">
    <input type="submit" value="提交">

</form>