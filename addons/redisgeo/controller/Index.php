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
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/07/1
//      |_|                                         | TpMeCMS


namespace addons\redisgeo\controller;

use think\addons\Controller;
use addons\redisgeo\library\Redis;

class Index extends Controller
{
    

    public function _initialize()
    {
        parent::_initialize();
        $this->redis = $this->getRedis();
    }

    /**
     * 首页说明
     *
     * @return void
     */
    public function index()
    {   
        $lng = $this->request->param('lng',120.00001);//经度
        $lat = $this->request->param('lat',31.60001);//纬度
        $m   = $this->request->param('m',10000000); //附近多少米
        $limit = $this->request->param('limit',10000);  //取前多少个数量
        $sort = 'ASC';// 降序    可选DESC  ASC



        //获取附近信息列表   返回的只有距离和id
        $geo_list = $this->get_nearby_list($lng,$lat,$m,'m',$limit,'ASC');

        if($geo_list){
            foreach ($geo_list as $key => &$geo) {
                $geo['geo'] = $this->get_data($geo[0])[0];
            }
        }
        // echo '<pre>';
        // var_dump($geo_list);
        $this->view->assign(['lng'=>$lng,'lat'=>$lat,'m'=>$m,'limit'=>$limit,'sort'=>$sort]);
        $this->view->assign('geo_list', $geo_list);
        return $this->view->fetch();
    }

    /**
     * 测试信息
     *
     * @return void
     */
    public function test()
    {
        // var_dump($this->del_geo_all());//删除全部坐标信息 非必要慎用

        
        if($this->request->isPost()){
            $params = $this->request->param();

            switch ($params['act']) {
                case 'add_geo':
                    $this->add_geo($params['lng'],$params['lat'],$params['id']);
                    $this->success('添加成功');
                    break;
                case 'del_geo':
                    $this->del_geo($params['id']);
                    $this->success('删除成功');
                    break;

                case 'query_distance':
                    $km = $this->get_coordinate_distance($params['lng1'],$params['lat1'],$params['lng2'],$params['lat2']);
                    $this->success($km.'km');
                    break;
                default:
                    # code...
                    break;
            }
        }
        // //生成10个坐标添加
        // for ($i=1; $i <=10; $i++) { 
        //     $val['lng'] = 120.320011+$i*0.0005;
        //     $val['lat'] = 31.68000+$i*0.0005;
        //     $val['id']  = $i;
        //     $this->add_geo($val['lng'],$val['lat'],$val['id']);
        // }

        // //删除id为1的坐标
        // // $this->del_geo(1);

        // //找这个坐标的附近400米的信息数量8个
        // $val['lng'] = 120.3200011;
        // $val['lat'] = 31.68540;

        // $list = $this->get_nearby_list($val['lng'],$val['lat'],400,'m',8,'ASC');

        
        // halt($list);
    }



    /**
     * 获取redis对象
     *
     * @return void
     */
    public function getRedis() {

        if (!isset($GLOBALS['REDIS'])) {
            $GLOBALS['REDIS'] = (new Redis())->getRedis();
        }
        return $GLOBALS['REDIS'];
    }

    /**
     * 获取附近的列表
     *
     * @param float $lng 经度
     * @param float $lat 纬度
     * @param int $m 距离多远
     * @param string $unit 单位:m米,km千米,mi英里,ft英尺
     * @param int $limit 需要的数量
     * @param string $sort 排序ASC,DESC
     * @param string $name 用于分类名
     * @return void
     */
    public function get_nearby_list($lng,$lat,$m=1000,$unit='m',$limit=5,$sort='ASC',$name='geo:tpmecms')
    {   

        if($limit<1){
            $limit = 10;
        }else{
            $limit = intval($limit);
        }
       

        $list = $this->redis->georadius($name,floatval($lng),floatval($lat),floatval($m),$unit,['WITHDIST', 'COUNT' => $limit, $sort]);
        return $list;
       
    }


    /**
     * 添加坐标(编辑这个id坐标也用这个)
     *
     * @param string $lng 经度
     * @param string $lat 纬度
     * @param int $m 距离多远
     * @param int $id 数据id
     * @param string $name 用于分类名
     * @return void
     */
    public function add_geo($lng,$lat,$id,$name='geo:tpmecms')
    {   
        //提示：如果有一样的$id，新坐标的会覆盖掉原来的坐标
        $this->redis->geoadd($name,$lng,$lat,$id);
    }

    /**
     * 获取信息
     *
     * @param string $id 添加时候的信息
     * @return void
     */
    public function get_data($id,$name='geo:tpmecms')
    {
        $data = $this->redis->geopos($name,$id);
        return $data;
    }

    /**
     * 删除某个坐标
     *
     * @param int $id 数据id
     * @param string $name 用于分类名
     * @return void
     */
    public function del_geo($id,$name='geo:tpmecms')
    {   
        
        $this->redis->zrem($name,$id);
    }   

    /**
     * 删除全部地理坐标
     * 
     * @param string $name 用于分类名
     * @return void
     */
    public function del_geo_all($name='geo:tpmecms')
    {
        
        $nums = $this->redis->zcard($name);
        for ($i=0; $i <= $nums; $i++) { 
            $this->del_geo($i+1);
        }
        return $nums;
    }


    /**
     * 获取2个坐标距离(km)
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


  

}
