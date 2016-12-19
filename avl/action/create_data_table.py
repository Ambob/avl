# -*- coding: utf8 -*-
import pymysql
import random
import time
conn = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='^&^^%)%%',db='avl',charset='utf8')
cur = conn.cursor()
sqlq="select ID from device where data_table='0'"
cur.execute(sqlq)
for s in cur.fetchall():
	sql="create table IF NOT EXISTS data_min_%s like data_min"%(s[0])
	cur.execute(sql)
	conn.commit()
	sql="create table IF NOT EXISTS data_ten_%s like data_ten"%(s[0])
	cur.execute(sql)
	conn.commit()
	sql="create table IF NOT EXISTS data_hour_%s like data_hour"%(s[0])
	cur.execute(sql)
	conn.commit()
	sql="CREATE EVENT IF NOT EXISTS copy_to_data_min_%s ON SCHEDULE EVERY 60 SECOND  ON COMPLETION PRESERVE  DO insert into data_min_%s (tag_num,D_Data,D_Data_hum,D_Data_v,Real_Time) select tag_num,D_Data,D_Data_hum,D_Data_v,DATE_FORMAT(from_unixtime(unix_timestamp()),'%%Y/%%m/%%d %%H:%%i') as Real_Time from data_temp where D_ID='%s' and UNIX_TIMESTAMP()-Real_Time<110 GROUP BY tag_num"%(s[0],s[0],s[0])
	cur.execute(sql)
	conn.commit()
	sql="CREATE EVENT IF NOT EXISTS copy_to_data_ten_%s ON SCHEDULE EVERY 600 SECOND  ON COMPLETION PRESERVE  DO insert into data_ten_%s (tag_num,D_Data,D_Data_hum,D_Data_v,Real_Time) select tag_num,D_Data,D_Data_hum,D_Data_v,DATE_FORMAT(from_unixtime(unix_timestamp()),'%%Y/%%m/%%d %%H:%%i') as Real_Time from data_temp where D_ID='%s' and UNIX_TIMESTAMP()-Real_Time<200 GROUP BY tag_num"%(s[0],s[0],s[0])
	cur.execute(sql)
	conn.commit()
	sql="CREATE EVENT IF NOT EXISTS copy_to_data_hour_%s ON SCHEDULE EVERY 3600 SECOND  ON COMPLETION PRESERVE  DO insert into data_hour_%s (tag_num,D_Data,D_Data_hum,D_Data_v,Real_Time) select tag_num,D_Data,D_Data_hum,D_Data_v,DATE_FORMAT(from_unixtime(unix_timestamp()),'%%Y/%%m/%%d %%H:%%i') as Real_Time from data_temp where D_ID='%s' and UNIX_TIMESTAMP()-Real_Time<200 GROUP BY tag_num"%(s[0],s[0],s[0])
	cur.execute(sql)
	conn.commit()
	sql="update device set data_table=1 where ID=%s"%(s[0])
	cur.execute(sql)
	conn.commit()
