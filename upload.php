<?php
header("Content-type:text/html;charset=utf-8");

//获取本机ip和定义alfresco服务端口端口
$server_ip = $_SERVER["REMOTE_ADDR"];//本机ip；

$server_port = "8080";      //端口号


//获取上传文档的必要参数
if(isset($_FILES['upfile'])){ 

  $upfile=$_FILES["upfile"];      //获取文件上传的句柄

  $filename=$upfile["name"];      //上传文件的文件名 

  $type=$upfile["type"];         //上传文件的类型 
 
  $size=$upfile["size"];        //上传文件的大小 
 
  $tmp_name=$upfile["tmp_name"];//上传文件的临时存放路径 

  echo "Upload: ". $filename . "<br />";
  echo "Type: "  . $type . "<br />";
  echo "Size: "  . ($size / 1024) . " Kb<br />";
  echo "temp:"   . $tmp_name."<br />";
}
//http://localhost:8080/alfresco/service/api/login?u=admin&pw=123456   登录这个地址获取$ticket，不同的账号不同的$ticket，这里用的是admin账号；
//这个$ticket值每天都是变化的，所以就需要写一个申请去获取，如果为了节省资源可以每天只申请一次，这里为了方便，每次都申请了;
//$contents获取到的内容是xml格式的，需要解析成xml对象，然后就能获取到标签<ticket></ticket>里面的值；
$contents = file_get_contents('http://localhost:8080/alfresco/service/api/login?u=admin&pw=123456');

$xml = simplexml_load_string($contents);//解析成xml对象

$ticket = $xml[0];//获取标签<ticket></ticket>里面的值，并赋值给$ticket;

$urlws = $server_ip.":".$server_port."/alfresco/service/api/upload?alf_ticket=".$ticket; // 登录此url可以使用upload脚本上传文件；

//构建数组
$postvalue = array(
'filename' => $filename,//只要获取到名称即可

'filedata' => '@'. realpath($tmp_name),//获取文件缓存路径。

'destination' => 'workspace://SpacesStore/c3dff0d2-779b-4660-87f4-9d1e92cd5385', // 下面的 destination是我们alfresco里面共享文件夹的workplaceId;

);


$ch = curl_init();                                //创建一个cURL资源
curl_setopt($ch, CURLOPT_POST, true);             //第三个参数为true时，php为常规请求 
//curl_setopt($ch, CURLOPT_PORT, $server_port);   //请求端口
curl_setopt($ch, CURLOPT_URL, $urlws);            //请求的url
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   //第三个参数为true时，将返回值储存在起来，不输出在页面
curl_setopt($ch, CURLOPT_POSTFIELDS, $postvalue); //发送一个数组参数
$result  = curl_exec($ch);                        //获取返回结果包括文当的id，为了以后文件分享做准备
curl_close($ch);                                  //释放资源           

echo "result:" .$result;
 ?>