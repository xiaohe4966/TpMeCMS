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
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/06/25/
//      |_|                                         | TpMeCMS

namespace app\cms\controller;

// use app\common\controller\Frontend;
use app\cms\controller\Cms;
use think\Db;
use fast\Random;

use app\common\library\Auth;//用户
use think\Hook;
use app\common\model\User as User2;
use app\common\library\Token;
use think\Cookie;

use addons\wechat\model\WechatCaptcha;

use app\common\library\Ems;
use app\common\library\Sms;
use app\common\model\Attachment;
use think\Config;
use think\Session;
use think\Validate;

use EasyWeChat\Factory;
use app\cms\controller\Com;



class User extends Cms
{

    protected $noNeedLogin = ['*'];//自己需要写的方法 这个是不需要登陆的其他则为需要登陆
    protected $noNeedRight = '*';
    protected $layout = '';


    public function _initialize()
    {   
        parent::_initialize();
        // halt($this->auth->id);
        $auth = $this->auth;
        // Hook::add('user_register_successed', function ($user) use ($auth) {
        //     Cookie::set('uid', $user->id);
        //     Cookie::set('token', $auth->getToken());
        // });


        //监听注册登录退出的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = input('post.keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_delete_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        //需要登陆的方法 加入到里面
        // $NeedLogin = ['add_apply','add_evaluate','add_line'];//根据需求更改
        // foreach($NeedLogin as $action){
        //     if($action==$this->request->action()){
        //         if(!$this->auth->isLogin()){
        //             $this->error('请登陆');
        //         }
        //     }
        // }
       
    }


    /**
     * 首页
     *
     * @return void
     */
    public function index()
    {
  
        $this->redirect('/cms/index/index');
        return $this->view->fetch();
    }

    public function is_login($url=null)
    {
        if(!$this->auth->id){
            $this->error('请登陆','/cms/user/login.html?url='.$url);
        }
    }


    /**
     * 自己定义 方法
     * 添加体验评价
     * 此方法需要登陆
     * @return void
     */
    public function add_evaluate()
    {
        // $this->is_login('/cms/user/'.$this->request->action());
        $this->is_login($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"/cms/index/index");
        $params = $this->request->param(); 

        $data = $this->get_field_arr($params,['name','content','email'],true);//过滤字段
        $data['uid'] = $this->auth->id;

        $res = Db::name('evaluate')->where($data)->find();
        if($res){
            $this->error('已有该数据');
        }

        $data['createtime'] = time();
        $data['status'] = 2;//状态:1=正常,2=隐藏
        $data['title'] = substr($data['content'],10);
        $res = Db::name('evaluate')->insert($data);
        if($res){
            $this->success('成功提交');
        }
       
    }

    /**
     * 添加申请合作
     *
     * @return void
     */
    public function add_apply()
    {
        if($this->request->IsPost()){

            $this->is_login($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"/cms/index/index");
            
            $params = $this->request->param(); 
            
            $data = $this->get_field_arr($params,['ltd_name','main','hzqj','name','tel','addr','email','content'],true);//过滤字段
            
            $data['uid'] = $this->auth->id;
        
            $res = Db::name('apply')->where($data)->find();
        
            if($res){
                $this->error('已有该数据');
            }

            $data['createtime'] = time();
            $data['status'] = 1;//状态:1=未读,2=已读
            $res = Db::name('apply')->insert($data);
            if($res){
                $this->success('成功提交');
            }
        }
    }


    /**
     * 添加申请合作
     *
     * @return void
     */
    public function add_line()
    {
        if($this->request->IsPost()){

            $this->is_login($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"/cms/index/index");
            
            $params = $this->request->param(); 
            // halt($params);
            $data = $this->get_field_arr($params,['year','sex','card','name','tel','email','work','school'],true);//过滤字段
            
            $data['uid'] = $this->auth->id;
            $data['classes_ids'] = implode(',',$params['class_ids']);
            $res = Db::name('line')->where($data)->find();
        
            if($res){
                $this->error('已有该数据');
            }

            $data['createtime'] = time();
            $data['status'] = 1;//状态:1=未读,2=已读
            $res = Db::name('line')->insert($data);
            if($res){
                $this->success('成功提交');
            }
        }
    }





    public function register()
    {
        $url = $this->request->request('url', '', 'trim');
        if ($this->auth->id) {
            $this->success('你已登录', $url ? $url : url('user/index'));
        }
        if($this->request->IsPost()){
            $data['mobile'] = $this->request->param('mobile','');
            if(strlen($data['mobile'])!=11){
                $this->error('手机号不正确');
            }

            $user = Db::name('user')->where($data)->find();
            if(!$user){
                $this->error('请关注公众号');
            }

            if($user['password']){
                $this->error('该账号已注册');
            }
            $data['password'] = $this->request->param('password','');
            $data['email'] = $this->request->param('email','');
            

            $captcha =$this->request->param('code','');
             // 验证码暂时关闭随便传
            // if (!Sms::check($mobile, $captcha, 'mobilelogin')) {   
            //     $this->error(__('Captcha is incorrect'));
            // }


            if($this->register2($data['mobile'],$data['password'],$data['email'],$data['mobile'])){
                $this->success('注册成功','/cms/index/index');
            }

        }
        
        $this->view->assign('cate', '1');//栏目信息
        return $this->view->fetch();
    }

    /**
     * 发送注册验证码
     *
     * @param string $mobile 手机号
     * @param string $self 自身调用（默认未接口调用
     * @return void
     */
    public function send_reg_sms($mobile=null,$self=false)
    {
        if(preg_match("/^1[3-9]{1}\d{9}$/",$mobile)){
            Sms::flush($mobile, 'mobilelogin');

            if($self){
                return true;
            }else{
                return json(['code'=>1,'msg'=>'发送成功']);
            }
            
        }else{
            if($self){
                return true;
            }else{
                return json(['code'=>-1,'msg'=>'手机号不正确']);
            }
            
        }
       
    }

    /**
     * 注册用户
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $email    邮箱
     * @param string $mobile   手机号
     * @param array  $extend   扩展参数
     * @return boolean
     */
    public function register2($username, $password, $email = '', $mobile = '', $extend = [])
    {

     
        // 检测用户名、昵称、邮箱、手机号是否存在
        if (User2::getByUsername($username)) {
            $this->error('Username already exist');
            return false;
        }
        if (User2::getByNickname($username)) {
            $this->error('Nickname already exist');
            return false;
        }
        if ($email && User2::getByEmail($email)) {
            $this->error('Email already exist');
            return false;
        }
        // if ($mobile && User2::getByMobile($mobile)) {
        //     $this->error('Mobile already exist');
        //     return false;
        // }

        $ip = request()->ip();
        $time = time();

        $data = [
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
            'level'    => 1,
            'score'    => 0,
            'avatar'   => '',
        ];
        $params = array_merge($data, [
            'nickname'  => preg_match("/^1[3-9]{1}\d{9}$/",$username) ? substr_replace($username,'****',3,4) : $username,
            'salt'      => Random::alnum(),
            'jointime'  => $time,
            'joinip'    => $ip,
            'logintime' => $time,
            'loginip'   => $ip,
            'prevtime'  => $time,
            'status'    => 'normal'
        ]);
        $params['password'] = $this->getEncryptPassword($password, $params['salt']);
        $params = array_merge($params, $extend);
        // halt($params);
        //账号注册时需要开启事务,避免出现垃圾数据
        // Db::startTrans();
        // try {
            // $user = User2::create($params, true);

            $user2 = Db::name('user')->where('mobile',$mobile)->find();
            Db::name('user')->where('mobile',$mobile)->update($params);

           
            
            
            $this->_user = User2::get($user2['id']);

            //设置Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $user2['id'], 2592000);

            //设置登录状态
            $this->_logined = true;

            //注册成功的事件
            Hook::listen("user_register_successed", $this->_user, $data);
            Db::commit();
        // } catch (Exception $e) {
        //     $this->error($e->getMessage());
        //     Db::rollback();
        //     return false;
        // }
        return true;
    }


    /**
     * 获取密码加密后的字符串
     * @param string $password 密码
     * @param string $salt     密码盐
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        //退出本站
        $this->auth->logout();
        $this->success(__('Logout successful'), url('index/index'));
    }

    /**
     * 会员登录
     */
    public function login()
    {
        $url = $this->request->request('url', '', 'trim');
        if ($this->auth->id) {
            $this->success(__('You\'ve logged in, do not login again'), $url ? $url : url('index/index'));
        }
        if ($this->request->isPost()) {
            $account = $this->request->post('account');
            $password = $this->request->post('password');
            $keeplogin = (int)$this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'account'   => 'require|length:3,50',
                'password'  => 'require|length:6,30',
                '__token__' => 'require|token',
            ];

            $msg = [
                'account.require'  => 'Account can not be empty',
                'account.length'   => 'Account must be 3 to 50 characters',
                'password.require' => 'Password can not be empty',
                'password.length'  => 'Password must be 6 to 30 characters',
            ];
            $data = [
                'account'   => $account,
                'password'  => $password,
                '__token__' => $token,
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error(__($validate->getError()), null, ['token' => $this->request->token()]);
                return false;
            }
            if ($this->auth->login($account, $password)) {
                $this->success(__('Logged in successful'), $url ? $url : url('index/index'));
            } else {
                $this->error($this->auth->getError(), null, ['token' => $this->request->token()]);
            }
        }
        //判断来源
        $referer = $this->request->server('HTTP_REFERER');
        if (!$url && (strtolower(parse_url($referer, PHP_URL_HOST)) == strtolower($this->request->host()))
            && !preg_match("/(user\/login|user\/register|user\/logout)/i", $referer)) {
            $url = $referer;
        }
        $this->view->assign('url', $url);
        $this->view->assign('title', __('Login'));
        return $this->view->fetch();
    }

    /**
     * 微信公众号绑定手机界面
     *
     * @return void
     */
    public function bind()
    {
        $this->get_user('/cms/user/bind');
        // $this->is_login();
        $user = Session::get('wechat_user');

        if($this->request->IsPost()){
            $mobile = $this->request->param('mobile','');
            $captcha =$this->request->param('code','');
            if(!$mobile){
                $this->error('请输入手机号');
            }

            $password = Db::name('user')->where('wx_openid',$user['openid'])->value('password');
            if(!$password){
                  // 验证码暂时关闭随便传
                // if (!Sms::check($mobile, $captcha, 'mobilelogin')) {   
                //     $this->error(__('Captcha is incorrect'));
                // }
                $this->register_send_template($user['openid'],$mobile);
            }else{
                $this->error('已注册过');
            }
            
        }
        return $this->view->fetch();
    }





    /**
     * 获取微信公众号
     *
     * @return void
     */
    public function get_wx_gzh_app()
    {

         $config = [
             'app_id' => Config('site.wx_app_id'),//微信公众号的appid
             'secret' => Config('site.wx_secret'),//微信公众号的密钥
         
             // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
             'response_type' => 'array',
         ];
         
         //微信公众号的方法
         $app = Factory::officialAccount($config);
         return $app;
    }

    /**
     * 获取用户信息，没有授权的自动授权，类似登录
     * 
     * @return void
     */
    public function get_user($re_url=null)
    {
        if($re_url){
            Session::set('re_url',$re_url);
        }
        $app = $this->get_wx_gzh_app();
        $oauth = $app->oauth;

        // 未登录或没openid数据的
        if (empty(Session::get('wechat_user'))) {
            
            $url = $this->request->domain().'/cms/user/code';//改成当前类的 访问路径，可以写成封装方法，怕一些用户看不懂
            $oauth->scopes(['snsapi_userinfo'])->redirect($url);//获取用户信息跳转
            //非Tp框架的可能要return
            
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
            $oauth->redirect()->send();
            // halt(4);
        }
            
        $user = Session::get('wechat_user');

        $Xiaohe = new Com;
        // halt($user);
        $Xiaohe->update_user_data($user,Session::get('sharer'));
        $user_data = $Xiaohe->openid_get_user($user['openid']);
        
        
        $ret = $this->auth->direct($user_data['id']);
        $id = $this->auth->id;//getUserinfo();
        // halt($token);
        Session::set('id',$id);
        
        return $user;
    }





    /* 
    * @Description: 公众号获取code
    * @return: 
    */   
    public function code()
    {
        $app = $this->get_wx_gzh_app();
        $oauth = $app->oauth;
       
        $user = $oauth->user();

        Session::set('wechat_user',$user->original);//获取到用户信息 自己可以存储
        // halt($user->original);//可以自己打印看下

        // $user = Session::get('wechat_user');

        $Xiaohe = new Com;
        // halt($user);
        $Xiaohe->update_user_data($user,Session::get('sharer'));
        $user_data = $Xiaohe->openid_get_user($user['openid']);


        
        //授权后挑战到那个地址
        $re_url = Session::get('re_url');
        if($re_url){
            Session::set('re_url',null);
            header("Location: ".$this->request->domain().$re_url);
        }else{
            header("Location: ".$this->request->domain()."/cms/user/index");
        }

    }


    /**
     * 注册发送模版消息
     *
     * @param string $com_openid 微信公众号的openid
     * @param string $keyword1 参数1
     * @param [type] $keyword2 参数2
     * @param string $path 跳转小程序路径
     * @return void
     */
    public function register_send_template($com_openid='o0kt76ZCpj8g2HhO8IctpJIwWyu4',$mobile=null,$keyword2=null,$url=null,$name=null)
    {

        if(!$keyword2){
            $keyword2 = date('Y年m月d号H:i:s');
        }

        if(!$url){
            $url = $this->request->domain().'/cms/user/register';
        }

        if(!$name){
            $name = 'XX';
        }
        $app = $this->get_wx_gzh_app();    
        $data = $app->template_message->send([
            'touser' => $com_openid,//微信openid,
            'template_id' => 't_ZOr6SGeoj0VFa3TukMjSONpR_h6lpNDGjhpnI0M2s',
            'url' => $url,//点击模版消息打开的页面地址
     
            'data' => [
                'first' =>['微信注册成功，请到注册页面绑定手机','#173177'],
                'keyword1' =>[$mobile,'#223177'],//手机
                'keyword2' =>[$keyword2,'#333177'],//时间

                'remark' =>['请绑定打开此消息绑定该手机号','#173199'],

            ],
        ]);
        if($data['errmsg']=='ok'){
            $this->update_user_mobile($com_openid,$mobile);
            $this->success('请绑定注册手机','/cms/user/register');
            
        }else{
            $this->error('模版消息发送失败，或配置错误或未关注公众号'.$data['errmsg'],'/cms/user/bind',-1,16);
        }
    }


    public function get_jssdk($url=null)
    {
        $app = $this->get_wx_gzh_app();
        if(!$url)
        $url = $this->request->domain().'/cms/user/自行修改';
        // halt($url);
        $app->jssdk->setUrl($url);
        // halt($url);
        $config = $app->jssdk->buildConfig(['startRecord', 'stopRecord', 'uploadVoice', 'onVoiceRecordEnd','downloadVoice','playVoice'],false,false,false,$url);

        return $config;
        // $app->js->setUrl($url);
        // $app->js->config(['updateAppMessageShareData','updateTimelineShareData'], $debug = false, $beta = false, $json = true); 
        //   halt($config);
    }

    protected function update_user_mobile($wx_openid,$mobile){
        return Db::name('user')->where('wx_openid',$wx_openid)->update(['mobile'=>$mobile]);
    }

}
