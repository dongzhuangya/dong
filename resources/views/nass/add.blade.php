<form action="{{url('nass/add_to')}}" method="post">
    @csrf
    第一节课 <input type="text" name="name"><br>
    第二节课 <input type="text" name="namea"><br>
    第三节课 <input type="text" name="nameb"><br>
    第四节课 <input type="text" name="namec"><br>
    <input type="submit" value="提交">
</form>