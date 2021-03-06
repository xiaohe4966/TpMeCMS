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
//   |_|| .__/|_|  |_|\___|  \____|_|  |_|____/     | DATETIME: 2021/06/25
//      |_|                                         | TpMeCMS


namespace app\cms\controller;

use app\common\controller\Frontend;
use think\Db;
use think\Config;
use fast\Tree;

class Cms extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    static public $nav;


    public function _initialize()
    {   
     

        parent::_initialize();
        self::$nav = $this->get_nav_list();

        $this->view->assign('nav', self::$nav);//?????????
        if($this->auth->id){    //????????????????????????
            $this->view->assign('user', $this->auth->getUserinfo());//??????
        }
        
    }

    /**
     * ??????
     *
     * @return void
     */
    public function index()
    {
        
        return $this->view->fetch();
    }


    /**
     * ?????????????????????
     *
     * @param integer $limit ??????15???
     * @param integer $pid ?????????id????????????0??????
     * @return void
     */
    protected function get_nav_list($pid = 0,$limit=15)
    {
        $nav_list = Db::name('cate')
            ->where('isnav_switch',1)
            ->where('status','1')
            ->order('weigh desc')
            ->select();

        $nav_list_ids = Db::name('cate')
            ->where('isnav_switch',1)
            ->where('status','1')
            ->order('weigh desc')
            ->column('parent_id,name','id');

        foreach($nav_list as &$val){

            if(empty($val['outlink'])){
                //??????????????????
                if(Config::get('site.route_switch')){
                    $val['url'] = '/'.$val['diyname'];
                }else{
                    $val['url'] = '/cms/index/cate/id/'.$val['id'];
                }
                
            }else{
                $val['url'] = $val['outlink'];
            }
        }


        $Tree = Tree::instance();
        $Tree->init($nav_list, 'parent_id');
        $list = $Tree->getTreeArray($pid, 'parent');

        
        
        return $list;
    }


    /**
     * ??????????????????
     *
     * @param int $id ??????id
     * @return void
     */
    public function get_cate_data($id)
    {
        $cate = Db::name('cate')->find($id);
        if(!$cate){
            $this->error('???????????????');
        } 

        $nav_list_ids = Db::name('cate')
            ->where('isnav_switch',1)
            ->where('status','1')
            ->order('weigh desc')
            ->column('parent_id,name','id');
        $cate['is_top'] = $this->getCateTop($cate);//????????????id

        return $cate;
    }


    /**
     * ??????????????????id
     * 
     * @param array $cate ????????????
     * @return void
     */
    public function getCateTop($cate)
    {
        if($cate['parent_id']>0){
            $p_cate = Db::name('cate')->find($cate['parent_id']);
            return $this->getCateTop($p_cate);
        }else{
            return $cate['id'];
        }
    }

    /**
     * ????????????????????????
     *
     * @param int $id ??????id
     * @param int $page ???
     * @param int $limit ????????????
     * @param string $field ????????????
     * @param string $sort ??????{???(asc)???(desc)}
     * @return void
     */
    public function get_cate_art_list($id=null,$page=1,$limit=null,$field='weigh',$sort='desc')
    {
        $where = null;
        if($id){
            $where['id'] = ['=',$id];

            if(!$limit){
                $pagesize = $this->get_cate_data($id)['pagesize'];
                $limit = $pagesize?$pagesize:10;
            }
        }else{
            if(!$limit)
                $limit = 10;
        }
        
        $list = Db::name('article')
            ->where($where)
            ->page($page,$limit)//???????????????
            ->order($field.' '.$sort)//??????
            ->select();
       
        return $list;
    }




    /**
     * ??????????????????
     *
     * @param array $cate ????????????
     * @return void
     */
    public function get_page_data($cate)
    {
        return  Db::name('page')
            ->where('cate_id',$cate['id'])
            ->find();        
    }

    /**
     * ????????????????????????
     *
     * @param int $id ??????id
     * @param int $page ???
     * @param int $limit ????????????
     * @param string $field ????????????
     * @param string $sort ??????{???(asc)???(desc)}
     * @return void
     */
    public function get_cate_art_list2($cate,$page=1,$field='weigh',$sort='desc')
    {
        $where['status'] = '1';//???????????????1?????????

        //????????????get???post????????????????????????????????????
        $params = $this->request->param();
        if(isset($params['tag'])){
            $where['tags'] = ['like',"%".$params['tag']."%"];
        }

        if(isset($params['search']) && !empty($params['search'])){
            $where['title|content|seotitle|keywords|description|memo'] = ['like',"%".$params['search']."%"];
        }


        //?????????????????????page??????
        if(isset($params['page'])){
            $page = $params['page'];
        }

        //??????????????????????????????
        if($cate['parent_id']>0){
            //????????????????????????????????????(?????????????????????)
            $is_cate_id = Db::query("Describe ".$cate['table_name']." cate_id");
            if($is_cate_id){
                $where['cate_id'] = $cate['id'];
            }

            //????????????????????????????????????(?????????????????????)
            $is_deletetime = Db::query("Describe ".$cate['table_name']." deletetime");
            if($is_deletetime){
                $where['deletetime'] = NULL;//????????????????????????????????????????????????????????????????????????
            }
            
        }
      
        $limit = $cate['pagesize'];//????????????
        $list = Db::table($cate['table_name'])
                ->where($where)
                ->order($field.' '.$sort)//??????
                ->page($page,$limit)
                ->limit($limit)
                ->select();

        //*******************?????????????????????
        //????????????
        $page_data['count']= Db::table($cate['table_name'])
                ->where($where)
                ->count();
        //?????????                ?????????1
        $page_data['pages'] = intval(ceil($page_data['count']/$limit));//??????????????????2021-07-02 10:44:43
       
        //????????????
        $page_data['limit'] = $limit;

        //????????????
        $page_data['page'] = $page;

        //????????????  ????????????list
        $page_data['list'] = null;
        for ($i=1; $i <=$page_data['pages'] ; $i++) {      
            $page_data['list'][] = ['url'=>$this->get_page_url($cate['id'],$i) , 'num'=>$i];
        }

        //?????????
        $page_data['first_page'] = ['num'=>1,'url'=>$this->get_page_url($cate['id'] , 1)]; //2021-07-02 10:44:43
        
        //?????????
        $page_data['prev_page'] = $page>1 ? ['num'=>$page-1,'url'=>$this->get_page_url($cate['id'] , $page-1)]: null;        
        
        //?????????
        $page_data['next_page'] = $page_data['pages']>$page ? ['num'=>$page+1,'url'=>$this->get_page_url($cate['id'] , $page+1)] : null;

        //????????????
        $page_data['last_page'] = $page_data['pages']?['num'=>$page_data['pages'],'url'=>$this->get_page_url($cate['id'] , $page_data['pages'])] : null;//2021-07-02 10:44:43
        
        //?????????????????????????????? ??????2021-06-23 22:09:59
        // halt($page_data);

        $this->view->assign('page' , $page_data);
        $this->view->assign('search' , $this->request->param('search',''));
        
        //*******************???????????????????????????




        foreach ($list as $key => &$val) {
            //???????????? ??????????????????????????????????????????????????????
            if(!isset($val['outlink']) || empty($val['outlink'])){
                $val['url'] = $this->get_show_url($cate['id'],$val['id']);
            }else{
                $val['url'] = $val['outlink'];
            }
           
        }
        

        return $list;
    }


    /**
     * ???????????????
     *
     * @param string $table_name ??????
     * @return void
     */
    public function get_limit($table_name)
    {
        $cate = Db::name('cate')->where('table_name',$table_name)->find();
        if(!$cate){
            $this->error('??????????????????');
        }
        return $cate['pagesize'];
    }



    /**
     * ??????????????????
     *
     * @param string $table_name ??????
     * @return void
     */
    public function get_cate_data_By_table_name($table_name)
    {
        $cate = Db::name('cate')->where('table_name',$table_name)->find();
        if(!$cate){
            $this->error('??????????????????');
        }
        return $cate;
    }

    /**
     * ??????????????????
     *
     * @param string $cate_id ??????id
     * @param int $id ??????id
     * @return void
     */
    public function get_show_url($cate_id,$id)
    {
        //??????????????????????????????url
        if(Config::get('site.route_switch')){
            $cate = Db::name('cate')->find($cate_id);
            // return '/cms/index/show/cate_id/'.$cate_id.'/id/'.$id;
            return '/'.$cate['diyname'].'_show/'.$id;
        }else{
            return '/cms/index/show/cate_id/'.$cate_id.'/id/'.$id;
        }
    }


    /**
     * ??????????????????????????????????????????
     *
     * @param array $arr ??????
     * @param array $fields ???????????????
     * @param array $NotNull ????????????????????????
     * @return void
     */
    protected function get_field_arr($arr,$fields,$NotNull=false)
    {
        if(!is_array($fields)){
             //?????????
             $fields = explode(',',$fields);
        }
        
        $new = array();
        foreach($fields as $key=>$val){
        
                if(isset($arr[$val])){
                    if(!$arr[$val])$this->error('???????????????');
                    $new[$val] = $arr[$val];

                }elseif($NotNull){
                    $this->error('????????????????????????');
                }
            
        }
        return $new;
    }

    /**
     * ??????????????????
     *
     * @param int $cate_id ??????id
     * @param int $page ??????
     * @return void
     */
    public function get_page_url($cate_id,$page)
    {
        if(Config::get('site.route_switch')){
            $cate = Db::name('cate')->find($cate_id);
            // return $this->request->domain().'/cms/index/cate/id/'.$cate_id.'/page/'.$page;
            return $this->request->domain().'/'.$cate['diyname'].'/page/'.$page;
        }else{
            return $this->request->domain().'/cms/index/cate/id/'.$cate_id.'/page/'.$page;
        }
        
    }
}
