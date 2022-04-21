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
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/07/3
//      |_|                                         | TpMeCMS


namespace app\api\controller;

use app\api\controller\Tpmecmscom;
use EasyWeChat\Factory;
use think\Db;

use think\Config;
// use think\Validate;//验证
use fast\Random;
// use fast\Http;

/**
 * 温馨提示：自己需要加入公共方法可以在app\common\controller\Tpmecmscom里面加，建议不要加入带有登录权限的
 * 或者继承该接口再加入自己的方法
 */



/**
 * 继承Tpmecmscom
 * @ApiInternal()
 */
class Tpmecms extends Tpmecmscom
{
    
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    protected $stores = null;

    /**微信相关  start
     *  ┏┻━━━━━━━━━┻┓
     *  ┃           ┃
     *  ┃ ┗┳     ┳┛ ┃
     *  ┃     ┻     ┃
     *  ┗━━━┓　┏━━━━┛
     *      ┃　┃
     *      ┃　┃
     *      ┃　┗━━━━━━━━━━┓
     *      ┃     He      ┣┓
     *      ┃　          ┏┛
     *      ┗━┓  ┏━━━┓  ┏┛
     *        ┗━━┛   ┗━━┛
     */



    /**
     * 获取支付app
     * @ApiSummary (内部调用)
     * @ApiWeigh (999)
     * @ApiInternal()
     * @return void
     */
    protected function get_pay_app(){
        $config = [
            'app_id' => Config('site.app_id'),
            'secret' => Config('site.secret'),
            // 'aes_key' => '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'info', //info  'driver' => 'daily',
                // 'file' => __DIR__.'/wechat_order.log',
            ],

            'mch_id'     => Config('site.mch_id'),//商户号
            'key'        => Config('site.key'),   // API 密钥

            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'  => 'cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path'   => 'cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
            // 'notify_url' => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它

        
        ];

        $app = Factory::payment($config);
        return $app;
    }


    /**
     * 获取小程序
     * @ApiWeigh (998)
     * @ApiInternal()
     * @return void
     */
    protected function get_app(){
        $config = [
            'app_id' => Config('site.app_id'),
            'secret' => Config('site.secret'),
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'driver' => 'daily',
               // 'level' => 'debug', //info  'driver' => 'daily', 
                // 'file' => __DIR__.'/wechat.log',
            ],
        ];

        $app = Factory::miniProgram($config);
        return $app;
    }

    /**
     * 获取微信公众号
     * @ApiWeigh (997)
     * @ApiInternal()
     * @return void
     */
    protected function get_wx_gzh_app()
    {
         $config = [
             'app_id' => Config('site.wx_app_id'),//,
             'secret' => Config('site.wx_secret'),//,
            //  'token'   => Config('site.wx_secret'),//微信公众号->基本设置->服务器配置->令牌(Token)
             // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
             'response_type' => 'array',
              //  'log' => [
                //     'level' => 'debug',
                //     'file'  => 'easywechat.log',
                // ],
             //...
         ];
         
         $app = Factory::officialAccount($config);

         return $app;
    }


    


    /**
     * 发送公众号模板消息测试
     * @ApiInternal()
     * @param string $wx_openid 微信公众号openid
     * @param string $keyword1 自定义
     * @param string $keyword2 自定义
     * @param string $path
     * @return void
     */
    public function send_template_message_test($wx_openid='ooB8g6Hu3fa5P0oeMSpURFPVZRS4',$keyword1,$keyword2,$path='/pages/u_xjdS/u_xjdS')
    {
        
        $app = $this->get_wx_gzh_app();    
        $data = $app->template_message->send([
            'touser' => $wx_openid,//微信公众号openid,
            'template_id' => '_es-MdHZUO-voldjdevPADHj28JQ_t9bPzxowh_M8LM',//模板消息id
            'url' => 'https://he4966.cn/',//模板消息跳转地址
            'miniprogram' => [              //模板消息跳转小程序。小程序比网址优先级高，也可以注释其一
                    'appid' => Config('site.app_id'),
                    'pagepath' => $path,
            ],
            'data' => [
                'first' =>['您好，您有新的订单信息，请及时查看','#173177'],
                'keyword1' =>[$keyword1,'#173177'],//订单编号
                'keyword2' =>[$keyword2,'#173177'],//订单时间
                // 'keyword3' =>[$service,'#173177'],
                'remark' =>['请尽快登入小程序查看','#173177'],

            ],
        ]);
        return $data;
    }




    /**微信相关  end
     *  ┏┻━━━━━━━━━┻┓
     *  ┃           ┃
     *  ┃ ┗┳     ┳┛ ┃
     *  ┃     ┻     ┃
     *  ┗━━━┓　┏━━━━┛
     *      ┃　┃
     *      ┃　┃
     *      ┃　┗━━━━━━━━━━┓
     *      ┃     He      ┣┓
     *      ┃　          ┏┛
     *      ┗━┓  ┏━━━┓  ┏┛
     *        ┗━━┛   ┗━━┛
     */






                                                        /**公共方法  start
                                                         *  ┏┻━━━━━━━━━┻┓
                                                         *  ┃           ┃
                                                         *  ┃ ┗┳     ┳┛ ┃
                                                         *  ┃     ┻     ┃
                                                         *  ┗━━━┓　┏━━━━┛
                                                         *      ┃　┃
                                                         *      ┃　┃
                                                         *      ┃　┗━━━━━━━━━━┓
                                                         *      ┃     He      ┣┓
                                                         *      ┃　          ┏┛
                                                         *      ┗━┓  ┏━━━┓  ┏┛
                                                         *        ┗━━┛   ┗━━┛
                                                         */







    //  _____       __  __         ____ __  __ ____  
    // |_   __ __  |  \/  | ___   / ___|  \/  / ___|     | AUTHOR: Xiaohe
    //   | || '_ \ | |\/| |/ _ \ | |   | |\/| \___ \     | EMAIL: 496631085@qq.com
    //   | || |_)| | |  | |  __/ | |___| |  | |___) |    | PS: 钱的操作及日志
    //   |_|| .__/ |_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/08/5
    //      |_|                                          | TpMeCMS

    /**
     * 减用户的余额钱💰
     *
     * @param int $uid 用户id
     * @param int $money 钱
     * @param string $ps 备注
     * @return void
     */
    protected function DecUserMoney($uid,$money,$ps=null)
    {
        $user = Db::name('user')->find($uid);
        $data['before'] = $user['money'];//变更前余额
        
        Db::name('user')->where('id',$uid)->setDec('money',$money);//减去用户余额
        
        $user = Db::name('user')->find($uid);
        $data['after'] = $user['money'];//变更后余额
        $data['user_id'] = $uid;
        $data['money'] = $money;
        $data['memo'] = $ps;
        $data['createtime'] = time();
        Db::name('user_money_log')->insert($data);//写入用户money日志
        
    }

    /**
     * 加用户的余额钱💰
     *
     * @param int $uid 用户id
     * @param int $money 钱
     * @param string $ps 备注
     * @return void
     */
    protected function IncUserMoney($uid,$money,$ps=null)
    {
        $user = Db::name('user')->find($uid);
        $data['before'] = $user['money'];//变更前余额
        
        Db::name('user')->where('id',$uid)->setInc('money',$money);//减去用户余额
        
        $user = Db::name('user')->find($uid);
        $data['after'] = $user['money'];//变更后余额
        $data['user_id'] = $uid;
        $data['money'] = $money;
        $data['memo'] = $ps;
        $data['createtime'] = time();
        Db::name('user_money_log')->insert($data);//写入用户money日志
        
    }

    //  _____       __  __         ____ __  __ ____  
    // |_   __ __  |  \/  | ___   / ___|  \/  / ___|     | AUTHOR: Xiaohe
    //   | || '_ \ | |\/| |/ _ \ | |   | |\/| \___ \     | EMAIL: 496631085@qq.com
    //   | || |_)| | |  | |  __/ | |___| |  | |___) |    | PS: 积分的操作及日志
    //   |_|| .__/ |_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/08/5
    //      |_|                                          | TpMeCMS

    /**
     * 减用户的积分💎
     *
     * @param int $uid 用户id
     * @param int $score 积分
     * @param string $ps 备注
     * @return void
     */
    protected function DecUserScore($uid,$score,$ps=null)
    {
        $user = Db::name('user')->find($uid);
        $data['before'] = $user['score'];//变更前积分
        
        Db::name('user')->where('id',$uid)->setDec('score',$score);//减去用户积分
        
        $user = Db::name('user')->find($uid);
        $data['after'] = $user['score'];//变更后积分
        $data['user_id'] = $uid;
        $data['score'] = $score;
        $data['memo'] = $ps;
        $data['createtime'] = time();
        Db::name('user_score_log')->insert($data);//写入用户score日志
        
    }

    /**
     * 加用户的积分💎
     *
     * @param int $uid 用户id
     * @param int $score 积分
     * @param string $ps 备注
     * @return void
     */
    protected function IncUserScore($uid,$score,$ps=null)
    {
        $user = Db::name('user')->find($uid);
        $data['before'] = $user['score'];//变更前积分
        
        Db::name('user')->where('id',$uid)->setInc('score',$score);//减去用户积分
        
        $user = Db::name('user')->find($uid);
        $data['after'] = $user['score'];//变更后积分
        $data['user_id'] = $uid;
        $data['score'] = $score;
        $data['memo'] = $ps;
        $data['createtime'] = time();
        Db::name('user_score_log')->insert($data);//写入用户score日志
        
    }




    /**
     * 生成订单号
     *
     * @param string $qian 前缀
     * @param string $uid 用户id
     * @param int $len 订单长度
     * @return void
     */
    protected function rand_order($qian=null,$uid=null,$len=32)
    {
        if(strlen($qian.$uid)<10){
            $str = implode(NULL, array_map('ord', str_split(substr(Random::uuid(), 7, 13), 1)));
           //.Random::uuid();
            if($uid){
                $order = date('YmdHis').$uid;    
            }else{
                // if($this->auth->id){//需要登录
                //     $order = $this->auth->id.$order;
                // }
                $order = date('YmdHis');
            }           
        
        }else{
            $str = implode(NULL, array_map('ord', str_split(substr(Random::uuid(), 7, 13), 1)));
            $order = date('is').$uid;
        }

        if($qian){$order = $qian.$order;}
        if($len - strlen($order)>0){
            // var_dump('order',$order);
            $order =$order. substr($str, 1, $len - strlen($order));
        }else{
            $order = substr($order,-32);
        }

        return $order;
    }
    


    /**
     * 小程序openid获取用户信息
     * @ApiInternal()
     * @param string $openid
     * @return void
     */
    public function openid_get_user($openid)
    {
       $user =  Db::name('user')->where('openid',$openid)->find();
       return $user;
    }


    /**
     * 微信公众号wx_openid获取用户信息
     * @ApiInternal()
     * @param string $wx_openid
     * @return void
     */
    public function wx_openid_get_user($openid)
    {
       $user =  Db::name('user')->where('wx_openid',$openid)->find();
       return $user;
    }


    /**
     * 获取用户的微信公众号openid
     * @ApiInternal()
     * 
     * @param array $uids 用户ids
     * @return void
     */
    public function get_user_wx_openids($uids = null)
    {
        if(is_array($uids)){
            $list = Db::name('user')->where('id','in',$uids)->column('wx_openid');
        }else{
            $list = Db::name('user')->where('id',$uids)->value('wx_openid');
        }

        return $list;
    }





    /**
     * 
     * 小程序更新用户信息
     * 
     * @ApiInternal()
     */
    protected function update_user_data($data,$sharer=null)
    {
        $user = Db::name('user')->where('openid',$data['openid'])->find();
        
        $in_data['logintime'] = time();//登陆时间
        if($user){
            $in_data['sex'] = $data['sex'];
            $in_data['city'] = $data['city'];
            $in_data['avatar'] = $data['headimgurl'];
            $in_data['nickname'] = $data['nickname'];
            if($sharer && empty($user['pid'])){
                $in_data['pid'] = base64_decode($sharer);
                //送优惠券
                
            }
            
            Db::name('user')->where('openid',$data['openid'])->update($in_data);
            //修改数据；
        }else{
            
            //分享者
            if($sharer){
                $in_data['pid'] = base64_decode($sharer);
                
            }
            $in_data['openid'] = $data['openid'];
            $in_data['sex'] = $data['sex'];
            $in_data['city'] = $data['city'];
            $in_data['avatar'] = $data['headimgurl'];
            $in_data['nickname'] = $data['nickname'];
            $in_data['status'] = 'normal';
            $new_uid = Db::name('user')->insertGetId($in_data);
            //插入数据
            //送优惠券
            // if($sharer){
            //     $this->add_coupons($in_data['pid'],'邀请新用户赠送',$new_uid);
            // }
            // $this->add_coupons($new_uid,'赠送新用户');
        }
    }


    /**
     * 更新店铺坐标
     * @ApiInternal()
     * @param int $store_id 店铺id
     * @return void
     */
    public function update_store_geo($store_id)
    {
        $store = $this->verify_store_id($store_id);
        $geo = new \addons\redisgeo\controller\Index();//需要装redisgeo插件，需要联系QQ496631085
        $geo->add_geo($store['lng'],$store['lat'],$store['id']);
    }



    /**
     * 计算坐标距离
     * @ApiInternal()
     * @param $lng1 经度1
     * @param $lat1 纬度1
     * @param $lng2 经度2
     * @param $lat2 纬度2
     * @return float
     */
    public function get_coordinate_distance($lng1=120.459799, $lat1=31.802264, $lng2=120.326568, $lat2=31.686598) 
    { 
        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lng1);
        $radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;
        $b=$radLng1-$radLng2;
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;
        return $s;
    }

 
    
    

 


    /**公共方法  end
     *  ┏┻━━━━━━━━━┻┓
     *  ┃           ┃
     *  ┃ ┗┳     ┳┛ ┃
     *  ┃     ┻     ┃
     *  ┗━━━┓　┏━━━━┛
     *      ┃　┃
     *      ┃　┃
     *      ┃　┗━━━━━━━━━━┓
     *      ┃     He      ┣┓
     *      ┃　          ┏┛
     *      ┗━┓  ┏━━━┓  ┏┛
     *        ┗━━┛   ┗━━┛
     */







/** 验证
 * ┌───┐   ┌───┬───┬───┬───┐ ┌───┬───┬───┬───┐ ┌───┬───┬───┬───┐ ┌───┬───┬───┐
 * │Esc│   │ F1│ F2│ F3│ F4│ │ F5│ F6│ F7│ F8│ │ F9│F10│F11│F12│ │P/S│S L│P/B│  ┌┐    ┌┐    ┌┐
 * └───┘   └───┴───┴───┴───┘ └───┴───┴───┴───┘ └───┴───┴───┴───┘ └───┴───┴───┘  └┘    └┘    └┘
 * ┌───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───────┐ ┌───┬───┬───┐ ┌───┬───┬───┬───┐
 * │~ `│! 1│@ 2│# 3│$ 4│% 5│^ 6│& 7│* 8│( 9│) 0│- _│+ =│ BacSp │ │Ins│Hom│PUp│ │N L│ / │ * │ - │
 * ├───┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─────┤ ├───┼───┼───┤ ├───┼───┼───┼───┤
 * │ Tab │ Q │ W │ E │ R │ T │ Y │ U │ I │ O │ P │[ {│] }│ | \ │ │Del│End│PDn│ │ 7 │ 8 │ 9 │   │
 * ├─────┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴─────┤ └───┴───┴───┘ ├───┼───┼───┤ + │
 * │ Caps │ A │ S │ D │ F │ G │ H │ J │ K │ L │; :│" '│ Enter  │               │ 4 │ 5 │ 6 │   │
 * ├──────┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴────────┤     ┌───┐     ├───┼───┼───┼───┤
 * │ Shift  │ Z │ X │ C │ V │ B │ N │ M │< ,│> .│? /│  Shift   │     │ ↑ │     │ 1 │ 2 │ 3 │   │
 * ├─────┬──┴─┬─┴──┬┴───┴───┴───┴───┴───┴──┬┴───┼───┴┬────┬────┤ ┌───┼───┼───┐ ├───┴───┼───┤ E││
 * │ Ctrl│    │Alt │ Tpmecms   store       │ Alt│    │    │Ctrl│ │ ← │ ↓ │ → │ │   0   │ . │←─┘│
 * └─────┴────┴────┴───────────────────────┴────┴────┴────┴────┘ └───┴───┴───┘ └───────┴───┴───┘
 */

   


    /**
     * 验证商品
     *
     * @param int $id 店铺id
     * @return void
     */
    protected function verify_pro_id($id)
    {
        $data = Db::name('pro')->find($id);
        if(!$data){
            $this->error('没有该商品');
        }else{
            $data['specs'] = Db::name('prospecs')->where('pro_id',$id)->select();
        }
        return $data;
    }







/** 验证
 * ┌───┐   ┌───┬───┬───┬───┐ ┌───┬───┬───┬───┐ ┌───┬───┬───┬───┐ ┌───┬───┬───┐
 * │Esc│   │ F1│ F2│ F3│ F4│ │ F5│ F6│ F7│ F8│ │ F9│F10│F11│F12│ │P/S│S L│P/B│  ┌┐    ┌┐    ┌┐
 * └───┘   └───┴───┴───┴───┘ └───┴───┴───┴───┘ └───┴───┴───┴───┘ └───┴───┴───┘  └┘    └┘    └┘
 * ┌───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───┬───────┐ ┌───┬───┬───┐ ┌───┬───┬───┬───┐
 * │~ `│! 1│@ 2│# 3│$ 4│% 5│^ 6│& 7│* 8│( 9│) 0│- _│+ =│ BacSp │ │Ins│Hom│PUp│ │N L│ / │ * │ - │
 * ├───┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─────┤ ├───┼───┼───┤ ├───┼───┼───┼───┤
 * │ Tab │ Q │ W │ E │ R │ T │ Y │ U │ I │ O │ P │[ {│] }│ | \ │ │Del│End│PDn│ │ 7 │ 8 │ 9 │   │
 * ├─────┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴┬──┴─────┤ └───┴───┴───┘ ├───┼───┼───┤ + │
 * │ Caps │ A │ S │ D │ F │ G │ H │ J │ K │ L │; :│" '│ Enter  │               │ 4 │ 5 │ 6 │   │
 * ├──────┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴─┬─┴────────┤     ┌───┐     ├───┼───┼───┼───┤
 * │ Shift  │ Z │ X │ C │ V │ B │ N │ M │< ,│> .│? /│  Shift   │     │ ↑ │     │ 1 │ 2 │ 3 │   │
 * ├─────┬──┴─┬─┴──┬┴───┴───┴───┴───┴───┴──┬┴───┼───┴┬────┬────┤ ┌───┼───┼───┐ ├───┴───┼───┤ E││
 * │ Ctrl│    │Alt │ Tpmecms   store       │ Alt│    │    │Ctrl│ │ ← │ ↓ │ → │ │   0   │ . │←─┘│
 * └─────┴────┴────┴───────────────────────┴────┴────┴────┴────┘ └───┴───┴───┘ └───────┴───┴───┘
 */




    /**
     * 加密字符串
     *
     * @param string $str
     * @return void
     */
    protected function enStr($str){

        $str = base64_encode(base64_encode($str));
        return $str;
    }

     /**
     * 解密字符串
     *
     * @param string $str
     * @return void
     */
    protected function deStr($str){

        $str = base64_decode(base64_decode($str));
        return $str;
    }

    /**
     * 获取字段数组（可处理时间戳）
     *
     * @param array $arr 数组
     * @param array $fields 需要的字段
     * @param boolean $time 是否转化时间戳
     * @return void
     */
    protected function get_field_arr($arr,$fields,$time=false)
    {
        if(!is_array($fields)){
             //字符串
             $fields = explode(',',$fields);
        }
        
        $new = array();
        foreach($fields as $key=>$val){
            if(isset($arr[$val])){
                //字段后缀未time的可转时间戳
                if($time && strlen($val)>3 && substr($val,-4)=='time'){
                    
                    $new[$val] = strtotime($arr[$val]);
                }else{
                    $new[$val] = $arr[$val];
                }
                
            }
        }
        return $new;
    }



//  _____      __  __         ____ __  __ ____  
// |_   __ __ |  \/  | ___   / ___|  \/  / ___|     | AUTHOR: Xiaohe
//   | || '_ \| |\/| |/ _ \ | |   | |\/| \___ \     | EMAIL: 496631085@qq.com
//   | || |_) | |  | |  __/ | |___| |  | |___) |    | WECHAT: he4966
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2022/03/31
//      |_|                                         | TpMeCms 支付配置Strat


    /**
     * @ApiInternal
     * 获取支付宝支付配置
     * @param integer $pay_role 1默认货主,2=司机
     *
     * @return void
     */   
    public function TpMe_get_alipay_params(){

        try {
            $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        } catch (\Throwable $th) {
            halt('请查看composer show 是否安装了yurunsoft/pay-sdk');
        }
        
        $config = Config::get('site');

        
        $params->appID = $config['ali_app_id'];
        $params->apiDomain = $config['ali_apiDomain'];
        $params->format = 'JSON';
        $params->sign_type = 'RSA2';

        // $params->isUseAES = true;
        // // 使用公钥证书模式
        $params->usePublicKeyCert = true;
        // 支付宝公钥证书文件路径
        $params->alipayCertPath = './cert/alipayCertPublicKey_RSA2.crt';
        // 支付宝根证书文件路径
        $params->alipayRootCertPath = './cert/alipayRootCert.crt';
        // 支付宝应用公钥证书文件路径
        $params->merchantCertPath = './cert/appCertPublicKey_2021003105661387.crt';

        $params->aesKey   = 'UlZ8TAMN688HF/MTWDqE2g==';
    
        // 下面用支付宝开发工具校验是否对应
        //应用公钥2048
        $params->appPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgu1NlQM8kIhHzYsNPl46SiKjUtz2E6YrGTdeNE4YijeX7zOHtYxNxzAb5aR4kSuUdJL7mwpa9t+gY/qthWFlrQnQ5jFtTwNVP5rGt4gJ2cs3DHrr1TksiHZKvXGtPTQcVGPyaDXnRNgxO6OdGoJzKbFJmctpIc/9fNIiLKeCfT5s8jPrxwAYQUTzWRMqCQdPgaF1OnB2KWQ2ZHtI3YYx2j8qJK4rMTTYmPS7Xcn0Eln6iE8S5/vLpe6h8LYGKMRiNiBzb27wC2hyFRD4BBqk6n9iGSarBHk9E0Pqv0lIAaGKkqnaJNkTWksvhQBLUzCk4Z3ZqfHKsGD0ftHwbrMHpQIDAQAB';
        //应用私钥2048.txt   支付宝开发工具生成
        $params->appPrivateKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCidx5KvTI/ZSHzMTnCCXAqVhn/KVkMeE9juZ5oPoAzznq/IKiOCNFENiHVwor0INnEjE119+5R9RCs3Ud2E/RLG/3apaGvR401SufH3fT6WOfETvd5L4O27Akapy58Db/UHJP9VGA8on90//YtEhveS0qci3b/W0aP/BavWGNuW1Ja7nmrEomlMPYFGOxbt+Z96xNkpudRC92Hqy9SHIe9gh3bAfON6J+8J6mkXQ6S5+bwk7PpPojLL+LQmZtIxwX125jzfjPYbAmmf0Y1iRfkeQ9D45L62EiJNxCIJCuDm/xu+m7IInnlLbQy9cyJZyZLn2SY4IxHFEWzwcx07lrjAgMBAAECggEAKGZosPs1SJB0jc4a58aDDvQUSOBWVYSBcXISEygfp0JcQbT0VUWaDFVNuqNC/IxCut6M0J4TtGu1dtcb0yAJKrhOOxXfDABF/A+hbZEjPzHmiGl+nrMTFOTDKs6R/6VHo45rup5FxC5Ada7E39ZV+cRNTHpd4AgpltqAJaBcMW+69rPESGnOCfk+sEdfVOfp4qntJtzaVX2F/nGKk/7A1RsydcZMo06Pu2fA0C7o5Hvlc/q1Sc33uWB0c+wLbXjSR99MhP9tQ3k2rB4lglGgIy/IGrMXYe7xQC9yOhvEpTUMCfL/JmKxZCmCEuzwouso/esNMYfLbQdCI60PcQ2E2QKBgQD1L61czyIGGkyIBlrQAhCmrgtQbEMbh12KK8GqpaZvG2F0ObI06x3jiOgyCzH985rf1GZSbWkBiOV9OwFsy8Ist+g6nB1HQWqt4KcsGaV1Rs2RvhLVWdxgN/w4GtACQzelGt5xYRD9Bj5mtE0FJLgOonyQ4n8TOqx3bBuwBeZXBQKBgQCpoXbiAzmRCiBAqwgScxTVnTOhuAhKnLXDiIgIEO1XbAsF8uuk6WXq/v5+ZquG8pMSlEP7IqdGRJDF7w/Pth13spOWkqGvlHgAeGeOnGXJt5MHGuqkgr1+vTLT4O1ScHrxERzGElLqtNJ1Vxbu4u+HnQIq3ogZu4slpq5/XQ++xwKBgQCz6epSggO7aqzh25fjrsA9LtVKjEap+qie7QDNkYQRX4whVvz3a9eg0SNhJWnnqUvqFteGgJZe8+9B93xSMyom2kRkTY4GbXA0avcAm0kYR34tBcaYG7pkOHCxUqt0HZLFanPzyoBGysAbaQ5jSdBtnLm/LxSELbfEYeXnQk0FaQKBgQCpWecLa7bMQ7TBez8a1EPnpANJICc+/kdFSIiurN+XWOmEbequgMncm+lOHoh+uKz2bomULxubPpbe1XYOpD21I9bPC+NaHLrVlV+l9CR3B8xE2q2JwnEXGCKnf7bPiDZld/UnFmIrqo020tS1eGE1Hh5s2DA+qIsZ8tfhlfJDPwKBgAicnhA49D+7/Sgle9yo1xB/q8L6mWd40FxCH1DQfA1lJ4Ix4TDdILRXfbBbJLqBHEf4HADOON0gvc2WMQt7EEYnCv4R13/8cAYSN0HaeS6pQFJfwT74DdQa8jw1JuBGhxMaIRn2v10LVKjtIUfoUBpk3u06KkPpgpnLp+rPsr1V';
      
        return $params;      
    }




}