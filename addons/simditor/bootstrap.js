require.config({
    paths: {
        'simditor': '../addons/simditor/js/simditor.min',
    },
    shim: {
        'simditor': [
            'css!../addons/simditor/css/simditor.min.css'
        ]
    }
});
require(['form'], function (Form) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        if ($(".editor", form).size() > 0) {
            //修改上传的接口调用
            require(['upload', 'simditor'], function (Upload, Simditor) {
                var editor, mobileToolbar, toolbar;
                Simditor.locale = 'zh-CN';
                Simditor.list = {};
                toolbar = ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', 'color', '|', 'ol', 'ul', 'blockquote', 'code', 'table', '|', 'link', 'image', 'hr', '|', 'indent', 'outdent', 'alignment'];
                mobileToolbar = ["bold", "underline", "strikethrough", "color", "ul", "ol"];
                $(".editor", form).each(function () {
                    var id = $(this).attr("id");
                    editor = new Simditor({
                        textarea: this,
                        toolbarFloat: false,
                        toolbar: toolbar,
                        pasteImage: true,
                        defaultImage: Config.__CDN__ + '/assets/addons/simditor/images/image.png',
                        upload: {url: '/'}
                    });
                    editor.uploader.on('beforeupload', function (e, file) {
                        Upload.api.send(file.obj, function (data) {
                            var url = Fast.api.cdnurl(data.url);
                            editor.uploader.trigger("uploadsuccess", [file, {success: true, file_path: url}]);
                        });
                        return false;
                    });
                    editor.on("blur", function () {
                        this.textarea.trigger("blur");
                    });
                    Simditor.list[id] = editor;
                });
            });
        }
    }
});