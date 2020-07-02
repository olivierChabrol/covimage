#!/bin/sh
cd ../../public
#ls -la .
pat="images/uploads/$1"
#echo $pat
#ls -la $pat
./dicom.py $1 $2
exit 0