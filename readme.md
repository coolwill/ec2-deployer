##功能简介
    简化EC2服务器上的程序部署工作，一次配置，终生受益。免去多台同类服务器重复部署工作，免去制作AMI镜像的麻烦。
    部署时只需制作好部署包和部署脚本(脚本可重复使用)，上传到指定位置，然后在EC2服务器上执行init.sh即可。
    这对于从autoscaling组中启动的服务器尤其有用，可将init.sh的内容放入user-data里面，然后只需重启EC2即可完成部署。


##文件列表
    get.php     服务器端程序
	rules.ini   服务器端配置文件
	init.sh     EC2执行脚本，需要在EC2上安装aws cli，并配置IAM角色(赋予ec2:DescribeTags权限)

##get.php文件说明：
    读取rules.ini，将当前请求参数与配置文件进行匹配，并返回匹配到的文件。

##rules.ini文件说明：
	每个section对应一个配置，匹配时按照从上至下原则，如果匹配到某一个配置，则立即返回该配置指定的文件(由"file"键值指定)。
	如果有多个满足匹配条件的配置，只匹配第一个找到的。
	匹配规则：若该配置中的每个键值(除"file"外)都能在url参数里找到，则认为匹配该配置。
    
	例如配置文件如下：
		[test]
		file="web.zip"
		Name=1233
		test=ok
		dev=dev
		[dev]
		file="web2.zip"
		Name=1233
		dev=dev
        
	当接收到以下url请求时：
		/get.php?Name=1233&dev=dev&test=ok		匹配[test]，返回web.zip
		/get.php?Name=1233&dev=dev&test=ok&a=b	匹配[test]，返回web.zip
		/get.php?Name=1233&dev=dev				匹配[dev]，返回web2.zip
		/get.php?Name=1233						不匹配，返回404

##init.sh文件说明：
	使用aws cli获取自身实例的tags，并作为参数传递到get.php，保存返回的文件(视为zip文件)。
	将返回的文件解压缩，并执行其中的deploy.sh
	*使用之前先修改脚本中的服务器地址
