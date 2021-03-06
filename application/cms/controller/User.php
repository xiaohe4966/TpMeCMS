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

use app\common\library\Auth;//??????
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

    protected $noNeedLogin = ['*'];//???????????????????????? ???????????????????????????????????????????????????
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


        //?????????????????????????????????
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
        //????????????????????? ???????????????
        // $NeedLogin = ['add_apply','add_evaluate','add_line'];//??????????????????
        // foreach($NeedLogin as $action){
        //     if($action==$this->request->action()){
        //         if(!$this->auth->isLogin()){
        //             $this->error('?????????');
        //         }
        //     }
        // }
       
    }


    /**
     * ??????
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
            $this->error('?????????','/cms/user/login.html?url='.$url);
        }
    }


    /**
     * ???????????? ??????
     * ??????????????????
     * ?????????????????????
     * @return void
     */
    public function add_evaluate()
    {
        // $this->is_login('/cms/user/'.$this->request->action());
        $this->is_login($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"/cms/index/index");
        $params = $this->request->param(); 

        $data = $this->get_field_arr($params,['name','content','email'],true);//????????????
        $data['uid'] = $this->auth->id;

        $res = Db::name('evaluate')->where($data)->find();
        if($res){
            $this->error('???????????????');
        }

        $data['createtime'] = time();
        $data['status'] = 2;//??????:1=??????,2=??????
        $data['title'] = substr($data['content'],10);
        $res = Db::name('evaluate')->insert($data);
        if($res){
            $this->success('????????????');
        }
       
    }

    /**
     * ??????????????????
     *
     * @return void
     */
    public function add_apply()
    {
        if($this->request->IsPost()){

            $this->is_login($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"/cms/index/index");
            
            $params = $this->request->param(); 
            
            $data = $this->get_field_arr($params,['ltd_name','main','hzqj','name','tel','addr','email','content'],true);//????????????
            
            $data['uid'] = $this->auth->id;
        
            $res = Db::name('apply')->where($data)->find();
        
            if($res){
                $this->error('???????????????');
            }

            $data['createtime'] = time();
            $data['status'] = 1;//??????:1=??????,2=??????
            $res = Db::name('apply')->insert($data);
            if($res){
                $this->success('????????????');
            }
        }
    }


    /**
     * ??????????????????
     *
     * @return void
     */
    public function add_line()
    {
        if($this->request->IsPost()){

            $this->is_login($_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:"/cms/index/index");
            
            $params = $this->request->param(); 
            // halt($params);
            $data = $this->get_field_arr($params,['year','sex','card','name','tel','email','work','school'],true);//????????????
            
            $data['uid'] = $this->auth->id;
            $data['classes_ids'] = implode(',',$params['class_ids']);
            $res = Db::name('line')->where($data)->find();
        
            if($res){
                $this->error('???????????????');
            }

            $data['createtime'] = time();
            $data['status'] = 1;//??????:1=??????,2=??????
            $res = Db::name('line')->insert($data);
            if($res){
                $this->success('????????????');
            }
        }
    }





    public function register()
    {
        $url = $this->request->request('url', '', 'trim');
        if ($this->auth->id) {
            $this->success('????????????', $url ? $url : url('user/index'));
        }
        if($this->request->IsPost()){
            $data['mobile'] = $this->request->param('mobile','');
            if(strlen($data['mobile'])!=11){
                $this->error('??????????????????');
            }

            $user = Db::name('user')->where($data)->find();
            if(!$user){
                $this->error('??????????????????');
            }

            if($user['password']){
                $this->error('??????????????????');
            }
            $data['password'] = $this->request->param('password','');
            $data['email'] = $this->request->param('email','');
            

            $captcha =$this->request->param('code','');
             // ??????????????????????????????
            // if (!Sms::check($mobile, $captcha, 'mobilelogin')) {   
            //     $this->error(__('Captcha is incorrect'));
            // }


            if($this->register2($data['mobile'],$data['password'],$data['email'],$data['mobile'])){
                $this->success('????????????','/cms/index/index');
            }

        }
        
        $this->view->assign('cate', '1');//????????????
        return $this->view->fetch();
    }

    /**
     * ?????????????????????
     *
     * @param string $mobile ?????????
     * @param string $self ????????????????????????????????????
     * @return void
     */
    public function send_reg_sms($mobile=null,$self=false)
    {
        if(preg_match("/^1[3-9]{1}\d{9}$/",$mobile)){
            Sms::flush($mobile, 'mobilelogin');

            if($self){
                return true;
            }else{
                return json(['code'=>1,'msg'=>'????????????']);
            }
            
        }else{
            if($self){
                return true;
            }else{
                return json(['code'=>-1,'msg'=>'??????????????????']);
            }
            
        }
       
    }

    /**
     * ????????????
     *
     * @param string $username ?????????
     * @param string $password ??????
     * @param string $email    ??????
     * @param string $mobile   ?????????
     * @param array  $extend   ????????????
     * @return boolean
     */
    public function register2($username, $password, $email = '', $mobile = '', $extend = [])
    {

     
        // ?????????????????????????????????????????????????????????
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
        //?????????????????????????????????,????????????????????????
        // Db::startTrans();
        // try {
            // $user = User2::create($params, true);

            $user2 = Db::name('user')->where('mobile',$mobile)->find();
            Db::name('user')->where('mobile',$mobile)->update($params);

           
            
            
            $this->_user = User2::get($user2['id']);

            //??????Token
            $this->_token = Random::uuid();
            Token::set($this->_token, $user2['id'], 2592000);

            //??????????????????
            $this->_logined = true;

            //?????????????????????
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
     * ?????????????????????????????????
     * @param string $password ??????
     * @param string $salt     ?????????
     * @return string
     */
    public function getEncryptPassword($password, $salt = '')
    {
        return md5(md5($password) . $salt);
    }

    /**
     * ????????????
     */
    public function logout()
    {
        //????????????
        $this->auth->logout();
        $this->success(__('Logout successful'), url('index/index'));
    }

    /**
     * ????????????
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
        //????????????
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
     * ?????????????????????????????????
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
                $this->error('??????????????????');
            }

            $password = Db::name('user')->where('wx_openid',$user['openid'])->value('password');
            if(!$password){
                  // ??????????????????????????????
                // if (!Sms::check($mobile, $captcha, 'mobilelogin')) {   
                //     $this->error(__('Captcha is incorrect'));
                // }
                $this->register_send_template($user['openid'],$mobile);
            }else{
                $this->error('????????????');
            }
            
        }
        return $this->view->fetch();
    }





    /**
     * ?????????????????????
     *
     * @return void
     */
    public function get_wx_gzh_app()
    {

         $config = [
             'app_id' => Config('site.wx_app_id'),//??????????????????appid
             'secret' => Config('site.wx_secret'),//????????????????????????
         
             // ?????? API ??????????????????????????????array(default)/collection/object/raw/???????????????
             'response_type' => 'array',
         ];
         
         //????????????????????????
         $app = Factory::officialAccount($config);
         return $app;
    }

    /**
     * ???????????????????????????????????????????????????????????????
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

        // ???????????????openid?????????
        if (empty(Session::get('wechat_user'))) {
            
            $url = $this->request->domain().'/cms/user/code';//?????????????????? ??????????????????????????????????????????????????????????????????
            $oauth->scopes(['snsapi_userinfo'])->redirect($url);//????????????????????????
            //???Tp??????????????????return
            
            // ??????????????????return?????????????????????action???????????????????????????????????????
            $oauth->redirect()->send();
            exit();
        }
        
        //?????????????????????????????????????????????  ??????????????????????????????????????????????????????
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

    /**
     * ????????????
     *
     * @param string $url ?????????????????????
     * @return void
     */
    public function wx_login($url='/cms/index/index')
    {
        $this->get_user($url);//????????????????????????????????????

        //??????????????????????????????????????????get_user ???????????????????????????
        $this->redirect($url);
        exit();
    }





    /* 
    * @Description: ???????????????code??????????????????????????????
    * @return: 
    */   
    public function code()
    {
        $app = $this->get_wx_gzh_app();
        $oauth = $app->oauth;
       
        $user = $oauth->user();
        $wechat_user = $user->original;
         // halt($user->original);//????????????????????????

        Session::set('wechat_user',$wechat_user);//????????????????????? ??????????????????

        $Xiaohe = new Com;
        $Xiaohe->update_user_data($wechat_user,Session::get('sharer'));//??????????????????????????????

        $user_data = $Xiaohe->openid_get_user($wechat_user['openid']);//?????????openid??????????????????

        if($user_data){
            $ret = $this->auth->direct($user_data['id']);//?????????????????????
        }
        
        //??????????????????????????????
        $re_url = Session::get('re_url');
        if($re_url){
            Session::set('re_url',null);//??????????????????
            header("Location: ".$this->request->domain().$re_url);
        }else{
            header("Location: ".$this->request->domain()."/cms/user/index");
        }

    }


    /**
     * ????????????????????????
     *
     * @param string $com_openid ??????????????????openid
     * @param string $keyword1 ??????1
     * @param [type] $keyword2 ??????2
     * @param string $path ?????????????????????
     * @return void
     */
    public function register_send_template($com_openid='o0kt76ZCpj8g2HhO8IctpJIwWyu4',$mobile=null,$keyword2=null,$url=null,$name=null)
    {

        if(!$keyword2){
            $keyword2 = date('Y???m???d???H:i:s');
        }

        if(!$url){
            $url = $this->request->domain().'/cms/user/register';
        }

        if(!$name){
            $name = 'XX';
        }
        $app = $this->get_wx_gzh_app();    
        $data = $app->template_message->send([
            'touser' => $com_openid,//??????openid,
            'template_id' => 't_ZOr6SGeoj0VFa3TukMjSONpR_h6lpNDGjhpnI0M2s',
            'url' => $url,//???????????????????????????????????????
     
            'data' => [
                'first' =>['???????????????????????????????????????????????????','#173177'],
                'keyword1' =>[$mobile,'#223177'],//??????
                'keyword2' =>[$keyword2,'#333177'],//??????

                'remark' =>['??????????????????????????????????????????','#173199'],

            ],
        ]);
        if($data['errmsg']=='ok'){
            $this->update_user_mobile($com_openid,$mobile);
            $this->success('?????????????????????','/cms/user/register');
            
        }else{
            $this->error('???????????????????????????????????????????????????????????????'.$data['errmsg'],'/cms/user/bind',-1,16);
        }
    }


    public function get_jssdk($url=null)
    {
        $app = $this->get_wx_gzh_app();
        if(!$url)
        $url = $this->request->domain().'/cms/user/????????????';
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
