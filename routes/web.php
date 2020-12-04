<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/vue', function () {
    return view('vue');
});
Route::get('/send-test', function (){


    //phpinfo();
//    \Illuminate\Support\Facades\Artisan::call('birthday:member');

//    $wxAccount = [];
//    $wxJsConfig = '';
//    $member = new App\Entities\Member();
//    $agent  = new App\Entities\Agent();
//    $memberAgent  =new App\Entities\MemberAgent();
//    $referrer_name = '';
//    $referrer_member_id = '';
//    $isEnableReg = 0;
//    $region = '';
//    return view('member.reg', compact('wxAccount', 'wxJsConfig', 'member',
//        'agent', 'memberAgent', 'referrer_name', 'referrer_member_id',
//        'isEnableReg',
//        'region'
//    ));
});

//二维码分享入口
Route::get('/scan-product/{no}','ScanController@product')->name('scan-product');
Route::get('/scan-share/{no}','ScanController@agent')->name('scan-share');
Route::get('/scan-agent/{no}','ScanController@agent')->name('scan-agent');
Route::get('/scan-member/{no}','ScanController@member')->name('scan-member');
Route::get('/scan-agent-bind/{no}','ScanController@agentBind')->name('scan-agent-bind');
Route::get('/scan-admin-bind/{no}','ScanController@adminBind')->name('scan-admin-bind');

//图片上传
Route::post ('upload/image/{name}', 'UploadController@image');
Route::post ('upload/annex/{name}', 'UploadController@annex');

//地区
Route::get('region/select_area', 'RegionController@select_area');
Route::post('region/get-region-pid', 'RegionController@getRegionPid');
Route::post('region/get-region-str-id','RegionController@getRegionStrId');
Route::any('region/get-region-str-pid','RegionController@getRegionStrPid');
Route::any('region/area-js','RegionController@setJs');

//验证码
Route::get('/admin/login_captcha', 'LoginController@captcha')->name('admin-login-captcha');
//登录入口
Route::get('/admin/login', 'LoginController@admin')->name('admin-login');
Route::get('/agent/login', 'LoginController@agent')->name('agent-login');
Route::post('/login/check', 'LoginController@check')->name('login-check');
Route::get('/login/auth_agent_login/{authCode}', 'LoginController@auth_agent_login')->name('auth-agent-login');
Route::post('login/check-lock-screen/{type}','LoginController@checkLockScreen');
Route::get('/admin-lockscreen', 'Admin\MainController@lockscreen');
Route::get('/agent-lockscreen', 'Agent\MainController@lockscreen');
/*后台*/
Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin', 'Admin\MainController@index');
    Route::post('/admin-main-tips', 'Admin\MainController@tips')->name('admin-main-tips');
    Route::get('/admin-console', 'Admin\ConsoleController@index');

    Route::get('/admin-logout', 'Admin\MainController@logout');

    Route::get('/admin/dialogs-referrer', 'DialogController@referrer')->name('admin.dialogs.referrer');

    Route::post('/admin/roles/disable/{id}', 'Admin\RolesController@disable')->name('roles.disable');
    Route::post('/admin/roles/enable/{id}', 'Admin\RolesController@enable')->name('roles.enable');
    Route::resource('/admin/roles', 'Admin\RolesController');
    Route::resource('/admin/menu', 'Admin\MenusController');
    Route::post('/admin/configs/send', 'Admin\ConfigsController@send')->name('configs.send');
    Route::resource('/admin/configs', 'Admin\ConfigsController');
    Route::get('/admin/expressDeliveries/all_code', 'Admin\ExpressDeliveriesController@allCode')->name('expressDeliveries.all_code');
    Route::resource('/admin/expressDeliveries', 'Admin\ExpressDeliveriesController');

    Route::resource('/admin/databaseBackups', 'Admin\DatabaseBackupsController');
    Route::get('/admin/databaseBackups/down/{id}', 'Admin\DatabaseBackupsController@down')->name('databaseBackups.down');

    Route::resource('/admin/regions', 'Admin\RegionsController');
    Route::resource('/admin/logs', 'Admin\LogsController');
    Route::resource('/admin/articles', 'Admin\ArticlesController');

    Route::resource('/admin/admins', 'Admin\AdminsController');
    Route::post('/admin/admins/disable/{id}', 'Admin\AdminsController@disable')->name('admins.disable');
    Route::post('/admin/admins/enable/{id}', 'Admin\AdminsController@enable')->name('admins.enable');


    Route::get('/admin/members/export', 'Admin\MembersController@export')->name('members.export');
    Route::post('/admin/members/disable/{id}', 'Admin\MembersController@disable')->name('members.disable');
    Route::post('/admin/members/enable/{id}', 'Admin\MembersController@enable')->name('members.enable');
    Route::resource('/admin/members', 'Admin\MembersController');


    Route::get('/admin/agents/export', 'Admin\AgentsController@export')->name('agents.export');
    Route::post('/admin/agents/disable/{id}', 'Admin\AgentsController@disable')->name('agents.disable');
    Route::post('/admin/agents/enable/{id}', 'Admin\AgentsController@enable')->name('agents.enable');
    Route::resource('/admin/agents', 'Admin\AgentsController');



    Route::resource('/admin/productCates', 'Admin\ProductCatesController');
    Route::resource('/admin/products', 'Admin\ProductsController');
    Route::post('/admin/products/disable/{id}', 'Admin\ProductsController@disable')->name('products.disable');
    Route::post('/admin/products/enable/{id}', 'Admin\ProductsController@enable')->name('products.enable');
    Route::get('/admin/products/video/{id}', 'Admin\ProductsController@video')->name('products.video');

    Route::resource('/admin/orderPending', 'Admin\OrderPendingController');
    Route::get('/admin/orderPending/qrcode/{id}', 'Admin\OrderPendingController@qrcode')->name('orderPending.qrcode');
    Route::get('/admin/orderPending/verify/{id}', 'Admin\OrderPendingController@verify')->name('orderPending.verify');
    Route::get('/admin/orderPending/deal/{id}', 'Admin\OrderPendingController@deal')->name('orderPending.deal');
    Route::get('/admin/orderPending/export-order/{id}', 'Admin\OrderPendingController@exportOrder')->name('orderPending.export-order');

    Route::get('/admin/orders/export', 'Admin\OrdersController@export')->name('orders.export');
    Route::get('/admin/orders/qrcode/{id}', 'Admin\OrdersController@qrcode')->name('orders.qrcode');
    Route::post('/admin/orders/progress/{id}', 'Admin\OrdersController@progress')->name('orders.progress');
    Route::get('/admin/orders/export-order/{id}', 'Admin\OrdersController@exportOrder')->name('orders.export-order');
    Route::resource('/admin/orders', 'Admin\OrdersController');

    Route::get('/admin/orderSales/deal/{id}', 'Admin\orderSalesController@deal')->name('orderSales.deal');
    Route::get('/admin/orderSales/qrcode/{id}', 'Admin\orderSalesController@qrcode')->name('orderSales.qrcode');
    Route::post('/admin/orderSales/progress/{id}', 'Admin\orderSalesController@progress')->name('orderSales.progress');
    Route::resource('/admin/orderSales', 'Admin\orderSalesController');



    Route::resource('/admin/orderQrcodes', 'Admin\OrderQrcodesController');
    Route::get('/admin/orderQrcodes/down/{id}', 'Admin\OrderQrcodesController@down')->name('orderQrcodes.down');
    Route::get('/admin/orderQrcodes/downType/{orderID}/{type?}', 'Admin\OrderQrcodesController@downType')->name('orderQrcodes.downType');
    Route::get('/admin/orderQrcodes/downSaleType/{orderID}/{type?}', 'Admin\OrderQrcodesController@downSaleType')->name('orderQrcodes.downSaleType');

    Route::get('/admin/admin-personals/wx', 'Admin\PersonalsController@wx')->name('personals.wx');
    Route::post('/admin/admin-personals/wx-unbind', 'Admin\PersonalsController@wxUnBind')->name('personals.wx-unbind');
    Route::post('/admin/admin-personals/check-wx-bind', 'Admin\PersonalsController@checkWxBind')->name('personals.check-wx-bind');
    Route::post('/admin/admin-personals/wx-new-code', 'Admin\PersonalsController@wxNewCode')->name('personals.wx-new-code');
    Route::resource('/admin/personals', 'Admin\PersonalsController')->only(['edit', 'update']);
    Route::get('/admin/personals/show', 'Admin\PersonalsController@show')->name('personals.show');
    Route::get('/admin/personals/password', 'Admin\PersonalsController@password')->name('personals.password');
    Route::resource('/admin/bills', 'Admin\BillsController');

//    报表
    Route::get('/admin/reports-order', 'Admin\ReportsController@order')->name('admin-reports.order');
    Route::get('/admin/reports-order-sale', 'Admin\ReportsController@orderSale')->name('admin-reports.order-sale');
    Route::get('/admin/reports-member', 'Admin\ReportsController@member')->name('admin-reports.member');
    Route::get('/admin/reports-agent-bill', 'Admin\ReportsController@agentBill')->name('admin-reports.agent-bill');
    Route::get('/admin/reports-one-product', 'Admin\ReportsController@oneProduct')->name('admin-reports.one-product');

    Route::get('/admin/reports-member-gender-bill', 'Admin\ReportsController@memberGenderBill')->name('admin-reports.member-gender-bill');
    Route::get('/admin/reports-member-age-bill', 'Admin\ReportsController@memberAgeBill')->name('admin-reports.member-age-bill');
    Route::get('/admin/reports-member-bill', 'Admin\ReportsController@memberBill')->name('admin-reports.member-bill');
    Route::get('/admin/reports-member-child', 'Admin\ReportsController@memberChild')->name('admin-reports.member-child');
    Route::get('/admin/reports-bill', 'Admin\ReportsController@bill')->name('admin-reports.bill');
    Route::get('/admin/reports-product-scan', 'Admin\ReportsController@productScan')->name('admin-reports.product-scan');
    Route::get('/admin/reports-member-area-bill', 'Admin\ReportsController@memberAreaBill')->name('admin-reports.member-area-bill');
    Route::get('/admin/reports-pay-trade', 'Admin\ReportsController@payTrade')->name('admin-reports.pay-trade');

    Route::resource ('/admin/wxReplies', 'Admin\WxRepliesController');
//:admin-end-bindings:
});



/*代理商*/
Route::group(['middleware' => 'agent'], function () {
    Route::get('/agent', 'Agent\MainController@index');
    Route::post('/agent-main-tips', 'Agent\MainController@tips')->name('agent-main-tips');
    Route::get('/agent-console', 'Agent\ConsoleController@index');

    Route::get('/agent-logout', 'Agent\MainController@logout');

    Route::get('/agent/dialogs-referrer', 'DialogController@referrer')->name('agent.dialogs.referrer');
    //日志
    Route::resource('/agent/agent-logs', 'Agent\LogsController');
    //支付二维码
    Route::post('/payment/agent-qrcode', 'PaymentController@qrcode')->name('payment-agent-qrcode');
    Route::post('/payment/check', 'PaymentController@check')->name('payment-check');

    //个人资料
    Route::resource('/agent/agent-personals', 'Agent\PersonalsController')->only(['edit', 'update']);
    Route::get('/agent/agent-personals/show', 'Agent\PersonalsController@show')->name('agent-personals.show');
    Route::get('/agent/agent-personals/down', 'Agent\PersonalsController@down')->name('agent-personals.down');
    Route::get('/agent/agent-personals/wx', 'Agent\PersonalsController@wx')->name('agent-personals.wx');
    Route::post('/agent/agent-personals/wx-unbind', 'Agent\PersonalsController@wxUnBind')->name('agent-personals.wx-unbind');
    Route::post('/agent/agent-personals/check-wx-bind', 'Agent\PersonalsController@checkWxBind')->name('agent-personals.check-wx-bind');
    Route::post('/agent/agent-personals/wx-new-code', 'Agent\PersonalsController@wxNewCode')->name('agent-personals.wx-new-code');
    Route::get('/agent/agent-personals/password', 'Agent\PersonalsController@password')->name('agent-personals.password');

    Route::resource('/agent/agent-receiptAddresses', 'Agent\ReceiptAddressesController');

    Route::get('/agent/agent-members/export', 'Agent\MembersController@export')->name('agent-members.export');
    Route::post('/agent/agent-members/disable/{id}', 'Agent\MembersController@disable')->name('agent-members.disable');
    Route::post('/agent/agent-members/enable/{id}', 'Agent\MembersController@enable')->name('agent-members.enable');
    Route::resource('/agent/agent-members', 'Agent\MembersController');

    Route::post('/agent/agent-memberAgrees/pass/{id}', 'Agent\MemberAgreesController@disable')->name('agent-memberAgrees.pass');
    Route::post('/agent/agent-memberAgrees/fail/{id}', 'Agent\MemberAgreesController@enable')->name('agent-memberAgrees.fail');
    Route::get('/agent/agent-memberAgrees/verity/{id}', 'Agent\MemberAgreesController@verity')->name('agent-memberAgrees.verity');
    Route::resource('/agent/agent-memberAgrees', 'Agent\MemberAgreesController');


    Route::resource('/agent/agent-bills', 'Agent\BillsController');
    Route::post('/agent/agent-bills/revoke/{id}', 'Agent\BillsController@revoke')->name('agent-bills.revoke');

    Route::resource('/agent/agent-billDeals', 'Agent\BillDealsController');
    Route::post('/agent/agent-billDeals/verity/{id}', 'Agent\BillDealsController@verity')->name('agent-billDeals.verity');
    Route::post('/agent/agent-billDeals/invalid/{id}', 'Agent\BillDealsController@invalid')->name('agent-billDeals.invalid');

    Route::resource('/agent-orderSales', 'Agent\OrderSalesController');
    Route::get('/agent/agent-orders/export', 'Agent\OrdersController@export')->name('agent-orders.export');
    Route::any('/agent-orders/check/{id}', 'Agent\OrdersController@check')->name('agent-orders.check');
    Route::get('/agent-orders/pay/{id}', 'Agent\OrdersController@pay')->name('agent-orders.pay');
    Route::post('/agent-orders/account-pay', 'Agent\OrdersController@AccountPay')->name('agent-orders.account-pay');
    Route::get('/agent-orders/setting/{id}', 'Agent\OrdersController@setting')->name('agent-orders.setting');
    Route::post('/agent-orders/cancel/{id}', 'Agent\OrdersController@cancel')->name('agent-orders.cancel');
    Route::post('/agent-orders/changeReceipt/{id}', 'Agent\OrdersController@changeReceipt')->name('agent-orders.changeReceipt');
    Route::post('/agent-orders/receipt/{id}', 'Agent\OrdersController@receipt')->name('agent-orders.receipt');
    Route::resource('/agent-orders', 'Agent\OrdersController');

    Route::resource('/agent/agent-articles', 'Agent\ArticlesController')->only(['index', 'show']);

    //报表
    Route::get('/agent/reports-member', 'Agent\ReportsController@member')->name('agent-reports.member');
    Route::get('/agent/reports-member-bill', 'Agent\ReportsController@memberBill')->name('agent-reports.member-bill');
    Route::get('/agent/reports-member-child', 'Agent\ReportsController@memberChild')->name('agent-reports.member-child');
    Route::get('/agent/reports-product', 'Agent\ReportsController@product')->name('agent-reports.product');
    Route::get('/agent/reports-product-scan', 'Agent\ReportsController@productScan')->name('agent-reports.product-scan');
    Route::get('/agent/reports-bill', 'Agent\ReportsController@bill')->name('agent-reports.bill');

    //:agent-end-bindings:
});



//微信端用户
Route::get('member/agent-wx-bind','Member\AgentController@wxBind');
Route::get('member/agent-qrcode','Member\AgentController@qrcode');
Route::get('member/agent-bill-confirm','Member\AgentController@billConfirm');
Route::post('member/agent-bill-update','Member\AgentController@billUpdate');

Route::match(['get','post'],'member/reg','Member\MemberController@reg');
Route::get('member/reg-success','Member\MemberController@regSuccess');
Route::get('member/scan-success','Member\MemberController@scanSuccess');
Route::get('member/scan-fail','Member\MemberController@scanFail');
Route::get('member/product-scan','Member\MemberController@productScan');
Route::get('member/product-manual/{id}','Member\MemberController@productManual');
Route::post('member/get-check-bill','Member\MemberController@getCheckBill');
Route::post('member/get-check-product-scan','Member\MemberController@getCheckProductScan');
Route::post('member/upload-media/{mediaID}','Member\MemberController@uploadMedia');

Route::get('member/birthday','Member\MemberController@birthday');
Route::get('member/birthday_tip','Member\AgentController@birthdayTip');
//微信公众号接口
Route::any('wx/callback','WeiXinController@callback');
//微信支付回调地址
Route::any('payments/wechat-notify','PaymentController@wechatNotify');
