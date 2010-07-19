#!/bin/sh
#DESC Скрипт создания релиза, первый параметр - имя релиза (например QuickFWv1.5 => QuickFWv1.5.tar.bz2)

test -z "$1" && echo "usage release.sh <releaseName>" && exit 1

cd `dirname $0`
olddir=`pwd`

cd ..
rm -rf $1
mkdir $1
cd $olddir
git archive --format=tar master | ( cd "../$1" ; tar xf - )
cd ../$1

find ./doc/* -type d | grep -v asciidoc | xargs rm -rf $1
find ./tmp/* -type f | xargs rm -rf $1
rm -rf addons
./perm.sh 777
rm -f release.sh
rm -f .gitignore

#cd doc/asciidoc
#make quickfw.pdf
#mv quickfw.pdf ../
#cd ../..

cd ..
rm -f $1.tar.bz2
tar -cjf $1.tar.bz2 $1
rm -rf $1
