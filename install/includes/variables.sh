#!/bin/bash


########## Start Installer Variables #########
RD_INSTALLER_VERSION='1.0.5'
ARCH_TYPE=`arch`
OS_VERSION=`[[ -f "/etc/redhat-release" ]] && awk -F' ' '{ print $0 }' /etc/redhat-release | grep -o "[0-9]" | head -1`
CONF_DIR='conf/'
TEMP_PATH='/tmp/radiusdesk/'
SOURCE_DIR='source/'
EXTJS_DIR='source/extjs/'
FREERADIUS_CFG='/etc/freeradius/3.0/'
if [[ "${OS_VERSION}" = "7" ]]; then
    IFCFG=`ip link | awk -F": " '{print $2}' |  head -3 | tail -1`
    IP_ADDRESS=`ip -f inet addr | grep inet |awk -F" " '{print $4}' | head -2 | tail -1`
else
    IFCFG=`ifconfig | awk -F" " '{print $1}' | head -1`
    IP_ADDRESS=`ifconfig ${IFACE} | grep "inet addr" | awk -F: '{print $2}' | awk '{print $1}'`
fi
if [[ "${IFCFG}" = "lo" ]]; then
	IFACE="eth0"
else
	IFACE="${IFCFG}"
fi
IP_ADDRESS=`ifconfig ${IFACE} | grep "inet addr" | awk -F: '{print $2}' | awk '{print $1}'`
#For ubuntu
SUDOERS_FILE='/etc/sudoers'
RADIUS_DIR='/etc/raddb/'
RADIUS_UBUNTU_DIR='//local/etc/raddb/'
CONF_NGINX='/etc/nginx/'
#IF_COUNT=`ifconfig | egrep '^eth' | wc -l`
COOVACHILLI_DIR='/etc/chilli/'
  
# Color Guide
LIGHT_RED='\e[91m'
LIGHT_GREEN='\e[92m'
LIGHT_BLUE='\e[94m'
LIGHT_YELLOW='\e[93m'

# Formatting Options
BOLD='\e[1m'
F_END='\e[0m'

########## End Installer Variables #########