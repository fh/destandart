<?php

/*
Plugin Name: DeStandart
Plugin URI: http://holzhauer.it
Description: Replace 'Standart' with 'Standard' in comments.
Version: 0.1
Author: Florian Holzhauer
License: WTFPL*/

$GLOBALS['destandart'] = new destandart();

function fhdbg($sString) {
   // echo $sString;
}
class destandart {
    
    var $version = '0.1';
    var $root    = false;
    var $base    = false;
    var $folder  = false;
        
    function destandart() {
        if(!function_exists('add_action')) {
            die('This is a wordpress plugin. Do not access it directly.');
        }

        $this->base   = plugin_basename(__FILE__);
        $this->folder = dirname($this->base);

        add_action('plugins_loaded', array(&$this, 'start'));
        add_filter('comment_text', array(&$this, 'replacestring'));

    }
    
    function replacestring($comment) {        //sure, could be done with regexes way nicer, but I just want to get rid of 'Standart' and 'Euronen'        //makes me kinda nuts.                $grmbl = array('andart','uronen');        $yay   = array('andard','uro');                $comment = str_replace($grmbl, $yay, $comment);        
        return $comment;    }

    function start() {
        if(is_admin()) {
            if(get_option('destandart-warnings')) {
                 add_action( 'admin_notices', create_function('', 'echo \'<div id="message" class="error"><p>' . get_option( 'destandart-warnings' ) . '</p></div>\';') );
            }
        }
    }
}