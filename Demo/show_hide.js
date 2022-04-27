// 此代码是在/public/assets/js/backend/下面对应的控制器名  
//就是简单的jq选择后执行里面方法
//用于演示 隐藏hide() 显示show() 不可编辑输入框 no_edit()
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'adv/index' + location.search,
                    add_url: 'adv/add',
                    edit_url: 'adv/edit',
                    del_url: 'adv/del',
                    multi_url: 'adv/multi',
                    import_url: 'adv/import',
                    table: 'adv',
                }
            });

            var table = $("#table");
            
            //调整添加窗口和编辑窗口的大小
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-add").data("area", ["1000px", "800px"]);
                $(".btn-editone").data("area", ["1000px", "800px"]);
            });

          
            

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'type_status', title: __('Type_status'), searchList: {"1":__('Type_status 1'),"2":__('Type_status 2'),"3":__('Type_status 3'),"4":__('Type_status 4')}, formatter: Table.api.formatter.status},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4')}, formatter: Table.api.formatter.status},
                        {field: 'top_status', title: __('Top_status'), searchList: {"0":__('Top_status 0'),"1":__('Top_status 1')}, formatter: Table.api.formatter.status},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'views', title: __('Views')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'show_status', title: __('Show_status'), searchList: {"1":__('Show_status 1'),"2":__('Show_status 2')}, formatter: Table.api.formatter.status},
                        {field: 's_time', title: __('S_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'e_time', title: __('E_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'likes', title: __('Likes')},
                        {field: 's', title: __('S')},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'user.avatar', title: __('User.avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'coopidentity.name', title: __('Coopidentity.name'), operate: 'LIKE'},
                        {field: 'live.name', title: __('Live.name'), operate: 'LIKE'},
                        {field: 'advcate.name', title: __('Advcate.name'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

           

            // 初始化表格
            table.bootstrapTable({
                url: 'adv/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'adv/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'adv/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            
            
            Controller.api.bindevent();
            hide_all();//隐藏全部

            change_status();
            //点击状态修改显示
            $(":radio").click(function(){
                change_status();
            });

            function change_status() {
                var now = $(':radio[name="row[type_status]"]:checked').val();
                switch (now) {
                    //信息类型:1=商务资源发布,2=主播团长MCN机构通告
                    case '1':                        
                        show1();
                        break;

                    case '2':                        
                        show2();
                        break;
                    case '3':                        
                        show3();
                        break;
                    case '4':                        
                        show4();
                        break;

                    default:
                        alert('没有此选项'+now);
                        break;
                }
            };

            //隐藏全部
            function hide_all(){
                $('.form-group').hide();
                show('type_status');

                // $('.type_status').show();
            }

            function show1(){
                hide_all();//隐藏全部

                show('top_status');
                
                //公共头部
                show('advcate_id');      
                show('image');    
                show('images');    
                show('tags');    
                show('addr');    
                show('lat');    
                show('lng');    
                show('memo');    
                show('content');    
                show('video_files');  
                //结尾

            }

            function show2(){
                hide_all();//隐藏全部

                show('top_status');
                
                //公共头部                
                show('cooperate_position');      
                show('cooperate_way');   
                show('tel');   
                show('wechat');   
                show('ltd_name');   
                show('live_id');   
                show('msn_anchor_num_images');   
                show('msn_head_anchor_num_images');   
                show('msn_head_anchor_fans_num_images');     
                //结尾
            }

    


            function show(v){
                v = '.tpmecms-'+v;
                $(v).show();
            }




        },
        edit: function () {
            Controller.api.bindevent();



            hide_all();//隐藏全部
            change_status();
            //点击状态修改显示
            $(":radio").click(function(){
                change_status();
            });

            function change_status() {
                var now = $(':radio[name="row[type_status]"]:checked').val();

                no_edit('type_status');
                switch (now) {
                    //信息类型:1=商务资源发布,2=主播团长MCN机构通告
                    case '1':                        
                        show1();
                        break;

                    case '2':                        
                        show2();
                        break;
                   

                    default:
                        alert('没有此选项'+now);
                        break;
                }
            };

            //隐藏全部
            function hide_all(){
                $('.form-group').hide();
                show('type_status');
                show('status');
                show('s');
                show('s_time');
                show('e_time');

                // $('.type_status').show();
            }

            function show1(){
                hide_all();//隐藏全部

                show('top_status');
                
                //公共头部
                show('advcate_id');      
                show('image');    
                show('images');    
                show('tags');    
                show('addr');    
                show('lat');    
                show('lng');    
                show('memo');    
                show('content');    
                show('video_files');  
                //结尾

            }

            function show2(){
                hide_all();//隐藏全部

                show('top_status');
                
                //公共头部                
                show('cooperate_position');      
                show('cooperate_way');   
                show('tel');   
                show('wechat');   
                show('ltd_name');   
                show('live_id');   
                show('msn_anchor_num_images');   
                show('msn_head_anchor_num_images');   
                show('msn_head_anchor_fans_num_images');     
                //结尾

            }


            //显示某个元素
            function show(v){
                v = '.tpmecms-'+v;
                $(v).show();
            }

            //不能编辑某个输入框
            function no_edit(v){
                v = '.tpmecms-'+v;
                $(v).children().find("input").attr("disabled","disabled");
            }

        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
