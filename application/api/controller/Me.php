<?php

namespace app\api\controller;
use app\api\controller\Tpmecms;

use think\Db;




/**
 * 我的
 */
class Me extends Tpmecms
{

    protected $noNeedLogin = ['get_openid','get_user_mobile'];
    protected $noNeedRight = ['*'];


    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 微信小程序获取openid
     * @ApiSummary (里面有token，提交接口都要带上)
     * @param string $code 小程序code
     * @return void
     */
    public function get_openid($code=null){
        //用微信小程序的code来获取用户的openid

        $app = $this->get_app();
      
        $user = $app->auth->session($code);
        // halt($user);
        if(!empty($user['errcode'])){
            halt($user);
        }
        //如果上面不报错，就能获取到小程序的openid
        $openid = $user['openid'];
        //下面就是插入或找用户的信息

        $data = array();
        $data['openid'] = $openid;
        $user_data = Db::name('user')->where('openid',$openid)->find();
        if($user_data){
            $ret = $this->auth->direct($user_data['id']);//直接登录该用户
            $data = $this->auth->getUserinfo(); //获取当前登录用户的信息
            // $data['uid'] = $user_data['id'];


            // $data['logintime'] = time();
            //如果有开放平台就更新unionid
            if(empty($user_data['unionid']) && (!empty($user['unionid']))){
                Db::name('user')->where('openid',$openid)->update(['unionid'=>$user['unionid']]);
            }
        }else{
            //插入新用户数据  
            $indata['openid'] = $openid;
            $indata['createtime'] = time();
            $indata['jointime'] = $indata['createtime'];
            $indata['joinip'] = $this->request->ip();
            $indata['logintime'] = time();
            $indata['status'] = 'normal';

            // $indata['stateswitch'] = 1;

            //开放平台
            if(!empty($user['unionid'])){
                $indata['unionid'] = $user['unionid'];
            }
            
     
            $id = Db::name('user')->insertGetId($indata);
            $ret = $this->auth->direct($id);//直接登录该用户
            $data = $this->auth->getUserinfo();//获取当前登录用户的信息
        }
        
        $this->success('ok',$data);
    }




    /**
     * 修改小程序用户的昵称和头像
     *
     * @param string $nickname 昵称
     * @param string $head_img 头像
     * @return void
     */ 
    public function update_user_head($nickname,$head_img)
    {   
        $u['id'] = $this->auth->id;
        $data['nickname'] = $nickname;
        $data['avatar'] = $head_img;
        $res = Db::name('user')->where($u)->update($data);
        if($res){
            $this->success('修改成功');
        }
        $this->success('修改失败');
    }


    /**
     * 获取用户信息
     *
     * @return void
     */
    public function get_user()
    {
        $list['user'] = Db::name('user')->find($this->auth->id);
        $this->success('ok',$list);
    }

   


    /**
     * 小程序授权获取手机号
     *
     * @param string $code 小程序code
     * @param string $encryptedData 加密数据
     * @param string $iv 加密算法的初始向量
     * @return void
     */    
    public function get_user_mobile($code=null,$encryptedData=null,$iv=null)
    {   
        if(!$code || !$encryptedData || !$iv){
            $this->error('请查看微信小程序授权获取手机号接口文档：https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/getPhoneNumber.html');
        }
        $app = $this->get_app();//获取微信小程序app
        $user = $app->auth->session($code);
        
        $session = $user['session_key'];//$app->sns->getSessionKey($code);
        $decryptedData = $app->encryptor->decryptData($session, $iv, $encryptedData);

        //修改手机号
        Db::name('user')->where('openid',$user['openid'])->update(['mobile'=>$decryptedData['phoneNumber']]);
        
        return $decryptedData['phoneNumber'];
        // halt($decryptedData);
    }




    /**
     * 获取我的邀请人数
     * 
     * @param boolean $self 掉接口不需要传
     * @return void
     */
    public function user_get_my_share_num($self=false)
    {
        
        $num = Db::name('user')
                    ->where('pid',$this->auth->id)
                    ->count();
        if($self)
            return $num; 

        $this->success('👌',$num);   
    }


    /**
     * 修改上级
     * 
     * @param string $pid 用户id
     * @return void
     */
    public function update_pid($pid)
    {
        
        $user = Db::name('user')->find($this->auth->id);
        if($user){
            if(!empty($user['pid'])){
                $this->error('已经有上级'.$user['pid']);
            }
        }

        
        $res_up = Db::name('user')->where('id',$this->auth->id)->update(['pid'=>$pid]);
        if($res_up){
            $this->success('绑定成功');
        }
    } 

    /**
     * 获取我的邀请列表
     * 
     * @param integer $page 1
     * @param integer $limit 200
     * @return void
     */
    public function get_share_list($page = 1,$limit = 200)
    {

        $list = Db::name('user')
                    ->where('pid',$this->auth->id)
                    ->order('createtime desc')
                    ->field([ '*,FROM_UNIXTIME(createtime,"%Y-%m-%d %H:%m:%s") as date'])  //创建时间戳更改年月日
                    ->select();
        $this->success(sizeof($list), $list);
    }


    // /**
    //  * 获取我的分享二维码
    //  * @ApiInternal()
    //  * @return void
    //  */
    // public function get_my_share_qr()
    // {
    //     $Qrcode = new Qrcode();
    //     // $Qrcode->user_get_my_qr();
    //     return $Qrcode->user_get_my_qr($this->auth->id);
    // }







}