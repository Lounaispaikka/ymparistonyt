#!/bin/bash
##
# Closure compiler script
#
# @author       Pyry Liukas / Lounaispaikka <pyry@lounaispaikka.fi>
# @version      20120606-1
##

INPUT=$1
OUTPUT=$2

function usage {
        echo "Usage: `basename $0` [input-file] [output-file-name]"
        exit $E_BADARGS
}

if [ ! -n "$1" ]; then
        usage
fi

java -jar /data/scripts/closure/compiler.jar --js $INPUT --js_output_file /tmp/compfile.tmp

{
echo "/***"
echo " * @version `eval date +%Y%m%d_%H%M%S`"
echo " * @author Pyry Liukas <pyry at lounaispaikka . fi>"
echo " * @copyright Lounaispaikka <`eval date +%Y`>"
echo " */"
} > /tmp/comphead.tmp

cat /tmp/comphead.tmp > $OUTPUT
cat /tmp/compfile.tmp >> $OUTPUT
