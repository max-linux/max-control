#!/bin/sh


#logs/trac/incoming/branches/max-ebox y luego ejecutas:
#inject_incoming branches/max-ebox

rsync -Pavz $1 max.educa:/usr/local/max/logs/trac/incoming/branches/max-ebox/
ssh max.educa -t /usr/local/max/logs/root/bin/inject_incoming branches/max-ebox
