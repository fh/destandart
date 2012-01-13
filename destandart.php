<?php
/*
Plugin Name: DeStandart
Plugin URI: http://holzhauer.it
Description: Replace 'Standart' with 'Standard' in comments.
Version: 0.2
Author: Florian Holzhauer
License: WTFPL
*/

if (!function_exists('add_action')) {
    //Direct access, no WP call
    header('HTTP/1.1 403 Forbidden');
    die();
}


if (!class_exists('fh_wpplugin_deStandart')) {
    class fh_wpplugin_deStandart
    {

        var $base = false;
        var $folder = false;

        const VERSION = '0.2';
        /**
         * Identifier-Prefix for Forms and Settings
         * @var string
         */
        const PREFIX = 'fh_wpplugin_deStandart';

        public function __construct()
        {
            $this->base = plugin_basename(__FILE__);
            $this->folder = dirname($this->base);

            /*
             * Unused in this plugin, but since I use this as a boilerplate for other plugins, lets just
             * comment it out:
            */
            /*
            add_action('plugins_loaded', array(&$this, 'onStart'));
            add_action('admin_menu', array(&$this, 'addAdminMenu'));
            //*/

            add_filter('comment_text', array(&$this, 'replaceString'));

        }

        public function replaceString($comment)
        {
            //sure, could be done with regexes way nicer, but I just want to get rid of 'Standart' and 'Euronen'
            //makes me kinda nuts.

            $grmbl = array('standart', 'Standart', 'Teuronen', 'Euronen', 'Teuro');
            $yay = array('standard', 'Standard', 'Euro', 'Euro', 'Euro');
            //dont use ireplace!
            $comment = str_replace($grmbl, $yay, $comment);

            return $comment;
        }

        /*
         * Renders Admin interface, stores settings
         * @todo i18n
         * @todo this is too cluttered, separate html-form-stuff from storage logic
         */
        public function adminInterface()
        {
            $current_settings = get_option(self::PREFIX . '_settings');

            if (isset($_POST['submitted'])) {
                if (array_key_exists(self::PREFIX . '_posttypes', $_POST)) {
                    $existent_types = get_post_types();
                    $types = array();
                    foreach ($_POST[self::PREFIX . '_posttypes'] as $type) {
                        if (!in_array($type, $existent_types)) {
                            continue; //someone is playing with the form
                        }
                        $types[] = $type;
                    }
                    $submitted_settings = array('posttypes' => $types, 'version' => self::VERSION);
                    if ($current_settings != $submitted_settings) {
                        update_option(self::PREFIX . '_settings', $submitted_settings);
                        $current_settings = $submitted_settings;
                        echo '<div class="updated"><p><strong>Changes saved.</strong></p></div>';
                    }
                    unset($submitted_settings);
                }
            } else if (empty($current_settings)) {
                //new installation?
                $current_settings = array('posttypes' => array('post', 'page'), 'version' => self::VERSION);
                update_option(self::PREFIX . '_settings', $current_settings);
            }
            echo <<<EOF
<div class="wrap">
<h2>The admin menu</h2>
<form name="settings" action="" method="post">
<table width="100%" cellspacing="2" cellpadding="2" class="editform">
	<tr valign="top">
		<th scope="row" width="33%">
EOF;
            echo '        <label for="' . self::PREFIX . '_posttypes">Modify Post Types:</label></th>		<td>';
            $postTypes = get_post_types();
            foreach ($postTypes as $type) {
                echo '<input type="checkbox" name="' . self::PREFIX . '_posttypes[]" value="' . $type . '" ';
                if (in_array($type, $current_settings['posttypes'])) {
                    echo 'checked';
                }
                echo ' />' . htmlentities($type) . '<br />';
            }
            echo <<<EOF
		<br />
		Select post types to be modified.</td>
	</tr>
</table>

<p class="submit">
	<input type="hidden" name="submitted" />
	<input type="submit" name="Submit" class="button-primary" value="Save &raquo;" />
</p>
</form>
</div>
EOF;
        }

        /**
         * Inits Admin-Hooks, adds Settings-Link in Backend
         */
        public function addAdminMenu()
        {
            add_options_page('Plugin Options', 'Plugin', 'manage_options', self::PREFIX, array(&$this, 'adminInterface'));
        }


        public function onStart()
        {
            if (is_admin()) {
                if (get_option(self::PREFIX . 'warnings')) {
                    add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error"><p>' . get_option(self::PREFIX . 'warnings') . '</p></div>\';'));
                }
            }
        }
    }
}

/**
 * Global Init Funktion, called by wordpress on init.
 * All other hooks and settings are set in the constructor of the object.
 */
function fh_wpplugin_deStandart_Init()
{
    new fh_wpplugin_deStandart();
}

add_action('plugins_loaded', 'fh_wpplugin_deStandart_Init');
