require('./bootstrap');

window.ERROR_TIP_TIME = 3000;
window.SUCCESS_TIP_TIME = 3000;

window.jQuery = require("jquery");
window.$ = jQuery;
require("../../public/admin-ui/lib/layer/layer.js")
// window.WdatePicker = require("../../public/admin-ui/lib/datejs/WdatePicker")

require("bootstrap");
require("admin-lte");
require("jquery-pjax");
window.NProgress = require("nprogress")
require('./admin/index')
require("./admin/login")
require("./admin/form")

$.pjax.defaults.timeout = 10000;
$(document).pjax('a', '#pjax-container')
NProgress.configure({ minimum: 0.25 });

NProgress.configure({ parent: '#pjax-container' });
if ($.support.pjax) {
    $(document).on('click', 'a[data-pjax]', function(event) {
        var container = $(this).closest('[data-pjax-container]')
        $.pjax.click(event, {container: container})
    })
    $(document).on('pjax:start', function() {
        console.log('start');
        NProgress.inc();
        NProgress.start();
    })
    $(document).on('pjax:end', function() {
        console.log('end');
        NProgress.done();
    })
}


// import App from './App.vue';



// new Vue({
//     el: '#app',
//     render: h => h(App)
// });



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
