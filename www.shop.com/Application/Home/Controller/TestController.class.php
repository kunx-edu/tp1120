<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of TestController
 *
 * @author qingf
 */
class TestController extends \Think\Controller {

    public function sendSMS() {
        $flag = sendSMS('17002810533', ['code' => (string) 1033, 'product' => '仙人跳']);
    }

    public function sendEmail() {
        $flag = sendEmail('kunx@kunx.org', '测试邮件', '今天早上没吃药,感觉自己萌萌哒');
        dump($flag);
    }

    /**
     * 使用多线程批量发送邮件
     */
    public function testPthread() {
        $start   = microtime(true);
        $threads = [];
        $addresses = [
            'kunx@kunx.org',
            'kunx_edu@126.com',
            'kunx-edu@qq.com',
            'fengshajin@vip.qq.com',
            'walker@kunx.org',
            'travel@kunx.org',
            'hitao@kunx.org',
            'ujrctvaq@sharklasers.com',
            'diqmncbi@sharklasers.com',
        ];
        foreach($addresses as $address){
            $threads[] = new \Home\Logic\SendMailLogic($address);
            
        }
        foreach ($threads as $item) {
            $item->start();
        }
        $end = microtime(true);
        echo '<hr />' . ($end - $start) . 's';
    }
    
    /**
     * 一对一添加
     */
    public function addStudent() {
        $data = [
            'name'=>'张三',
            'age'=>17,
            'cid'=>1,
            'detail'=>[
                'intro'=>'我的名字叫张三,我喂自己袋盐',
            ],
        ];
        $model =D('Student');
        $model->relation(true)->add($data);
    }
    
    /**
     * 一对一删除
     * @param type $id
     */
    public function delStudent($id) {
        $model =D('Student');
        $model->relation(true)->delete($id);
    }
    
    /**
     * 一对一修改
     */
    public function updateStudent() {
        $data = [
            'id'=>45,
            'name'=>'张'.  mt_rand(1, 9),
            'age'=>mt_rand(17,99),
            'cid'=>1,
            'detail'=>[
                'intro'=>'我的名字叫张三,我喂自己袋盐!阿姐鲁!',
            ],
        ];
        $model =D('Student');
        $model->relation(true)->save($data);
    }
    
    /**
     * 一对一获取
     * @param type $id
     */
    public function getStudent($id){
        $model =D('Student');
        $info = $model->relation(['detail'])->find($id);
        dump($info);
    }
    
    /**
     * 一对多添加
     */
    public function addWenzhang(){
        $data = [
            'name'=>'张三',
            'age'=>17,
            'cid'=>1,
            'wenzhang'=>[
                ['title'=>'test1','content'=>'testtest1'],
                ['title'=>'test2','content'=>'testtest2'],
                ['title'=>'test3','content'=>'testtest3'],
                ['title'=>'test4','content'=>'testtest4'],
            ],
        ];
        $model =D('Student');
        $model->relation('wenzhang')->add($data);
    }
    
    /**
     * 一对多修改
     */
    public function updateWenzhang(){
        $data = [
            'id'=>61,
            'name'=>'张三',
            'age'=>17,
            'cid'=>1,
            'wenzhang'=>[
                ['id'=>1,'title'=>'test1','content'=>'testtest11'],
                ['id'=>2,'title'=>'test2','content'=>'testtest22'],
                ['id'=>10,'title'=>'test3','content'=>'testtest33'],//指定了id都是修改
                ['title'=>'test4','content'=>'testtest44'],//没有指定id就是添加
            ],
        ];
        $model =D('Student');
        $model->relation('wenzhang')->save($data);
    }
    
    /**
     * 一对多删除
     * @param type $id
     */
    public function deleteWenzhang($id){
        $model =D('Student');
        $model->relation('wenzhang')->delete($id);
    }

}
