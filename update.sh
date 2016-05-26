#!/bin/sh
#get shell path
absdir=$(cd $(dirname $0); pwd)
tcpdffontsdir="${absdir}/../../tecnickcom/tcpdf/fonts/"
if [ -e $tcpdffontsdir ]; then
chmod 777 ${tcpdffontsdir}
fi
