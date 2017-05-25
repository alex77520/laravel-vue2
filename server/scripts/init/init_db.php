<?php
/**
 * 根据config/Db.config.php来生成初始化数据库user的命名
 * @author QingliangCn
 * @since 2012.10.30
 */

//自动生成新密码
$x = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_!#%_!#%";
$strLen = strlen($x);
$Passwd = "";
for($i=0; $i<16; $i++){
    $Passwd .= substr($x, (mt_rand()%($strLen)), 1);
}
if (empty($Passwd)) {
    die("DB_ERROR:密码生成错误");
}

echo "初始化数据库和用户：\n";
$DBuser = $DBName = "mc_m17_admin";
$Host = 'localhost';
$file = "../../.env";
$dbConfigStr = file_get_contents($file);
$content = str_replace( '__DBPASSWD__', $Passwd, $dbConfigStr);
$content = str_replace( '__DBNAME__', $DBName, $content);
$content = str_replace( '__DBUSER__', $DBuser, $content);

$fp = fopen($file, 'w');
fwrite($fp, $content);
fclose($fp);

// 此处的单双引号不要随便乱改，1、不然密码中带$符号后，会悲剧的；2、有时由于是双引号会mysql会报错
$sql = 'CREATE DATABASE IF NOT EXISTS '.$DBName.';'
.'GRANT ALL ON '.$DBName.'.* TO "'.$DBuser.'"@"'.$Host.'" IDENTIFIED BY "'.$Passwd.'";'
.'FLUSH PRIVILEGES;';

$cmdList = array(
    'create_database' => "/usr/local/bin/mysql -uroot -p`cat /data/save/mysql_root` -e'{$sql}'"
);

foreach ($cmdList as $cmd) {
    shell_exec($cmd);
}

echo "新建数据库结束;\n";