#!/bin/bash
LOCK_FILE='/tmp/updateDHCP.lock'
if [ -e $LOCK_FILE ]
then
        exit 0
fi

touch $LOCK_FILE
CZAS=$(/bin/date +'%Y.%m.%d %H:%M:%S')
CZAS_FILE=$(/bin/date +'%Y.%m.%d')
LOCAL_DIR='/var/www/wachowiakisyn/console/dhcp'
REMOTE_ROOT='/home/dhcpsync'
REMOTE_DIR="$REMOTE_ROOT/awaiting_conf"

if [ -e $LOCAL_DIR/error.lock ]
then
  rm $LOCAL_DIR/error.lock
fi

rsync -a -e 'ssh -q -p 22222' --size-only --checksum --delete-after $LOCAL_DIR/subnets/ dhcpsync@172.20.4.5:$REMOTE_DIR/ &>> $LOCAL_DIR/log/updateDHCP.log
echo "$CZAS Wykonano synchronizacje plikow dhcp." >> $LOCAL_DIR/log/$CZAS_FILE.updateDHCP.log
sleep 1
if [ `rsync -t -e 'ssh -q -p 22222' --size-only --dry-run dhcpsync@172.20.4.5:$REMOTE_ROOT/ | grep error.lock | wc -l` -gt 0 ]
then
        echo "$CZAS Wystapil blad podczas synchronizacji plikow dhcp!" >> $LOCAL_DIR/log/$CZAS_FILE.updateDHCP_error.log
        rsync -e 'ssh -q -p 22222' --size-only dhcpsync@172.20.4.5:$REMOTE_ROOT/error.lock $LOCAL_DIR/ &>> $LOCAL_DIR/log/$CZAS_FILE.updateDHCP_error.log
fi
rm $LOCK_FILE
sleep 1
