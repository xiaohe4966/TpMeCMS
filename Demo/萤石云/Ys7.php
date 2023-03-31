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
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2022/08/30
//      |_|                                         | TpMeCMS

namespace app\api\controller;
use app\common\controller\Api;
use think\Config;
use think\Db;

/**
 * 萤石云
 */
class Ys7 extends Api
{

    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];
    protected $configFile = 'ys7.ini';//配置文件名 自行定义文件路径
    protected $accessToken = '';

  

    public function __construct($appKey=null,$appSecret=null)
    {
        parent::__construct();
        if(empty($appKey) || empty($appSecret)){
            $this->appKey = Config::get('site.ys7_AppKey');
            $this->appSecret = Config::get('site.ys7_Secret');
        }
        
        $this->get_access_token(true);
    }



    /**
     * 获取设备列表
     *
     * @return void
     */
    public function get_device_list()
    {

       $url = 'https://open.ys7.com/api/lapp/device/list';
       $data['accessToken'] = $this->accessToken;
       $json = $this->post($url,$data);
       $res = json_decode($json,true);
       if($res['code']=='200'){
            //逻辑代码 //自行修改
            $list = $res['data'];
            $jia = 0;
            $xin = 0;
            foreach ($list as $key => $val) {
                //判断设备是否存在
                $device = Db::name('ys7')->where('deviceSerial',$val['deviceSerial'])->find();
                if(!$device){
                    $ys7['deviceSerial'] = $val['deviceSerial'];
                    $ys7['deviceName'] = $val['deviceName'];
                    $ys7['deviceType'] = $val['deviceType'];
                    $ys7['status'] = $val['status'];
                    $ys7['defence'] = $val['defence'];
                    $ys7['deviceVersion'] = $val['deviceVersion'];
                    $ys7['addTime'] = $val['addTime']/1000;
                    $ys7['updateTime'] = $val['updateTime']/1000;


                    $ys7['netAddress'] = $val['netAddress'];
                    $ys7['riskLevel'] = $val['riskLevel'];
                    $ys7['parentCategory'] = $val['parentCategory'];

                    Db::name('ys7')->strict(false)->insert($ys7);
                    $jia++;
                }else{
                    $ys7['netAddress'] = $val['netAddress'];
                    $ys7['status'] = $val['status'];
                    Db::name('ys7')->where('deviceSerial',$val['deviceSerial'])->strict(false)->update($ys7);
                    $xin++;
                }
            }
            $this->success("加入{$jia}更新{$xin}",$list);
            //逻辑代码 //自行修改
       }else{
           $this->error($res['code'],$res);
       }
    }

    

    /**
     * 获取流量
     *
     * @return void
     */
    public function get_traffic_user_total()
    {
        $url = 'https://open.ys7.com/api/lapp/traffic/user/total';
        $data['accessToken'] = $this->accessToken;

        $json = $this->post($url,$data);
        $res = json_decode($json,true);
      
        if($res['code']=='200'){
            $this->success($res['msg'],$res);
        }else{
            $this->error($res['code'],$res);
        }
    }


    /**
     * 获取设备播放地址
     *
     * @param string $deviceSerial 设备号
     * @return void
     */
    public function get_device_live_url($deviceSerial='VXHE4966')
    {
        $url = 'https://open.ys7.com/api/lapp/v2/live/address/get';
        $data['accessToken'] = $this->accessToken;
        $data['deviceSerial'] = $deviceSerial;
        $data['protocol'] = '4';//流播放协议，1-ezopen、2-hls、3-rtmp、4-flv，默认为1

        $json = $this->post($url,$data);
        $res = json_decode($json,true);
      
        if($res['code']=='200'){
            $this->success($res['msg'],$res['data']);
        }else{
            $this->error($res['code'],$res);
        }
    }

    /**
     * 旧接口获取设备列表
     *
     * @return void
     */
    public function get_device_list_old()
    {
        $url = 'https://open.ys7.com/api/lapp/live/video/list';
        $data['accessToken'] = $this->accessToken;
        // $data['deviceSerial'] = $deviceSerial;
        // $data['protocol'] = '4';//流播放协议，1-ezopen、2-hls、3-rtmp、4-flv，默认为1

        $json = $this->post($url,$data);
        $res = json_decode($json,true);
      
        if($res['code']=='200'){
            $this->success($res['msg'],$res['data']);
        }else{
            $this->error($res['code'],$res);
        }
    }


    /**
     * 旧接口获取设备信息及播放地址
     *
     * @return void
     */
    public function get_device_data_old($deviceSerial=null)
    {
        $url = 'https://open.ys7.com/api/lapp/live/video/list';
        $data['accessToken'] = $this->accessToken;
        $json = $this->post($url,$data);
        $res = json_decode($json,true);
      
        if($res['code']=='200'){
            foreach ($res['data'] as $key => $val) {
                if($val['deviceSerial']==$deviceSerial){
                    $this->success($deviceSerial,$val);
                }
            }
        }else{
            $this->error($res['code'],$res);
        }

        $this->error('没有该设备号'.$deviceSerial,$res);
    }


    /**
     * 获取access_token
     *
     * @return void
     */
    public function get_access_token($self=false)
    {
        if(file_exists($this->configFile)){
            $config = json_decode(file_get_contents($this->configFile),true);
         
            if(($config['expireTime']-86400)<time()){
                $this->update_access_token();
            }else{
               
                $this->accessToken = $config['accessToken'];
            }
        }else{
            $this->update_access_token();
        }

        if(!$self)
        $this->success('ok',$this->accessToken);
        
    }

    /**
     * 更新token
     *
     * @return void
     */
    protected function update_access_token()
    {
        $url = 'https://open.ys7.com/api/lapp/token/get';
        $data['appKey'] = $this->appKey;
        $data['appSecret'] = $this->appSecret;
        $json = $this->post($url,$data);
        $res = json_decode($json,true);
        if($res['code']=='200'){
            $this->accessToken = $res['data']['accessToken'];
            $expireTime = $res['data']['expireTime'];

            file_put_contents($this->configFile,json_encode(['accessToken'=>$this->accessToken,'expireTime'=>$expireTime/1000]));
        }else{
            $this->error($res['code'],$res);
        }
    }

    /**
     * post
     *
     * @param stting $url 地址
     * @param array $data
     * @return void
     */
    protected function post($url,$data=[])
    {
        $连接 = new \GuzzleHttp\Client;
        // $返回 = $连接->post($url,$data);
        $返回 = $连接->request('POST', $url, [
            'form_params' => $data
        ]);
        return $返回->getBody()->getContents();
    }


}
