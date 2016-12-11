#!/bin/bash
LOCK_FILE='/tmp/updateDHCP.lock'
if [ -e $LOCK_FILE ]
then
        exit 0
fi

touch $LOCK_FILE
CZAS=$(/bin/date +'%Y.%m.%d %H:%M:%S')
CZAS_FILE=$(/bin/date +'%Y.%m.%d')
LOCAL_DIR='/usr/share/nginx/html/wachowiakisyn/console/dhcp'
REMOTE_ROOT='/home/dhcp_sync'
REMOTE_DIR="$REMOTE_ROOT/awaiting_conf"

if [ -e $LOCAL_DIR/error.lock ]
then
  rm $LOCAL_DIR/error.lock
fi

rsync -a -e 'ssh -q -p 22222' --size-only --checksum --delete-after $LOCAL_DIR/subnets/ dhcp_sync@172.20.4.6:$REMOTE_DIR/ &>> /home/daniel/log/updateDHCP.log
echo "$CZAS Wykonano synchronizacje plikow dhcp." >> /home/daniel/log/$CZAS_FILE.updateDHCP.log
sleep 1
if [ `rsync -t -e 'ssh -q -p 22222' --size-only --dry-run dhcp_sync@172.20.4.6:$REMOTE_ROOT/ | grep error.lock | wc -l` -gt 0 ]
then
        echo "$CZAS Wystapil blad podczas synchronizacji plikow dhcp!" >> /home/daniel/log/$CZAS_FILE.updateDHCP_error.log
        rsync -e 'ssh -q -p 22222' --size-only dhcp_sync@172.20.4.6:$REMOTE_ROOT/error.lock $LOCAL_DIR/ &>> /home/daniel/log/$CZAS_FILE.updateDHCP_error.log
fi
rm $LOCK_FILE
sleep 1
/usr/share/nginx/html/wachowiakisyn/console/dhcp/bash/slave.sh
#/home/daniel/skrypty/updateDHCP.sh
#sleep 3
#/home/daniel/skrypty/updateDHCPtemp.sh

