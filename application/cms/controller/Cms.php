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

        $this->view->assign('nav', self::$nav);//导航栏
        if($this->auth->id){    //页面加入用户信息
            $this->view->assign('user', $this->auth->getUserinfo());//用户
        }
        
    }

    /**
     * 首页
     *
     * @return void
     */
    public function index()
    {
        
        return $this->view->fetch();
    }


    /**
     * 获取导航栏列表
     *
     * @param integer $limit 默认15个
     * @param integer $pid 上级的id（默认为0全部
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
                $val['url'] = '/cms/index/cate/id/'.$val['id'];
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
     * 获取栏目信息
     *
     * @param int $id 栏目id
     * @return void
     */
    public function get_cate_data($id)
    {
        $cate = Db::name('cate')->find($id);
        if(!$cate){
            $this->error('没有该栏目');
        } 

        $nav_list_ids = Db::name('cate')
            ->where('isnav_switch',1)
            ->where('status','1')
            ->order('weigh desc')
            ->column('parent_id,name','id');
        $cate['is_top'] = $this->getCateTop($cate);//顶级栏目id

        return $cate;
    }


    /**
     * 获取栏目顶级id
     * 
     * @param array $cate 栏目信息
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
     * 获取栏目文章列表
     *
     * @param int $id 栏目id
     * @param int $page 页
     * @param int $limit 每页数量
     * @param string $field 排序字段
     * @param string $sort 排序{升(asc)降(desc)}
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
            ->page($page,$limit)//翻页及数量
            ->order($field.' '.$sort)//排序
            ->select();
       
        return $list;
    }




    /**
     * 获取单页内容
     *
     * @param array $cate 栏目信息
     * @return void
     */
    public function get_page_data($cate)
    {
        return  Db::name('page')
            ->where('cate_id',$cate['id'])
            ->find();        
    }

    /**
     * 获取栏目文章列表
     *
     * @param int $id 栏目id
     * @param int $page 页
     * @param int $limit 每页数量
     * @param string $field 排序字段
     * @param string $sort 排序{升(asc)降(desc)}
     * @return void
     */
    public function get_cate_art_list2($cate,$page=1,$field='weigh',$sort='desc')
    {
        $where['status'] = '1';//需要状态为1才显示

        //可以直接get或post提交各种参数自行添加条件
        $params = $this->request->param();
        if(isset($params['tag'])){
            $where['tags'] = ['like',"%".$params['tag']."%"];
        }
        //例如前段直接传page参数
        if(isset($params['page'])){
            $page = $params['page'];
        }

      
        $limit = $cate['pagesize'];//分页数量
        $list = Db::table($cate['table_name'])
                ->where($where)
                ->where('deletetime',NULL)//如果删除，但没有被真是删除（在回收站），就不显示
                ->order($field.' '.$sort)//排序
                ->page($page,$limit)
                ->limit($limit)
                ->select();

        //*******************翻页自定义区域
        //翻页总数
        $page_data['count']= Db::table($cate['table_name'])
                ->where($where)
                ->count();
        //总页数                有余进1
        $page_data['pages'] = ceil($page_data['count']/$limit);

        //一页数量
        $page_data['limit'] = $limit;

        //当前页码
        $page_data['page'] = $page;

        //页码列表  可以循环list
        $page_data['list'] = null;
        for ($i=1; $i <=$page_data['pages'] ; $i++) {      
            $page_data['list'][] = ['url'=>$this->get_page_url($cate['id'],$i) , 'num'=>$i];
        }

        //上一页
        $page_data['prev_page'] = $page>1 ? ['num'=>$page-1,'url'=>$this->get_page_url($cate['id'] , $page-1)]: null;        
        
        //下一页
        $page_data['next_page'] = $page_data['pages']>$page ? ['num'=>$page+1,'url'=>$this->get_page_url($cate['id'] , $page+1)] : null;
        
        //可以自行打断查看数据 ⌚️2021-06-23 22:09:59
        // halt($page_data);

        $this->view->assign('page' , $page_data);
        //*******************翻页自定义区域结束




        foreach ($list as $key => &$val) {
            //没有外链 就生成（也可以自己注释了在前段判断）
            if(!isset($val['outlink']) || empty($val['outlink'])){
                $val['url'] = $this->get_show_url($cate['id'],$val['id']);
            }else{
                $val['url'] = $val['outlink'];
            }
           
        }
        

        return $list;
    }


    /**
     * 获取分页数
     *
     * @param string $table_name 表名
     * @return void
     */
    public function get_limit($table_name)
    {
        $cate = Db::name('cate')->where('table_name',$table_name)->find();
        if(!$cate){
            $this->error('没有该数据表');
        }
        return $cate['pagesize'];
    }



    /**
     * 获取栏目信息
     *
     * @param string $table_name 表名
     * @return void
     */
    public function get_cate_data_By_table_name($table_name)
    {
        $cate = Db::name('cate')->where('table_name',$table_name)->find();
        if(!$cate){
            $this->error('没有该数据表');
        }
        return $cate;
    }

    /**
     * 获取显示页面
     *
     * @param string $table_name 表名
     * @param int $id 内容id
     * @return void
     */
    public function get_show_url($table_name,$id)
    {
        //也可以自己定义自己的url
        return '/cms/index/show/cate_id/'.$table_name.'/id/'.$id;
    }


    /**
     * 获取字段数组（可处理时间戳）
     *
     * @param array $arr 数组
     * @param array $fields 需要的字段
     * @param array $NotNull 不能为空默认为假
     * @return void
     */
    protected function get_field_arr($arr,$fields,$NotNull=false)
    {
        if(!is_array($fields)){
             //字符串
             $fields = explode(',',$fields);
        }
        
        $new = array();
        foreach($fields as $key=>$val){
        
                if(isset($arr[$val])){
                    if(!$arr[$val])$this->error('请内容信息');
                    $new[$val] = $arr[$val];

                }elseif($NotNull){
                    $this->error('请提交完整的信息');
                }
            
        }
        return $new;
    }

    /**
     * 获取翻页连接
     *
     * @param int $cate_id 栏目id
     * @param int $page 页码
     * @return void
     */
    public function get_page_url($cate_id,$page)
    {
        return $this->request->domain().'/cms/index/cate/id/'.$cate_id.'/page/'.$page;
    }
}
