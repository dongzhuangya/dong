<center>
    <form action="{{url('song/do_add')}}" method="post">
        @csrf
        <input type="text" name="name">
        <input type="submit" value="提交">
    </form>
</center>