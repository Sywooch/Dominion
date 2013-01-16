<?php
//=======================================
//###################################
// Holbi Web Solutions
//
// Source Copyright 2000-2009 Holbi Web Solutions
// Unauthorized reproduction is not allowed
// $Author: Oleksandr Grytsenko $ ($Date: 20.07.2009)
// $RCSfile: erp\library\Erp\View\Helper\BaseUrl.php$ 
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                   www.holbi.co.uk
//###################################
//=======================================
class App_View_Helper_BaseUrl
{
    /**
     *  Get base url
     * 
     * @return string
     */
    public function baseUrl ()
    {
        if (isset($_SERVER['HTTPS'])) {
            $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
        } else {
            $protocol = 'http';
        }
        $server = $_SERVER['HTTP_HOST'];
        $port = $_SERVER['SERVER_PORT'] != 80 ? ":{$_SERVER['SERVER_PORT']}" : '';
        $path = '/';
        return "$protocol://$server$port$path";
    }


}
