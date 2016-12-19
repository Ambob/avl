# encoding: utf-8
from WechatPush import WechatPush
import pymysql
import sys
import time
reload(sys) 
sys.setdefaultencoding('utf-8')
conn = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='^&^^%)%%',db='wechat',charset='utf8')
cur = conn.cursor()
conn1 = pymysql.connect(host='127.0.0.1', port=3306, user='root', passwd='^&^^%)%%',db='avl',charset='utf8')
cur1 = conn1.cursor()
sql="select (select nick_name from `user` where ID=`user`),(select serial from device where ID=device),(case (select user_enable from device where ID=device) when 0 then 'obYe0juyV1Ba_pTghOgo5RdcwsYw' else (select openid from `user` where ID=(select user_enable from device where ID=device)) end),ID,(select company from `user` where ID=`user`),(select company_more from company where ID=(select company from `user` where ID=`user`)) from user_device where quanxian=0 and send_it=0"
cur1.execute(sql)
for sa in cur1.fetchall():
	sqlq="select ID,appid,secret,access_token,UNIX_TIMESTAMP()-last_time,temp_id1 from wechat where company='%s'"%(sa[4])
	cur.execute(sqlq)
	for s in cur.fetchall():
		ID=s[0]
		appid=s[1]
		secrect=s[2]
		access_token=s[3]
		las=s[4]
		temp_id=s[5]
	company=sa[5]
	push=WechatPush(appid,secrect)
	if(s[4]>7200):
		access_token=push.getToken()
		sql="update wechat set access_token='%s',last_time='%d' where ID='%d'"%(access_token,int(time.time()),ID)
		cur.execute(sql)
		conn.commit()
	touser=sa[2]
	data={"first":{"value":"有用户提交了查看编号为%s设备的申请"%(sa[1]),"color":"#173177"},"keyword1":{"value":"%s"%(sa[0]),"color":"#173177"},"keyword2":{"value":"2016年10月19日","color":"#173177"},"remark":{"value":"%s"%(company),"color":"#173177"}}
	result=push.do_push(touser,temp_id,'temp.hbd.so/avl/index.html?company=liuchang',data,'1e1e1e',access_token)
	print result
	if(result=='ok'):
		sql="update user_device set send_it=1 where ID=%s"%(sa[3])
		cur1.execute(sql)
		conn1.commit()



