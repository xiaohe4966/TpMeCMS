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

// use app\common\controller\Frontend;
use app\cms\controller\Cms;
use think\Db;

use fast\Tree;


class Index extends Cms
{

    protected $noNeedLogin = ['*'];//自己需要写的方法 这个是不需要登陆的其他则为需要登陆
    protected $noNeedRight = '*';
    protected $layout = '';


    public function _initialize()
    {   
        parent::_initialize();

        //需要登陆的方法 加入到里面
        $NeedLogin = ['add_apply','add_evaluate','add_line'];//根据需求更改
        foreach($NeedLogin as $action){
            if($action==$this->request->action()){
                if(!$this->auth->isLogin()){
                    $this->error('请登陆');
                }
            }
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
     * 栏目页面
     *
     * @param int $id 栏目id
     * @param int $page 栏目为list才有效
     * @return void
     */
    public function cate($id=null,$page=1)
    {
        if(!$id){
            $this->error('没有栏目ID');
        }

        $cate = $this->get_cate_data($id);
        if(!$cate){
            $this->error('没有该栏目ID');
        }

        switch ($cate['type']) {
            case 'page':
                $content = $this->get_page_data($cate);
                $this->view->assign('content', $content);//单页内容
                break;

            case 'list':
                $list = $this->get_cate_art_list2($cate);
                $this->view->assign('list', $list);//列表
                break;

            case 'link':
                
                break;
            
            default:
                
                break;
        }


        //自定义区域  添加或修改自己需要的数据
        if($cate['table_name']=='he_line'){
            $classes = Db::name('classes')->order('weigh desc')->select();
            $this->view->assign('classes', $classes);//需要渲染的数据

            $catelist = Db::name('classcate')->order('weigh desc')->select();
            $this->view->assign('catelist', $catelist);//需要渲染的数据
        }



        // 示例：获取新闻表he_news的tags里面相关的内容 如需要可取消注释和修改
        // $limit_tag = 20;//如果不限制个数请赋值null;
        // $tags_list = array();
        // if($cate['table_name']=='he_news'){
        //     $tags = Db::name('news')->column('tags');
        //     foreach ($tags as $key => $val) {
                
        //         if(!empty($val)){
        //             $arr = explode(',',$val);
        //             foreach($arr as $v){
        //                 if(isset($tags_list[$v])){ 
        //                     $tags_list[$v] = $tags_list[$v]+1;
        //                 }else{
        //                     $tags_list[$v] = 1;
        //                 }
        //             }
        //         }
        //     }
        //     ksort($tags_list);//保持键名，按高到低排列
        //     if($limit_tag){
        //         $tags_list = array_slice($tags_list,0,$limit_tag);  //  取前多少个
        //     }       
        //     $this->view->assign('tags_list', $tags_list);//需要渲染的数据
        // }
        //自定义区域结束

        
        
        $this->view->assign('cate', $cate);//栏目信息
      
        return $this->view->fetch($cate['listtpl']);
    }



    /**
     * 显示页面
     *
     * @param int $id 内容id
     * @param int $cate_id 分类id
     * @return void
     */
    public function show($id=null,$cate_id=null)
    {
        $cate = $this->get_cate_data($cate_id);
        $content = Db::table($cate['table_name'])->find($id);
        if($content){
            //增加点击
            Db::table($cate['table_name'])->where('id',$id)->setInc('views');
        }else{
            $this->error('暂无此内容');
        }
        $this->view->assign('content', $content);//内容信息
        $this->view->assign('cate', $cate);//栏目信息
        return $this->view->fetch($cate['showtpl']);
    }





    /***********************************以下为功能随便定义可以参考User示例 */

  


}
