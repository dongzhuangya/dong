<center>
        <form action="{{url('aaa/addo')}}" method="post" enctype="multipart/form-data">
            @csrf
            新闻标题：<input type="text" name="title"><br>
            新闻作者：<input type="text" name="author"><br>
            新闻详情：<input type="text" name="details"><br>
            新闻图片：<input type="file" name="pic"><br>
            <input type="submit" value="添加">
        </form>
</center>
