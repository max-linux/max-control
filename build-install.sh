#!/bin/sh
set -e

rm -f ../max-control*deb
VERSION=$(dpkg-parsechangelog | awk '/^Version/ {print $2}')


debuild -us -uc -I



fakeroot debian/rules clean
dpkg -i ../max-control_*${VERSION}*deb ../zentyal-maxcontrol_*${VERSION}*deb



bash update.config.sh nobin
