<?php
/**
 * Created by PhpStorm.
 * User: gui
 * Date: 2019/12/23
 */

namespace App\Http\Controllers\Admin;


use App\Entities\Product;
use App\Http\Controllers\ReportBaseController;
use Illuminate\Http\Request;

class ReportsController extends ReportBaseController
{
    public function __construct(Request $request)
    {
        $this->moduleName = 'admin';
        $this->request = $request;
        if (isset($this->request->level) && $this->request->level >= 10) {
            $this->limit = $this->request->level;
        }
    }
    //代理商总进货统计
    public function order()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '代理商总进货统计', [
            'order_at' => '下单时间',
            'area_region_name' => '所属区域',
            'agent_name' => '代理商名称',
            'contact_phone' => '代理商电话',
            'level' => '排行名次',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' order_amount DESC';
        $order_at_start = $this->request->order_at_start ?? '';
        $order_at_end = $this->request->order_at_end ?? '';
        $agent_name = $this->request->agent_name ?? '';
        $contact_phone = $this->request->contact_phone ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1 AND o.status IN(1,3,5) ';
        if ($order_at_start) {
            $where_sql .= " AND DATE(o.created_at)>=:order_at_start ";
            $this->reportParams['order_at_start'] = $order_at_start;
        }
        if ($order_at_end) {
            $where_sql .= " AND DATE(o.created_at)<=:order_at_end ";
            $this->reportParams['order_at_end'] = $order_at_end;
        }
        if ($agent_name) {
            $where_sql .= " AND CONCAT(a.agent_name,a.company_name) LIKE '%$agent_name%' ";
        }
        if ($contact_phone) {
            $where_sql .= " AND a.contact_phone LIKE '%$contact_phone%' ";
        }
        if ($region_id) {
            $where_sql .= " AND a.id IN(SELECT ar.agent_id FROM `tb_agent_regions` ar INNER JOIN tb_regions r on ar.proxy_region_id=r.id WHERE area_region like '%|$region_id|%') ";
        }

        $this->reportSql = "SELECT
	o.agent_id,
	count(DISTINCT o.id) AS order_number,
	SUM(op.price * op.number) AS order_amount,
	sum(op.number) AS product_number
FROM
	`tb_orders` o
INNER JOIN tb_order_products op ON o.id = op.order_id
INNER JOIN tb_agents a ON o.agent_id=a.id
$where_sql
GROUP BY
	o.agent_id";

        return $this->getReportCount();
    }
    //产品销售排行榜
    public function orderSale()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '产品销售排行榜', [
            'countType' => '统计方式',
            'sale_at' => '销售时间',
            'area_region_name' => '所属区域',
            'product_name' => '商品名称',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' order_number DESC';
        $countType = $this->request->countType ?? 'province_id';
        $sale_at_start = $this->request->sale_at_start ?? '';
        $sale_at_end = $this->request->sale_at_end ?? '';
        $product_name = $this->request->product_name ?? '';
        $region_id = $this->request->region_id ?? '';
        $field_count = 'r.' . $countType;
        $where_sql = ' WHERE 1=1  AND o.status IN(1,3,5) ';
        if ($sale_at_start) {
            $where_sql .= " AND DATE(o.created_at)>=:sale_at_start ";
            $this->reportParams['sale_at_start'] = $sale_at_start;
        }
        if ($sale_at_end) {
            $where_sql .= " AND DATE(o.created_at)<=:sale_at_end ";
            $this->reportParams['sale_at_end'] = $sale_at_end;
        }
        if ($product_name) {
            $where_sql .= " AND p.title LIKE '%$product_name%' ";
        }
        if ($region_id) {
            $where_sql .= " AND r.area_region like '%|$region_id|%' ";
//            $this->reportParams['region_id'] = $region_id;
        }

        $this->reportSql = "SELECT
	{$field_count} as region_id,
	op.product_id,
	sum(op.number) AS order_number
FROM
	`tb_orders` o
INNER JOIN tb_order_products op ON o.id = op.order_id
INNER JOIN tb_products p ON op.product_id=p.id
INNER JOIN tb_agents a ON o.agent_id = a.id
LEFT JOIN tb_regions r ON a.office_region_id = r.id
$where_sql AND $field_count>0
GROUP BY
	{$field_count},op.product_id";

        return $this->getReportCount();
    }
    //会员新增情况统计
    public function member()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '会员新增情况统计', [
            'countType' => '统计方式',
            'reg_date' => '注册时间',
            'area_region_name' => '所属区域',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' count_type DESC';
        $reg_date_start = $this->request->reg_date_start ?? '';
        $reg_date_end = $this->request->reg_date_end ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('reg_date');
        if ($reg_date_start) {
            $where_sql .= " AND DATE(m.reg_date)>=:reg_date_start ";
            $this->reportParams['reg_date_start'] = $reg_date_start;
        }
        if ($reg_date_end) {
            $where_sql .= " AND DATE(m.reg_date)<=:reg_date_end ";
            $this->reportParams['reg_date_end'] = $reg_date_end;
        }
        if ($region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$region_id|%') ";
        }

        $this->reportSql = "SELECT
	{$field_count} AS count_type,
	count(1) AS count
FROM
	`tb_members` m
INNER JOIN tb_member_agents ma ON m.id = ma.member_id
 $where_sql
GROUP BY
	count_type";

        return $this->getReportCount();
    }
    //代理商会员及佣金统计
    public function agentBill()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '代理商会员及佣金统计', [
            'countType' => '统计方式',
            'name' => '代理商名称',
            'count_date' => ($this->request->timeType == 'reg' ? '注册时间' : '扫码时间'),
            'area_region_name' => '代理商区域',
            'level' => '排行名次',
        ]);
        $name = $this->request->name ?? '';
        $region_id = $this->request->region_id ?? '';
        $timeType = $this->request->timeType ?? '';
        $count_date_start = $this->request->count_date_start ?? '';
        $count_date_end = $this->request->count_date_end ?? '';
        $this->orderBy = $this->request->_orderby ?? ' amount DESC';

        $where_sql = ' WHERE 1=1 ';
        if ($name) {
            $where_sql .= " AND a.agent_name LIKE '%$name%' ";
        }
        if ($timeType == 'reg') {
            if ($count_date_start) {
                $where_sql .= " AND DATE(ma.created_at)>=:count_date_start ";
                $this->reportParams['count_date_start'] = $count_date_start;
            }

            if ($count_date_end) {
                $where_sql .= " AND DATE(ma.created_at)<=:count_date_end ";
                $this->reportParams['count_date_end'] = $count_date_end;
            }

        } elseif ($timeType == 'scan') {
            if ($count_date_start) {
                $where_sql .= " AND DATE(b.bill_at)>=:count_date_start ";
                $this->reportParams['count_date_start'] = $count_date_start;
            }

            if ($count_date_end) {
                $where_sql .= " AND DATE(b.bill_at)<=:count_date_end ";
                $this->reportParams['count_date_end'] = $count_date_end;
            }
        }
        if ($region_id) {
            $where_sql .= " AND a.id IN(SELECT ar.agent_id FROM `tb_agent_regions` ar INNER JOIN tb_regions r on ar.proxy_region_id=r.id WHERE area_region like '%|$region_id|%') ";
        }
        $this->reportSql = "SELECT
	a.id as agent_id,
	count(1) AS number,
	(SELECT SUM(amount) from tb_bills where agent_id=a.id) as amount
FROM
	`tb_agents` a
INNER JOIN tb_member_agents ma ON a.id = ma.agent_id
LEFT JOIN tb_bills b ON a.id = b.agent_id
$where_sql
GROUP BY
	a.id";

        return $this->getReportCount();
    }
    //代理商单品进货统计
    public function oneProduct()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '代理商单品进货统计', [
            'order_at' => '下单时间',
            'agent_name' => '代理商名称',
            'contact_phone' => '代理商电话',
            'product_name' => '代理商名称',
            'product_name' => '商品名称',
            'area_region_name' => '代理商区域',
            'level' => '排行名次',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' order_amount DESC';
        $order_at_start = $this->request->order_at_start ?? '';
        $order_at_end = $this->request->order_at_end ?? '';
        $agent_name = $this->request->agent_name ?? '';
        $contact_phone = $this->request->contact_phone ?? '';
        $product_name = $this->request->product_name ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1  AND o.status IN(1,3,5)';
        if ($order_at_start) {
            $where_sql .= " AND DATE(o.created_at)>=:order_at_start ";
            $this->reportParams['order_at_start'] = $order_at_start;
        }
        if ($order_at_end) {
            $where_sql .= " AND DATE(o.created_at)<=:order_at_end ";
            $this->reportParams['order_at_end'] = $order_at_end;
        }
        if ($agent_name) {
            $where_sql .= " AND CONCAT(a.agent_name,a.company_name) LIKE '%$agent_name%' ";
//            $this->reportParams['agent_name'] = $agent_name;
        }
        if ($contact_phone) {
            $where_sql .= " AND a.contact_phone LIKE '%$contact_phone%' ";
//            $this->reportParams['contact_phone'] = $contact_phone;
        }
        if ($product_name) {
            $where_sql .= " AND p.title LIKE '%$product_name%' ";
//            $this->reportParams['product_name'] = $product_name;
        }
        if ($region_id) {
            $where_sql .= " AND a.id IN(SELECT ar.agent_id FROM `tb_agent_regions` ar INNER JOIN tb_regions r on ar.proxy_region_id=r.id WHERE area_region like '%|$region_id|%') ";
        }
        $this->reportSql = "SELECT
	o.agent_id,
       op.product_id,
	count(DISTINCT o.id) AS order_number,
	SUM(op.price*op.number) AS order_amount,
	sum(op.number) AS product_number
FROM
	`tb_orders` o
INNER JOIN tb_order_products op ON o.id = op.order_id
INNER JOIN tb_products p ON op.product_id=p.id
INNER JOIN tb_agents a ON o.agent_id=a.id
$where_sql
GROUP BY
	o.agent_id,op.product_id";

        return $this->getReportCount();
    }
    //会员地区分布情况佣金统计
    public function memberAreaBill()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '会员地区分布情况佣金统计', [
            'countType' => '统计方式',
            'bill_at' => '佣金领取时间',
            'area_region_name' => '会员地区',
            'area_region_name2' => '代理商区域',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' amount DESC';
        $bill_at_start = $this->request->bill_at_start ?? '';
        $bill_at_end = $this->request->bill_at_end ?? '';
        $real_name = $this->request->real_name ?? '';
        $mobile = $this->request->mobile ?? '';
        $member_region_id = $this->request->member_region_id ?? '';
        $agent_region_id = $this->request->agent_region_id ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('bill_at');
        if ($bill_at_start) {
            $where_sql .= " AND DATE(b.bill_at)>=:bill_at_start ";
            $this->reportParams['bill_at_start'] = $bill_at_start;
        }
        if ($bill_at_end) {
            $where_sql .= " AND DATE(b.bill_at)<=:bill_at_end ";
            $this->reportParams['bill_at_end'] = $bill_at_end;
        }
        if ($real_name) {
            $where_sql .= " AND m.real_name LIKE '%$real_name%' ";
        }
        if ($mobile) {
            $where_sql .= " AND m.mobile LIKE '%$mobile%' ";
        }
        if ($member_region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$member_region_id|%') ";
        }
        if ($agent_region_id) {
            $where_sql .= " AND b.agent_id IN(SELECT ar.agent_id FROM `tb_agent_regions` ar INNER JOIN tb_regions r on ar.proxy_region_id=r.id WHERE area_region like '%|$agent_region_id|%') ";
        }

        $this->reportSql = "SELECT
	{$field_count} AS count_type,
	m.resident_region_id as region_id,
	SUM(b.amount) AS amount,
	SUM(
		CASE
		WHEN b.`status` = 1 THEN
			b.amount
		ELSE
			0
		END
	) AS yes_pay,
	SUM(
		CASE
		WHEN b.`status` = 0 THEN
			b.amount
		ELSE
			0
		END
	) AS no_pay
FROM
	`tb_bills` b
INNER JOIN tb_members m ON b.member_id = m.id
$where_sql
GROUP BY
	count_type,
	m.resident_region_id";

        return $this->getReportCount();
    }
    //会员男女比例佣金统计
    public function memberGenderBill()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '会员男女比例佣金统计', [
            'countType' => '统计方式',
            'bill_at' => '佣金领取时间',
            'gender' => '会员性别',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' amount DESC';
        $bill_at_start = $this->request->bill_at_start ?? '';
        $bill_at_end = $this->request->bill_at_end ?? '';
        $gender = $this->request->gender ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('bill_at');
        if ($bill_at_start) {
            $where_sql .= " AND DATE(b.bill_at)>=:bill_at_start ";
            $this->reportParams['bill_at_start'] = $bill_at_start;
        }
        if ($bill_at_end) {
            $where_sql .= " AND DATE(b.bill_at)<=:bill_at_end ";
            $this->reportParams['bill_at_end'] = $bill_at_end;
        }
        if ($gender) {
            $where_sql .= " AND m.gender=:gender ";
            $this->reportParams['gender'] = $gender;
        }
        if ($region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$region_id|%') ";
        }

        $this->reportSql = "SELECT
	{$field_count} AS count_type,
	m.gender,
	SUM(b.amount) AS amount,
	SUM(
		CASE
		WHEN b.`status` = 1 THEN
			b.amount
		ELSE
			0
		END
	) AS yes_pay,
	SUM(
		CASE
		WHEN b.`status` = 0 THEN
			b.amount
		ELSE
			0
		END
	) AS no_pay
FROM
	`tb_bills` b
INNER JOIN tb_members m ON b.member_id = m.id
$where_sql
GROUP BY
	count_type,
	m.gender";

        return $this->getReportCount();
    }
    //会员年龄区段佣金统计
    public function memberAgeBill()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '会员年龄区段佣金统计', [
            'countType' => '统计方式',
            'bill_at' => '佣金领取时间',
            'age_type' => '会员年龄段',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' amount DESC';
        $bill_at_start = $this->request->bill_at_start ?? '';
        $bill_at_end = $this->request->bill_at_end ?? '';
        $age_type = $this->request->age_type ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('bill_at');
        if ($bill_at_start) {
            $where_sql .= " AND DATE(b.bill_at)>=:bill_at_start ";
            $this->reportParams['bill_at_start'] = $bill_at_start;
        }
        if ($bill_at_end) {
            $where_sql .= " AND DATE(b.bill_at)<=:bill_at_end ";
            $this->reportParams['bill_at_end'] = $bill_at_end;
        }
        if ($age_type) {
            list($age_min, $age_max) = explode('-', $age_type);
            if ($age_min) {
                $where_sql .= " AND (
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	) >=:age_min ";
                $this->reportParams['age_min'] = $age_min;
            }
            if ($age_max) {
                $where_sql .= " AND (
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	) >=:age_max ";
                $this->reportParams['age_max'] = $age_max;
            }
        }

        $this->reportSql = "SELECT
	{$field_count} AS count_type,
	CASE
WHEN CASE
WHEN IFNULL(YEAR(birthday), 0) = 0 THEN
	0
ELSE
	(
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	)
END BETWEEN 1
AND 20 THEN
	'1-20'
WHEN CASE
WHEN IFNULL(YEAR(birthday), 0) = 0 THEN
	0
ELSE
	(
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	)
END BETWEEN 21
AND 30 THEN
	'11-20'
WHEN CASE
WHEN IFNULL(YEAR(birthday), 0) = 0 THEN
	0
ELSE
	(
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	)
END BETWEEN 31
AND 40 THEN
	'31-40'
WHEN CASE
WHEN IFNULL(YEAR(birthday), 0) = 0 THEN
	0
ELSE
	(
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	)
END BETWEEN 41
AND 50 THEN
	'41-50'
WHEN CASE
WHEN IFNULL(YEAR(birthday), 0) = 0 THEN
	0
ELSE
	(
		DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(birthday, '%Y') - 1 + (
			DATE_FORMAT(birthday, '%m%d') <= DATE_FORMAT(NOW(), '%m%d')
		)
	)
END > 50 THEN
	'51+'
END AS new_age,
 SUM(b.amount) AS amount,
 SUM(
	CASE
	WHEN b.`status` = 1 THEN
		b.amount
	ELSE
		0
	END
) AS yes_pay,
 SUM(
	CASE
	WHEN b.`status` = 0 THEN
		b.amount
	ELSE
		0
	END
) AS no_pay
FROM
	`tb_bills` b
INNER JOIN tb_members m ON b.member_id = m.id
$where_sql
GROUP BY
	count_type,
	new_age";

        return $this->getReportCount();
    }
    //会员佣金排行榜
    public function memberBill()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '会员佣金排行榜', [
            'countType' => '统计方式',
            'bill_at' => '佣金领取时间',
            'area_region_name' => '所属区域',
            'real_name' => '会员名称',
            'mobile' => '会员电话',
            'level' => '排行名次',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' amount DESC';
        $bill_at_start = $this->request->bill_at_start ?? '';
        $bill_at_end = $this->request->bill_at_end ?? '';
        $real_name = $this->request->real_name ?? '';
        $mobile = $this->request->mobile ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('bill_at');
        if ($bill_at_start) {
            $where_sql .= " AND DATE(b.bill_at)>=:bill_at_start ";
            $this->reportParams['bill_at_start'] = $bill_at_start;
        }
        if ($bill_at_end) {
            $where_sql .= " AND DATE(b.bill_at)<=:bill_at_end ";
            $this->reportParams['bill_at_end'] = $bill_at_end;
        }
        if ($real_name) {
            $where_sql .= " AND m.real_name LIKE '%$real_name%' ";
//            $this->reportParams['real_name'] = $real_name;
        }
        if ($mobile) {
            $where_sql .= " AND m.mobile LIKE '%$mobile%' ";
//            $this->reportParams['mobile'] = $mobile;
        }
        if ($region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$region_id|%') ";
        }
        $this->reportSql = "SELECT
	{$field_count} AS count_type,
	b.member_id,
	SUM(b.amount) AS amount,
	SUM(
		CASE
		WHEN b.`status` = 1 THEN
			b.amount
		ELSE
			0
		END
	) AS yes_pay,
	SUM(
		CASE
		WHEN b.`status` = 0 THEN
			b.amount
		ELSE
			0
		END
	) AS no_pay
FROM
	`tb_bills` b
INNER JOIN tb_members m ON b.member_id = m.id
$where_sql
GROUP BY
	count_type,
	b.member_id";

        return $this->getReportCount();
    }
    //会员下线数量排行榜
    public function memberChild()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '会员下线数量排行榜', [
            'countType' => '统计方式',
            'bill_at' => '佣金领取时间',
            'area_region_name' => '所属区域',
            'real_name' => '会员名称',
            'mobile' => '会员电话',
            'level' => '排行名次',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' count DESC';
        $real_name = $this->request->real_name ?? '';
        $mobile = $this->request->mobile ?? '';
        $region_id = $this->request->region_id ?? '';

        $where_sql = ' WHERE 1=1 ';
        if ($real_name) {
            $where_sql .= " AND m.real_name LIKE '%$real_name%' ";
        }
        if ($mobile) {
            $where_sql .= " AND m.mobile LIKE '%$mobile%' ";
        }
        if ($region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$region_id|%') ";
        }
        $this->reportSql = "SELECT
	ma.referrer_member_id AS member_id,
	count(1) AS count
FROM
	`tb_members` m
INNER JOIN tb_member_agents ma ON m.id = ma.referrer_member_id
$where_sql
GROUP BY
	ma.referrer_member_id";

        return $this->getReportCount();
    }
    //佣金汇总统计
    public function bill()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '佣金汇总统计', [
            'countType' => '统计方式',
            'bill_at' => '佣金领取时间',
            'area_region_name' => '所属区域',
            'real_name' => '会员名称',
            'mobile' => '会员电话',
            'level' => '排行名次',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' amount DESC';
        $bill_at_start = $this->request->bill_at_start ?? '';
        $bill_at_end = $this->request->bill_at_end ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('bill_at');
        if ($bill_at_start) {
            $where_sql .= " AND DATE(b.bill_at)>=:bill_at_start ";
            $this->reportParams['bill_at_start'] = $bill_at_start;
        }
        if ($bill_at_end) {
            $where_sql .= " AND DATE(b.bill_at)<=:bill_at_end ";
            $this->reportParams['bill_at_end'] = $bill_at_end;
        }
        if ($region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$region_id|%') ";
        }
        $this->reportSql = "SELECT
	{$field_count} AS count_type,
	SUM(b.amount) AS amount,
	SUM(
		CASE
		WHEN b.`status` = 1 THEN
			b.amount
		ELSE
			0
		END
	) AS yes_pay,
	SUM(
		CASE
		WHEN b.`status` = 0 THEN
			b.amount
		ELSE
			0
		END
	) AS no_pay
FROM
	`tb_bills` b
INNER JOIN tb_members m ON b.member_id = m.id
$where_sql
GROUP BY
	count_type";

        return $this->getReportCount();
    }
    //产品扫码次数统计
    public function productScan()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '产品扫码次数统计', [
            'countType' => '统计方式',
            'scan_at' => '扫码时间',
            'area_region_name' => '所属区域',
            'real_name' => '会员名称',
            'mobile' => '会员电话',
            'product_name' => '商品名称',
            'level' => '排行名次',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' count_type DESC';
        $scan_at_start = $this->request->scan_at_start ?? '';
        $scan_at_end = $this->request->scan_at_end ?? '';
        $product_name = $this->request->product_name ?? '';
        $region_id = $this->request->region_id ?? '';
        $where_sql = ' WHERE 1=1 ';
        $field_count = $this->getCountTypeDate('oql.created_at');
        if ($scan_at_start) {
            $where_sql .= " AND DATE(oql.created_at) >=:scan_at_start ";
            $this->reportParams['scan_at_start'] = $scan_at_start;
        }
        if ($scan_at_end) {
            $where_sql .= " AND DATE(oql.created_at) <=:scan_at_end ";
            $this->reportParams['scan_at_end'] = $scan_at_end;
        }
        if ($product_name) {
            $where_sql .= " AND p.title LIKE '%$product_name%' ";
//            $this->reportParams['product_name'] = $product_name;
        }
        if ($region_id) {
            $where_sql .= " AND m.resident_region_id IN(SELECT r.id FROM tb_regions r  WHERE area_region like '%|$region_id|%') ";
        }

        $this->reportSql = "SELECT
	    $field_count AS count_type,
	p.id AS product_id,
	p.title,
	SUM(
		CASE
		WHEN oql.member_id > 0 THEN
			1
		ELSE
			0
		END
	) AS member_num,
	SUM(
		CASE
		WHEN oql.member_id = 0 THEN
			1
		ELSE
			0
		END
	) AS no_member_num
FROM
	`tb_order_qrcode_logs` oql
INNER JOIN tb_order_qrcodes oq ON oql.qrcode_id = oq.id
INNER JOIN tb_order_products op ON oq.order_product_id = op.id
INNER JOIN tb_products p ON op.product_id = p.id
LEFT JOIN tb_members m ON oql.member_id=m.id
$where_sql
GROUP BY
	count_type,
	p.id";

        return $this->getReportCount();
    }

    /**
     * 财务明细表
     */
    public function payTrade()
    {
        $this->functionName = __FUNCTION__;
        $this->setExportName(null, '财务明细表', [
            'trade_at' => '交易时间',
            'order_no' => '订单号',
            'transaction_no' => '第三方交易号',
        ]);
        $this->orderBy = $this->request->_orderby ?? ' pt.trade_at DESC';
        $trade_at_start = $this->request->trade_at_start ?? '';
        $trade_at_end = $this->request->trade_at_end ?? '';
        $order_no = $this->request->order_no ?? '';
        $transaction_no = $this->request->transaction_no ?? '';
        $where_sql = ' ';
//        $field_count        = $this->getCountTypeDate('oql.created_at');
        $where_sql .= " AND pt.access_type =:access_type ";
        $this->reportParams['access_type'] = 'App\\Entities\\Order';
        if ($trade_at_start) {
            $where_sql .= " AND DATE(pt.trade_at) >=:trade_at_start ";
            $this->reportParams['trade_at_start'] = $trade_at_start;
        }
        if ($trade_at_end) {
            $where_sql .= " AND DATE(pt.trade_at) <=:trade_at_end ";
            $this->reportParams['trade_at_end'] = $trade_at_end;


        }
        if ($order_no) {
            $where_sql .= " AND o.order_no LIKE '%$order_no%' ";
        }
        if ($transaction_no) {
            $where_sql .= " AND pt.transaction_no LIKE '%$transaction_no%' ";
        }

        $this->reportSql = "SELECT
	pt.trade_at,
	o.order_no,
	pt.transaction_no,
	pt.type,
	pt.trade_price
FROM
	`tb_pay_trades` pt
INNER JOIN tb_orders o ON pt.access_id = o.id
WHERE
 pt.`status` = 1 $where_sql";
//        echo $this->reportSql;

        return $this->getReportCount();
    }
}
