<?php
namespace Home\Logic;
class SendMailLogic  extends \thread{
    
    public function __construct($address,$subject='test pthread',$content='944') {
        $this->address = $address;
        $this->subject = $subject;
        $this->content = $content;
    }
    public function run(){
        return sendEmail($this->address, $this->subject, $this->content);
    }
}
