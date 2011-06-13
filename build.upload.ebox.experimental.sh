#!/bin/sh
set -e

cp debian/control.ebox debian/control
VERSION=$(dpkg-parsechangelog | awk '/^Version/ {print $2}')


[ "$1" != "nobuild" ] && debuild -us -uc -I

mkdir -p ../ebox-experimental
mv ../*${VERSION}* ../ebox-experimental
cd ../ebox-experimental
apt-ftparchive packages .| gzip > Packages.gz
zcat Packages.gz > Packages
rm -f Release Release.gpg
cat << EOF > Release
Origin: MAX server EBox experimental
Label: MAX-server-EBox
Suite: max
Codename: max
Date: $(date)
Architectures: i386 amd64
Components: ./
Description: MAX server EBox experimental
EOF
apt-ftparchive release . | grep -v Release$ >> Release
gpg -bao Release.gpg --default-key 0C32D249 Release



[ "$1" != "noupload" ] && rsync --bwlimit=500 -Pavz ../ebox-experimental/ --delete max.educa.madrid.org:/usr/local/max/html/branches/ebox-experimental
cd ../max-control

fakeroot debian/rules clean
rm -f debian/control
