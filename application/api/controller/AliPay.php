<?php
                                                                                                                                                                                                                                                                                                                                        
// TTTTTTTTTTTTTTTTTTTTTTT                  MMMMMMMM               MMMMMMMM                                CCCCCCCCCCCCMMMMMMMM               MMMMMMMM  SSSSSSSSSSSSSSS 
// T:::::::::::::::::::::T                  M:::::::M             M:::::::M                             CCC::::::::::::M:::::::M             M:::::::MSS:::::::::::::::S
// T:::::::::::::::::::::T                  M::::::::M           M::::::::M                           CC:::::::::::::::M::::::::M           M::::::::S:::::SSSSSS::::::S
// T:::::TT:::::::TT:::::T                  M:::::::::M         M:::::::::M                          C:::::CCCCCCCC::::M:::::::::M         M:::::::::S:::::S     SSSSSSS
// TTTTTT  T:::::T  TTTTTppppp   ppppppppp  M::::::::::M       M::::::::::M   eeeeeeeeeeee          C:::::C       CCCCCM::::::::::M       M::::::::::S:::::S            
//         T:::::T       p::::ppp:::::::::p M:::::::::::M     M:::::::::::M ee::::::::::::ee       C:::::C             M:::::::::::M     M:::::::::::S:::::S            
//         T:::::T       p:::::::::::::::::pM:::::::M::::M   M::::M:::::::Me::::::eeeee:::::ee     C:::::C             M:::::::M::::M   M::::M:::::::MS::::SSSS         
//         T:::::T       pp::::::ppppp::::::M::::::M M::::M M::::M M::::::e::::::e     e:::::e     C:::::C             M::::::M M::::M M::::M M::::::M SS::::::SSSSS    
//         T:::::T        p:::::p     p:::::M::::::M  M::::M::::M  M::::::e:::::::eeeee::::::e     C:::::C             M::::::M  M::::M::::M  M::::::M   SSS::::::::SS  
//         T:::::T        p:::::p     p:::::M::::::M   M:::::::M   M::::::e:::::::::::::::::e      C:::::C             M::::::M   M:::::::M   M::::::M      SSSSSS::::S 
//         T:::::T        p:::::p     p:::::M::::::M    M:::::M    M::::::e::::::eeeeeeeeeee       C:::::C             M::::::M    M:::::M    M::::::M           S:::::S
//         T:::::T        p:::::p    p::::::M::::::M     MMMMM     M::::::e:::::::e                 C:::::C       CCCCCM::::::M     MMMMM     M::::::M           S:::::S
//       TT:::::::TT      p:::::ppppp:::::::M::::::M               M::::::e::::::::e                 C:::::CCCCCCCC::::M::::::M               M::::::SSSSSSS     S:::::S
//       T:::::::::T      p::::::::::::::::pM::::::M               M::::::Me::::::::eeeeeeee          CC:::::::::::::::M::::::M               M::::::S::::::SSSSSS:::::S
//       T:::::::::T      p::::::::::::::pp M::::::M               M::::::M ee:::::::::::::e            CCC::::::::::::M::::::M               M::::::S:::::::::::::::SS 
//       TTTTTTTTTTT      p::::::pppppppp   MMMMMMMM               MMMMMMMM   eeeeeeeeeeeeee               CCCCCCCCCCCCMMMMMMMM               MMMMMMMMSSSSSSSSSSSSSSS   
//                        p:::::p                                                                                                                                       
//                        p:::::p                                                                                                                                       
//                       p:::::::p                                                                                                                                      
//                       p:::::::p                                                                                                                                      
//                       p:::::::p                                                                                                                                      
//                       ppppppppp                                                                                                                                      
                                                                                                                                                                     
//  _____      __  __         ____ __  __ ____  
// |_   __ __ |  \/  | ___   / ___|  \/  / ___|     | AUTHOR: Xiaohe
//   | || '_ \| |\/| |/ _ \ | |   | |\/| \___ \     | EMAIL: 496631085@qq.com
//   | || |_) | |  | |  __/ | |___| |  | |___) |    | WECHAT: he4966
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/09/16
//      |_|                                         | TpMeCMS

namespace app\api\controller;
use app\api\controller\Tpmecms;

use think\Db;
use EasyWeChat\Factory;


/**
 * 支付宝接口
 */
class AliPay extends Tpmecms
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];


    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 🔐阿里充值钱包💰
     *
     * @param array $data 订单信息
     * @return void
     */
    public function ali_pay_money_order($data)
    {
        
        $this->_params = $this->TpMe_get_alipay_params();//获取支付宝支付参数
        // halt($this->_params);
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($this->_params);
       
        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\App\Params\Pay\Request;
        $request->notify_url = 'http://tpmecms.cn/api/ali_pay/ali_pay_callback';//回调接口(在网站上设置的,好像不能带参数哦)
        // $request->notify_url = $this->domain.'/api/query_pay/ali_pay_money_order_query?order='.$data['order'];//$GLOBALS['PAY_CONFIG']['notify_url'];// 支付后通知地址（作为支付成功回调，这个可靠）
        // $request->return_url = $this->domain;
        $request->businessParams->out_trade_no = $data['order']; // 商户订单号
        $request->businessParams->total_amount = $data['money']; // 价格
        $request->businessParams->subject = Config::get('site.name').'充值'; // 商品标题
        $request->businessParams->body = $this->pay_role==1?'货主充值':'司机充值'; // 商品标题
        $request->businessParams->time_expire = date('Y-m-d H:i:s',time()+600);

        $request->businessParams->timeout_express = "10m";
        $request->businessParams->goods_type = "0";//商品主类型：0—虚拟类商品，1—实物类商品（默认）
        $request->businessParams->return_params="pay_money";

        
        // halt($pay);
        $pay->prepareExecute($request, $url, $res);
        // halt($res);
        // $this->error('no');

        // $this->success($res,http_build_query($res));

        return http_build_query($res); // 输出的是可以让app直接使用的参数
    }


    /**
     * 阿里司机钱包充值回调（非接口）
     * @ApiInternal
     * @return void
     */
    public function ali_pay_callback()
    {
        //这个方法支付宝要能访问,所以不需要登陆和判断权限
        $params = $this->request->param();
        //开始使用时可以查看日志,成功后自行注释
            file_put_contents('./alipaylog/'.$params['out_trade_no'].'ali_pay_callback'.date('Y_m_d_H_i_s').'.txt',json_encode($params));
        $this->_params = $this->TpMe_get_alipay_params();
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($this->_params);
        if($pay->verifyCallback($_POST))    //校验提交的参数信息
        {
            file_put_contents('./alipaylog/'.$params['out_trade_no'].'success'.date('Y_m_d_H_i_s').'.txt',json_encode($params));
            // 通知验证成功，可以通过POST参数来获取支付宝回传的参数
            $order = $params['out_trade_no'];
            $order_data = Db::name('money_order')->where('order',$order)->find();
            if(!$order_data){
                $this->error('没有该订单号');
            }
            if($order_data['pay_status']=='1' || $order_data['pay_status']=='4'){//支付状态:1=待支付,2=已支付,3=已退款,4=已超时
                $up_data['pay_time'] = strtotime($params['gmt_payment']);
                $up_data['pay_update_time'] = strtotime($params['notify_time']);
                $up_data['pay_status'] = '2';
                $up_data['pay_openid'] = $params['buyer_id'];
                $up_data['pay_money'] = $params['total_amount']*100;//实际支付（分）

                $money_data = $this->IncUserMoney($order_data['uid'],$order_data['money'],'支付宝充值',$order_data['id']);
                $up_data['before'] = $money_data['before'];
                $up_data['after'] = $money_data['after'];

                Db::name('money_order')->where('order',$order)->update($up_data);
                echo 'success';
            }

        }
        else
        {
            $this->AliUpdatePayOrderPayStatus($params);
            // if($params['trade_status']=='TRADE_SUCCESS'){
            //     $order = $params['out_trade_no'];
            //     $order_data = Db::name('money_order')->where('order',$order)->find();
            //     if(!$order_data){
            //         $this->error('没有该订单号');
            //     }
            //     if($order_data['pay_status']=='1' || $order_data['pay_status']=='4'){//支付状态:1=待支付,2=已支付,3=已退款,4=已超时
            //         $up_data['pay_time'] = strtotime($params['gmt_payment']);
            //         $up_data['pay_update_time'] = strtotime($params['notify_time']);
            //         $up_data['pay_status'] = '2';
            //         $up_data['pay_openid'] = $params['buyer_id'];
            //         $up_data['pay_money'] = $params['total_amount']*100;//实际支付（分）

            //         $money_data = $this->IncUserMoney($order_data['uid'],$order_data['money'],'支付宝充值',$order_data['id']);
            //         $up_data['before'] = $money_data['before'];
            //         $up_data['after'] = $money_data['after'];

            //         Db::name('money_order')->where('order',$order)->update($up_data);
            //         echo 'success';
            //     }
            // }
           
        }

        
        // halt($params);
        
    }


     /**
     * 修改支付订单状态
     *
     * @param array $params 接收参数
     * @return void
     */
    protected function AliUpdatePayOrderPayStatus($params){

        if($params['trade_status']=='TRADE_SUCCESS'){
            $order = $params['out_trade_no'];//赋值新变量
            //echo 'success';

            //查询钱包订单
            $order_data = Db::name('money_order')
                        ->where('order',$order)
                        ->find();

            if(!$order_data){

                $pay_order = Db::name('pay_order')
                            ->where('order',$order)
                            // ->where('pay_status','1')//支付状态:1=待支付,2=已支付,3=已退款,4=已超时
                            ->find();

                if(!$pay_order){
                    $this->error('没有该支付押金订单');
                }


                // halt($pay_order);
                if($pay_order && ($pay_order['pay_status']=='1' || $pay_order['pay_status']=='4')){

                    $data['pay_status'] = '2';//支付状态:1=待支付,2=已支付,3=已退款,4=已超时
                    $data['pay_time'] = strtotime($params['gmt_payment']);//支付时间
                    $data['pay_type'] = '2';    //支付类型:1=微信,2=支付宝,9=其他
                    $data['pay_money'] = $params['total_amount']*100;//实际支付的金额 分//invoice_amount

                    $data['pay_openid'] = $params['buyer_id'];

                    //修改押金订单
                    $res_up = Db::name('pay_order')
                            ->where('order',$order)
                            ->update($data);
                    
                    //修改主订单  订单类型:1=普通发货,2=城市快运,3=拉货搬家,4=专线
                    $db = $this->GetTableByOrderStatus($pay_order['order_status']);
                
                    //角色:1=货主,2=司机
                    switch ($pay_order['role_status']) {
                        case '1':
                            $pay_status['spay_status'] = '1';
                            $pay_status['pay_time'] = $data['pay_time'];
                         
                            break;
                        case '2':
                            $pay_status['jpay_status'] = '1';
                            // if($pay_order['order_status']!='4'){//订单类型:1=普通发货,2=城市快运,3=拉货搬家,4=专线
                            //     $pay_status['j_time'] = $data['pay_time'];
                            // }
                            break;
                        default:

                            break;
                    }
                    //修改押金单单支付状态
                    Db::name($db)->where('id',$pay_order['order_id'])->update($pay_status);
                    $this->PushOrderMsg($pay_order['order_status'],$pay_order['order_id']);//推送发布订单消息
                    
                    echo 'success';
                }

                
            }elseif($order_data['pay_status']=='1' || $order_data['pay_status']=='4'){//支付状态:1=待支付,2=已支付,3=已退款,4=已超时
                $up_data['pay_time'] = strtotime($params['gmt_payment']);
                $up_data['pay_update_time'] = strtotime($params['notify_time']);
                $up_data['pay_status'] = '2';//支付类型:1=微信,2=支付宝,9=其他
                $up_data['pay_openid'] = $params['buyer_id'];
                $up_data['pay_money'] = $params['total_amount']*100;//实际支付（分）
                $money_data = $this->IncUserMoney($order_data['uid'],$order_data['money'],'支付宝司机充值',$order_data['id']);
                $up_data['before'] = $money_data['before'];
                $up_data['after'] = $money_data['after'];
                Db::name('money_order')->where('order',$order)->update($up_data);
                
                echo 'success';
            }
        }else{
            halt($params);
        }
    }



}