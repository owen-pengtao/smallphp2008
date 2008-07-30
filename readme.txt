使用方法
需要上传目录的，先执行 /install.php，或根据情况修改install.php，所创建的目录加入到svn过滤列表

后台登录	http://www.smallphp.cn/root/
用户名：owen
密码：owen

后台开发方法：
1、参考 root/tag.php
2、复制 root/tag.php为 test.php，并修改class名称，需要显示的文字，以及数据库字段对应的文字
3、复制 app/controller/root_tag.php 为 root_test.php 并设置表名，自增id，添加对应的save方法

