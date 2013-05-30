#!/bin/sh
#set -x


sed -e 's/False/True/'g /etc/max-control/conf.inc.php > conf.inc.php

VERSION=$(dpkg-parsechangelog 2>/dev/null | awk '/^Version/ {print $2}')
sed -i -e "s/$VERSION/__GIT__/g" conf.inc.php| grep VERSION

cp bin/max-control /usr/bin/max-control


for f in $(git status| grep "bin/"| awk '{print $3}'); do
  cp $f /usr/$f
done


touch /tmp/importer.log
cat /dev/null > /tmp/importer.log
