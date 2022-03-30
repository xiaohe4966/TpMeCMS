<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Tree;
/**
 * 栏目管理
 *
 * @icon fa fa-circle-o
 */
class Cate extends Backend
{
    
    /**
     * Cate模型对象
     * @var \app\admin\model\Cate
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Cate;

        $Tree = Tree::instance();
        $Tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'parent_id');
        $this->categorylist = $Tree->getTreeList($Tree->getTreeArray(0), 'name');

        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v) {
            $categorydata[$v['id']] = $v;
        }

        // //获取数据库的表名和注释
        $this->view->assign("tableList", $this->renderTable());
        $this->view->assign("parentList", $categorydata);

        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            // $list = $this->model
                    
            //         ->where($where)
            //         ->order($sort, $order)
            //         ->paginate($limit);

            // foreach ($list as $row) {
            //     $row->visible(['id','name','image','pagesize','isnav_switch','createtime','status']);
                
            // }

            // $result = array("total" => $list->total(), "rows" => $list->items());

            //构造父类select列表选项数据
            $search = $this->request->request("search");
            $type = $this->request->request("type");
            $list = [];
            // halt($this->categorylist);
            foreach ($this->categorylist as $k => $v) {
                if ($search) {
                    if ($v['type'] == $type && stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false) {
                        if ($type == "all" || $type == null) {
                            $list = $this->categorylist;
                        } else {
                            $list[] = $v;
                        }
                    }
                } else {
                    if ($type == "all" || $type == null) {
                        $list = $this->categorylist;
                    } elseif ($v['type'] == $type) {
                        $list[] = $v;
                    }
                }
            }

            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                //加入判断上级id不能等于自己id(否则死循环)
                if($ids==$params['parent_id'])
                    $ths->error('上级ID和自己栏目不能一样');
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }



    /**
     * 获取模板列表
     * @internal
     */
    public function get_template_list()
    {
        $files = [];
        $keyValue = $this->request->request("keyValue");
        
        if (!$keyValue) {
            $type = $this->request->request("type");
            $name = $this->request->request("name");
            
            if ($name) {
                //$files[] = ['name' => $name . '.html'];
            }
            //设置过滤方法
            $this->request->filter(['strip_tags']);
         
            // $config = get_addon_config('cms');
            // $themeDir = ADDON_PATH . 'cms' . DS . 'view' . DS . $config['theme'] . DS;

            $themeDir = APP_PATH . 'cms' . DS . 'view' . DS . 'index' . DS;
            // halt($themeDir);
            $dh = opendir($themeDir);
            while (false !== ($filename = readdir($dh))) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
                // if ($type) {
                //     $rule = $type == 'channel' ? '(channel|list)' : $type;
                //     if (!preg_match("/^{$rule}(.*)/i", $filename)) {
                //         continue;
                //     }
                // }
                $files[] = ['name' => str_replace(strrchr($filename, "."),"",$filename)];
            }
            sort($files);
        } else {
            $files[] = ['name' => $keyValue];
        }
        return $result = ['total' => count($files), 'list' => $files];
    }


    
    /**
     * 数据库的表名即备注
     */
    protected function renderTable()
    {
        $tableList = [];
        $dbname = \think\Config::get('database.database');
        $list = \think\Db::query("SELECT `TABLE_NAME`,`TABLE_COMMENT` FROM `information_schema`.`TABLES` where `TABLE_SCHEMA` = '{$dbname}';");
        foreach ($list as $key => $row) {
           
            $tableList[$row['TABLE_NAME']] = $row['TABLE_COMMENT'];
        }
        return $tableList;
       
    }

    public function ajax_table_list()
    {
        $tableList = $this->renderTable();
        foreach($tableList as $name=>$ps){
            $list[] = ['name'=>$name,'ps'=>$name."👉".$ps];
        }
        return $result = ['total' => count($list), 'list' => $list];
    }

    /**
     * 规则列表
     * @internal
     */
    public function rulelist()
    {
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $keyValue = $this->request->request("keyValue", "");

        $keyValueArr = array_filter(explode(',', $keyValue));
        $regexList = Config::getRegexList();
        $list = [];
        foreach ($regexList as $k => $v) {
            if ($keyValueArr) {
                if (in_array($k, $keyValueArr)) {
                    $list[] = ['id' => $k, 'name' => $v];
                }
            } else {
                $list[] = ['id' => $k, 'name' => $v];
            }
        }
        return json(['list' => $list]);
    }
}
