define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            $('.btn-edit').data('area',['80%','80%']);
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/index' + location.search,
                    add_url: 'goods/add',
                    edit_url: 'goods/edit',
                    del_url: 'goods/del',
                    multi_url: 'goods/multi',
                    import_url: 'goods/import',
                    table: 'goods',
                }
            });

            var table = $("#table");

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
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'images', title: __('Images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'num', title: __('Num')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'goodscate.name', title: __('Goodscate.name'), operate: 'LIKE'},
                        {field: 'size.name', title: __('Size.name'), operate: 'LIKE'},
                        {field: 'material.name', title: __('Material.name'), operate: 'LIKE'},
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
                url: 'goods/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
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
                                    url: 'goods/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'goods/destroy',
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
        },
        edit: function () {
            console.log(decodeURIComponent(escape(window.atob('UVE0OTY2MzEwODU='))));
            //先获取接口价格
            get_api_price();

            //组件的显示与隐藏
            $('#t-specs').on('click',function(){
                $('#goods').hide();
                $('#specs').show();
            });

            $('#t-goods').on('click',function(){
                $('#specs').hide();
                $('#goods').show();
            });

            $(function () {
                $('#t-specs').show();
            })

            $('#t-specs').on('click',function(){
                $('#goods').hide();
                $('#specs').show();
            });


            //材质选择框监听
            $(document).on("change", "#c-material_ids", function(){
                update_price();
            });

            //尺寸选择框监听
            $(document).on("change", "#c-size_ids", function(){
                update_price();
            });

            // 获取api的价格
            function get_api_price(){
                //写价格到缓存
                var id = $('#id').val();
                $.get("/api/com/get_goods_price_list?goods_id="+id,function(data,status){
                    console.log(data);
                    var price_list = data.data;
                    for (let p = 0; p < price_list.length; p++) {
                        // console.log(price_list[p]);

                        var name = price_list[p].name;
                        var price = price_list[p].price;
                        console.log('接口字段和价格:',name,price);
                        save_price(name,price);

                    }
                });
            }

            // 保存换成价格
            function save_price(name,price){
                var id = $('#id').val();
                localStorage.setItem(id+'_'+name,price); //缓存名称为name，值为data 的数据
            }

            // 获取本地价格
            function get_price(name){
                var id = $('#id').val();
                var price = localStorage.getItem(id+'_'+name); // 读取缓存名为name的值
                console.log('读取价格',name,price);
                return price;
                
            }



            //绑定监听事件
            function f5(){
                Controller.api.bindevent();//绑定事件

                $(".price").bind("input propertychange",function () {
                    var price = $(this).val();
                    var name = $(this).attr('data-name');

                    console.log('propertychange字段和价格:',name,price);
                    save_price(name,price);                
                });
            }
                    
            
           

            /**更新价格 */
            function update_price(){
                
                var size_ids = $('#c-size_ids').val();
                var material_ids = $('#c-material_ids').val();

                if(size_ids==null || size_ids==''){
                    Layer.msg('请选择至少一个尺寸哦');
                }
                if(material_ids==null || material_ids==''){
                    Layer.msg('请选择至少一个材质哦');
                }

                //调试选中没有
                console.log('尺寸',size_ids,'材质',material_ids);
                // Layer.msg('尺寸：'+size_ids+'材质：'+material_ids);

                var size_list = size_ids.split(",");//尺寸
                var material_list = material_ids.split(",");//材质
                var html ='';
                for (var s = 0; s < size_list.length; s++) {
                
                    for (var c = 0; c < material_list.length; c++) {
                        // console.log('尺寸'+size_list[s]+'材质'+material_list[c]);

                        var temp_html = '<div class="form-group tpmecms-size_imaterial">\
                        <label class="control-label col-xs-6 col-sm-2">规格:</label>\
                        <div class="col-xs-6 col-sm-3">\
                            <input id="c-size'+s+'" data-rule="required" disabled data-source="size/index" data-multiple="true" class="form-control selectpage update_select"  data-num="'+s+'" name="row[prices]['+s+'_'+c+'][size_id]" type="text" value="'+size_list[s]+'">\
                        </div>\
            \
                        <label class="control-label col-xs-3 col-sm-1">材质:</label>\
                        <div class="col-xs-6 col-sm-3">\
                            <input id="c-material'+s+'" data-rule="required" disabled data-source="material/index" data-multiple="true" class="form-control selectpage update_select"  data-num="'+s+'" name="row[prices]['+s+'_'+c+'][material_id]" type="text" value="'+material_list[c]+'">\
                        </div>\
                        <div class="col-xs-6 col-sm-2">\
                            <input id="c-price'+s+'" data-rule="required" class="form-control price" name="row[prices]['+s+'_'+c+'][price]" data-name="'+size_list[s]+'_'+material_list[c]+'" placeholder="价格" type="number" value="'+get_price(size_list[s]+'_'+material_list[c])+'">\
                        </div>\
                        </div>';
                        
                        html = html + temp_html;
                    
                    }
                }

                $('.tpmecms-size_imaterial').html(html);
                f5();

                console.log('___________________________');
            }
            
            f5();//页面加载完执行
           
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
