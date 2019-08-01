<form action="{{url('hhoo/addTo')}}" method="post">
    @csrf
    <center>
    <h1>添加竞猜球队</h1>
    <input type="text" name="name">vs <input type="text" name="namm"><br>
    结束竞猜时间 <input type="text" name="atime"><br>
    <input type="submit" value="添加">
    </center>
</form>