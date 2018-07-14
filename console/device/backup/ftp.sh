#!/bin/sh

pathToTftp="/var/tftp";

mkdir $pathToTftp/$(date +%Y-%m-%d)
sleep 3
mv $pathToTftp/*.rtf $pathToTftp/$(date +%Y-%m-%d)
lftp -e "set ssl:verify-certificate no; mirror -R /var/tftp/$(date +%Y-%m-%d)/ /switch/; exit" -p 2121 -u backup,b@c4@p 10.111.233.2