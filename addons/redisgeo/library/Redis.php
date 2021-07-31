<?php

namespace addons\redisgeo\library;

use addons\redisgeo\exception\Exception;
use think\Response;

class Redis
{
    protected $handler = null;

    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 0,
        'persistent' => false,
        'prefix'     => '',
    ];

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if(!extension_loaded('redis')) {
            $this->error("请检查redis扩展是否已添加");
        }

        // 获取 redis 配置
        $config = \think\Config::get('redis');
        
        
        //优先使用框架的配置
        if(!empty($config)) {
            $this->options = array_merge($this->options, $config);
        }
        if(!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Redis();
        if($this->options['persistent']) {
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        } else {
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }

        if('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }

        if(0 != $this->options['select']) {
            $this->handler->select($this->options['select']);
        }

        // if(empty($config) && empty($options)) {
        //     $this->error('请检查config.php里是否有redis配置，或addons\redisgeo\Redis.php里配置');
        // } 

        // 赋值全局，避免多次实例化
        $GLOBALS['REDIS'] = $this->handler;
    }

    public function getRedis() {
        return $this->handler;
    }


    protected function error($msg, $data = null)
    {
        $data = [
            'code' => 0,
            'msg' => $msg,
            'data' => $data,
            'time' => time()
        ];
        Response::create($data, 'json', 0)->send();
        die;
    }
}