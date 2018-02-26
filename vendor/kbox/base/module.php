<?php

$params = $_SERVER['argv'];

if(empty($params[1])){
    echo 'no module name';exit();
}
$namespace = strtolower($params[1]);
$ret = preg_match('/^[a-z]+$/',$namespace);
if(!$ret){
    echo 'module format error, only can user a-z';exit();
}

$root = str_replace('\\', '/', __DIR__);

$exampleDir = $root.'/vendor/kbox/base/example';
$toDir = $root.'/'.$namespace;
//不存在创建
if(!file_exists($root.'/'.$namespace)){
    //创建
    recurse_copy_with_namespace($exampleDir,$toDir,$namespace);
    //env file
    $newEnvFrom = $root.'/'.$namespace.'/environments';
    foreach (scandir($root.'/environments') as $newEnvPath){
        if(strpos($newEnvPath,'.') !== false){
            continue;
        }
        if(is_dir($root.'/environments/'.$newEnvPath)){
            copydir($newEnvFrom,$root.'/environments/'.$newEnvPath.'/'.$namespace);
        }
    }
    rmdirs($newEnvFrom);
    //env index
    $envFile = $root.'/environments/index.php';
    $envs = require "$envFile";
    foreach ($envs as $k=>$v){
        $envs[$k]['setWritable'][] = $namespace.'/runtime';
        $envs[$k]['setWritable'][] = $namespace.'/web/assets';
    }
    $envText = "<?php \r\n return ".var_export($envs,true).';';
    file_put_contents($envFile,$envText);
    //bootstrap
    $bootFile = $root.'/common/config/bootstrap.php';
    file_put_contents($bootFile,file_get_contents($bootFile)."\r\nYii::setAlias('@{$namespace}', dirname(dirname(__DIR__)) . '/{$namespace}');");
}


function recurse_copy_with_namespace($src,$des,$namespace) {
    $dir = opendir($src);
    @mkdir($des);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy_with_namespace($src . '/' . $file,$des . '/' . $file,$namespace);
            }  else  {
                file_put_contents(
                    $des.'/'.$file,
                    str_replace('kbox\base\example',$namespace,file_get_contents($src .'/'.$file))
                );
            }
        }
    }
    closedir($dir);
}


function copydir($source, $dest)
{
    if (!file_exists($dest)) {
        mkdir($dest);
    }
    $handle = opendir($source);
    while (($item = readdir($handle)) !== false) {
        if ($item == '.' || $item == '..') continue;
        $_source = $source . '/' . $item;
        $_dest = $dest . '/' . $item;
        if (is_file($_source)) copy($_source, $_dest);
        if (is_dir($_source)) copydir($_source, $_dest);
    }
    closedir($handle);
}

function rmdirs($path)
{
    $handle = opendir($path);
    while (($item = readdir($handle)) !== false) {
        if ($item == '.' || $item == '..') continue;
        $_path = $path . '/' . $item;
        if (is_file($_path)) unlink($_path);
        if (is_dir($_path)) rmdirs($_path);
    }
    closedir($handle);
    return rmdir($path);
}