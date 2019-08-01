<center>
    <h1>
       竞猜结果<br>
    对阵结果：{{$data->name}}@if($data->sss==1)胜@elseif($data->sss==2)平@elseif($data->sss==3)负@endif{{$data->namm}}<br>
    您的竞猜：{{$data->name}}@if($data->aaa==1)胜@elseif($data->aaa==2)平@elseif($data->aaa==3)负@endif{{$data->namm}}<br>
    @if($data->aaa==$data->sss)恭喜你！玩对了@elseif($data->aaa!=$data->sss)恭喜你没猜对哈哈@endif
    </h1>
</center>