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



class Core_Lang {

	/**
	 * Define language for get-text translator
	 *
	 * Directory structure for the translation must be:
	 *		./app/locale/Lang/LC_MESSAGES/messages.mo
	 * Example (French):
	 *		./app/locale/fr_FR/LC_MESSAGES/messages.mo
	 */
	public static function setLanguage( $lang = 'en_EN' ) {
		$encoding = 'UTF-8';
		$languages = parse_ini_file( CONF_LANG_INI );

		if ( isset($lang) && in_array($lang, $languages) ) {
			$locale = $lang;
		} else {
			$locale = CONF_DEFAULT_LOCALE;
		}

		// gettext setup
		T_setlocale(LC_MESSAGES, $locale);
		// Set the text domain as 'messages'
		$domain = 'messages';
		T_bindtextdomain($domain, LOCALE_DIR);
		T_bind_textdomain_codeset($domain, $encoding);
		T_textdomain($domain);
	}
}
