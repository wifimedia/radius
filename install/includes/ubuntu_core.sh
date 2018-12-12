#!/bin/bash

# Description: Ubuntu/Debian Core Installer.
# Date: 12/9/2014

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

########## KICKSTART & PACKAGES For CentOS/Linux ##############
# Check if user is Root
echo ""
echo "============================================================="
echo -n "1. Checking if you are root: "
check_root_privileges

# Check if SELinux is disabled
echo ""
echo "============================================================="
echo -n "2. Checking if AppArmor is enabled: "
check_apparmor_status
# Flush iptable rules -> TODO: Revert to a more secure system
echo ""
#echo -n "Flushing default Iptable rules: "
reset_iptables_rules

# Install some packages from base repo
echo "============================================================="
echo -e "3. Installing ${LIGHT_BLUE}${BOLD}pre-requisite packages${F_END}"
#aptget_install nano curl wget unzip language-pack-en-base npm nodejs language-pack-en-base nginx php-fpm mysql-server php-mysql php-cli php-gd php-curl php-xml php-mbstring php-intl subversion

# Install required packages
#echo ""
#echo "============================================================="
#echo -e "4. Installing ${LIGHT_BLUE}${BOLD}freeradius${F_END}"
#export DEBIAN_FRONTEND=noninteractive
#sudo apt install -y software-properties-common python-software-properties
#sudo -y add-apt-repository ppa:freeradius/stable-3.0 > /dev/null 2>&1
#aptget_install freeradius freeradius-mysql
########## RADIUSDESK REQUIREMENTS ###########

# Prepare Temp directory for downloaded files
mk_temp_dir

if [[ ! -f "${TEMP_PATH}download_complete.txt" ]]; then
	# Download CakePHP 2.9.7 -> TODO: Find a way to make this version agnostic
	echo ""
	echo "============================================================="
	echo -e "5. Downloading ${LIGHT_BLUE}${BOLD}CakePHP${F_END}"
	wget_download https://github.com/cakephp/cakephp/archive/2.9.7.tar.gz ${TEMP_PATH}2.9.7.tar.gz
	
	# Download RADIUSdesk Source
	echo ""
	echo "============================================================="
	echo -e "6. Downloading ${LIGHT_BLUE}${BOLD}Source RADIUSdesk${F_END}"
	#svn --quiet checkout http://svn.code.sf.net/p/radiusdesk/code ${TEMP_PATH} > /dev/null 2>&1
	git clone -b Radius1212 https://github.com/wifimedia/radius.git ${TEMP_PATH}source
fi

# Check for download completion
flag_download_complete ${TEMP_PATH}

######## RADIUSDESK COMPONENT INSTALLATION ###########

# Use web server to install to default location -> TODO: Might be useful to allow user input
if [[ "${webserver}" = "nginx" ]]; then
	
	# Document Root
	HTTP_DOCUMENT_ROOT='/usr/share/nginx/html/'
	
	# Copy nginx configuration files
	copy_ubuntu_nginx_configs ${TEMP_PATH}${SOURCE_DIR}${CONF_DIR}
	
	# Start services needed by RADIUSdesk
	echo ""
	echo "============================================================="
	echo -e "7. Starting ${LIGHT_BLUE}${BOLD}services${F_END} needed by RADIUSdesk"
	restart_ubuntu_service php7.2-fpm
	restart_ubuntu_service ${webserver}
	
elif [[ "${webserver}" = "apache2" ]]; then
	HTTP_DOCUMENT_ROOT='/var/www/'
	
	# Copy apache configuration files
	copy_ubuntu_apache_configs ${CONF_DIR}
	
	# Start services needed by RADIUSdesk
	echo ""
	echo "============================================================="
	echo -e "8. Starting ${LIGHT_BLUE}${BOLD}services${F_END} needed by RADIUSdesk"
	restart_ubuntu_service ${webserver}
else
	echo ""
	echo "============================================================="
	echo -e "${LIGHT_RED}${BOLD}Something happened and we can not configure your system${F_END}"
	exit 1
fi

# Start services needed by RADIUSdesk contd.
#restart_ubuntu_service mysql

# Get to tmp directory where the action begins
get_to ${TEMP_PATH}

# Install CakePHP
echo ""
echo "============================================================="
echo -e "9. Installing ${LIGHT_BLUE}${BOLD}CakePHP${F_END}"
install_cakephp ${TEMP_PATH} ${HTTP_DOCUMENT_ROOT}

# Install rd_cake, rd2, meshdesk, rd_clients, rd_login_pages
echo ""
echo "============================================================="
echo -e "10. Installing ${LIGHT_BLUE}${BOLD}RADIUSdesk${F_END}"
install_radiusdesk ${TEMP_PATH} ${SOURCE_DIR} ${HTTP_DOCUMENT_ROOT}

echo ""
echo "============================================================="
echo -e "11. Installing ${LIGHT_BLUE}${BOLD}Sencha ExtJS${F_END}"
install_extjs ${TEMP_PATH} ${EXTJS_DIR} ${HTTP_DOCUMENT_ROOT}

# RADIUSdesk cron script
echo ""
echo "============================================================="
echo -e "12. Installing ${LIGHT_BLUE}${BOLD}Cron Script${F_END} for RADIUSdesk"
install_radiusdesk_ubuntu_cron ${HTTP_DOCUMENT_ROOT}

echo ""
echo "============================================================="
echo -e "13. Updating ${LIGHT_BLUE}${BOLD}RADIUSdesk Paths${F_END} for Ubuntu/Debian compatibility"
update_radiusdesk_ubuntu_paths ${HTTP_DOCUMENT_ROOT}

echo
echo "============================================================="
echo -e "14. Installing ${LIGHT_BLUE}${BOLD}FreeRADIUS${F_END}"
# Customize FreeRADIUS
customize_freeradius ${HTTP_DOCUMENT_ROOT} ${rad_secret}

# FreeRADIUS configuration
configure_ubuntu_freeradius ${HTTP_DOCUMENT_ROOT} ${RADIUS_UBUNTU_DIR} ${TEMP_PATH}

# Customize Database
customize_database ${HTTP_DOCUMENT_ROOT} ${db_host} ${db_user} ${db_password} ${db_name} ${RADIUS_UBUNTU_DIR}

# Import sql file to database
echo ""
echo "============================================================="
echo -e "15. Installing ${LIGHT_BLUE}${BOLD}Database Schema${F_END} for RADIUSdesk"
install_radiusdesk_schema ${HTTP_DOCUMENT_ROOT} ${db_name} ${db_user} ${db_password}

# Disabled TTY Requirements for Sudoers
fix_ubuntu_radiusdesk_sudoers ${SUDOERS_FILE} ${HTTP_DOCUMENT_ROOT}

########### RADIUSDESK OWNERSHIP AND PERMISSIONS ###########
# Update Ownership and Permissions
fix_radiusdesk_permissions_ownership_ubuntu ${HTTP_DOCUMENT_ROOT}

# NodeJS Installation
echo ""
echo "============================================================="
echo -e "16. Installing ${LIGHT_BLUE}${BOLD}NodeJS${F_END}"
install_ubuntu_nodejs ${HTTP_DOCUMENT_ROOT} /etc/init.d/

# Make things start on boot
start_ubuntu_service_on_boot ${webserver}
start_ubuntu_service_on_boot pptpd
start_ubuntu_service_on_boot mysql
#start_ubuntu_service_on_boot php7.2-fpm

# Start/Restart services
echo ""
echo "============================================================="
echo -e "17. Checking if services are ${LIGHT_BLUE}${BOLD}fully Operational${F_END}"
restart_ubuntu_service nodejs-socket-io
restart_ubuntu_service ${webserver}
#restart_ubuntu_service radiusd
restart_ubuntu_service pptpd
restart_ubuntu_service mysql
restart_ubuntu_service php7.2-fpm

# Clear temporary directory
#clear_dir ${TEMP_PATH}

# RADIUSdesk Installation complete
echo ""
echo "==========================================================================="
echo -e "${LIGHT_GREEN}${BOLD}INSTALLATION COMPLETED SUCCESSFULLY!!!${F_END}"
echo ""
echo -e "To access your RADIUSdesk server, visit ${LIGHT_BLUE}${BOLD}http://${IP_ADDRESS}/rd${F_END} on your browser"
echo -e "USERNAME: ${LIGHT_BLUE}${BOLD}root${F_END}  PASSWORD: ${LIGHT_BLUE}${BOLD}admin${F_END}"
echo ""
echo -e "We recommend ${LIGHT_RED}${BOLD}rebooting${F_END} your computer to ensure everything went as planned :)"
echo "============================================================================"

# Prompt for CoovaChilli Installation
ask_for_coovachilli_install_ubuntu

# Prompt User to reboot
ask_for_reboot


# END