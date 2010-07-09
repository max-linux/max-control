#!/bin/sh
set -e

VERSION=$(dpkg-parsechangelog | awk '/^Version/ {print $2}')


[ "$1" != "nobuild" ] && debuild -us -uc -I


[ "$1" != "noupload" ] && rsync --bwlimit=70 -Pavz ../*${VERSION}* max.educa:/usr/local/max/logs/trac/incoming/branches/max-ebox/
[ "$1" != "noupload" ] && ssh max.educa -t /usr/local/max/logs/root/bin/inject_incoming branches/max-ebox



fakeroot debian/rules clean
