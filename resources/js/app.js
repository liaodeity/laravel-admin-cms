require('./bootstrap');

window.ERROR_TIP_TIME = 3000;
window.SUCCESS_TIP_TIME = 2000;

window.jQuery = require("jquery");
window.$ = jQuery;
require("../../public/admin-ui/lib/layer/layer.js")
require("bootstrap");
require("admin-lte");
require("jquery-pjax");
require("./admin/login")

$.pjax.defaults.timeout = 10000;
$(document).pjax('a', '#pjax-container')
if ($.support.pjax) {
    $(document).on('click', 'a[data-pjax]', function(event) {
        var container = $(this).closest('[data-pjax-container]')
        $.pjax.click(event, {container: container})
    })
}



$ (function () {
    // $ ('[data-toggle="tooltip"]').tooltip ()

    //
    // layer.photos ({
    //     photos: '.layer-photos-preview'
    //     , anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
    // });
    //
    // $ (".treeview-menu li").click (function () {
    //     $ (".treeview-menu li").removeClass ('active')
    //     $ (this).addClass ('active')
    // });
    //
    // $('input:not([autocomplete]),textarea:not([autocomplete]),select:not([autocomplete])').attr('autocomplete', 'off');
})
