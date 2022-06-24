<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
/**
 * 商品管理
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{

    /**
     * Goods模型对象
     * @var \app\admin\model\Goods
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Goods;
        $this->view->assign("statusList", $this->model->getStatusList());
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
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['goodscate','size','material'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','status','images','image','name','num','weigh']);
                $row->visible(['goodscate']);
				$row->getRelation('goodscate')->visible(['name']);
				$row->visible(['size']);
				$row->getRelation('size')->visible(['name']);
				$row->visible(['material']);
				$row->getRelation('material')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }




    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $this->check_price($params['price']);//添加****🌈🌈🌈🌈🌈🌈
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
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
                    $this->update_price($this->model->id,$params['prices']);//添加****🌈🌈🌈🌈🌈🌈
                    unset($params['prices']);//添加****🌈🌈🌈🌈🌈🌈
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
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
                $this->update_price($ids,$params['prices']);//添加****🌈🌈🌈🌈🌈🌈
                unset($params['prices']);//添加****🌈🌈🌈🌈🌈🌈
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
     * 更新商品价格
     *
     * @param int $goods_id 商品id
     * @param int $prices 价格数组
     * @return void
     */
    protected function update_price($goods_id=null,$prices)
    {
        if(sizeof($prices)<1)
            $this->error('请上传规格和价格');

        foreach ($prices as $key => $val) {
            unset($data);
            $data['goods_id'] = $goods_id;
            $data['size_id'] = $val['size_id'];
            $data['material_id'] = $val['material_id'];
            if($val['price']<0.01){
                $this->error('请输入正确的价格');
            }
            $res = Db::name('goodsprice')->where($data)->find();
            if($res){
                if($res['price']== $val['price'])
                    continue;
                Db::name('goodsprice')->where('id',$res['id'])->update(['price'=>$val['price']]);
            }

            $data['price'] = $val['price'];
            Db::name('goodsprice')->insert($data);
        }
    }

    /**
     * 检测价格和规格
     *
     * @param array $prices 价格数组
     * @return void
     */
    protected function check_price($prices)
    {
        if(sizeof($prices)<1)
            $this->error('请上传规格和价格');

        foreach ($prices as $key => $val) {
            unset($data);
            if($val['size_id']<1)
                $this->error('请上传规格');
            if($val['material_id']<1)
                $this->error('请上传材质');
            if($val['price']<0.01){
                $this->error('请输入正确的价格');
            }
           
        }
    }
}
