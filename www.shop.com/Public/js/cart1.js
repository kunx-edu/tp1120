/*
 @功能：购物车页面js
 @作者：diamondwang
 @时间：2013年11月14日
 */

$(function () {

    //减少
    $(".reduce_num").click(function () {
        var amount = $(this).parent().find(".amount");
        if (parseInt($(amount).val()) <= 1) {
            alert("商品数量最少为1");
        } else {
            $(amount).val(parseInt($(amount).val()) - 1);
        }
        
        //将商品的购买数量更新到后台持久化数据中.
        if(!change_amount($(amount).attr('data'),$(amount).val())){
            $(amount).val(parseInt($(amount).val()) + 1);
        }
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        calc_money();
    });

    //增加
    $(".add_num").click(function () {
        var amount = $(this).parent().find(".amount");
        $(amount).val(parseInt($(amount).val()) + 1);
        //将商品的购买数量更新到后台持久化数据中.
        if(!change_amount($(amount).attr('data'),$(amount).val())){
            $(amount).val(parseInt($(amount).val()) - 1);
        }
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        calc_money();
    });

    //直接输入
    $(".amount").blur(function () {
        if (parseInt($(this).val()) < 1) {
            alert("商品数量最少为1");
            $(this).val(1);
        }
        
        //将商品的购买数量更新到后台持久化数据中.
        if(!change_amount($(this).attr('data'),$(this).val())){
            console.debug('修改商品数量失败,请重试');
        }
        
        //小计
        var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
        $(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
        calc_money();

    });
    
    
    //删除
    $(".col6 a").click(function () {
        var amount = $(this).parent().parent().find(".amount");
        //将商品的购买数量更新到后台持久化数据中.
        if(!change_amount($(amount).attr('data'),0)){
            console.debug('修改数量失败,请重试');
        }else{
            $(this).parent().parent().remove();
        }
        calc_money();
    });
    
    /**
     * 计算总金额
     * @returns null
     */
    function calc_money(){
        //总计金额
        var total = 0;
        $(".col5 span").each(function () {
            total += parseFloat($(this).text());
        });

        $("#total").text(total.toFixed(2));
    }
    
    
    function change_amount(goods_id,amount){
        data = {
            goods_id:goods_id,
            amount:amount,
        };
        var flag = true;
        $.getJSON(url,data,function(response){
            if(!response.status){
                flag = false;
            }
        });
        return flag;
    }
});