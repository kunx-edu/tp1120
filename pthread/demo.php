<?php

/* 
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */
class Demo extends Thread{
    private $num = 0;
    public function __construct($num) {
        $this->num = $num+200;
    }
    
    public function run(){
        echo $this->num;
        sleep(1);
    }
}
$start = microtime(true);
$threads = [];
for($i=0;$i<10;++$i){
    $threads[] = new Demo($i);
}

foreach($threads as $key=>$value){
    $value->start();
}
$end = microtime(true);
echo '<hr />';
echo ($end-$start) . 's';



