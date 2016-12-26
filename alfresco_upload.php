<?php
header('Content-type:text/html;charset=utf-8');

//服务器ip alfresco服务端口端口 alfresco账户和密码  文件上传到alfresco的位置 
$server_ip   =  '192.168.1.107';
$server_port =  '8080';     
$admin       =  'admin';
$pw          =  '123456';
$workspace   =  'workspace://SpacesStore/be42ae59-2b52-4f50-8480-40e8a8cd0b0f'; 


 //执行函数,上传文件;最后可以输出查看文件的链接
 //http://127.0.0.1:8080/share/page/context/shared/document-details?nodeRef
 $nodeRef    =  upload_file($server_ip,$server_port,$admin,$pw,$workspace,$argv);
 //echo upload_file($server_ip,$server_port,$admin,$pw,$workspace,$argv); 


function get_tickt($server_ip,$server_port,$admin,$pw){

   $contents = file_get_contents('http://'.$server_ip.':'.$server_port.'/alfresco/service/api/login?u='.$admin.'&pw='.$pw);

        $xml = simplexml_load_string($contents);


      return $xml[0];

}
 
 function upload_file($server_ip,$server_port,$admin,$pw,$workspace,array $argv_array = array()){
        

  $urlws    = $server_ip.':'.$server_port.'/alfresco/service/api/upload?alf_ticket='.get_tickt($server_ip,$server_port,$admin,$pw);
                            
  $filename = iconv('GB2312', 'UTF-8',basename($argv_array[1])); //将字符串的编码从GB2312转到UTF-8 
               
  $path     = str_replace('\\','/',$argv_array[1]);

 $postvalue = array('filename' => $filename, 'filedata' => '@'. realpath($path), 'destination' => $workspace);

              $ch = curl_init();                                //创建一个cURL资源
              curl_setopt($ch, CURLOPT_POST, true);             //第三个参数为true时，php为常规请求 
            //curl_setopt($ch, CURLOPT_PORT, $server_port);     //请求端口
              curl_setopt($ch, CURLOPT_URL, $urlws);            //请求的url
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   //第三个参数为true时，将返回值储存在起来，不输出在页面
              curl_setopt($ch, CURLOPT_POSTFIELDS, $postvalue); //发送一个数组参数
              $result  = curl_exec($ch);                        //获取返回结果包括文当的id，为了以后文件分享做准备
              curl_close($ch);                                  //释放资源    
              $arr = json_decode($result,true);                 //
                                                  
$sharedhref = 'http://'.$server_ip.':'.$server_port.'/share/page/context/shared/document-details?'.$arr['nodeRef'];
              echo $sharedhref;
              return  $sharedhref;                                     //文件分享的链接

 }

 ?>