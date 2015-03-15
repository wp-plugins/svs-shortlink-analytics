<?php

class ShortlinkAnalyticsUtil
{
    /**
     * Get it from $_SERVER request
     */
    public function getip(){

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Get Operating System
     */
    public function getOS() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/windows nt 6.2/i', $user_agent)) { $os_platform = "Windows 8";}
        elseif (preg_match('/windows nt 6.1/i', $user_agent)){$os_platform = "Windows 7";}
        elseif (preg_match('/windows nt 6.0/i', $user_agent)){$os_platform = "Windows Vista";}
        elseif (preg_match('/windows nt 5.2/i', $user_agent)){$os_platform = "Windows Server 2003/XP x64";}
        elseif (preg_match('/windows nt 5.1/i', $user_agent)){$os_platform = "Windows XP";}
        elseif (preg_match('/windows xp/i', $user_agent)){$os_platform = "Windows XP";}
        elseif (preg_match('/windows nt 5.0/i', $user_agent)){$os_platform = "Windows 2000";}
        elseif (preg_match('/windows me/i', $user_agent)){$os_platform = "Windows ME";}
        elseif (preg_match('/win98/i', $user_agent)){$os_platform = "Windows 98";}
        elseif (preg_match('/win95/i', $user_agent)){$os_platform = "Windows 95";}
        elseif (preg_match('/win16/i', $user_agent)){$os_platform = "Windows 3.11";}
        elseif (preg_match('/macintosh|mac os x/i', $user_agent)){$os_platform = "Mac OS X";}
        elseif (preg_match('/mac_powerpc/i', $user_agent)){$os_platform = "Mac OS 9";}
        elseif (preg_match('/linux/i', $user_agent)){$os_platform = "Linux";}
        elseif (preg_match('/ubuntu/i', $user_agent)){$os_platform = "Ubuntu";}
        elseif (preg_match('/iphone/i', $user_agent)){$os_platform = "iPhone";}
        elseif (preg_match('/ipod/i', $user_agent)){$os_platform = "iPod";}
        elseif (preg_match('/ipad/i', $user_agent)){$os_platform = "iPad";}
        elseif (preg_match('/android/i', $user_agent)){$os_platform = "Android";}
        elseif (preg_match('/blackberry/i', $user_agent)){$os_platform = "BlackBerry";}
        elseif (preg_match('/webos/i', $user_agent)){$os_platform = "Mobile";}
        else {$browser = "Other";}

        return $os_platform;
    }

    /**
     * Get browser signature
     */
    public function getbrowser(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $user_agent)) { $browser = "Internet Explorer";}
        elseif (preg_match('/Firefox/i', $user_agent)){$browser = "Firefox";}
        elseif (preg_match('/Chrome/i', $user_agent)){$browser = "Google Chrome";}
        elseif (preg_match('/Safari/i', $user_agent)){$browser = "Safari";}
        elseif (preg_match('/Opera/i', $user_agent)){$browser = "Opera";}
        else {$browser = "Other";}

        return $browser;
    }

    /**
     * Get country using geoplugin.net public service
     */
    public function getcountry(){
    	$ip = $this->getip();
        $details = file_get_contents("http://www.geoplugin.net/json.gp?ip=$ip");
        return $details;
    }

    /**
     * Get device signature
     */
    public function getdevice(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/iphone/i', $user_agent)) {$device = "Mobile";}
        elseif (preg_match('/ipod/i', $user_agent)){$device = "Mobile";}
        elseif (preg_match('/ipad/i', $user_agent)){$device = "Mobile";}
        elseif (preg_match('/android/i', $user_agent)){$device = "Mobile";}
        elseif (preg_match('/blackberry/i', $user_agent)){$device = "Mobile";}
        elseif (preg_match('/webos/i', $user_agent)){$device = "Mobile";}
        else {$device = "Desktop";}
        return $device;
    }
}