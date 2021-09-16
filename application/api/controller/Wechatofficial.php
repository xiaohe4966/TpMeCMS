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
 * 微信公众号
 */
class Wechatofficial extends Tpmecms
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];


    public function _initialize()
    {
        parent::_initialize();
    }




    /**
     * 微信公众号接口配置信息
     * @ApiSummary (接口配置信息：网站/api/wechatofficial/message)
     * @return void
     */
    public function message()
    {
        //微信测试号地址：https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index
        $app = $this->get_wx_gzh_app();

        //始终返回固定内容
        // $app->server->push(function($message){
        //     return '本代码是由TpMeCms提供';
        // });


        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return 'TpMsCms收到事件消息'.json_encode($message);
                    break;
                case 'text':
                    return 'TpMsCms收到文字消息'.json_encode($message);//'TpMsCms收到文字消息你发的内容：'.$message['Content'];
                    break;
                case 'image':
                    return 'TpMsCms收到图片消息'.json_encode($message);
                    break;
                case 'voice':
                    return 'TpMsCms收到语音消息'.json_encode($message);
                    break;
                case 'video':
                    return 'TpMsCms收到视频消息'.json_encode($message);
                    break;
                case 'location':
                    return 'TpMsCms收到坐标消息'.json_encode($message);
                    break;
                case 'link':
                    return 'TpMsCms收到链接消息'.json_encode($message);
                    break;
                case 'file':
                    return 'TpMsCms收到文件消息'.json_encode($message);
                //其它消息
                default:
                    return 'TpMsCms收到其它消息'.json_encode($message);
                    break;
            }
        

        });

        //可以使用闭包获取用户信息
        // $app->server->push(function($message) use($app){

        //     $user = $app->user->get($message['FromUserName']);

        //     return '你好！'.$user['nickname'].'你发的内容：'.$message['Content'];
        // });
     
        $response = $app->server->serve();
        $response->send();
  
    }

    /**
     * 获取关注微信用户列表
     *
     * @return void
     */
    public function get_user_list()
    {
        $app = $this->get_wx_gzh_app();
        $res = $app->user->list();
        $this->success('关注用户列表',$res);
    }







}