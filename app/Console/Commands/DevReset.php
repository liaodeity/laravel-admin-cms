<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DevReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '项目重置数据库，清空非基础数据的资料表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table ('admins')->where('username','<>','admin')->delete ();
        DB::table ('agent_regions')->delete ();
        DB::table ('agents')->delete ();
        DB::table ('article_cates')->delete ();
        DB::table ('article_reads')->delete ();
        DB::table ('articles')->delete ();
        //DB::table ('auth_infos')->delete ();
        DB::table ('bills')->delete ();
        //DB::table ('configs')->delete ();
        DB::table ('coordinates')->delete ();
        DB::table ('database_backups')->delete ();
        DB::table ('express_apis')->delete ();
        //DB::table ('express_deliveries')->delete ();
        DB::table ('express_delivery_infos')->delete ();
        DB::table ('failed_jobs')->delete ();
        DB::table ('logs')->delete ();
        DB::table ('member_agents')->delete ();
        DB::table ('members')->delete ();
        //DB::table ('menus')->delete ();
        DB::table ('message_notice_users')->delete ();
        DB::table ('message_notices')->delete ();
        DB::table ('migrations')->delete ();
        //DB::table ('model_has_permissions')->delete ();
        //DB::table ('model_has_roles')->delete ();
        DB::table ('order_logs')->delete ();
        DB::table ('order_products')->delete ();
        DB::table ('order_qrcode_logs')->delete ();
        DB::table ('order_qrcodes')->delete ();
        DB::table ('order_sale_logs')->delete ();
        DB::table ('order_sale_products')->delete ();
        DB::table ('order_sales')->delete ();
        DB::table ('orders')->delete ();
        DB::table ('pay_trades')->delete ();
        //DB::table ('permissions')->delete ();
        DB::table ('pictures')->delete ();
        DB::table ('product_cates')->delete ();
        DB::table ('product_prices')->delete ();
        DB::table ('products')->delete ();
        DB::table ('receipt_addresses')->delete ();
        //DB::table ('regions')->delete ();
        //DB::table ('role_has_permissions')->delete ();
        //DB::table ('roles')->delete ();
        DB::table ('serial_numbers')->delete ();
        DB::table ('share_qrcodes')->delete ();
        DB::table ('template_message_logs')->delete ();
        DB::table ('template_messages')->delete ();
        DB::table ('users')->delete ();
        DB::table ('wx_accounts')->delete ();
        DB::table ('wx_qrcodes')->delete ();
        //DB::table ('wx_reply')->delete ();
        //DB::table ('wx_reply_keywords')->delete ();
    }
}
