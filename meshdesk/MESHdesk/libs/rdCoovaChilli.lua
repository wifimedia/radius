require( "class" )

-------------------------------------------------------------------------------
-- CoovaChilli
-------------------------------------------------------------------------------
class "rdCoovaChilli"

--Init function for object
function rdCoovaChilli:rdCoovaChilli()
	self.socket    	= require("socket")
	local uci 	= require("uci")
	self.version 	= "1.0.0"
	self.x		= uci.cursor(nil,'/var/state')
	self.cpCount	= 16 -- The amount of captive portals allowed / worked on
	self.specific   = "/etc/MESHdesk/captive_portals/"
	self.priv_start = "1"
	--self.proxy_start= 3128
	self.proxy_start    = 8118
	self.privoxy_conf   = "privoxy.conf"
	self.resolv_dnsdesk = "/tmp/resolv.conf.dnsdesk"
	
	-- character table string
    self.enc_str ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/'
end
        
function rdCoovaChilli:getVersion()
	return self.version
end


 
function rdCoovaChilli:createConfigs(cp)
	cp = cp or {}
	self:removeConfigs()    --Clean up previous ones
	self:stopPortals()
	self:__doConfigs(cp)
	self:__checkDnsDesk(cp)
	
end     

function rdCoovaChilli:removeConfigs()
	self:__removeConfigs()
end


function rdCoovaChilli:startPortals()
	
	for i=1,self.cpCount do	
	        local s_file = self.specific..i.."/specific.conf"
	        print("Testing for file " .. s_file)
	        local f=io.open(s_file,"r")
	        if f~=nil then --File do exist; try to start this captive portal
	        	io.close(f)
	        	print("Found config for "..s_file)
	        	local config_file = self.specific .. "chilli_"..i..".conf"
	        	print("Start chilli with "..config_file)
	        	local ret_val = os.execute("chilli --conf "..config_file)
	        	print("We have a return value of "..ret_val)
	        	print("Sleep first a bit...")
	        	self:__sleep(5)
	        end
 	end
end

function rdCoovaChilli:stopPortals()
	local ret_val = os.execute("killall chilli")
	print("We have a return value of "..ret_val)
end

function rdCoovaChilli:setDnsMasq(cp)
    local dnsDeskFound  = false;
    for k,v in ipairs(cp)do
        if(v['dnsdesk'] == true)then
            dnsDeskFound = true;
            os.execute('ip addr add '..v['dns1']..'/16 brd + dev '..v['hslan_if']) 
        end
    end
    if(dnsDeskFound == true)then
        os.execute("/etc/init.d/dnsmasq stop");
        os.execute("/etc/init.d/dnsmasq start");
    end
end

--[[--
=========================================
========= Private methods =============== 
=========================================
--]]--


function rdCoovaChilli.__sleep(self,sec)                                                                     
    self.socket.select(nil, nil, sec)                              
end

function rdCoovaChilli.__checkDnsDesk(self,p)
    local dnsDeskFound  = false;
    local resolv_string = '';
    
    for k,v in ipairs(p)do
        if(v['dnsdesk'] == true)then
            dnsDeskFound = true;
            --Get the upstream DNS server we will use on dnsmasq
            resolv_string = "nameserver "..v['upstream_dns1'].."\n";
		    if(string.len(v['upstream_dns2']) ~= 0)then --Maybe a fallback value. Not guranteed
		        resolv_string = resolv_string.."nameserver "..v['upstream_dns2'].."\n";   
		    end          
            break --Found one no need to go on      
        end
    end
    
    if(dnsDeskFound == true)then
        local f,err = io.open(self.resolv_dnsdesk,"w")
		if not f then return print(err) end
		f:write(resolv_string);
		f:close();
		self.x.foreach('dhcp','dnsmasq', 
		    function(a)
		        self.x.set('dhcp', a['.name'], 'addmac','1');
		        self.x.set('dhcp', a['.name'], 'resolvfile',self.resolv_dnsdesk);
	    end)
        self.x.commit('dhcp');
    else
        self.x.foreach('dhcp','dnsmasq', 
		    function(a)
		        self.x.delete('dhcp', a['.name'], 'addmac');
		        self.x.set('dhcp', a['.name'], 'resolvfile','/tmp/resolv.conf.auto');
	    end)
        self.x.commit('dhcp');
    end    
end
  
function rdCoovaChilli.__doConfigs(self,p)

	for k,v in ipairs(p)do 
		local s_file = self.specific..k.."/specific.conf" -- The file name to build
		print("Specific file is "..s_file)
		--The file's content--
		local r2 = v['radius_2']
		if(string.len(v['radius_2']) == 0)then
			r2 = "localhost"
		end

		--Check if we need to activate the postauth proxy
		local proxy_string = ''
		if(v['proxy_enable'] == true)then
		
		    --Only if the privoxy binary is present
		    local t=io.open('/usr/sbin/privoxy',"r");
		    if(t == nil)then
		        return --We silently fail
		    end
			require("rdNetwork")
			local n = rdNetwork()
			local ip = n:getIpForInterface("br-lan")
			
			if(ip)then
				proxy_string = "postauthproxy  '"..ip.."'\n".."postauthproxyport  '"..self.proxy_start.."'\n";
				--Now we can set the config file for privoxy up also
				local conf_file 	= self.specific..k.."/"..self.privoxy_conf
				local proxy_ip  	= v['proxy_ip'];
				local proxy_port	= v['proxy_port'];
				local username 		= v['proxy_auth_username'];
				local password		= v['proxy_auth_password'];
				local enc_string    = nil;
				if((username ~= '')and(password ~= ''))then
				    enc_string    = self:__enc(username..':'..password);
		        end
		
				self:__doPrivoxy(conf_file,ip,proxy_ip,proxy_port,enc_string,k);
			end
		end
		--Up this one regardless
		self.proxy_start = self.proxy_start+1
		
		-- Make the walled garden --
		local wg = "10."..self.priv_start..".0.1" 
		if(string.len(v['walled_garden']) ~= 0)then
			wg = "10."..self.priv_start..".0.1"..","..v['walled_garden']
		end
		self.priv_start = self.priv_start + 1
		

		-- See if there are additional Coova Settings and specifiaclly if they include DNS overrides
		local coova_optional = '';
		local dns_override1 = false;
		local dns_override2 = false;
		--We also add another few checks..
		
		--FOR DNS Specifics--
		--(remove uamanydns from common.conf)
		local uamanydns = '';
		if(v['uamanydns'] == true)then
		    uamanydns = "uamanydns\n";
		end
		
		local dnsparanoia = '';
		if(v['dnsparanoia'] == true)then
		    dnsparanoia = "dnsparanoia\n";
		end
			
		if(string.len(v['coova_optional']) ~= 0)then
			coova_optional = v['coova_optional'];
			if(string.find(coova_optional, "dns1"))then
			    dns_override1 = true
			end
			if(string.find(coova_optional, "dns2"))then
			    dns_override2 = true
			end
		end
		
		-- Get the DNS --
		local dns = self:__getDns()
		local dns_string = ''
		if(dns[2] == nil)then
		    if(dns_override1 ~= true)then
			    dns_string = "dns1  '" ..dns[1].."'\n"
		    end
		else
		    if((dns_override1 ~= true)and(dns_override2 ~= true))then --Think we covered all the probabilities              
                            dns_string = "dns1  '"..dns[1].."'\n"..                                                                
                            "dns2  '"..dns[2].."'\n"                                                                               
            else                                                                                                           
                if((dns_override1 == true)and(dns_override2 == false))then                                                 
                    dns_string =        "dns2  '"..dns[2].."'\n"  --Keep 2 override 1                                      
                end                                                                                                        
                if((dns_override1 == false)and(dns_override2 == true))then                                                 
                    dns_string = "dns1  '"..dns[1].."'\n"   --Keep 1 override 2                                            
                end                                                                                                        
            end      
		end
		
		--If we use DNSdesk we override the dns_string
		if(v['dnsdesk'] == true)then
		    dns_string = "dns1 '"..v['dns1'].."'\n"; --One should always have an auto generated value
		    if(string.len(v['dns2']) ~= 0)then --Maybe a fallback value. Not guranteed
		        dns_string = dns_string.."dns2 '"..v['dns2'].."'\n";   
		    end
		end
		
		--If we specify the DNS servers manually we override the dns string
		if(v['dns_manual'] == true)then
		    dns_string = "dns1 '"..v['dns1'].."'\n"; --One should always have at least one value
		    if(string.len(v['dns2']) ~= 0)then --Maybe a fallback value. Not guranteed
		        dns_string = dns_string.."dns2 '"..v['dns2'].."'\n";   
		    end
		end
		
		local s_content = "radiusserver1  '"..v['radius_1'].."'\n"..
			"radiusserver2  '".. r2 .."'\n"..
			"radiussecret '".. v['radius_secret'].."'\n"..
			"uamserver '"   .. v['uam_url'].."'\n"..
			"radiusnasid '" .. v['radius_nasid'].."'\n"..
			"uamsecret   '" .. v['uam_secret'].."'\n"..
			"dhcpif	'"      .. v['hslan_if'].."'\n"..
			"uamallowed '"  .. wg .."'\n"..
			"cmdsocket  '/var/run/chilli." .. v['hslan_if'] .. ".sock'\n"..
			"unixipc    'chilli." .. v['hslan_if'] .. ".ipc'\n"..
			"pidfile    '/var/run/chilli." .. v['hslan_if'] .. ".pid'\n"..
			dns_string..
			proxy_string..
			uamanydns..
			dnsparanoia..
			coova_optional

		if(v['mac_auth'])then
			s_content = s_content.."macauth\n"
		end

		if(v['swap_octets'])then
			s_content = s_content.."swapoctets\n"
		end

		
		--print(s_content) 
		--Write this to the config file
		local f,err = io.open(s_file,"w")
		if not f then return print(err) end
		f:write(s_content)
		f:close()
	end
end

function rdCoovaChilli.__getDns(self)

    --First check for the 'auto' file which is our first choice
    local file = io.open("/tmp/resolv.conf.auto", "r");
    if(file == nil) then
	    file = io.open("/etc/resolv.conf", "r");
    end
	local dns_start = 1
	local dns_list  = {}
	for line in file:lines() do
		if(string.match(line,"^\s*nameserver") ~= nil)then
		
			local s = string.gsub(line, "^\s*nameserver", "")
			s = s:find'^%s*$' and '' or s:match'^%s*(.*%S)' -- Remove leading and trailing spaces
			if(s == '127.0.0.1')then -- We assume this is not normal
				local one = self.x.get('meshdesk','captive_portal','default_dns_1')
				local two = self.x.get('meshdesk','captive_portal','default_dns_2')
				dns_list[1] = one
				dns_list[2] = two
				return dns_list
			else
				dns_list[dns_start] = s
			end
			dns_start = dns_start + 1
		end
	end
	--dns_list[1]= 'o' --Dunno what this is.... probably an artefact 
	return dns_list
end

function rdCoovaChilli.__removeConfigs(self)
	print("Removing configs")
	for i=1,self.cpCount do 
	
		local s_file = self.specific..i.."/specific.conf"
		print("Removing file " .. s_file)
		os.remove(s_file)
		 
	end
end

function rdCoovaChilli.__doPrivoxy(self,conf_file,ip,proxy_ip,proxy_port,enc_string,number)
    --=============================
    --First we do the config file==
    --=============================
    local fp 	= io.open( conf_file, "r" )
    local str 	= fp:read( "*all" )
    --Find the listen address and override it (NOTE in Lua we escape a modiefier with %)
    str 		= string.gsub( str, "listen%-address%s+.-:.-\n", "listen-address "..ip..':'..self.proxy_start.."\n")
    str 		= string.gsub( str, "forward%s+/%s+.-\n", "forward / "..proxy_ip..":"..proxy_port.."\n")
    
    --Take care of Auth inclusion or removal
    local a_string = "#*actionsfile%s+/etc/MESHdesk/captive_portals/"..number.."/auth.action\n"
    local b_string = "actionsfile /etc/MESHdesk/captive_portals/"..number.."/auth.action\n"
    if(enc_string == nil)then   
        str = string.gsub(str,a_string,"#"..b_string); --Comment it out if not needed
    else
        str = string.gsub(str,a_string,b_string); --Add if if needed
    end
    fp:close()
    fp 		    = io.open( conf_file, "w+" )
   	fp:write( str )
    fp:close()
    
    --===============================
    --Now we do the auth.action file=
    --===============================
    if(enc_string ~= nil)then
        print("=== HEADSUP ====");
        print("NEED TO DO AUTH THING")
        fp  = io.open("/etc/MESHdesk/captive_portals/"..number.."/auth.action","r");
        str = fp:read("*all");
        str = string.gsub( str, "%+add%-header{Proxy%-Authorization:.-\n", "+add-header{Proxy-Authorization: Basic "..enc_string.."} \\\n")
        
        fp:close()
        fp  = io.open( "/etc/MESHdesk/captive_portals/"..number.."/auth.action", "w+" )
   	    fp:write( str )
        fp:close()
    end
	--We got out file now we can fire up the privoxy
	local pid_file= ' --pidfile /var/run/privoxy'..number..'.pid '
	local ret_val = os.execute('/usr/sbin/privoxy'..pid_file..conf_file)
	--print("Privoxy return value "..ret_val)
end


-- encoding
function rdCoovaChilli.__enc(self,data)
    return ((data:gsub('.', function(x) 
        local r,b='',x:byte()
        for i=8,1,-1 do r=r..(b%2^i-b%2^(i-1)>0 and '1' or '0') end
        return r;
    end)..'0000'):gsub('%d%d%d?%d?%d?%d?', function(x)
        if (#x < 6) then return '' end
        local c=0
        for i=1,6 do c=c+(x:sub(i,i)=='1' and 2^(6-i) or 0) end
        return self.enc_str:sub(c+1,c+1)
    end)..({ '', '==', '=' })[#data%3+1])
end
