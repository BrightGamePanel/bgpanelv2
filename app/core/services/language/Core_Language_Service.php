<?php

/**
 * LICENSE:
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package		Bright Game Panel V2
 * @version		0.1
 * @category	Systems Administration
 * @author		warhawk3407 <warhawk3407@gmail.com> @NOSPAM
 * @copyright	Copyleft 2015, Nikita Rousseau
 * @license		GNU General Public License version 3.0 (GPLv3)
 * @link		http://www.bgpanel.net/
 */



final class Core_Language_Service implements Core_Service_Interface {

    private static $service_handle = null;

    // Current Locale
    private $locale = '';

    /**
     * Core_Lang constructor.
     */
    private function __construct()
    {
        $this->setLanguage();
    }

    /**
     * Creates a Language Manager
     */
    public static function getService() {
        if (empty(self::$service_handle) ||
            !is_object(self::$service_handle) ||
            (get_class(self::$service_handle) != 'Core_Language_Service')) {

            self::$service_handle = new Core_Language_Service();
        }

        return self::$service_handle;
    }

    /**
     * Define language for get-text translator
     *
     * Directory structure for the translation must be:
     *        ./app/locale/Lang/LC_MESSAGES/messages.mo
     * Example (French):
     *        ./app/locale/fr_FR/LC_MESSAGES/messages.mo
     *
     * @param string $lang
     */
	public function setLanguage($lang = CONF_DEFAULT_LOCALE) {

        $languages = parse_ini_file( CONF_LANG_INI );
        if (!empty($lang) && in_array($lang, $languages)) {
            $this->locale = $lang;
        } else {
            $this->locale = CONF_DEFAULT_LOCALE;
        }

        // gettext setup
        T_setlocale(LC_MESSAGES, $this->locale);

        // Set the text domain as 'messages'
        T_bindtextdomain('messages', LOCALE_DIR);
        T_bind_textdomain_codeset('messages', 'UTF-8');
        T_textdomain('messages');
	}

    /**
     * Gets the current language
     *
     * @return string
     */
	public function getLanguage() {
	    return $this->locale;
    }
}
