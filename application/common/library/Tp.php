<?php
/**
 * +----------------------------------------------------------------------
 * | 自定义标签TpMeCMS
 * +----------------------------------------------------------------------
 *                      .::::.
 *                    .::::::::.            | AUTHOR: Xiaohe
 *                    :::::::::::           | EMAIL: 496631085@qq.com
 *                 ..:::::::::::'           | QQ: 496631085
 *             '::::::::::::'               | WECHAT: he4966
 *                .::::::::::               | DATETIME: 2021/06/20
 *           '::::::::::::::..
 *                ..::::::::::::.
 *              ``::::::::::::::::
 *               ::::``:::::::::'        .:::.
 *              ::::'   ':::::'       .::::::::.
 *            .::::'      ::::     .:::::::'::::.
 *           .:::'       :::::  .:::::::::' ':::::.
 *          .::'        :::::.:::::::::'      ':::::.
 *         .::'         ::::::::::::::'         ``::::.
 *     ...:::           ::::::::::::'              ``::.
 *   ```` ':.          ':::::::::'                  ::::..
 *                      '.:::::'                    ':'````..
 * +----------------------------------------------------------------------
 */
namespace app\common\library;
use think\template\TagLib;
use think\Db;
class Tp extends TagLib {

    protected $tags = array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'close'     => ['attr' => 'time,format', 'close' => 0],                           //闭合标签，默认为不闭合
        'open'      => ['attr' => 'name,type', 'close' => 1],
        'nav'       => ['attr' => 'id,limit', 'close' => 1],                              //通用导航信息
        'cate'      => ['attr' => 'id,type','close' => 0],                                //通用栏目信息
        'position'  => ['attr' => 'name','close' => 1],                                   //通用位置信息
        'link'      => ['attr' => 'name','close' => 1],                                   //获取友情链接
        'ad'        => ['attr' => 'name,type','close' => 1],                              //获取广告信息
        'debris'    => ['attr' => 'name,type','close' => 0],                              //获取碎片信息
        'list'      => ['attr' => 'id,name,pagesize,where,limit,order','close' => 1],     //通用列表
        'search'    => ['attr' => 'search,table,name,pagesize,where,order','close' => 1], //通用搜索
        'prev'	    => ['attr'	=> 'len','close' => 0],                                   //上一篇
        'next'	    => ['attr'	=> 'len','close' => 0],                                   //下一篇

    );

    //这是一个闭合标签的简单演示
    public function tagClose($tag)
    {
        $format = empty($tag['format']) ? 'Y-m-d H:i:s' : $tag['format'];
        $time = empty($tag['time']) ? time() : $tag['time'];
        $parse = '<?php ';
        $parse .= 'echo date("' . $format . '",' . $time . ');';
        $parse .= ' ?>';
        return $parse;
    }

    //这是一个非闭合标签的简单演示
    public function tagOpen($tag, $content)
    {
        $type = empty($tag['type']) ? 0 : 1; // 这个type目的是为了区分类型，一般来源是数据库
        $name = $tag['name']; // name是必填项，这里不做判断了
        $parse = '<?php ';
        $parse .= '$test_arr=[[1,3,5,7,9],[2,4,6,8,10]];'; // 这里是模拟数据
        $parse .= '$__LIST__ = $test_arr[' . $type . '];';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //通用导航信息
    Public function tagNav($tag,$content){
        $tag['limit'] = isset($tag['limit']) ? $tag['limit'] : '0';
        $tag['id']    = isset($tag['id'])    ? $tag['id']    : '';
        $name         = isset($tag['name'])  ? $tag['name']  : 'nav';

        if(!empty($tag['id'])){
            $catestr = '$__CATE__ = \think\Db::name(\'cate\')->where(\'is_menu\',1)->order(\'weigh DESC,id DESC\')->select();';
            $catestr.= '$__LIST__ = getChildsOn($__CATE__,'.$tag['id'].');';
        }else{
            $catestr = '$__CATE__ = \think\Db::name(\'cate\')->where(\'is_menu\',1)->order(\'weigh DESC,id DESC\')->select();';
            $catestr.= '$__LIST__ = unlimitedForLayer($__CATE__);';
        }
        //提取前N条数据,因为sql的LIMIT避免不了子栏目的问题
        if(!empty($tag['limit'])){
            $catestr.= '$__LIST__ =  array_slice($__LIST__, 0,'.$tag['limit'].');';
        }
        $parse = '<?php ';
        $parse .= $catestr;
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //通用栏目信息
    Public function tagCate($tag){
        
        $id   = isset($tag['id'])?$tag['id']:"input('catId')";
        $type = $tag['type']?$tag['type']:'catname';

        $str = '<?php ';
        $str .= '$__CATE__=\think\Db::name("cate")->where("id",'.$id.')->find();';
        $str .= 'if(is_array($__CATE__)){ ';
        $str .= '$__CATE__[\'url\']=getCateUrl($__CATE__);';
        $str .= '$__CATE__[\'tag\']='.$id.';';
        $str .= 'echo $__CATE__[\''.$type.'\'];';
        $str .= '}';
        $str .= '?>';
        return $str;
    }

    //通用位置信息
    Public function tagPosition($tag,$content){
        $name   = $tag['name']?$tag['name']:'position';
        $parse  = '<?php ';
        $parse .= '$__CATE__ = \think\Db::name(\'cate\')->select();';
        $parse .= '$__LIST__ = getParents($__CATE__,input(\'catId\'));';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= '<?php $' . $name . '[\'url\']=getCateUrl( $' . $name . '); ?>';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //获取友情链接
    Public function tagLink($tag,$content){
        $name   = $tag['name']?$tag['name']:'link';
        $parse  = '<?php ';
        $parse .= '$__LIST__ = \think\Db::name(\'link\')->where(\'status\',1)->order(\'weigh DESC,id desc\')->select();';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //获取广告信息
    Public function tagAd($tag,$content){
        $name = isset($tag['name']) ? $tag['name'] : 'ad';
        $type = isset($tag['type']) ? $tag['type'] : '';
        $id   = isset($tag['id'])   ? $tag['id']   : '';
        $parse = '<?php ';
        $parse .= '
            $__WHERE__ = array();
            if (!empty(\'' . $id . '\')) {
                $__WHERE__[] = [\'a.type_id\', \'=\', ' . $id . '];
            }
            if (!empty(\'' . $type . '\')) {
                $__WHERE__[] = [\'at.name\', \'=\', \'' . $type . '\'];
            }';
        $parse .= '$__LIST__ = \think\Db::name(\'ad\')
            ->alias(\'a\')
            ->leftJoin(\'ad_type at\',\'a.type_id = at.id\')
            ->field(\'a.*,at.name as type_name\')
            ->where(\'a.status\',1)
            ->where($__WHERE__)
            ->order(\'a.weigh DESC,a.id desc\')
            ->select();';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //通用碎片信息
    Public function tagDebris($tag){
        $name   = $tag['name']?$tag['name']:'';
        $type   = $tag['type']?$tag['type']:'';
        $str = '<?php ';
        $str .= 'echo \think\Db::name("debris")->where("name",\''.$name.'\')->value("'.$type.'");';
        $str .= '?>';
        return $str;
    }

    //通用列表
    Public function tagList($tag,$content){
        $id    = isset($tag['id'])    ? $tag['id']     : "input('catId')";                //可以为空
        $name  = isset($tag['name'])  ?  $tag['name']  : "list";                          //不可为空
        $order = isset($tag['order']) ?  $tag['order'] : 'weigh DESC,id DESC';              //排序
        $limit = isset($tag['limit']) ?  $tag['limit'] : '0';                             //多少条数据,传递时不再进行分页
        $page = isset($tag['page']) ?  $tag['page'] : '1';  
        $where = isset($tag['where']) ?  $tag['where'].' AND status = 1 ' : 'status = 1'; //查询条件
        $pagesize = isset($tag['pagesize']) ?  $tag['pagesize']   : config('page_size');
        //paginate(1,false,['query' => request()->param()]); //用于传递所有参数，目前只需要page参数
        $__CATE__ = Db::name('cate')->find($id);
        $parse  = '<?php ';
        // $parse .='
        //     //查找栏目对应的表信息
        //     $__TABLE_=\think\Db::name(\'cate\')
        //         ->alias(\'a\')
        //         ->leftJoin(\'module m\',\'a.moduleid = m.id\')
        //         ->field(\'a.id,a.moduleid,a.pagesize,a.catname,m.name as modulename\')
        //         ->where(\'a.id\',\'=\','.$id.')
        //         ->find();
        //     //获取表名称    
        //     $__TABLENAME_ = $__TABLE_[\'modulename\'];
        //     //获取模型ID
        //     $__MODULEID__ = $__TABLE_[\'moduleid\'];
        //     //查询子分类,列表要包含子分类内容
        //     $__ALLCATE__ = \think\Db::name(\'cate\')->field(\'id,parentid\')->select();
        //     $__IDS__ = getChildsIdStr(getChildsId($__ALLCATE__,'.$id.'),'.$id.');

        //     //表名称为空时（id不存在）直接返回
        //     if(!empty($__TABLENAME_)){
        //         //当传递limit时，不再进行分页
        //         if('.$limit.'!=0){
        //             $__LIST__ = \think\Db::name($__TABLENAME_)
        //             ->order(\''.$order.'\')
        //             ->limit(\''.$limit.'\')
        //             ->where(" '.$where.'")
        //             ->where(\'catid\',\'in\',$__IDS__)
        //             ->select();
        //             $page = \'\';
        //         }else{
        //             $__TABLE_[\'pagesize\'] = empty($__TABLE_[\'pagesize\'])?'.$pagesize.':$__TABLE_[\'pagesize\'];
        //             $__LIST__ = \think\Db::name($__TABLENAME_)
        //             ->order(\''.$order.'\')
        //             //->limit(\''.$limit.'\')
        //             ->where(" '.$where.'")
        //             ->where(\'catid\',\'in\',$__IDS__)
        //             ->paginate($__TABLE_[\'pagesize\']);
        //             $page = $__LIST__->render();
        //         }
        //         //处理数据（把列表中需要处理的字段转换成数组和对应的值）
        //         $__LIST__ = changeFields($__LIST__,$__MODULEID__);
        //     }else{
        //         $__LIST__ = [];
        //     }
        //     ';



        $parse .='
        //查找栏目对应的表信息
        $__TABLE_=\think\Db::name(\'cate\')
            ->find('.$id.');
        //获取表名称    
        $__TABLENAME_ = $__TABLE_[\'table_name\'];
        //获取模型ID

        //表名称为空时（id不存在）直接返回
        if(!empty($__TABLENAME_)){
            //当传递limit时，不再进行分页
            if('.$limit.'!=0){
                $__LIST__ = \think\Db::table($__TABLENAME_)
                ->order(\''.$order.'\')
                ->limit(\''.$limit.'\')
                ->page(\''.$page.'\')
                ->where(" '.$where.'")
                ->select();
                $page = \'\';
            }else{
                $__TABLE_[\'pagesize\'] = empty($__TABLE_[\'pagesize\'])?'.$pagesize.':$__TABLE_[\'pagesize\'];
                $__LIST__ = \think\Db::table($__TABLENAME_)
                ->order(\''.$order.'\')
                // ->limit(\''.$limit.'\')
                ->page(\''.$page.'\')
                ->where(" '.$where.'")
                ->select();
                // ->paginate($__TABLE_[\'pagesize\']);
                // $page = $__LIST__->render();
            }
            //处理数据（把列表中需要处理的字段转换成数组和对应的值）
            $__LIST__ = changeFields($__LIST__,'.$id.');
        }else{
            $__LIST__ = [];
        }
        ';



        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$name.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //通用搜索 search,table,name,pagesize,where,order
    Public function tagSearch($tag,$content){
        $search = isset($tag['search'])    ? $tag['search']     : "";                     //关键字
        $table  = isset($tag['table'])     ? $tag['table']      : "article";              //表名称
        $name  = isset($tag['name'])  ?  $tag['name']  : "list";                          //不可为空
        $order = isset($tag['order']) ?  $tag['order'] : 'weigh DESC,id DESC';              //排序
        $where = isset($tag['where']) ?  $tag['where'].' AND status = 1 ' : 'status = 1'; //查询条件
        $pagesize = isset($tag['pagesize']) ?  $tag['pagesize']   : config('page_size');

        $parse  = '<?php ';
        $parse .='
                $__MODULEID__ = \think\Db::name("module")->where("name","'.$table.'")->value("id");
                $__LIST__ = \think\Db::name("'.$table.'")
                ->order("'.$order.'")
                ->where("'.$where.'")
                ->where("title", "like", "%'.$search.'%")
                ->paginate("'.$pagesize.'",false,[\'query\' => request()->param()]);
                $page = $__LIST__->render();

            //处理数据（把列表中需要处理的字段转换成数组和对应的值）
            $__LIST__ = changeFields($__LIST__,$__MODULEID__);
            ';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="'.$name.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //详情上一篇
    Public function tagPrev($tag){
        $len = $tag['len']?$tag['len']:'';

        $str  = '<?php ';
        $str .= '
                //查找表名称
                $__TABLENAME__ = \think\Db::name(\'cate\')
                    ->alias(\'c\')
                    ->leftJoin(\'module m\',\'c.moduleid = m.id\')
                    ->field(\'m.name as table_name\')
                    ->where(\'c.id\',input(\'catId\'))
                    ->find();
                //根据ID查找上一篇的信息
                $__PREV__ = \think\Db::name($__TABLENAME__[\'table_name\'])
                    ->where(\'catid\',input(\'catId\'))
                    ->where(\'id\',\'<\',input(\'id\'))
                    ->field(\'id,catid,title\')
                    ->order(\'weigh DESC,id DESC\')
                    ->find();
                if($__PREV__){
                    //处理上一篇中的URL
                    $__PREV__[\'url\'] = getShowUrl($__PREV__);
                    $__PREV__ = "<a class=\"prev\" title=\" ".$__PREV__[\'title\']." \" href=\" ".$__PREV__[\'url\']." \">".$__PREV__[\'title\']."</a>"; 
                }else{
                    $__PREV__ = "<a class=\"prev_no\" href=\"javascript:;\">暂无数据</a>"; 
                }
                echo $__PREV__;
                ';
        $str .= '?>';
        return $str;
    }

    //详情下一篇
    Public function tagNext($tag){
        $len = $tag['len']?$tag['len']:'';

        $str  = '<?php ';
        $str .= '
                //查找表名称
                $__TABLENAME__ = \think\Db::name(\'cate\')
                    ->alias(\'c\')
                    ->leftJoin(\'module m\',\'c.moduleid = m.id\')
                    ->field(\'m.name as table_name\')
                    ->where(\'c.id\',input(\'catId\'))
                    ->find();
                //根据ID查找下一篇的信息
                $__PREV__ = \think\Db::name($__TABLENAME__[\'table_name\'])
                    ->where(\'catid\',input(\'catId\'))
                    ->where(\'id\',\'>\',input(\'id\'))
                    ->field(\'id,catid,title\')
                    ->order(\'weigh DESC,id DESC\')
                    ->find();
                if($__PREV__){
                    //处理下一篇中的URL
                    $__PREV__[\'url\'] = getShowUrl($__PREV__);
                    $__PREV__ = "<a class=\"next\" title=\" ".$__PREV__[\'title\']." \" href=\" ".$__PREV__[\'url\']." \">".$__PREV__[\'title\']."</a>"; 
                }else{
                    $__PREV__ = "<a class=\"next_no\" href=\"javascript:;\">暂无数据</a>"; 
                }
                echo $__PREV__;
                ';
        $str .= '?>';
        return $str;
    }


}