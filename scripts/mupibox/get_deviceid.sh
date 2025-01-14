#!/bin/bash
#
# HOSTNAME

CONFIG="/etc/mupibox/mupiboxconfig.json"
HOSTNAME=$(sudo /usr/bin/jq -r .mupibox.host ${CONFIG})
DEVICES=$(curl http://${HOSTNAME}:5005/getDevices 2>/dev/null)
sudo echo $DEVICES > /tmp/.spotify_devices