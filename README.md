TpMeCMS是一款基于FastAdmin框架（version:V1.3.3.20220121）开发的，FastAdmin基于ThinkPHP5+Bootstrap开发的框架。

## 已开发插件版本地址：https://gitee.com/xiaohe4966/tp-me-cms-addons

## TpMeCMS由来
    用过FastAdmin的插件cms，SIYUCMS，行云海CMS等，功能功能各有优点缺点，发现自己的项目经常使用（微信小程序/公众号，PC网站，手机网站，App（Api接口）等），结合还需要二次开发，才能使用，于是就使用自己熟悉等thinkphp5，FastAdmin来开发自己的cms框架

## TpMeCMS主要优点
    集成微信小程序和微信公众号功能，后台集成栏目管理等，接口可以直接拷贝app\cms\controller\Cms

## 环境要求
    PHP>=7.1 本项目是7.1运行环境

## 下载方法
    下载默认直接:
    `git clone git@gitee.com:xiaohe4966/tp-me-cms.git`

    下载某个分支:
    `git clone -b 分支名 网址.git`
    可以用git安装：  
    `git clone -b v1.3.3.2 git@gitee.com:xiaohe4966/tp-me-cms.git`
    或：
    `git clone -b v1.3.3.2 git@github.com:xiaohe4966/tp-me-cms.git`
    
    或直接下载zip完整包
    https://he4966.cn/uploads/tpmecms/tpmecms.zip   解压到安装目录

    没有剪辑安装的视频：https://www.bilibili.com/video/BV1B54y1J7yW/
## 安装方法
     
    修改网站运行目录为public
    修改伪静态！！！修改伪静态！！修改伪静态！

    （可以跳过该步骤）如果 php 版本非7.1可运行命令  composer update   （更新安装各种包）注意php -v 的版本最好大于等于7.1
            
    
    文件在application/database.php里面修改   //或者在根目录.env里面更改(没有此文件请忽略)

    然后安装即可  
          
    安装完成，就可以访问了后台地址 域名/h.php
    后台地址在public/h.php 可以自行修改文件名即可    
    cms首页： 打开域名会跳转到/cms/index/index，如果不需要可以自行注释跳转 application/index/controller/Index.php
     

  
## 使用流程
    安装好框架后
    只需要复制表修改表字段（只需要字段）
    一键生成后台菜单命令（增删改查）
    添加/修改栏目选择表即可（如果数据库表名未修改不用修改）

## 注意事项及说明
* 数据库设计要求
    * 字段命名要求https://doc.fastadmin.net/doc/database.html
    * 使用后台的《在线命令管理》或者用命令php think crud -t 表名生成CRUD时会自动生成对应的HTML元素和组件
* 数据库说明
    * xx代表表前缀
    * xx_cate CMS栏目的栏目，不能删除（可以添加其他通用字段
    * xx_page CMS的单页内容信息，不能删除（可以添加其他通用字段
    * xx_user 在原Fastadmin的user表
    * xx_admin 登陆后台的账号
    * xx_command Fastadmin的一键命令插件记录
    * xx_config 网站常规配置就在这里面，在后台可直接添加
    * xx_news 列表页的内容，通用都可以复制此表改名改备注即可（列表页面的 deletetime 字段不要删除，用户在后台删除后，可以进入回收站，但前台会看不见，以防误操作）


## 在线演示

http://tpmecms.he4966.cn/cms/index/index
微信公众号直接登陆
http://tpmecms.he4966.cn/cms/user/wx_login


## 配置
    微信配置在  后台常规管理->系统配置->微信->设置小程序公众号等资料
![设置图](https://he4966.cn/uploads/tpmecms/1.png "设置图")
    调试模式开启application/config.php  
    // 应用调试模式
    'app_debug' => Env::get('app.debug', 1),    //或者在根目录.env里面更改(没有此文件请忽略)

    数据库配置修改application/database.php里面修改   //或者在根目录.env里面更改(没有此文件请忽略)

## 使用标签
    网站里面需要添加字段，在后台 常规管理->系统配置->点击+  前段使用{$site.字段名}
![设置字段图](https://he4966.cn/uploads/tpmecms/2.png "设置字段图")
    

    获取栏目名
    {tp:cate id="38" type="name"}
    获取栏目地址链接
    {tp:cate id="38" type="url"}    //这个url在application/common.php  getCateUrl方法里面（可自行修改封装
     获取栏目某个字段
    {tp:cate id="38" type="字段"} 


    获取列表  
    {tp:list name="list" id="1" limit="3"}
        //{$list.这个栏目表里面的字段}
        {$list.title}
        {$list.image}
        {$list.url}//这个url在application/common.php 里面的getShowUrl方法里面（可自行修改封装
    {/tp:list}


    循环多级栏目 及 当前栏目高亮
    ```html
       {volist name="nav" id="v"}                            
            {if $v.childlist}
            <li class="dropdown {if $cate['id'] eq $v['id']  OR $cate['is_top'] eq $v['id']}current{/if}   "><a href="{$v.url}">{$v.name}</a>                                
                <ul>
                    {volist name="$v.childlist" id="v2"}                                       
                    
                        {if $v2.childlist}                                            
                            <li class="dropdown"><a href="{$v2.url}">{$v2.name}</a>
                                <ul>
                                    {volist name="$v2.childlist" id="v3"}
                                    <li><a href="{$v3.url}">{$v3.name}</a></li>
                                    {/volist}                                
                                </ul>
                            </li>
                        {else /}
                        
                            <li><a href="{$v2.url}">{$v2.name}</a></li>
                        {/if}
                    {/volist}
                </ul>
            </li>   
            {else /}
                <li {if $cate['id'] eq $v['id']}class="current"{/if}><a href="{$v.url}">{$v.name}</a></li>
            {/if}
        {/volist}
    ```
    列表页分页示例
        <!-- 上一页 -->
    ```html
    {if $page['prev_page']}<li><a href="{$page.prev_page.url}"><span class="fa fa-angle-left"></span></a></li>{/if}
    
    {volist name="$page['list']" id="v"}
    <li><a href="{$v.url}" {if $page['page'] eq $v['num']}class="active"{/if}>{$v.num}</a></li>
    {/volist}
        <!-- 下一页 -->
    {if $page['next_page']}<li><a href="{$page.next_page.url}"><span class="fa fa-angle-right"></span></a></li>{/if}
    ```

    详情页面
    上一篇:
    
    `{if $content.prev}<ul class="fot_prv com_url" data-url="{$content.prev.url}">上一篇：{$content.prev.title}</ul>{/if}`
	下一篇：
    `{if $content.next}<ul class="fot_next com_url" data-url="{$content.next.url}">下一篇：{$content.next.title}</ul>{/if}`
    不放a标签点击跳转页面（确定✅页面里有引用过jquery）
    ```js
    <script>
        $('.com_url').on('click',function(e){
            var url = $(this).attr('data-url');
            location.href = url;
        });
    </script>
    ```



## 通用特性
* 基于`Auth`验证的权限管理系统
    * 支持无限级父子级权限继承，父级的管理员可任意增删改子级管理员及权限设置
    * 支持单管理员多角色
    * 支持管理子级数据或个人数据
* 强大的一键生成功能
    * 一键生成CRUD,包括控制器、模型、视图、JS、语言包、菜单、回收站等
    * 一键压缩打包JS和CSS文件，一键CDN静态资源部署
    * 一键生成控制器菜单和规则
    * 一键生成API接口文档
* 完善的前端功能组件开发
    * 基于`AdminLTE`二次开发
    * 基于`Bootstrap`开发，自适应手机、平板、PC
    * 基于`RequireJS`进行JS模块管理，按需加载
    * 基于`Less`进行样式开发
* 强大的插件扩展功能，在线安装卸载升级插件
* 通用的会员模块和API模块
* 共用同一账号体系的Web端会员中心权限验证和API接口会员权限验证
* 二级域名部署支持，同时域名支持绑定到应用插件
* 多语言支持，服务端及客户端支持
* 支持大文件分片上传、剪切板粘贴上传、拖拽上传，进度条显示，图片上传前压缩
* 支持表格固定列、固定表头、跨页选择、Excel导出、模板渲染等功能
* 丰富的插件应用市场






## 界面截图
https://he4966.cn/index/product/10.html?cate=9
![栏目管理](https://he4966.cn/uploads/tpmecms/3.png "栏目管理")
![栏目添加](https://he4966.cn/uploads/tpmecms/4.png "栏目添加")
![栏目添加选择模板](https://he4966.cn/uploads/tpmecms/6.png "栏目添加选择模板")
![附近插件](https://he4966.cn/uploads/tpmecms/redisgeo.png "附近插件")


## 版本更新日志
    2021-07-31 cms加入路由
                加入附近插件（基于 redis geo）
    2021-09-16 api加入微信公众号消息接口
                用户通过公众号发送消息内容互动(文字,定位,图片,视频...先去微信公众号配置好接口信息：网站/api/wechatofficial/message )（文件Wechatofficial.php）
    2021-10-21 详情页面加入上一篇，下一篇 
    2021-11-19 加入微信公众号授权登陆 `http://tpmecms.he4966.cn/cms/user/wx_login`
    2022-03-29 更fastadmin版本为V1.3.3.20220121
    2022-04-24 修改一键生成命令是系统表里面的表 可以生成(不能为系统表里面的名 比如user表 一键CRUD里模型和控制器可以改成user2)
    2022-07-19 加入news搜索
    2024-03-01 修复搜索后，翻页参数消失问题


## 代码说明
    自定义路由：application/route.php 和 application/cms/controller/Cms.php 里自行修改
    附近插件：  addons/redisgeo 可以自行添加修改配置文件也在里面

## 问题反馈

在使用中有任何问题，请加QQ群153073132 请备注TpMeCMS

## 特别鸣谢
FastAdmin

## 版权信息

TpMeCMS遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2021-2022 by Xiaohe (https://he4966.cn)

All rights reserved。
