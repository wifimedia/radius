
package.cpath = package.cpath..";./?.dll;./?.so;../lib/?.so;../lib/vc_dll/?.dll;../lib/bcc_dll/?.dll;../lib/mingw_dll/?.dll;"
require("wx")

--Some variables
local version       = '15.10.1'
local port          = 3000
local SERVER_ID, SOCKET_ID = 100, 101
local m_busy        = false
local m_numClients  = 0
local md5           = require './lib/md5'
local shared_secret = 'verysecure'
local app           = wx.wxGetApp()

--Some GUI variables (global)
local frame,tc_shared_secret,tc_timestamp,tc_eth0,tc_c_srvr,tc_c_key,tc_firmware,choice_hw,tc_new_secret,tc_new_srvr,tc_new_key

--Mobile add-on
local choice_m_active,choice_m_proto,choice_m_service,tc_m_device,tc_m_apn,tc_m_pincode,tc_m_username,tc_m_password

--Some hardware
local choices = {
    "Dragino", 
    "MP2 Basic",
    "MP2 Phone",
    "OpenMesh", 
    "PicoStation M2",
    "PicoStation M5",
    "NanoStation M2",
    "NanoStation M5",
    "UniFi AP", 
    "UniFi AP-LR",
    "UniFi AP PRO",
    "AirGateway",
    "AirRouter",
    "AirRouter HP",
    "BulletM2",
    "RB433 2 Radio",
    "Alix 3D2",
    "TP-Link WDR3500",
    "TP-Link WDR3600",
    "TP-Link WR841N",
    "TP-Link WA850RE",
    "TP-Link WA901N",
    "TP-Link WR1043ND",
    "TP-Link CPE210",
    "EnGenius EAP300",
    "Generic 1 Radio",
    "Generic 2 Radio",
    "ZBT WE1526",
    "ZBT WE2026",
    "ZBT WE3826",
    "Archer C7(AC)",
    "MiWiFi Mini(AC)",
    "Yuncore AP90Q",
    "Yuncore XD3200 (AC)",
    "AP505"
}
local hardware      = {}
hardware[0]         = 'dragino'
hardware[1]         = 'mp2_basic'
hardware[2]         = 'mp2_phone'
hardware[3]         = 'om2p'
hardware[4]         = 'pico2'
hardware[5]         = 'pico5'
hardware[6]         = 'nano2'
hardware[7]         = 'nano5'
hardware[8]         = 'unifiap'
hardware[9]         = 'unifilrap'
hardware[10]        = 'unifiappro'
hardware[11]        = 'airgw'
hardware[12]        = 'airrouter'
hardware[13]        = 'airrouterhp'
hardware[14]        = 'bulm2'
hardware[15]        = 'rb433'
hardware[16]        = 'alix3d2'
hardware[17]        = 'tl_wdr3500'
hardware[18]        = 'tl_wdr3600'
hardware[19]        = 'tl841n'
hardware[20]        = 'tl_wa850re'
hardware[21]        = 'tl_wa901n'
hardware[22]        = 'tl_wr1043'
hardware[23]        = 'tl_cpe210'
hardware[24]        = 'eap300'
hardware[25]        = 'genoneradio'
hardware[26]        = 'gentworadio'
hardware[27]        = 'zbt_we1526'
hardware[28]        = 'zbt_we2026'
hardware[29]        = 'zbt_we3826'
hardware[30]        = 'tl_ac1750_c7'
hardware[31]        = 'miwifi_mini'
hardware[32]        = 'yc_ap90q'
hardware[33]        = 'yc_xd3200'
hardware[34]        = 'pw_cpe505n'

local mode_choices = {
    "Mesh",
    "Access Point"
}

local mode = {}

mode[0] = 'mesh'
mode[1] = 'ap'

--Protocol for Xfer
local xfer_protocol_choices = {
    "HTTP",
    "HTTPS"
}

local xfer_protocol = {}

xfer_protocol[0] = 'http'
xfer_protocol[1] = 'https'


--Protocol for 3G--
local protocol_choices = {
    "3G",
    "QMI",
    "MCN",
    "WWAN"
}

local protocol = {}

protocol[0] = '3g'
protocol[1] = 'qmi'
protocol[2] = 'mcn'
protocol[3] = 'wwan'

local active_choices = {
    "Do Not Change",
    "Enable",
    "Disable"
}

local active = {}
active[0] = 'unchanged'
active[1] = '1'
active[2] = '0'

--Service for 3G--
local service_choices = {
    "UMTS",
    "UMTS Only",
    "GPRS",
    "GPRS Only",
    "CDMA",
    "EVDO"
}

local service = {}

service[0] = "umts"
service[1] = "umts_only"
service[2] = "gprs"
service[3] = "gprs_only"
service[4] = "cdma"
service[5] = "evdo"

local radio_choices = {
    "Radio0",
    "Radio1"
}

local radio = {}

radio[0] = "radio0"
radio[1] = "radio1"

local encryption_choices = {
    "None",
    "WEP",
    "WPA-PSK",
    "WPA2-PSK"
}

local encryption = {}
encryption[0]   = 'none'
encryption[1]   = 'wep'
encryption[2]   = 'psk'
encryption[3]   = 'psk2'

local space = 3
local label_size = wx.wxSize(80,-1)
local grey = wx.wxColour(219, 221, 224)

local blue = wx.wxColour(0, 255, 255)
local green = wx.wxColour(0, 255, 0)

local timer;
local fresh_timer = 0;
local fresh_trigger = 30;


function EventMobileAction(event)

    local c = grey
    local state = false
    
    if(event:GetSelection() == 1)then
        c = wx.wxWHITE
        state = true
    end
    
    choice_m_proto:Enable(state)
    choice_m_service:Enable(state)
    
    tc_m_device:SetEditable(state)
    tc_m_device:SetBackgroundColour(c)
    
    tc_m_apn:SetEditable(state)
    tc_m_apn:SetBackgroundColour(c)
    
    tc_m_pincode:SetEditable(state)
    tc_m_pincode:SetBackgroundColour(c)
    
    tc_m_username:SetEditable(state)
    tc_m_username:SetBackgroundColour(c)
    
    tc_m_password:SetEditable(state)
    tc_m_password:SetBackgroundColour(c)
end

function EventWifiAction(event)

    local c = grey
    local state = false
    
    if(event:GetSelection() == 1)then
        c = wx.wxWHITE
        state = true
    end
    
    choice_w_radio:Enable(state)
    choice_w_encryption:Enable(state)
    
    tc_w_ssid:SetEditable(state)
    tc_w_ssid:SetBackgroundColour(c)
    
    tc_w_key:SetEditable(state)
    tc_w_key:SetBackgroundColour(c)
    
end

function EventApplySettings(event)

    local c = grey
    local state = false
    
    if(cb_apply_settings:GetValue())then
        c = wx.wxWHITE
        state = true
    end

    local controlls = {tc_new_srvr, choice_mode,choice_proto,choice_hw,tc_new_key,tc_new_secret}

    tc_new_srvr:SetEditable(state)
    tc_new_srvr:SetBackgroundColour(c)
    
    tc_new_key:SetEditable(state)
    tc_new_key:SetBackgroundColour(c)
    
    tc_new_secret:SetEditable(state)
    tc_new_secret:SetBackgroundColour(c)
    
    choice_mode:Enable(state)
    choice_proto:Enable(state)
    choice_hw:Enable(state)

end

function HandleEvents(event)

    if(event:GetSelection() == 0)then
        sbmHardware:SetBitmap(bm_dragino)
    end
    if(event:GetSelection() == 1)then
        sbmHardware:SetBitmap(bm_mp2_basic)
    end
    if(event:GetSelection() == 2)then
        sbmHardware:SetBitmap(bm_mp2_phone)
    end
    if(event:GetSelection() == 3)then
        sbmHardware:SetBitmap(bm_om2p)
    end
    if(event:GetSelection() == 4)then
        sbmHardware:SetBitmap(bm_pico2)
    end
    if(event:GetSelection() == 5)then
        sbmHardware:SetBitmap(bm_pico5)
    end
    if(event:GetSelection() == 6)then
        sbmHardware:SetBitmap(bm_nano2)
    end
    if(event:GetSelection() == 7)then
        sbmHardware:SetBitmap(bm_nano5)
    end
    if(event:GetSelection() == 8)then
        sbmHardware:SetBitmap(bm_unifiap)
    end
    if(event:GetSelection() == 9)then
        sbmHardware:SetBitmap(bm_unifilrap)
    end
    if(event:GetSelection() == 10)then
        sbmHardware:SetBitmap(bm_unifiappro)
    end
    if(event:GetSelection() == 11)then
        sbmHardware:SetBitmap(bm_airgw)
    end
    if(event:GetSelection() == 12)then
        sbmHardware:SetBitmap(bm_airrouter)
    end
    if(event:GetSelection() == 13)then
        sbmHardware:SetBitmap(bm_airrouterhp)
    end
    if(event:GetSelection() == 14)then
        sbmHardware:SetBitmap(bm_bulm2)
    end
    if(event:GetSelection() == 15)then
        sbmHardware:SetBitmap(bm_rb433)
    end
    if(event:GetSelection() == 16)then
        sbmHardware:SetBitmap(bm_alix3d2)
    end
    if(event:GetSelection() == 17)then
        sbmHardware:SetBitmap(bm_n600)
    end
    if(event:GetSelection() == 18)then
        sbmHardware:SetBitmap(bm_n600)
    end
    if(event:GetSelection() == 19)then
        sbmHardware:SetBitmap(bm_tl841n)
    end
    if(event:GetSelection() == 20)then
        sbmHardware:SetBitmap(bm_tl_wa850re)
    end
    if(event:GetSelection() == 21)then
        sbmHardware:SetBitmap(bm_tl_wa901n)
    end
    if(event:GetSelection() == 22)then
        sbmHardware:SetBitmap(bm_tl_wr1043)
    end
    if(event:GetSelection() == 23)then
        sbmHardware:SetBitmap(bm_tl_cpe210)
    end
    if(event:GetSelection() == 24)then
        sbmHardware:SetBitmap(bm_eap300)
    end
    if(event:GetSelection() == 25)then
        sbmHardware:SetBitmap(bm_genoneradio)
    end
    if(event:GetSelection() == 26)then
        sbmHardware:SetBitmap(bm_gentworadio)
    end
     if(event:GetSelection() == 27)then
        sbmHardware:SetBitmap(bm_zbt_we1526)
    end
    if(event:GetSelection() == 28)then
        sbmHardware:SetBitmap(bm_zbt_we2026)
    end
    if(event:GetSelection() == 29)then
        sbmHardware:SetBitmap(bm_zbt_we3826)
    end
    if(event:GetSelection() == 30)then
        sbmHardware:SetBitmap(bm_tl_ac1750_c7)
    end
    if(event:GetSelection() == 31)then
        sbmHardware:SetBitmap(bm_miwifi_mini)
    end
    if(event:GetSelection() == 32)then
        sbmHardware:SetBitmap(bm_yc_ap90q)
    end
    if(event:GetSelection() == 33)then
        sbmHardware:SetBitmap(bm_yc_xd3200)
    end
    if(event:GetSelection() == 34)then
        sbmHardware:SetBitmap(bm_pw_cpe505n)
    end
    
end

function md5sum_to_client(sock)
    sock:Write('md5sum='..md5.sumhexa(tc_shared_secret:GetValue()).."\n")
    open_sock_input(sock)
end

function get_info(sock)
    local i = read_line_from_sock(sock)
    print("--------INFO--------")
    print(i)
    print("===== END INFO ======")
    
    timer:Stop();
    timer:Start(1000);
    fresh_timer = 0;
    tc_timestamp:SetBackgroundColour(green)
    tc_timestamp:SetValue("Just Now")
    
    --Check what we need to paint
    if(string.find(i, "eth0="))then
        local eth = string.gsub(i, "eth0=", "")
        tc_eth0:SetValue(eth)
    end
    
    if(string.find(i, "protocol="))then
        local proto = string.gsub(i, "protocol=", "")
        tc_c_proto:SetValue(proto)
    end
    
    if(string.find(i, "server="))then
        local srv = string.gsub(i, "server=", "")
        tc_c_srvr:SetValue(srv)
    end
    
    if(string.find(i, "firmware="))then
        local fw = string.gsub(i, "firmware=", "")
        fw = string.gsub(fw, "|", "\n")
        tc_firmware:SetValue(fw)
    end
    
    if(string.find(i, "key="))then
        local key = string.gsub(i, "key=", "")
        key = string.gsub(key, "|", "\n")
        tc_c_key:SetValue(key)
    end
    
    if(string.find(i, "mode="))then
        local mode = string.gsub(i, "mode=", "")
        mode = string.gsub(mode, "|", "\n")
        tc_c_mode:SetValue(mode)
    end

    sock:Write("ok\n")
    open_sock_input(sock)
end

function set_info(sock)
    print("--- NEW SETTINGS ----")
    local a = {}
    
    if(cb_apply_settings:GetValue())then
        table.insert(a, 'mode='..mode[choice_mode:GetSelection()])
        table.insert(a, 'hardware='..hardware[choice_hw:GetSelection()])
        table.insert(a, 'protocol='..xfer_protocol[choice_proto:GetSelection()])
        if(tc_new_srvr:GetValue() ~= '')then
            table.insert(a, 'server='..tc_new_srvr:GetValue())
        end
        if(tc_new_secret:GetValue() ~= '')then
            table.insert(a, 'secret='..tc_new_secret:GetValue())
        end
        if(tc_new_key:GetValue() ~= '')then
            table.insert(a, 'key='..tc_new_key:GetValue())
        end
    end
    
    -- Here we pack the 3G settings
    
    --Only when we have it Enable (1) do we send the detail
    --When it is 0 we do nothing and 2 we Disable it
    if(choice_m_active:GetSelection()== 1)then
        table.insert(a, 'm_active=1')
        
        table.insert(a, 'm_proto='..protocol[choice_m_proto:GetSelection()])
        table.insert(a, 'm_service='..service[choice_m_service:GetSelection()])
        
        if(tc_m_device:GetValue() ~= '')then
            table.insert(a, 'm_device='..tc_m_device:GetValue())
        end
        
        if(tc_m_apn:GetValue() ~= '')then
            table.insert(a, 'm_apn='..tc_m_apn:GetValue())
        end
        
        if(tc_m_pincode:GetValue() ~= '')then
            table.insert(a, 'm_pincode='..tc_m_pincode:GetValue())
        end
        
        if(tc_m_username:GetValue() ~= '')then
            table.insert(a, 'm_username='..tc_m_username:GetValue())
        end
        
        if(tc_m_password:GetValue() ~= '')then
            table.insert(a, 'm_password='..tc_m_password:GetValue())
        end
    end
    
    --2 = Disable
    if(choice_m_active:GetSelection()== 2)then
        table.insert(a, 'm_active=0')
    end
    
    
    -- Here we pack the WIFI Client settings
    
    --Only when we have it Enable (1) do we send the detail
    --When it is 0 we do nothing and 2 we Disable it
    if(choice_w_active:GetSelection()== 1)then
        table.insert(a, 'w_active=1')
        
        table.insert(a, 'w_radio='..radio[choice_w_radio:GetSelection()])
        table.insert(a, 'w_encryption='..encryption[choice_w_encryption:GetSelection()])

        
        if(tc_w_ssid:GetValue() ~= '')then
            table.insert(a, 'w_ssid='..tc_w_ssid:GetValue())
        end
        
        if(tc_w_key:GetValue() ~= '')then
            table.insert(a, 'w_key='..tc_w_key:GetValue())
        end
    end
    
    --2 = Disable
    if(choice_w_active:GetSelection()== 2)then
        table.insert(a, 'w_active=0')
    end
    
    
    table.insert(a,'last')
    for i, v in ipairs(a) do
        sock:Write(v.."\n")
        local r = read_line_from_sock(sock)
        print("GOT:"..r..":END")
        if(r ~='ok')then --Stop of we don't get 'ok' back each time
                print("Did not get an ok back")
                break
        end
    end
    print("--- END NEW SETTINGS ----")

end

function get_error(sock)
    local e = read_line_from_sock(sock)
    print('---------------------')
    print(e)
    print('========================')
    if(string.find(e, "the shared secrets"))then
        --Turn the shared secret background red
        tc_shared_secret:SetBackgroundColour(wx.wxRED)
        tc_shared_secret:Refresh(false)
    end
    
end

function clear_errors()
    print("clear errors")
    tc_shared_secret:SetBackgroundColour(wx.wxWHITE)
    tc_shared_secret:Refresh(true)
end

function open_sock_input(sock)
    sock.Notify = wx.wxSOCKET_LOST_FLAG + wx.wxSOCKET_INPUT_FLAG
end

function read_line_from_sock(sock)
    sock:WaitForRead(50);
    local message = sock:Read(1000)
    local translate = {}
    local line_end = false
    local client_string
    
    for i= 1, #message do
        local b = string.byte(message, i, i)
        if b ~= 0 then
            if (string.char(b) == "\n")then
                line_end = true
            end
            if(not(line_end))then
                translate[#translate+1] = string.char(b)
            end
        end
    end
    client_string = table.concat(translate)
    return client_string
end


--We build the GUI 
function build_gui()

    bm_dragino      = wx.wxBitmap();
    bm_dragino:LoadFile("./graphics/ms14.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_mp2_basic    = wx.wxBitmap();
    bm_mp2_basic:LoadFile("./graphics/mp2_basic.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_mp2_phone    = wx.wxBitmap();
    bm_mp2_phone:LoadFile("./graphics/mp2_phone.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_om2p      = wx.wxBitmap();
    bm_om2p:LoadFile("./graphics/om2p.jpg",wx.wxBITMAP_TYPE_ANY )

    bm_pico2      = wx.wxBitmap();
    bm_pico2:LoadFile("./graphics/pico2.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_pico5      = wx.wxBitmap();
    bm_pico5:LoadFile("./graphics/pico5.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_nano2      = wx.wxBitmap();
    bm_nano2:LoadFile("./graphics/nano2.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_nano5      = wx.wxBitmap();
    bm_nano5:LoadFile("./graphics/nano5.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_unifiap      = wx.wxBitmap();
    bm_unifiap:LoadFile("./graphics/unifiap.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_unifilrap      = wx.wxBitmap();
    bm_unifilrap:LoadFile("./graphics/unifilrap.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_unifiappro    = wx.wxBitmap();
    bm_unifiappro:LoadFile("./graphics/unifiappro.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_airgw    = wx.wxBitmap();
    bm_airgw:LoadFile("./graphics/airgw.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_airrouter    = wx.wxBitmap();
    bm_airrouter:LoadFile("./graphics/airrouter.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_airrouterhp    = wx.wxBitmap();
    bm_airrouterhp:LoadFile("./graphics/airrouter.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_bulm2    = wx.wxBitmap();
    bm_bulm2:LoadFile("./graphics/bulm2.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_rb433    = wx.wxBitmap();
    bm_rb433:LoadFile("./graphics/rb433.png",wx.wxBITMAP_TYPE_ANY )

    bm_alix3d2      = wx.wxBitmap();
    bm_alix3d2:LoadFile("./graphics/alix3d2.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_n600      = wx.wxBitmap();
    bm_n600:LoadFile("./graphics/n600.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_n600      = wx.wxBitmap();
    bm_n600:LoadFile("./graphics/n600.jpg",wx.wxBITMAP_TYPE_ANY )
    
    bm_tl841n      = wx.wxBitmap();
    bm_tl841n:LoadFile("./graphics/tl841n.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_tl_wa850re      = wx.wxBitmap();
    bm_tl_wa850re:LoadFile("./graphics/tl_wa850re.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_tl_wa901n      = wx.wxBitmap();
    bm_tl_wa901n:LoadFile("./graphics/tl_wa901n.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_tl_cpe210        = wx.wxBitmap();
    bm_tl_cpe210:LoadFile("./graphics/tl_cpe210.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_tl_wr1043        = wx.wxBitmap();
    bm_tl_wr1043:LoadFile("./graphics/tl_wr1043.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_eap300           = wx.wxBitmap();
    bm_eap300:LoadFile("./graphics/eap300.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_genoneradio      = wx.wxBitmap();
    bm_genoneradio:LoadFile("./graphics/genoneradio.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_gentworadio      = wx.wxBitmap();
    bm_gentworadio:LoadFile("./graphics/gentworadio.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_zbt_we2026      = wx.wxBitmap();
    bm_zbt_we2026:LoadFile("./graphics/zbt_we2026.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_zbt_we1526      = wx.wxBitmap();
    bm_zbt_we1526:LoadFile("./graphics/zbt_we1526.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_zbt_we3826      = wx.wxBitmap();
    bm_zbt_we3826:LoadFile("./graphics/zbt_we3826.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_tl_ac1750_c7      = wx.wxBitmap();
    bm_tl_ac1750_c7:LoadFile("./graphics/tl_ac1750_c7.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_miwifi_mini      = wx.wxBitmap();
    bm_miwifi_mini:LoadFile("./graphics/miwifi_mini.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_yc_ap90q      = wx.wxBitmap();
    bm_yc_ap90q:LoadFile("./graphics/yc_ap90q.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_yc_xd3200      = wx.wxBitmap();
    bm_yc_xd3200:LoadFile("./graphics/yc_xd3200.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_pw_cpe505n     = wx.wxBitmap();
    bm_pw_cpe505n:LoadFile("./graphics/pw_cpe505n.png",wx.wxBITMAP_TYPE_ANY )
    
    --Some Icons--
    bm_icn_info         = wx.wxBitmap();
    bm_icn_info:LoadFile("./graphics/info.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_icn_settings     = wx.wxBitmap();
    bm_icn_settings:LoadFile("./graphics/settings.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_icn_mobile      = wx.wxBitmap();
    bm_icn_mobile:LoadFile("./graphics/mobile.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_icn_wifi         = wx.wxBitmap();
    bm_icn_wifi:LoadFile("./graphics/wifi.png",wx.wxBITMAP_TYPE_ANY )
    
    bm_icn_security         = wx.wxBitmap();
    bm_icn_security:LoadFile("./graphics/security.png",wx.wxBITMAP_TYPE_ANY )
    
    
     --Nice Icons
    imageList = wx.wxImageList(32, 32)
    imageList:Add(bm_icn_info)
    imageList:Add(bm_icn_settings)
    imageList:Add(bm_icn_mobile)
    imageList:Add(bm_icn_wifi)
    imageList:Add(bm_icn_security)




    frame = wx.wxFrame(wx.NULL, wx.wxID_ANY, 'MESHdesk Node Config Utility',
        wx.wxDefaultPosition, wx.wxSize(600, 450),
                    wx.wxDEFAULT_FRAME_STYLE)
                    
        local statusBar = frame:CreateStatusBar(1)
    
        frame:SetBackgroundColour(wx.wxWHITE )    --Set the color of the main panel red
        local vbox = wx.wxBoxSizer(wx.wxVERTICAL)
        frame:SetSizer(vbox)
        -- create a simple file menu
        local fileMenu = wx.wxMenu()
        fileMenu:Append(wx.wxID_EXIT, "E&xit", "Quit the program")

        -- create a simple help menu
        local helpMenu = wx.wxMenu()
        helpMenu:Append(wx.wxID_ABOUT, "&About", "About the MESHdesk Node config utility")

        -- create a menu bar and append the file and help menus
        local menuBar = wx.wxMenuBar()
        menuBar:Append(fileMenu, "&File")
        menuBar:Append(helpMenu, "&Help")

        -- attach the menu bar into the frame
        frame:SetMenuBar(menuBar)
        
        frame:Connect(wx.wxID_EXIT, wx.wxEVT_COMMAND_MENU_SELECTED,
                  function (event) frame:Close(true) end )

        -- connect the selection event of the about menu item
        frame:Connect(wx.wxID_ABOUT, wx.wxEVT_COMMAND_MENU_SELECTED,
            function (event)
                wx.wxMessageBox('MESHdesk is part of RADIUSdesk\nhttp://www.radiusdesk.com',
                                "About MESHdesk",
                                wx.wxOK + wx.wxICON_INFORMATION,
                                frame)
            end )
            
        
        --Four Tabs--
        local notebook = wx.wxNotebook(frame, wx.wxID_ANY,wx.wxDefaultPosition, wx.wxDefaultSize)
           

        notebook:SetImageList(imageList)
        
            --Info tab--
            local pnlInfo = wx.wxPanel(notebook, wx.wxID_ANY)
            local szrInfo = wx.wxBoxSizer(wx.wxVERTICAL)
            pnlInfo:SetSizer(szrInfo)
            szrInfo:SetSizeHints(pnlInfo)
          
             --Node info box--
            local hbox_info  = wx.wxBoxSizer(wx.wxHORIZONTAL)
        
                local hbox_left = wx.wxBoxSizer(wx.wxVERTICAL)
        
            local hboxts = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local stts   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'Last Contact ',wx.wxDefaultPosition,label_size)
            tc_timestamp = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY)
            tc_timestamp:SetEditable(false)
            tc_timestamp:SetBackgroundColour(blue)
            tc_timestamp:SetValue('Never Before')
            hboxts:Add(stts, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hboxts:Add(tc_timestamp, 1, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
        
            local hbox2 = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local st2   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'Eth0 ',wx.wxDefaultPosition,label_size)
            tc_eth0     = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY)
            tc_eth0:SetEditable(false)
            tc_eth0:SetBackgroundColour(grey)
            hbox2:Add(st2, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hbox2:Add(tc_eth0, 1, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            
            local hbox3b = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local st3b   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'Mode ',wx.wxDefaultPosition,label_size)
            tc_c_mode    = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY)
            tc_c_mode:SetEditable(false)
            tc_c_mode:SetBackgroundColour(grey)
            hbox3b:Add(st3b, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hbox3b:Add(tc_c_mode, 1, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            
            local hbox3c = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local st3c   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'Protocol ',wx.wxDefaultPosition,label_size)
            tc_c_proto   = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY)
            tc_c_proto:SetEditable(false)
            tc_c_proto:SetBackgroundColour(grey)
            hbox3c:Add(st3c, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hbox3c:Add(tc_c_proto, 1, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
        
            local hbox3 = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local st3   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'Server ',wx.wxDefaultPosition,label_size)
            tc_c_srvr   = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY)
            tc_c_srvr:SetEditable(false)
            tc_c_srvr:SetBackgroundColour(grey)
            hbox3:Add(st3, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hbox3:Add(tc_c_srvr, 1, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
    
            local hbox3a = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local st3a   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'WPA2 Key ',wx.wxDefaultPosition,label_size)
            tc_c_key     = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY)
            tc_c_key:SetEditable(false)
            tc_c_key:SetBackgroundColour(grey)
            hbox3a:Add(st3a, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hbox3a:Add(tc_c_key, 1, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
        
          
    
                hbox_left:Add(hboxts, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                hbox_left:Add(hbox2, 0,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                hbox_left:Add(hbox3b, 0,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                hbox_left:Add(hbox3c, 0,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                hbox_left:Add(hbox3, 0,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                hbox_left:Add(hbox3a, 0,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)

            hbox_info:Add(hbox_left, 1,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            
            
                local hbox_right = wx.wxBoxSizer(wx.wxHORIZONTAL)
        
            local hbox4 = wx.wxBoxSizer(wx.wxHORIZONTAL)
            local st4   = wx.wxStaticText(pnlInfo, wx.wxID_ANY, 'Firmware ')
            tc_firmware = wx.wxTextCtrl(pnlInfo, wx.wxID_ANY,'',wx.wxDefaultPosition, wx.wxDefaultSize,
                                 wx.wxTE_MULTILINE+wx.wxTE_DONTWRAP+wx.wxTE_NO_VSCROLL)
            tc_firmware:SetEditable(false)
            tc_firmware:SetBackgroundColour(grey)
            hbox4:Add(st4, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
            hbox4:Add(tc_firmware, 1, wx.wxALIGN_LEFT + wx.wxALL+ wx.wxEXPAND , space)
                    hbox_right:Add(hbox4, 1,  wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND , space)
            hbox_info:Add(hbox_right, 1,  wx.wxALIGN_CENTER + wx.wxALL + wx.wxEXPAND , space)
            
        szrInfo:Add(hbox_info,1,wx.wxALL+ wx.wxEXPAND,7)
          
          
  
        notebook:AddPage(pnlInfo, "Current Settings",false,0)
        
            --Settings tab--
            local pnlSettings = wx.wxPanel(notebook, wx.wxID_ANY)
            local szrSettings = wx.wxBoxSizer(wx.wxVERTICAL)
            pnlSettings:SetSizer(szrSettings)
            szrSettings:SetSizeHints(pnlSettings)
            
            
            local vbox3  = wx.wxBoxSizer(wx.wxVERTICAL);
            
                local hbox5a = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st5a   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, '',wx.wxDefaultPosition,label_size)
                cb_apply_settings = wx.wxCheckBox(pnlSettings, wx.wxID_UNDO,'Apply New Settings ',wx.wxDefaultPosition,label_size)
                hbox5a:Add(st5a, 0, wx.wxALIGN_LEFT + wx.wxALL, 0)
                hbox5a:Add(cb_apply_settings, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
            
            
                local hbox5 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st5   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, 'Server IP ',wx.wxDefaultPosition,label_size)
                tc_new_srvr = wx.wxTextCtrl(pnlSettings, wx.wxID_ANY)
                tc_new_srvr:SetEditable(false)
                tc_new_srvr:SetBackgroundColour(grey)
                hbox5:Add(st5, 0, wx.wxALIGN_LEFT + wx.wxALL, 0)
                hbox5:Add(tc_new_srvr, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
        
                local hbox6a = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st6a   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, 'Mode',wx.wxDefaultPosition,label_size)
                choice_mode   = wx.wxChoice(pnlSettings, wx.wxID_NEW,
                           wx.wxDefaultPosition, wx.wxDefaultSize,
                           mode_choices)
                choice_mode:Disable()
                choice_mode:SetSelection(0)
                hbox6a:Add(st6a, 0, wx.wxALIGN_LEFT + wx.wxALL , 0)
                hbox6a:Add(choice_mode, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
        
                local hbox6 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st6   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, 'Hardware type ',wx.wxDefaultPosition,label_size)
                choice_hw   = wx.wxChoice(pnlSettings, wx.wxID_REDO,
                           wx.wxDefaultPosition, wx.wxDefaultSize,
                           choices)
                choice_hw:Disable()
                choice_hw:SetSelection(0)
                hbox6:Add(st6, 0, wx.wxALIGN_LEFT + wx.wxEXPAND,0)
                hbox6:Add(choice_hw, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                
                local hbox6b = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st6b   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, 'Protocol',wx.wxDefaultPosition,label_size)
                choice_proto = wx.wxChoice(pnlSettings, wx.wxID_NEW,
                           wx.wxDefaultPosition, wx.wxDefaultSize,
                           xfer_protocol_choices)
                choice_proto:Disable()
                choice_proto:SetSelection(0)
                hbox6b:Add(st6b, 0, wx.wxALIGN_LEFT + wx.wxALL , 0)
                hbox6b:Add(choice_proto, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
        

                local hbox7 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st7   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, 'Change secret to ',wx.wxDefaultPosition,label_size)
                tc_new_secret = wx.wxTextCtrl(pnlSettings, wx.wxID_ANY)
                tc_new_secret:SetEditable(false)
                tc_new_secret:SetBackgroundColour(grey)
                hbox7:Add(st7, 0, wx.wxALIGN_LEFT + wx.wxALL, 0)
                hbox7:Add(tc_new_secret, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
        
                local hbox8 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st8   = wx.wxStaticText(pnlSettings, wx.wxID_ANY, 'WPA2 Key ',wx.wxDefaultPosition,label_size)
                tc_new_key  = wx.wxTextCtrl(pnlSettings, wx.wxID_ANY)
                tc_new_key:SetEditable(false)
                tc_new_key:SetBackgroundColour(grey)
                hbox8:Add(st8, 0, wx.wxALIGN_LEFT + wx.wxALL, 0)
                hbox8:Add(tc_new_key, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
        
            vbox3:Add(hbox5a, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            vbox3:Add(hbox6a,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            vbox3:Add(hbox6, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            vbox3:Add(hbox6b, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            vbox3:Add(hbox5, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            vbox3:Add(hbox8, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
            vbox3:Add(hbox7, 0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
        
            --Fit the image in
            local hboxZ = wx.wxBoxSizer(wx.wxHORIZONTAL)
            sbmHardware = wx.wxStaticBitmap(pnlSettings,-1,bm_dragino)
            hboxZ:Add(vbox3, 1,  wx.wxALL +wx.wxEXPAND ,5)
            --hboxZ:Add(sbmHardware,0,wx.wxALL+wx.wxEXPAND,20)
            hboxZ:Add(sbmHardware,0,1,20)
            
            szrSettings:Add(hboxZ,1,wx.wxALL +wx.wxEXPAND,5)
            
        notebook:AddPage(pnlSettings, "New Settings",false,1)
        
            --3G Config--
            local pnlMobile = wx.wxPanel(notebook, wx.wxID_ANY)
            local szrMobile = wx.wxBoxSizer(wx.wxVERTICAL)
            pnlMobile:SetSizer(szrMobile)
            szrMobile:SetSizeHints(pnlMobile)
            
                local vbox_mobile  = wx.wxBoxSizer(wx.wxVERTICAL);
                
                    local hbox_m_1 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_1   = wx.wxStaticText(pnlMobile, wx.wxID_ANY, 'Action',wx.wxDefaultPosition,label_size)
                    choice_m_active   = wx.wxChoice(pnlMobile, wx.wxID_SAVE,
                               wx.wxDefaultPosition, wx.wxDefaultSize,
                               active_choices)
                    choice_m_active:SetSelection(0)
                    hbox_m_1:Add(st_m_1, 0, wx.wxALIGN_LEFT + wx.wxALL , space)
                    hbox_m_1:Add(choice_m_active, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
                    
                    local hbox_m_2 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_2   = wx.wxStaticText(pnlMobile, wx.wxID_ANY, '* Protocol',wx.wxDefaultPosition,label_size)
                    choice_m_proto   = wx.wxChoice(pnlMobile, wx.wxID_NEW,
                               wx.wxDefaultPosition, wx.wxDefaultSize,
                               protocol_choices)
                    choice_m_proto:SetSelection(0)
                    choice_m_proto:Disable()
                    hbox_m_2:Add(st_m_2, 0, wx.wxALIGN_LEFT + wx.wxALL , space)
                    hbox_m_2:Add(choice_m_proto, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
                    
                    local hbox_m_2a = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_2a   = wx.wxStaticText(pnlMobile, wx.wxID_ANY, '* Service',wx.wxDefaultPosition,label_size)
                    choice_m_service   = wx.wxChoice(pnlMobile, wx.wxID_NEW,
                               wx.wxDefaultPosition, wx.wxDefaultSize,
                               service_choices)
                    choice_m_service:SetSelection(0)
                    choice_m_service:Disable()
                    hbox_m_2a:Add(st_m_2a, 0, wx.wxALIGN_LEFT + wx.wxALL , space)
                    hbox_m_2a:Add(choice_m_service, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
                    
                    local hbox_m_3  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_3    = wx.wxStaticText(pnlMobile, wx.wxID_ANY, '* Device',wx.wxDefaultPosition,label_size)
                    tc_m_device     = wx.wxTextCtrl(pnlMobile, wx.wxID_ANY)
                    tc_m_device:SetValue("/dev/ttyUSB0")
                    tc_m_device:SetEditable(false)
                    tc_m_device:SetBackgroundColour(grey)
                    hbox_m_3:Add(st_m_3, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_m_3:Add(tc_m_device, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                    
                    local hbox_m_4  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_4    = wx.wxStaticText(pnlMobile, wx.wxID_ANY, '* APN',wx.wxDefaultPosition,label_size)
                    tc_m_apn        = wx.wxTextCtrl(pnlMobile, wx.wxID_ANY)
                    tc_m_apn:SetValue("internet")
                    tc_m_apn:SetEditable(false)
                    tc_m_apn:SetBackgroundColour(grey)
                    hbox_m_4:Add(st_m_4, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_m_4:Add(tc_m_apn, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                    
                    local hbox_m_5  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_5    = wx.wxStaticText(pnlMobile, wx.wxID_ANY, 'PIN',wx.wxDefaultPosition,label_size)
                    tc_m_pincode    = wx.wxTextCtrl(pnlMobile, wx.wxID_ANY)
                    tc_m_pincode:SetEditable(false)
                    tc_m_pincode:SetBackgroundColour(grey)
                    hbox_m_5:Add(st_m_5, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_m_5:Add(tc_m_pincode, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                    
                    local hbox_m_6  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_6    = wx.wxStaticText(pnlMobile, wx.wxID_ANY, 'Username',wx.wxDefaultPosition,label_size)
                    tc_m_username   = wx.wxTextCtrl(pnlMobile, wx.wxID_ANY)
                    tc_m_username:SetEditable(false)
                    tc_m_username:SetBackgroundColour(grey)
                    hbox_m_6:Add(st_m_6, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_m_6:Add(tc_m_username, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                    
                    local hbox_m_7  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_m_7    = wx.wxStaticText(pnlMobile, wx.wxID_ANY, 'Password',wx.wxDefaultPosition,label_size)
                    tc_m_password   = wx.wxTextCtrl(pnlMobile, wx.wxID_ANY)
                    tc_m_password:SetEditable(false)
                    tc_m_password:SetBackgroundColour(grey)
                    hbox_m_7:Add(st_m_7, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_m_7:Add(tc_m_password, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
        
                    
                vbox_mobile:Add(hbox_m_1,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_2,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_2a,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_3,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_4,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_5,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_6,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_mobile:Add(hbox_m_7,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                    
                
            szrMobile:Add(vbox_mobile,1,wx.wxALL +wx.wxEXPAND,5)
            
        notebook:AddPage(pnlMobile, "3G Option",false,2)
        
        
            --WiFi Client options
            local pnlWiFi = wx.wxPanel(notebook, wx.wxID_ANY)
            local szrWiFi = wx.wxBoxSizer(wx.wxVERTICAL)
            pnlWiFi:SetSizer(szrWiFi)
            szrWiFi:SetSizeHints(pnlWiFi)
            
                local vbox_wifi  = wx.wxBoxSizer(wx.wxVERTICAL);
                
                    local hbox_w_1 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_w_1   = wx.wxStaticText(pnlWiFi, wx.wxID_ANY, 'Action',wx.wxDefaultPosition,label_size)
                    choice_w_active= wx.wxChoice(pnlWiFi, wx.wxID_BOLD,
                               wx.wxDefaultPosition, wx.wxDefaultSize,
                               active_choices)
                    choice_w_active:SetSelection(0)
                    hbox_w_1:Add(st_w_1, 0, wx.wxALIGN_LEFT + wx.wxALL , space)
                    hbox_w_1:Add(choice_w_active, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
                    
                    local hbox_w_2 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_w_2   = wx.wxStaticText(pnlWiFi, wx.wxID_ANY, 'Radio',wx.wxDefaultPosition,label_size)
                    choice_w_radio = wx.wxChoice(pnlWiFi, wx.wxID_SAVE,
                               wx.wxDefaultPosition, wx.wxDefaultSize,
                               radio_choices)
                    choice_w_radio:Disable()
                    choice_w_radio:SetSelection(0)
                    hbox_w_2:Add(st_w_2, 0, wx.wxALIGN_LEFT + wx.wxALL , space)
                    hbox_w_2:Add(choice_w_radio, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
                    
                    local hbox_w_3 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_w_3   = wx.wxStaticText(pnlWiFi, wx.wxID_ANY, 'Encryption',wx.wxDefaultPosition,label_size)
                    choice_w_encryption = wx.wxChoice(pnlWiFi, wx.wxID_SAVE,
                               wx.wxDefaultPosition, wx.wxDefaultSize,
                               encryption_choices)
                    choice_w_encryption:SetSelection(0)
                    choice_w_encryption:Disable()
                    hbox_w_3:Add(st_w_3, 0, wx.wxALIGN_LEFT + wx.wxALL , space)
                    hbox_w_3:Add(choice_w_encryption, 1, wx.wxALL + wx.wxGROW + wx.wxCENTER, space)
                    
                    
                    local hbox_w_4  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_w_4    = wx.wxStaticText(pnlWiFi, wx.wxID_ANY, 'SSID',wx.wxDefaultPosition,label_size)
                    tc_w_ssid   = wx.wxTextCtrl(pnlWiFi, wx.wxID_ANY)
                    tc_w_ssid:SetEditable(false)
                    tc_w_ssid:SetBackgroundColour(grey)
                    hbox_w_4:Add(st_w_4, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_w_4:Add(tc_w_ssid, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                    
                     local hbox_w_5  = wx.wxBoxSizer(wx.wxHORIZONTAL)
                    local st_w_5    = wx.wxStaticText(pnlWiFi, wx.wxID_ANY, 'Key',wx.wxDefaultPosition,label_size)
                    tc_w_key   = wx.wxTextCtrl(pnlWiFi, wx.wxID_ANY)
                    tc_w_key:SetEditable(false)
                    tc_w_key:SetBackgroundColour(grey)
                    hbox_w_5:Add(st_w_5, 0, wx.wxALIGN_LEFT + wx.wxALL, space)
                    hbox_w_5:Add(tc_w_key, 1, wx.wxALIGN_LEFT + wx.wxALL, space)
                    
                vbox_wifi:Add(hbox_w_1,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_wifi:Add(hbox_w_2,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_wifi:Add(hbox_w_4,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_wifi:Add(hbox_w_3,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                vbox_wifi:Add(hbox_w_5,0, wx.wxALIGN_LEFT + wx.wxALL + wx.wxEXPAND, space)
                    
                
            szrWiFi:Add(vbox_wifi,1,wx.wxALL +wx.wxEXPAND,5)
        
        
        notebook:AddPage(pnlWiFi, "WiFi Client",false,3)
        
            --Security Config--
            local pnlSecurity = wx.wxPanel(notebook, wx.wxID_ANY)
            local szrSecurity = wx.wxBoxSizer(wx.wxVERTICAL)
            pnlSecurity:SetSizer(szrSecurity)
            szrSecurity:SetSizeHints(pnlSecurity)
            
                --Shared Secret box--
                local sb1   = wx.wxStaticBox( pnlSecurity, wx.wxID_ANY, "Shared Secret")
                local sbs1  = wx.wxStaticBoxSizer( sb1, wx.wxVERTICAL);
                local hbox1 = wx.wxBoxSizer(wx.wxHORIZONTAL)
                local st1   = wx.wxStaticText(pnlSecurity, wx.wxID_ANY, 'Secret ')
                tc_shared_secret   = wx.wxTextCtrl(pnlSecurity, wx.wxID_ANY,shared_secret)
                --tc_shared_secret:SetBackgroundColour(wx.wxGREEN )    --Set the color of the main panel red
                --FIXME You have to select this tab when shared secret is wrong
                hbox1:Add(st1, 1,   wx.wxALIGN_LEFT + wx.wxALL, 10)
                hbox1:Add(tc_shared_secret, 3,   wx.wxALIGN_LEFT + wx.wxALL, 10)
                sbs1:Add(hbox1, 0,  wx.wxALL , 5)
                
            szrSecurity:Add(sbs1,1,wx.wxEXPAND+wx.wxALL,5)
            
        notebook:AddPage(pnlSecurity, "Security",false,4)
            
        vbox:Add(notebook,1,wx.wxEXPAND+wx.wxALL,5)
        
    
    frame:Connect(wx.wxID_REDO, wx.wxEVT_COMMAND_CHOICE_SELECTED, HandleEvents)
    frame:Connect(wx.wxID_SAVE, wx.wxEVT_COMMAND_CHOICE_SELECTED, EventMobileAction)
    frame:Connect(wx.wxID_BOLD, wx.wxEVT_COMMAND_CHOICE_SELECTED, EventWifiAction)
    frame:Connect(wx.wxID_UNDO, wx.wxEVT_COMMAND_CHECKBOX_CLICKED, EventApplySettings)
    
    frame:Centre()
    frame:Show(true)
end

function set_up_server()
    local addr = wx.wxIPV4address() --We first create an IPv4 address
    addr:AnyAddress()               --We bind this to any address? (I think)
    addr:Service(port)              --We listen to requests on port
    local  m_server = wx.wxSocketServer(addr,wx.wxSOCKET_NOWAIT)--We create a server on that address
    
    if m_server:Ok() then
        --display("\nServer listening.")
        m_server:SetEventHandler(frame, SERVER_ID)          --Looks like we will send these events to the frame
        m_server:SetNotify(wx.wxSOCKET_CONNECTION_FLAG);    --Looks like we are only sending connection events to the event handler
        m_server:Notify(true)                               --Looks like we 'activate' notification
        UpdateStatusBar()
    else
        --display("\nCould not listen at the specified port !")
    end
    
    frame:Connect(SOCKET_ID, wx.wxEVT_SOCKET,
    function(event)
        local sock = event.Socket
        local socketEvent = event.SocketEvent
        print("The event is:"..socketEvent..":END")
        
        if socketEvent  == wx.wxSOCKET_INPUT then
            -- We disable input events, so that the test doesn't trigger wxSocketEvent again.
            sock.Notify = wx.wxSOCKET_LOST_FLAG
            local v = string.byte(sock:Read(1))
            local s = string.char(v)
            clear_errors()
            print("Incomming Character:"..s..":end")
            if(s == 'a')then
                md5sum_to_client(sock)
            elseif(s == 'b')then
                get_info(sock)
            elseif(s == 'c')then
                set_info(sock)
            elseif(s == 'x')then --x = error message to follow
                get_error(sock)
            else
                print("Unknown instruction id: ".. s .." received from client")
            end
            --sock:Close()
            --sock.Notify = wx.wxSOCKET_LOST_FLAG + wx.wxSOCKET_INPUT_FLAG
        elseif socketEvent == wx.wxSOCKET_LOST then
            print('wx.wxSOCKET_LOST')
            m_numClients = m_numClients -1
            UpdateStatusBar()
        else
            print("Unexpected socketEvent")
            print("Deleting socket.");
            sock:Destroy();
        end
    end)

    --We connected the wSocketServer to the frame's event handler(touchable thing)
    --We then filter for the SERVER_ID first and then if we get a socket; we connect
    -- to THAT socket's events (SOCKET_INPUT and SOCKET_LOST)
    -- (Each connection has a unique socket)
    frame:Connect(SERVER_ID, wx.wxEVT_SOCKET,
        function(event)
            if event:GetSocketEvent()  == wx.wxSOCKET_CONNECTION then
                print("wxSOCKET_CONNECTION")
            else
                print("Unexpected event !")
            end
            local sock = m_server:Accept(false) --wait = false + get the new socket
            if sock then
                print("New client connection accepted")
            else
                print("Error: couldn't accept a new connection")
                return
            end
            sock:SetEventHandler(frame, SOCKET_ID)
            sock:SetNotify(wx.wxSOCKET_INPUT_FLAG + wx.wxSOCKET_LOST_FLAG);
            sock:Notify(true)
            m_numClients = m_numClients + 1
            UpdateStatusBar()
        end)
end

function UpdateStatusBar()
    frame:SetStatusText('client connected: '.. m_numClients);
    local s =frame:GetStatusBar()
    s:SetBackgroundColour(wx.wxGREEN)
    s:ClearBackground ()
    if(m_numClients > 0)then
        s:Refresh(false)
    else
        s:Refresh()
    end
end

build_gui()
set_up_server()

wx.wxGetApp():MainLoop()

timer = wx.wxTimer(frame);
frame:Connect(wx.wxEVT_TIMER, function (event)
    --print("Timer event");
    fresh_timer = fresh_timer + 1;
    tc_timestamp:SetValue(os.date("!%X",fresh_timer).. "  (Time Ago)")
        if(fresh_timer > fresh_trigger)then
            tc_timestamp:SetBackgroundColour(blue)
        end
    end
);
frame:Connect(wx.wxEVT_CLOSE_WINDOW, function (event)
    timer:Stop();
    frame:Destroy();
    end
);

--timer:Start(1000);
--tc_timestamp:SetBackgroundColour(green)
