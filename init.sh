#!/bin/sh
SERVER_URL="http://127.0.0.1/get.php"
WORK_DIR=`mktemp -d`

echo work dir is $WORK_DIR
cd $WORK_DIR
curl -o archive.zip -G $(aws ec2 describe-tags --filters "Name=resource-id,Values=$(curl -s http://169.254.169.254/latest/meta-data/instance-id)" --region $(curl -s http://169.254.169.254/latest/meta-data/placement/availability-zone | sed -e "s/.$//") --output=text | sed -r 's/TAGS\t(.*)\t.*\t.*\t(.*)/-d \1=\2/') $SERVER_URL
echo extracting files...
unzip -q archive.zip
echo excuting script...
sh ./deploy.sh