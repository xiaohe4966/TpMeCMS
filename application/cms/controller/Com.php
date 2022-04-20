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
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2022/04/20
//      |_|                                         | TpMeCMS

namespace app\cms\controller;

// use app\common\controller\Frontend;
use app\cms\controller\Cms;
use think\Db;

class Com extends Cms
{

    protected $noNeedLogin = ['*'];//自己需要写的方法 这个是不需要登陆的其他则为需要登陆
    protected $noNeedRight = '*';



    public function _initialize()
    {   
        parent::_initialize();       
    }

    /**公共方法  更多通用方法写在里面
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
     * 
     * 更新用户信息
     * 
     * @ApiInternal()
     */
    public function update_user_data($data,$sharer=null)
    {
        $user = Db::name('user')->where('wx_openid',$data['openid'])->find();
        
        $in_data['logintime'] = time();//登陆时间
        if($user){
            $in_data['avatar'] = $data['headimgurl'];
            $in_data['nickname'] = $data['nickname'];
            if($sharer && empty($user['pid'])){
                $in_data['pid'] = base64_decode($sharer);
               
                
            }
            
            Db::name('user')->where('wx_openid',$data['openid'])->update($in_data);
            //修改数据；
        }else{
            
            //分享者
            if($sharer){
                $in_data['pid'] = base64_decode($sharer);
                
                

            }
            $in_data['wx_openid'] = $data['openid'];
            $in_data['avatar'] = $data['headimgurl'];
            $in_data['nickname'] = $data['nickname'];
            $in_data['status'] = 'normal';
            $new_uid = Db::name('user')->insertGetId($in_data);
            //插入数据
       
        }
    }





    /**
     * 获取用户的openid
     * @ApiInternal()
     *
     * @param array $staffs
     * @return void
     */
    public function get_user_openids($uids = null)
    {
        if(is_array($uids)){
            $list = Db::name('user')->where('id','in',$uids)->column('wx_openid');
        }else{
            $list = Db::name('user')->where('id',$uids)->value('wx_openid');
        }

        return $list;
    }



    /**
     * openid获取用户信息
     * @ApiInternal()
     * @param string $openid
     * @return void
     */
    public function openid_get_user($openid)
    {
       $user =  Db::name('user')->where('wx_openid',$openid)->find();
       return $user;
    }
    

}
