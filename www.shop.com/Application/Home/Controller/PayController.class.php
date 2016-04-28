<?php

/**
 * 文件说明.
 * kunx-edu <kunx-edu@qq.com>
 */

namespace Home\Controller;

/**
 * Description of PayController
 *
 * @author qingf
 */
class PayController extends \Think\Controller {

    /**
     * 显示简单的支付方式列表
     * 根据订单的支付方式,决定最终页面上显示的是什么支付产品的链接地址
     * 1.根据用户订单支付方式调用不同的私有方法,在方法中调用对应的支付接口
     * 2.用户跳转到第三方支付的支付页面
     *  2.1一旦用户登录,就会创建一个支付宝的订单号,同时支付宝会在后台主动调用商家的回调通知接口notify
     *  2.2收到通知后进行验证请求是否是支付宝发起的,然后将支付宝的交易号存入商家的订单表中
     * 3.支付完成,第三方支付会自动跳转到商家指定的页面
     *  3.1支付宝会通知商家的notify,告知订单已经完成支付
     *  3.2商家应当更改订单状态为待发货
     * 4.后台订单管理模块
     *  4.1找到待发货的订单,填写物流信息
     *  4.2发起请求到支付宝的发货接口
     * @param integer $id 订单号码.
     */
    public function pay($id) {
        $userinfo   = session('MEMBER_INFO');
        //获取支付类型,如果是1,就使用支付宝支付
        $cond       = [
            'status'    => 1,
            'member_id' => $userinfo['id'],
        ];
        $order_info = M('OrderInfo')->where($cond)->find($id);
        if (empty($order_info)) {
            $this->error('找不到符合要求的订单');
        }
        $flag = true;
        switch ($order_info['pay_type']) {
            //在线支付,使用支付宝
            case 1:
                $flag = $this->_simulatedAlipay($order_info);
                break;
            //银联支付
            case 2:
                break;
        }
        if($flag){
            $this->success('支付成功',U('OrderInfo/index'));
        }
    }

    private function _simulatedAlipay(array $order_info){
        //更改订单状态
        $data  =  [
            'id'=>$order_info['id'],
            'trade_no'=>'2016052800010070008',
            'status'=>2,
        ];
        return M('OrderInfo')->setField($data);
    }
    /**
     * 支付宝支付
     * @param array $order_info
     */
    private function _alipay(array $order_info) {
        header('Content-Type: text/html;charset=utf-8');
        vendor('Alipay.lib.alipay_submit#class');
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config = C('ALIPAY_SETTING');

        /*         * ************************请求参数************************* */

        //支付类型
        $payment_type      = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url        = U('notify','','',true);//"http://商户网关地址/create_partner_trade_by_buyer-PHP-UTF-8/notify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        $return_url        = U('OrderInfo/show', ['id' => $order_info['id']],'',true);
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //商户订单号
        $out_trade_no      = $order_info['id'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject           = '啊咿呀哟母婴用品';
        //必填
        //付款金额
        $price             = $order_info['price'];
        //必填
        //商品数量
        $quantity          = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee     = $order_info['delivery_price'];
        //必填，即运费
        //物流类型
        $logistics_type    = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "BUYER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述
        $body              = '啊咿呀哟母婴用品';
        //商品展示地址
        $show_url          = U('OrderInfo/show', ['id' => $order_info['id']],'',true);
        //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html
        //收货人姓名
        $receive_name      = $order_info['name'];
        //如：张三
        //收货人地址
        $receive_address   = $order_info['province_name'] . $order_info['city_name'] . $order_info['area_name'] . $order_info['detail_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
        //收货人邮编
        $receive_zip       = '402125';
        //如：123456
        //收货人电话号码
        $receive_phone     = $order_info['tel'];
        //如：0571-88158090
        //收货人手机号码
        $receive_mobile    = $order_info['tel'];
        //如：13312341234


        /*         * ********************************************************* */

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"           => "create_partner_trade_by_buyer",
            "partner"           => trim($alipay_config['partner']),
            "seller_email"      => trim($alipay_config['seller_email']),
            "payment_type"      => $payment_type,
            "notify_url"        => $notify_url,
            "return_url"        => $return_url,
            "out_trade_no"      => $out_trade_no,
            "subject"           => $subject,
            "price"             => $price,
            "quantity"          => $quantity,
            "logistics_fee"     => $logistics_fee,
            "logistics_type"    => $logistics_type,
            "logistics_payment" => $logistics_payment,
            "body"              => $body,
            "show_url"          => $show_url,
            "receive_name"      => $receive_name,
            "receive_address"   => $receive_address,
            "receive_zip"       => $receive_zip,
            "receive_phone"     => $receive_phone,
            "receive_mobile"    => $receive_mobile,
            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text    = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        echo $html_text;
    }

    /**
     * 
     */
    public function notify() {
        require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
        $alipayNotify  = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号
            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if ($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的price、quantity、seller_id与通知时获取的price、quantity、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                echo "success";  //请不要修改或删除
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的price、quantity、seller_id与通知时获取的price、quantity、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                echo "success";  //请不要修改或删除
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
                //该判断表示卖家已经发了货，但买家还没有做确认收货的操作
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的price、quantity、seller_id与通知时获取的price、quantity、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                echo "success";  //请不要修改或删除
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                //该判断表示买家已经确认收货，这笔交易完成
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的price、quantity、seller_id与通知时获取的price、quantity、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                echo "success";  //请不要修改或删除
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else {
                //其他状态判断
                echo "success";

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

}
