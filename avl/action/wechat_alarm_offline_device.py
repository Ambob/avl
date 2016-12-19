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
#从device表中找出时间过久的或者电池有问题的
sql="select ID,d_name,serial,battery,battery_v,(UNIX_TIMESTAMP()-last_time),FROM_UNIXTIME(last_time),(select openid from user where ID=(select user from user_device where device=p1.ID and quanxian='2')),company,(select company_more from company where ID=p1.company),battery,battery_v from device as p1 where send_it='0'"
cur1.execute(sql)
for d in cur1.fetchall():
	#设备离线的
	if(int(d[5]>600)):
		sqlq="select ID,appid,secret,access_token,UNIX_TIMESTAMP()-last_time,temp_id from wechat where company='%s'"%(d[8])
		cur.execute(sqlq)
		for s in cur.fetchall():
			ID=s[0]
			appid=s[1]
			secrect=s[2]
			access_token=s[3]
			las=s[4]
			temp_id=s[5]
		push=WechatPush(appid,secrect)
		if(s[4]>7200):
			access_token=push.getToken()
			sql="update wechat set access_token='%s',last_time='%d' where ID='%d'"%(access_token,int(time.time()),ID)
			cur.execute(sqlq)
		data={"first":{"value":"%s设备离线"%(d[1].encode("utf-8")),"color":"#173177"},"content":{"value":"监测到当前编号为%s的%s离线,请及时处理,外接电源DC%sV,内部电池DC%sV"%(d[2].encode("utf-8"),d[1].encode("utf-8"),d[11].encode("utf-8"),d[10].encode("utf-8")),"color":"#173177"},"occurtime":{"value":"%s"%(d[6].strftime("%Y-%m-%d %H:%M:%S")),"color":"#173177"},"remark":{"value":"%s"%(d[9].encode("utf-8")),"color":"#173177"}}
		ok=push.do_push(d[7].encode("utf-8"),s[5].encode("utf-8"),'',data,'1e1e1e',access_token)
		if(ok=='ok'):
			sql="update device set send_it='1' where ID='%d'"%(d[0])
			cur1.execute(sql)
			conn1.commit()
	#失去了外接电源的
	# if(float(d[4])==0):
	# 	sqlq="select ID,appid,secret,access_token,UNIX_TIMESTAMP()-last_time from wechat"
	# 	cur.execute(sqlq)
	# 	for s in cur.fetchall():
	# 		ID=s[0]
	# 		appid=s[1]
	# 		secrect=s[2]
	# 		access_token=s[3]
	# 		las=s[4]
	# 	push=WechatPush(appid,secrect)
	# 	if(s[4]>7000):
	# 		access_token=push.getToken()
	# 		sql="update wechat set access_token='%s' where ID='%d'"%(access_token,ID)
	# 		cur.execute(sqlq)
	# 	data={"first":{"value":"%s设备外接电源为0V"%(d[1].encode("utf-8")),"color":"#173177"},"content":{"value":"监测到当前编号为%s的%s外接电源为0V，请及时处理"%(d[2].encode("utf-8"),d[1].encode("utf-8")),"color":"#173177"},"occurtime":{"value":"%s"%(d[6].strftime("%Y-%m-%d %H:%M:%S")),"color":"#173177"},"remark":{"value":"恒必达科技","color":"#173177"}}
	# 	ok=push.do_push(d[7].encode("utf-8"),'cL_tUnqLMCS1OOOBSvzICe_1JRvET60eTfdik1nMfaE','',data,'1e1e1e')
	# 	print ok
	# 	if(ok=='ok'):
	# 		sql="update device set send_it='1' where ID='%d'"%(d[0])
	# 		print sql
	# 		cur1.execute(sql)
	# 		conn1.commit()
	# #失去了外接电源的
	# if(float(d[3])<3.6):
	# 	sqlq="select ID,appid,secret,access_token,UNIX_TIMESTAMP()-last_time from wechat"
	# 	cur.execute(sqlq)
	# 	for s in cur.fetchall():
	# 		ID=s[0]
	# 		appid=s[1]
	# 		secrect=s[2]
	# 		access_token=s[3]
	# 		las=s[4]
	# 	push=WechatPush(appid,secrect)
	# 	if(s[4]>7000):
	# 		access_token=push.getToken()
	# 		sql="update wechat set access_token='%s' where ID='%d'"%(access_token,ID)
	# 		cur.execute(sqlq)
	# 	data={"first":{"value":"%s设备内部电池电压低"%(d[1].encode("utf-8")),"color":"#173177"},"content":{"value":"监测到当前编号为%s的%s内部电池电压为%s，请及时处理"%(d[2].encode("utf-8"),d[1].encode("utf-8")),"color":"#173177"},"occurtime":{"value":"%s"%(d[6].strftime("%Y-%m-%d %H:%M:%S")),"color":"#173177"},"remark":{"value":"恒必达科技","color":"#173177"}}
	# 	ok=push.do_push(d[7].encode("utf-8"),'cL_tUnqLMCS1OOOBSvzICe_1JRvET60eTfdik1nMfaE','',data,'1e1e1e')
	# 	if(ok=='ok'):
	# 		sql="update device set send_it='1' where ID='%d'"%(d[0])
	# 		cur1.execute(sql)
	# 		conn1.commit()
