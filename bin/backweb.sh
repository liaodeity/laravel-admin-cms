#!/bin/bash

#添加定时crontab每天0点执行备份 01 00 * * * /alidata/www/qinglong/bin/backweb.sh
cd /alidata/backup
tar -zcPf back_web_$(date +%Y%m%d).tar.gz /alidata/www/qinglong/* /alidata/www/qinglong/.[!.]* --exclude /alidata/www/qinglong/storage
rm -f back_web_$(date +%Y%m%d --date='30 days ago').tar.gz
