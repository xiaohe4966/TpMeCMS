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

    protected $noNeedLogin = ['*'];//???????????????????????? ???????????????????????????????????????????????????
    protected $noNeedRight = '*';
    protected $layout = '';


    public function _initialize()
    {   
        parent::_initialize();

        //????????????????????? ???????????????
        $NeedLogin = ['add_apply','add_evaluate','add_line'];//??????????????????
        foreach($NeedLogin as $action){
            if($action==$this->request->action()){
                if(!$this->auth->isLogin()){
                    $this->error('?????????');
                }
            }
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
     * ????????????
     *
     * @param int $id ??????id
     * @param int $page ?????????list?????????
     * @return void
     */
    public function cate($id=null,$page=1)
    {
        if(!$id){
            $this->error('????????????ID');
        }

        $cate = $this->get_cate_data($id);
        if(!$cate){
            $this->error('???????????????ID');
        }

        switch ($cate['type']) {
            case 'page':
                $content = $this->get_page_data($cate);
                $this->view->assign('content', $content);//????????????
                break;

            case 'list':
                $list = $this->get_cate_art_list2($cate);
                $this->view->assign('list', $list);//??????
                break;

            case 'link':
                
                break;
            
            default:
                
                break;
        }


        //???????????????  ????????????????????????????????????
        if($cate['table_name']=='he_line'){
            $classes = Db::name('classes')->order('weigh desc')->select();
            $this->view->assign('classes', $classes);//?????????????????????

            $catelist = Db::name('classcate')->order('weigh desc')->select();
            $this->view->assign('catelist', $catelist);//?????????????????????
        }



        // ????????????????????????he_news???tags????????????????????? ?????????????????????????????????
        // $limit_tag = 20;//??????????????????????????????null;
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
        //     ksort($tags_list);//?????????????????????????????????
        //     if($limit_tag){
        //         $tags_list = array_slice($tags_list,0,$limit_tag);  //  ???????????????
        //     }       
        //     $this->view->assign('tags_list', $tags_list);//?????????????????????
        // }
        //?????????????????????

        
        
        $this->view->assign('cate', $cate);//????????????
      
        return $this->view->fetch($cate['listtpl']);
    }



    /**
     * ????????????
     *
     * @param int $id ??????id
     * @param int $cate_id ??????id
     * @return void
     */
    public function show($id=null,$cate_id=null)
    {
        // halt($this->request->param());
        $cate = $this->get_cate_data($cate_id);
        $content = Db::table($cate['table_name'])->find($id);
        if($content){
            //????????????
            Db::table($cate['table_name'])->where('id',$id)->setInc('views');
        }else{
            $this->error('???????????????');
        }


        //????????? 
        $prev = Db::table($cate['table_name'])
                ->where('deletetime',null)//?????????????????????????????????
                ->where('id','>',$id)
                ->order('id asc')
                ->limit(1)
                ->find();
        if($prev){
            $prev['url'] = $this->get_show_url($cate_id,$prev['id']);//????????????
        }
        $content['prev'] = $prev;
        
        //?????????
        $next = Db::table($cate['table_name'])
                ->where('deletetime',null)
                ->where('id','<',$id)
                ->order('id desc')
                ->limit(1)
                ->find();
        if($next){
            $next['url'] = $this->get_show_url($cate_id,$next['id']);//????????????
        }
        $content['next'] = $next;


        $this->view->assign('content', $content);//????????????
        $this->view->assign('cate', $cate);//????????????
        return $this->view->fetch($cate['showtpl']);
    }





    /***********************************???????????????????????????????????????User?????? */

  


}
