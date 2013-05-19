#!/bin/sh
set -e

[ "$1" != "nobuild" ] && rm -f ../max-control*deb
cp debian/control.zentyal debian/control
VERSION=$(dpkg-parsechangelog | awk '/^Version/ {print $2}')


[ "$1" != "nobuild" ] && debuild -us -uc -I


[ "$1" != "noupload" ] && rsync --bwlimit=500 -Pavz ../*${VERSION}* max.educa.madrid.org:/usr/local/max/html/max-server/max-server-2013/incoming
[ "$1" != "noupload" ] && ssh max.educa.madrid.org -t /usr/local/max/html/max-server/max-server-2013/inject_incoming



fakeroot debian/rules clean
rm -f debian/control
[ "$1" != "nobuild" ] && dpkg -i ../max-control*deb
