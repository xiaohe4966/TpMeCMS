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
 * ???????????????
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
     * ?????????????????????????????????
     * @ApiSummary (???????????????????????????/api/wechatofficial/message)
     * @return void
     */
    public function message()
    {
        //????????????????????????https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index
        $app = $this->get_wx_gzh_app();

        //????????????????????????
        // $app->server->push(function($message){
        //     return '???????????????TpMeCms??????';
        // });


        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':                    
                    //????????????????????? ???????????? {"ToUserName":"gh_3c8ce7c6bff0","FromUserName":"oS3Dn52old15oY69sD0ldnhcWqoQ","CreateTime":"1638167541","MsgType":"event","Event":"subscribe","EventKey":null}
                    //???????????? ???????????????????????????   ????????????????????????->???????????????EncodingAESKey???
                    if($message['Event']=='subscribe'){ //?????????????????????
                        try {
                            $data['subscribe_time'] = $message['CreateTime'];
                            $data['status'] = '2';//????????????:1=?????????,2:?????????,3=?????????
                            //???????????????
                            $wechat_user = Db::name('wechat_user')->where('openid',$message['FromUserName'])->find();
                            if($wechat_user){
                                Db::name('wechat_user')->where('openid',$message['FromUserName'])->update($data);
                            }else{
                                $data['type_status'] = '1';//??????:1=???????????????,2=???????????????
                                $data['openid'] = $message['FromUserName'];
                                $data['createtime'] = $message['CreateTime'];
                                Db::name('wechat_user')->insert($data);
                            }
                        } catch (\Throwable $th) {
                            
                        }


                    }elseif($message['Event']=='unsubscribe'){  //??????????????????
                        //???????????????{\"ToUserName\":\"gh_3c8ce7c6bff0\",\"FromUserName\":\"oS3Dn52old15oY69sD0ldnhcWqoQ\",\"CreateTime\":\"1638167529\",\"MsgType\":\"event\",\"Event\":\"unsubscribe\",\"EventKey\":null}
                        try {
                            $data['unsubscribe_time'] = $message['CreateTime'];
                            $data['status'] = '3';//????????????:1=?????????,2:?????????,3=?????????
                            //???????????????
                            $wechat_user = Db::name('wechat_user')->where('openid',$message['FromUserName'])->find();
                            if($wechat_user){
                                Db::name('wechat_user')->where('openid',$message['FromUserName'])->update($data);
                            }else{  //???????????????????????????????????????
                                $data['type_status'] = '1';//??????:1=???????????????,2=???????????????
                                $data['openid'] = $message['FromUserName'];
                                // $data['createtime'] = $message['CreateTime'];
                                Db::name('wechat_user')->insert($data);
                            }
                        } catch (\Throwable $th) {
                            
                        }
                    }
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
                case 'text':
                    return 'TpMsCms??????????????????'.json_encode($message);//'TpMsCms????????????????????????????????????'.$message['Content'];
                    break;
                case 'image':
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
                case 'voice':
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
                case 'video':
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
                case 'location':
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
                case 'link':
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
                case 'file':
                    return 'TpMsCms??????????????????'.json_encode($message);
                //????????????
                default:
                    return 'TpMsCms??????????????????'.json_encode($message);
                    break;
            }
        

        });

        //????????????????????????????????????
        // $app->server->push(function($message) use($app){

        //     $user = $app->user->get($message['FromUserName']);

        //     return '?????????'.$user['nickname'].'??????????????????'.$message['Content'];
        // });
     
        $response = $app->server->serve();
        $response->send();
  
    }

    /**
     * ??????????????????????????????
     *
     * @return void
     */
    public function get_user_list()
    {
        $app = $this->get_wx_gzh_app();
        $res = $app->user->list();
        $this->success('??????????????????',$res);
    }







}