#!/bin/bash

# Description: RHEL/CentOS Core Installer.
#12/9/2014
# Ensure variables, functions and prompts exist
if [[ ! -e "includes/variables.sh" ]] || [[ ! -e "includes/functions.sh" ]] || [[ ! -e "includes/prompts.sh" ]]; then
	echo "=================================================================================================="
	echo "We can not start the installer because some files are missing, try re-downloading the installer!"
	echo "=================================================================================================="
	exit 1
else
	source includes/variables.sh
	source includes/functions.sh
	source includes/prompts.sh
fi

# Prompt for web server technology
OT=$(os_distro_type)
ask_for_webserver ${OT}


# Prompt for database customization
ask_for_database_customization


# Prompt for RADIUS customization
ask_for_radius_customization

########## KICKSTART & PACKAGES ##############
# Check if user is Root
echo
echo "============================================================="
echo -n "1. Checking if you are root: "
check_root_privileges

# Check if SELinux is disabled
echo
echo "============================================================="
echo -n "2. Checking if SELinux is enabled: "
check_selinux_status

# Flush iptable rules -> TODO: Revert to a more secure system
echo
#echo -n "Flushing default Iptable rules: "
reset_iptables_rules

#echo -Customize timezone
echo
customize_timezone

# Install some packages from base repo
echo "============================================================="
echo -e "3. Installing ${LIGHT_BLUE}${BOLD}pre-requisite packages${F_END}"
yum_install nano curl wget unzip
 
# Install EPEL/POPTOP repo
#echo
#echo "============================================================="
#echo -e "4. Installing ${LIGHT_BLUE}${BOLD}EPEL Repository${F_END}"
#install_epel_repo ${OS_VERSION} ${ARCH_TYPE}

# Install required packages
echo
echo "============================================================="
echo -e "4. Installing ${LIGHT_BLUE}${BOLD}required packages${F_END}"
yum_install ${webserver} ${php_processor}  openssl openssl-devel boost-devel git-core mysql-devel php-fpm php-pear php-gd php-common php-cli php-mysql php-xcache mysql-server mysql subversion git vixie-cron mailx python perl unixODBC postgresql krb5 openldap libtool-ltdl gcc-c++ gcc make pptpd

# Download & Install RHEL/CentOS 6 FreeRADIUS RPMs -> TODO: Compile RPMs for more OS_VERSIONS
#install_mao_repo ${ARCH_TYPE}


########### RADIUSDESK REQUIREMENTS ###########

# Prepare Temp directory for downloaded files
mk_temp_dir

if [[ ! -f "${TEMP_PATH}download_complete.txt" ]]; then
	# Download CakePHP 2.5.4 -> TODO: Find a way to make this version agnostic
	echo
	echo "============================================================="
	echo -e "5. Downloading ${LIGHT_BLUE}${BOLD}CakePHP${F_END}"
	wget_download https://codeload.github.com/cakephp/cakephp/zip/2.5.4 ${TEMP_PATH}cakephp-2.5.4.zip

	# Download Ext.Js 4.2.1
	echo
	echo "============================================================="
	echo -e "6. Downloading ${LIGHT_BLUE}${BOLD}Sencha ExtJS${F_END}"
	#wget_download http://sourceforge.net/p/radiusdesk/code/HEAD/tree/extjs/ext-4.2.1-gpl.zip?format=raw ${TEMP_PATH}ext-4.2.1-gpl.zip
	wget_download http://cdn.sencha.com/ext/gpl/ext-4.2.1-gpl.zip ${TEMP_PATH}ext-4.2.1-gpl.zip

	# Download FreeRADIUS
	echo ""
	echo "============================================================="
	echo -e "7. Downloading ${LIGHT_BLUE}${BOLD}FreeRADIUS${F_END}"
	wget_download http://ftp.cc.uoc.gr/mirrors/ftp.freeradius.org/freeradius-server-2.2.5.tar.gz ${TEMP_PATH}freeradius-server-2.2.5.tar.gz
	#git clone https://github.com/smartwifi/rd_wifi
	
	# Download FreeRADIUS start
	echo ""
	echo "============================================================="
	echo -e "*****. Downloading ${LIGHT_BLUE}${BOLD}Auto startup RADIUS${F_END}"
	#wget_download https://codeload.github.com/smartwifi/rd_wifi/zip/master ${TEMP_PATH}rd_wifi-master.zip
	git clone -q https://github.com/smartwifi/rd_wifi ${TEMP_PATH}rd_wifi > /dev/null 2>&1
	# Download RADIUSdesk Source
	echo
	echo "============================================================="
	echo -e "8. Downloading ${LIGHT_BLUE}${BOLD}RADIUSdesk${F_END}"
	svn --quiet checkout http://svn.code.sf.net/p/radiusdesk/code/trunk ${TEMP_PATH}source > /dev/null 2>&1

	# Download NodeJS Source
	echo
	echo "============================================================="
	echo -e "9. Downloading ${LIGHT_BLUE}${BOLD}NodeJS${F_END}"
	wget_download http://nodejs.org/dist/v0.10.26/node-v0.10.26.tar.gz ${TEMP_PATH}node-v0.10.26.tar.gz
fi

# Check for download completion
flag_download_complete ${TEMP_PATH}

########### RADIUSDESK COMPONENT INSTALLATION ###########

# Use web server to install to default location -> TODO: Might be useful to allow user input
if [[ "${webserver}" = "nginx" ]]; then
	
	# Document Root
	HTTP_DOCUMENT_ROOT='/usr/share/nginx/html/'
	
	# Copy nginx configuration files
	copy_nginx_configs ${CONF_DIR}
	
	# Start services needed by RADIUSdesk
	echo
	echo "============================================================="
	echo -e "10. Starting ${LIGHT_BLUE}${BOLD}services${F_END} needed by RADIUSdesk"
	start_service_on_boot php-fpm
	start_service php-fpm
	start_service ${webserver}
	
elif [[ "${webserver}" = "httpd" ]]; then
	HTTP_DOCUMENT_ROOT='/var/www/html/'
	
	# Copy apache configuration files
	copy_apache_configs ${CONF_DIR}
	
	# Start services needed by RADIUSdesk
	echo
	echo "============================================================="
	echo -e "10. Starting ${LIGHT_BLUE}${BOLD}services${F_END} needed by RADIUSdesk"
	start_service ${webserver}
else
	echo
	echo "============================================================="
	echo -e "${LIGHT_RED}${BOLD}Something happened and we can not configure your system${F_END}"
	exit 1
fi


# Start services needed by RADIUSdesk contd.
start_service mysqld

# Get to tmp directory where the action begins
get_to ${TEMP_PATH}

# Install CakePHP
echo
echo "============================================================="
echo -e "11. Installing ${LIGHT_BLUE}${BOLD}CakePHP${F_END}"
install_cakephp ${TEMP_PATH} ${HTTP_DOCUMENT_ROOT}

# Install rd_cake, rd2, meshdesk, rd_clients, rd_login_pages
echo
echo "============================================================="
echo -e "12. Installing ${LIGHT_BLUE}${BOLD}RADIUSdesk${F_END}"
install_radiusdesk ${TEMP_PATH} ${SOURCE_DIR} ${HTTP_DOCUMENT_ROOT}

echo
echo "============================================================="
echo -e "13. Installing ${LIGHT_BLUE}${BOLD}Sencha ExtJS${F_END}"
install_extjs ${TEMP_PATH} ${HTTP_DOCUMENT_ROOT}

# RADIUSdesk cron script
echo
echo "============================================================="
echo -e "14. Installing ${LIGHT_BLUE}${BOLD}Cron Script${F_END} for RADIUSdesk"
install_radiusdesk_cron ${HTTP_DOCUMENT_ROOT} ${webserver}

echo
echo "============================================================="
echo -e "15. Updating ${LIGHT_BLUE}${BOLD}RADIUSdesk Paths${F_END} for RHEL/CentOS compatibility"
update_radiusdesk_paths ${HTTP_DOCUMENT_ROOT}

echo
echo "============================================================="
echo -e "16. Installing ${LIGHT_BLUE}${BOLD}FreeRADIUS${F_END}"
# Customize FreeRADIUS
customize_freeradius ${HTTP_DOCUMENT_ROOT} ${rad_secret}

# FreeRADIUS configuration
configure_radiusdesk_freeradius ${HTTP_DOCUMENT_ROOT} ${RADIUS_DIR} ${TEMP_PATH}

# Customize Database
customize_database ${HTTP_DOCUMENT_ROOT} ${db_host} ${db_user} ${db_password} ${db_name} ${RADIUS_DIR}

# Import sql file to database
echo
echo "============================================================="
echo -e "17. Installing ${LIGHT_BLUE}${BOLD}Database Schema${F_END} for RADIUSdesk"
install_radiusdesk_schema ${HTTP_DOCUMENT_ROOT} ${db_name} ${db_user} ${db_password}

# Disabled TTY Requirements for Sudoers
fix_radiusdesk_sudoers ${SUDOERS_FILE} ${HTTP_DOCUMENT_ROOT}

########### RADIUSDESK OWNERSHIP AND PERMISSIONS ###########
# Update Ownership and Permissions
fix_radiusdesk_permissions_ownership ${HTTP_DOCUMENT_ROOT}

# NodeJS Installation
echo
echo "============================================================="
echo -e "18. Installing ${LIGHT_BLUE}${BOLD}NodeJS${F_END}"
install_nodejs ${TEMP_PATH} ${HTTP_DOCUMENT_ROOT} /etc/init.d/

# Make things start on boot
start_service_on_boot nodejs-socket-io
start_service_on_boot ${webserver}
start_service_on_boot radiusd
start_service_on_boot pptpd
start_service_on_boot mysqld

# Start/Restart services
echo
echo "============================================================="
echo -e "19. Checking if services are ${LIGHT_BLUE}${BOLD}fully Operational${F_END}"
start_service nodejs-socket-io
restart_service ${webserver}
start_service radiusd
start_service pptpd

# Clear temporary directory
clear_dir ${TEMP_PATH}

# RADIUSdesk Installation complete
echo
echo "==========================================================================="
echo -e "${LIGHT_GREEN}${BOLD}INSTALLATION COMPLETED SUCCESSFULLY!!!${F_END}"
echo
echo -e "To access your RADIUSdesk server, visit ${LIGHT_BLUE}${BOLD}http://${IP_ADDRESS}/rd${F_END} on your browser"
echo -e "USERNAME: ${LIGHT_BLUE}${BOLD}root${F_END}  PASSWORD: ${LIGHT_BLUE}${BOLD}admin${F_END}"
echo
echo -e "We recommend ${LIGHT_RED}${BOLD}rebooting${F_END} your computer to ensure everything went as planned :)"
echo "============================================================================"

# Prompt for CoovaChilli Installation
ask_for_coovachilli_install ${HTTP_DOCUMENT_ROOT}

# Prompt User to reboot
ask_for_reboot

# END
