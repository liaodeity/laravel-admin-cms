#! /bin/bash
#如信息已修改，需改的user和密码以及数据库名
#添加定时crontab每天0点执行备份 01 00 * * * /alidata/www/qinglong/bin/backsql.sh
cd /alidata/backup
SqlBakName=back_mysql_$(date +%Y%m%d).tar.gz
mysqldump --host=127.0.0.1 --user=root --password="9jZdg-.ekHy2187" --lock-all-tables qinglong > qinglong.sql
tar -zcf $SqlBakName -C /alidata/backup qinglong.sql
rm -f back_mysql_$(date +%Y%m%d --date='60 days ago').tar.gz
