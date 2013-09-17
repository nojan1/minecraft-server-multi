#!/bin/bash

. /etc/conf.d/minecraft || echo "can't source /etc/conf.d/minecraft" && exit 1

[ -z "$ROTATEDELAY" ] && ROTATEDELAY="1h"

[ -z "$1" ] && echo "No servername specified" && exit 1
SRVNAME="$1"

[ ! -f "$_SRVDIR/$SRVNAME/server.properties" ] && echo "No server config" && exit 1

while [ 1 ]; do
    if [ -f "$_SRVDIR/$SRVNAME/worlds.conf" ]; then
	WORLDS=$(cat "$_SRVDIR/$SRVNAME/worlds.conf")
    else
	WORLDS=$(cd "$_SRVDIR/$SRVNAME" && find * -maxdepth 0 -type d -print)
    fi

    CURWORLD=$(grep "level-name" "$_SRVDIR/$SRVNAME/server.properties" | cut -d "=" -f 2)
    NEXTWORLD=$(echo "$WORLDS" | grep -A 1 "$CURWORLD" | tail -n 1)
    if [ -z "$NEXTWORLD" or "$NEXTWORLD" == "$CURWORLD" ]; then
	NEXTWORLD=$(echo "$WORLDS" | head -n 1)
    fi

    if [ -z "$NEXTWORLD" ]; then
	echo "Something went wrong! No world was selected.. Bailing"
	exit 1
    fi

    echo "Changing from $CURWORLD to $NEXTWORLD"

    minecraftctl "$SRVNAME" command "say Server is about to change to level; $NEXTWORLD"
    sed -i "s/level-name=$CURWORLD/level-name=$NEXTWORLD/" "$_SRVDIR/$SRVNAME/server.properties" 
    systemctl restart minecraftd@$SRVNAME.service

    echo "Sleeping for $ROTATEDELAY"
    sleep "$ROTATEDELAY"
done
