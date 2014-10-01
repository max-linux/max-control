#!/bin/sh

diff -ur <(cd .. && find -maxdepth 1 -type f) <(find -type f)| grep ^-
