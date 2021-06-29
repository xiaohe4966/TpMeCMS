<?php

namespace app\index\controller;

use app\common\controller\Frontend;

class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function index()
    {
        //跳转cms首页
        $this->redirect('/cms/index/index');
        return $this->view->fetch();
    }

}
