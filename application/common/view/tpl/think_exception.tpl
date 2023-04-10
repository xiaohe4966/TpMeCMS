<?php
header("Content-type:application/json");
$dataa['msg'] = "请求访问：".$_SERVER['REQUEST_URI']."，认证失败，无法访问系统资源";
$dataa['code'] = 401;
echo str_replace("\\/", "/", json_encode($dataa,JSON_UNESCAPED_UNICODE));
exit();
?>