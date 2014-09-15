#!/bin/sh
#set -x


sed -e 's/False/True/'g /etc/max-control/conf.inc.php > conf.inc.php

VERSION=$(dpkg-parsechangelog 2>/dev/null | awk '/^Version/ {print $2}')
sed -i -e "s/$VERSION/__GIT__/g" conf.inc.php| grep VERSION

cp bin/max-control /usr/bin/max-control


for f in $(git status| grep "bin/"| awk '{if($1 == "modified:") print $2; else if($1 == "new") print $3; else if($1~"bin/") print $1}'); do
  if [ -e "$f" ]; then
    echo "  cp $f  => /usr/$f"
    cp $f /usr/$f
  fi
done




touch /tmp/importer.log
cat /dev/null > /tmp/importer.log
