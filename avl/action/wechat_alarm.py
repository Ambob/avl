# encoding: utf-8
from WechatPush import WechatPush
import pymysql
import sys
import time
import datetime
reload(sys) 
sys.setdefaultencoding('utf-8')
conn = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='^&^^%)%%',db='wechat',charset='utf8')
cur = conn.cursor()
conn1 = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='^&^^%)%%',db='avl',charset='utf8')
cur1 = conn1.cursor()
#更新报警记录，当10分钟内没有新的报警发生后
sql="update alarm set End_Time='%d' where (UNIX_TIMESTAMP()-last_time)>600 and End_Time='0'"%(int(time.time()))
cur1.execute(sql)
conn1.commit()
# sql="update alarm set weixin=2 where (UNIX_TIMESTAMP()-last_time)>weixin*7200 and End_Time='0'"
# cur1.execute(sql)
# conn1.commit()
#从data_temp表里面查找超出上限或者低于下限的数据
sql="select D_ID,tag_num,D_Data,Real_Time,top,buttom from data_temp where (0+D_Data>0+top) or (0+D_Data<0+buttom)"
cur1.execute(sql)
for d in cur1.fetchall():
	#去alarm表里面看一看是不是已经有这一条报警记录了
	sql="select ID from alarm where End_Time='0' and tag_num='%s'"%(d[1])
	cur1.execute(sql)
	i=0
	for g in cur1.fetchall():
		i=1
	if(i==1):
		#如果有，那就把这一条报警记录的最后发生时间last_time更新一下
		sql="update alarm set last_time='%d' where ID='%d'"%(int(time.time()),g[0])
		cur1.execute(sql)
		conn1.commit()
	if(i==0):
		#如果没有，那就插入一条新的
		sql="insert into alarm values (null,%d,'%s','%s','0','0','0','0','%s','0','0','%s')"%(d[0],d[1],d[2],d[3],int(time.time()))
		cur1.execute(sql)
		conn1.commit()
		#同时查找需要下发报警新的各项参数,如果要给权限为1的用户发，该语句需修改，left join user  in
sql="select (select d_name from device where ID=p1.D_ID),(select tag_name from data_temp where tag_num=p1.tag_num),p1.tag_num,p1.data,(select top from data_temp where tag_num=p1.tag_num),p2.openid,FROM_UNIXTIME(p1.Start_Time),p1.ID,(select buttom from data_temp where tag_num=p1.tag_num),(select company from device where ID=p1.D_ID),(select company_more from company where ID=(select company from device where ID=p1.D_ID)) from alarm as p1 left join user as p2 on p2.ID in (select user from user_device where device=p1.D_ID) where UNIX_TIMESTAMP()-p1.End_Time>p1.weixin*600 and p2.alarm='1' and p1.weixin<5"
cur1.execute(sql)
for f in cur1.fetchall():
	sqlq="select ID,appid,secret,access_token,UNIX_TIMESTAMP()-last_time,temp_id from wechat where company='%s'"%(f[9])
	cur.execute(sqlq)
	for s in cur.fetchall():
		ID=s[0]
		appid=s[1]
		secrect=s[2]
		access_token=s[3]
		las=s[4]
	push=WechatPush(appid,secrect)
	if(s[4]>7200):
		access_token=push.getToken()
		sql="update wechat set access_token='%s',last_time='%d' where ID='%d'"%(access_token,int(time.time()),ID)
		cur.execute(sql)
		conn.commit()
	if(f[4]):
		if(float(f[3])>float(f[4])):
			data={"first":{"value":"%s%s温度超过限制"%(f[0].encode("utf-8"),f[1].encode("utf-8")),"color":"#173177"},"content":{"value":"监测到当前%s编号%s的%s温度为%s℃，超过上限%s℃请及时处理"%(f[0].encode("utf-8"),f[2].encode("utf-8"),f[1].encode("utf-8"),f[3].encode("utf-8"),f[4].encode("utf-8")),"color":"#173177"},"occurtime":{"value":"%s"%(f[6].strftime("%Y-%m-%d %H:%M:%S")),"color":"#173177"},"remark":{"value":"%s"%(f[10].encode("utf-8")),"color":"#173177"}}
			if(f[5]):
				ok=push.do_push(f[5].encode("utf-8"),s[5].encode("utf-8"),'',data,'1e1e1e',access_token)
				print ok
				if(ok=='ok'):
					sql="update alarm set weixin=weixin+1 where ID='%d'"%(f[7])
					cur1.execute(sql)
					conn1.commit()
	if(f[8]):
		if(float(f[3])<float(f[8])):
			data={"first":{"value":"%s%s温度超过限制"%(f[0].encode("utf-8"),f[1].encode("utf-8")),"color":"#173177"},"content":{"value":"监测到当前%s编号%s的%s温度为%s℃，低于下限%s℃请及时处理"%(f[0].encode("utf-8"),f[2].encode("utf-8"),f[1].encode("utf-8"),f[3].encode("utf-8"),f[8].encode("utf-8")),"color":"#173177"},"occurtime":{"value":"%s"%(f[6].strftime("%Y-%m-%d %H:%M:%S")),"color":"#173177"},"remark":{"value":"%s"%(f[10].encode("utf-8")),"color":"#173177"}}
			if(f[5]):
				ok=push.do_push(f[5].encode("utf-8"),s[5].encode("utf-8"),'',data,'1e1e1e',access_token)
				print ok
				if(ok=='ok'):
					sql="update alarm set weixin=weixin+1 where ID='%d'"%(f[7])
					cur1.execute(sql)
					conn1.commit()
