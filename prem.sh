#!/bin/sh
# DESC: Скрипт выставляет указанные права на нужные папки, чтобы фреймворк мог работать

cd `dirname $0`

test -z "$1" && echo "usage prem.sh <rights>" && exit 1

chmod $1 -R tmp
chmod $1 log
chmod $1 tests/Smarty/cache
chmod $1 tests/Smarty/templates_c
