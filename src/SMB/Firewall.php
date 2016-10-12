<?php
/**
 * User: jonathan.bak
 * Date: 2016. 10. 12.
 */

namespace SMB;


class Firewall
{
    public static function ruleStart()
    {
        //firewall 가동
        $firewallFlag = Configure::site('firewall');
        if(empty($firewallFlag)) $firewallFlag = 0;
        if($firewallFlag==1||strtolower($firewallFlag)=='on'){
            $allowIPs = Configure::site('allowIps');
            if(!in_array($_SERVER['REMOTE_ADDR'],$allowIPs)){
                throw new FirewallException("Access Denied - ". $_SERVER['REMOTE_ADDR']);
            }
        }
    }
}

class FirewallException extends Exception {

}