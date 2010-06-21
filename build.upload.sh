#!/bin/sh
set -e

VERSION=$(dpkg-parsechangelog | awk '/^Version/ {print $2}')


[ "$1" != "nobuild" ] && debuild -us -uc -I


rsync --bwlimit=40 -Pavz ../*${VERSION}* max.educa:/usr/local/max/logs/trac/incoming/branches/max-ebox/
ssh max.educa -t /usr/local/max/logs/root/bin/inject_incoming branches/max-ebox

#rsync -Pavz ../*${VERSION}* root@thinetic.com:/var/www/virtual/thinetic.com/htdocs/max-control/


fakeroot debian/rules clean
