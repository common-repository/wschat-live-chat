<?php

class Browser {
	public $agent        = '';
	public $browser_name = '';
	public $version      = '';
	public $platform     = '';
	public $os           = '';
	public $is_aol       = false;
	public $is_mobile    = false;
	public $is_robot     = false;
	public $aol_version  = '';

	public $BROWSER_UNKNOWN = 'unknown';
	public $VERSION_UNKNOWN = 'unknown';

	public $BROWSER_OPERA        = 'Opera';                            // Http://www.opera.com/
	public $BROWSER_OPERA_MINI   = 'Opera Mini';                  // Http://www.opera.com/mini/
	public $BROWSER_WEBTV        = 'WebTV';                            // Http://www.webtv.net/pc/
	public $BROWSER_IE           = 'Internet Explorer';                   // Http://www.microsoft.com/ie/
	public $BROWSER_POCKET_IE    = 'Pocket Internet Explorer';     // Http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
	public $BROWSER_KONQUEROR    = 'Konqueror';                    // Http://www.konqueror.org/
	public $BROWSER_ICAB         = 'iCab';                              // Http://www.icab.de/
	public $BROWSER_OMNIWEB      = 'OmniWeb';                        // Http://www.omnigroup.com/applications/omniweb/
	public $BROWSER_FIREBIRD     = 'Firebird';                      // Http://www.ibphoenix.com/
	public $BROWSER_FIREFOX      = 'Firefox';                        // Http://www.mozilla.com/en-US/firefox/firefox.html
	public $BROWSER_ICEWEASEL    = 'Iceweasel';                    // Http://www.geticeweasel.org/
	public $BROWSER_SHIRETOKO    = 'Shiretoko';                    // Http://wiki.mozilla.org/Projects/shiretoko
	public $BROWSER_MOZILLA      = 'Mozilla';                        // Http://www.mozilla.com/en-US/
	public $BROWSER_AMAYA        = 'Amaya';                            // Http://www.w3.org/Amaya/
	public $BROWSER_LYNX         = 'Lynx';                              // Http://en.wikipedia.org/wiki/Lynx
	public $BROWSER_SAFARI       = 'Safari';                          // Http://apple.com
	public $BROWSER_IPHONE       = 'iPhone';                          // Http://apple.com
	public $BROWSER_IPOD         = 'iPod';                              // Http://apple.com
	public $BROWSER_IPAD         = 'iPad';                              // Http://apple.com
	public $BROWSER_CHROME       = 'Chrome';                          // Http://www.google.com/chrome
	public $BROWSER_ANDROID      = 'Android';                        // Http://www.android.com/
	public $BROWSER_GOOGLEBOT    = 'GoogleBot';                    // Http://en.wikipedia.org/wiki/Googlebot
	public $BROWSER_SLURP        = 'Yahoo! Slurp';                     // Http://en.wikipedia.org/wiki/Yahoo!_Slurp
	public $BROWSER_W3CVALIDATOR = 'W3C Validator';             // Http://validator.w3.org/
	public $BROWSER_BLACKBERRY   = 'BlackBerry';                  // Http://www.blackberry.com/
	public $BROWSER_ICECAT       = 'IceCat';                          // Http://en.wikipedia.org/wiki/GNU_IceCat
	public $BROWSER_NOKIA_S60    = 'Nokia S60 OSS Browser';        // Http://en.wikipedia.org/wiki/Web_Browser_for_S60
	public $BROWSER_NOKIA        = 'Nokia Browser';                    // * all other WAP-based browsers on the Nokia Platform
	public $BROWSER_MSN          = 'MSN Browser';                        // Http://explorer.msn.com/
	public $BROWSER_MSNBOT       = 'MSN Bot';                         // Http://search.msn.com/msnbot.htm
															  // Http://en.wikipedia.org/wiki/Msnbot  (used for Bing as well)

	public $BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator';  // Http://browser.netscape.com/ (DEPRECATED)
	public $BROWSER_GALEON             = 'Galeon';                          // Http://galeon.sourceforge.net/ (DEPRECATED)
	public $BROWSER_NETPOSITIVE        = 'NetPositive';                // Http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
	public $BROWSER_PHOENIX            = 'Phoenix';                        // Http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)

	public $PLATFORM_UNKNOWN     = 'unknown';
	public $PLATFORM_WINDOWS     = 'Windows';
	public $PLATFORM_WINDOWS_CE  = 'Windows CE';
	public $PLATFORM_APPLE       = 'Apple';
	public $PLATFORM_LINUX       = 'Linux';
	public $PLATFORM_OS2         = 'OS/2';
	public $PLATFORM_BEOS        = 'BeOS';
	public $PLATFORM_IPHONE      = 'iPhone';
	public $PLATFORM_IPOD        = 'iPod';
	public $PLATFORM_IPAD        = 'iPad';
	public $PLATFORM_BLACKBERRY  = 'BlackBerry';
	public $PLATFORM_NOKIA       = 'Nokia';
	public $PLATFORM_FREEBSD     = 'FreeBSD';
	public $PLATFORM_OPENBSD     = 'OpenBSD';
	public $PLATFORM_NETBSD      = 'NetBSD';
	public $PLATFORM_SUNOS       = 'SunOS';
	public $PLATFORM_OPENSOLARIS = 'OpenSolaris';
	public $PLATFORM_ANDROID     = 'Android';

	public $OPERATING_SYSTEM_UNKNOWN = 'unknown';

	public function Browsers( $useragent = '' ) {
		$this->reset();
		if ( '' !== $useragent ) {
			$this->setUserAgent( $useragent );
		} else {
			$this->determine();
		}
	}

	/**
	* Reset all properties
	*/
	public function reset() {
		$this->agent        = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';
		$this->browser_name = $this->BROWSER_UNKNOWN;
		$this->version      = $this->VERSION_UNKNOWN;
		$this->platform     = $this->PLATFORM_UNKNOWN;
		$this->_os          = $this->OPERATING_SYSTEM_UNKNOWN;
		$this->_is_aol      = false;
		$this->_is_mobile   = false;
		$this->_is_robot    = false;
		$this->_aol_version = $this->VERSION_UNKNOWN;
	}

	/**
	* Check to see if the specific browser is valid
		 *
	* @param string $browserName
	* @return True if the browser is the specified browser
	*/
	public function isBrowser( $browserName ) {
 return( 0 === strcasecmp( $this->browser_name, trim( $browserName ) ) ); }

	/**
	* The name of the browser.  All return types are from the class contants
		 *
	* @return string Name of the browser
	*/
	public function getBrowser() {
 return $this->browser_name; }
	/**
	* Set the name of the browser
		 *
	* @param $browser The name of the Browser
	*/
	public function setBrowser( $browser ) {
  $this->browser_name = $browser; 
  return $this->browser_name;
	}
	/**
	* The name of the platform.  All return types are from the class contants
		 *
	* @return string Name of the browser
	*/
	public function getPlatform() {
 return $this->platform; }
	/**
	* Set the name of the platform
		 *
	* @param $platform The name of the Platform
	*/
	public function setPlatform( $platform ) {
		$this->platform = $platform;
		return $this->platform;
	}
	/**
	* The version of the browser.
		 *
	* @return string Version of the browser (will only contain alpha-numeric characters and a period)
	*/
	public function getVersion() {
 return $this->version; }
	/**
	* Set the version of the browser
		 *
	* @param $version The version of the Browser
	*/
	public function setVersion( $version ) {
 $this->version = preg_replace( '/[^0-9,.,a-z,A-Z-]/', '', $version ); }
	/**
	* The version of AOL.
		 *
	* @return string Version of AOL (will only contain alpha-numeric characters and a period)
	*/
	public function getAolVersion() {
 return $this->_aol_version; }
	/**
	* Set the version of AOL
		 *
	* @param $version The version of AOL
	*/
	public function setAolVersion( $version ) {
 $this->_aol_version = preg_replace( '/[^0-9,.,a-z,A-Z]/', '', $version ); }
	/**
	* Is the browser from AOL?
		 *
	* @return boolean True if the browser is from AOL otherwise false
	*/
	public function isAol() {
 return $this->_is_aol; }
	/**
	* Is the browser from a mobile device?
		 *
	* @return boolean True if the browser is from a mobile device otherwise false
	*/
	public function isMobile() {
 return $this->_is_mobile; }
	/**
	* Is the browser from a robot (ex Slurp,GoogleBot)?
		 *
	* @return boolean True if the browser is from a robot otherwise false
	*/
	public function isRobot() {
 return $this->_is_robot; }
	/**
	* Set the browser to be from AOL
		 *
	* @param $isAol
	*/
	public  function setAol( $isAol ) {
 $this->_is_aol = $isAol; }
	/**
	 * Set the Browser to be mobile
		 *
	 * @param boolean $value is the browser a mobile brower or not
	 */
	public function setMobile( $value = true ) {
 $this->_is_mobile = $value; }
	/**
	 * Set the Browser to be a robot
		 *
	 * @param boolean $value is the browser a robot or not
	 */
	public function setRobot( $value = true ) {
 $this->_is_robot = $value; }
	/**
	* Get the user agent value in use to determine the browser
		 *
	* @return string The user agent from the HTTP header
	*/
	public function getUserAgent() {
 return $this->agent; }
	/**
	* Set the user agent value (the construction will use the HTTP header value - this will overwrite it)
		 *
	* @param $agent_string The value for the User Agent
	*/
	public function setUserAgent( $agent_string ) {
		$this->reset();
		$this->agent = $agent_string;
		$this->determine();
	}
	/**
	 * Used to determine if the browser is actually "chromeframe"
		 *
	 * @since 1.7
	 * @return boolean True if the browser is using chromeframe
	 */
	public function isChromeFrame() {
		return( strpos( $this->agent, 'chromeframe' ) !== false );
	}
	/**
	* Returns a formatted string with a summary of the details of the browser.
		 *
	* @return string formatted string with a summary of the browser
	*/
	public function __toString() {
		$text1    = $this->getUserAgent(); //grabs the UA (user agent) string
		$UAline1  = substr( $text1, 0, 32 ); //the first line we print should only be the first 32 characters of the UA string
		$text2    = $this->getUserAgent();//now we grab it again and save it to a string
		$towrapUA = str_replace( $UAline1, '', $text2 );//the rest of the printoff (other than first line) is equivolent
		// To the whole string minus the part we printed off. IE
		// User Agent:      thefirst32charactersfromUAline1
		//                  the rest of it is now stored in
		//                  $text2 to be printed off
		// But we need to add spaces before each line that is split other than line 1
		$space = '';
		for ( $i = 0; $i < 25; $i++ ) {
		$space .= ' ';
		}
		// Now we split the remaining string of UA ($text2) into lines that are prefixed by spaces for formatting
		$wordwrapped = chunk_split( $towrapUA, 32, "\n $space" );
		return "Platform:                 {$this->getPlatform()} \n" .
			   "Browser Name:             {$this->getBrowser()}  \n" .
			   "Browser Version:          {$this->getVersion()} \n" .
			   "User Agent String:        $UAline1 \n\t\t\t  " .
			   "$wordwrapped";
	}
	/**
	 * Protected routine to calculate and determine what the browser is in use (including platform)
	 */
	public function determine() {
		$this->checkPlatform();
		$this->checkBrowsers();
		$this->checkForAol();
	}
	/**
	 * Protected routine to determine the browser type
		 *
	 * @return boolean True if the browser was detected otherwise false
	 */
	public function checkBrowsers() {
		  return (
			  // Well-known, well-used
			  // Special Notes:
			  // (1) Opera must be checked before FireFox due to the odd
			  //     user agents used in some older versions of Opera
			  // (2) WebTV is strapped onto Internet Explorer so we must
			  //     check for WebTV before IE
			  // (3) (deprecated) Galeon is based on Firefox and needs to be
			  //     tested before Firefox is tested
			  // (4) OmniWeb is based on Safari so OmniWeb check must occur
			  //     before Safari
			  // (5) Netscape 9+ is based on Firefox so Netscape checks
			  //     before FireFox are necessary
			  $this->checkBrowserWebTv() ||
			  $this->checkBrowserInternetExplorer() ||
			  $this->checkBrowserOpera() ||
			  $this->checkBrowserGaleon() ||
			  $this->checkBrowserNetscapeNavigator9Plus() ||
			  $this->checkBrowserFirefox() ||
			  $this->checkBrowserChrome() ||
			  $this->checkBrowserOmniWeb() ||

			  // Common mobile
			  $this->checkBrowserAndroid() ||
			  $this->checkBrowseriPad() ||
			  $this->checkBrowseriPod() ||
			  $this->checkBrowseriPhone() ||
			  $this->checkBrowserBlackBerry() ||
			  $this->checkBrowserNokia() ||

			  // Common bots
			  $this->checkBrowserGoogleBot() ||
			  $this->checkBrowserMSNBot() ||
			  $this->checkBrowserSlurp() ||

			  // WebKit base check (post mobile and others)
			  $this->checkBrowserSafari() ||

			  // Everyone else
			  $this->checkBrowserNetPositive() ||
			  $this->checkBrowserFirebird() ||
			  $this->checkBrowserKonqueror() ||
			  $this->checkBrowserIcab() ||
			  $this->checkBrowserPhoenix() ||
			  $this->checkBrowserAmaya() ||
			  $this->checkBrowserLynx() ||

			  $this->checkBrowserShiretoko() ||
			  $this->checkBrowserIceCat() ||
			  $this->checkBrowserW3CValidator() ||
			  $this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
		  );
	}

		/**
		 * Determine if the user is using a BlackBerry (last updated 1.7)
		 *
		 * @return boolean True if the browser is the BlackBerry browser otherwise false
		 */
	public function checkBrowserBlackBerry() {
		if ( stripos( $this->agent, 'blackberry' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'BlackBerry' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->browser_name = $this->BROWSER_BLACKBERRY;
			$this->setMobile( true );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the user is using an AOL User Agent (last updated 1.7)
		 *
		 * @return boolean True if the browser is from AOL otherwise false
		 */
	public function checkForAol() {
		$this->setAol( false );
		$this->setAolVersion( $this->VERSION_UNKNOWN );

		if ( stripos( $this->agent, 'aol' ) !== false ) {
			$aversion = explode( ' ', stristr( $this->agent, 'AOL' ) );
			$this->setAol( true );
			$this->setAolVersion( preg_replace( '/[^0-9\.a-z]/i', '', $aversion[1] ) );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is the GoogleBot or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is the GoogletBot otherwise false
		 */
	public function checkBrowserGoogleBot() {
		if ( stripos( $this->agent, 'googlebot' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'googlebot' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( str_replace( ';', '', $aversion[0] ) );
			$this->browser_name = $this->BROWSER_GOOGLEBOT;
			$this->setRobot( true );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is the MSNBot or not (last updated 1.9)
		 *
		 * @return boolean True if the browser is the MSNBot otherwise false
		 */
	public function checkBrowserMSNBot() {
		if ( stripos( $this->agent, 'msnbot' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'msnbot' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( str_replace( ';', '', $aversion[0] ) );
			$this->browser_name = $this->BROWSER_MSNBOT;
			$this->setRobot( true );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is the W3C Validator or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is the W3C Validator otherwise false
		 */
	public function checkBrowserW3CValidator() {
		if ( stripos( $this->agent, 'W3C-checklink' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'W3C-checklink' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->browser_name = $this->BROWSER_W3CVALIDATOR;
			return true;
		} elseif ( stripos( $this->agent, 'W3C_Validator' ) !== false ) {
			// Some of the Validator versions do not delineate w/ a slash - add it back in
			$ua       = str_replace( 'W3C_Validator ', 'W3C_Validator/', $this->agent );
			$aresult  = explode( '/', stristr( $ua, 'W3C_Validator' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->browser_name = $this->BROWSER_W3CVALIDATOR;
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is the Yahoo! Slurp Robot or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is the Yahoo! Slurp Robot otherwise false
		 */
	public function checkBrowserSlurp() {
		if ( stripos( $this->agent, 'slurp' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'Slurp' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->browser_name = $this->BROWSER_SLURP;
			$this->setRobot( true );
			$this->setMobile( false );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Internet Explorer or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Internet Explorer otherwise false
		 */
	public function checkBrowserInternetExplorer() {

		// Test for v1 - v1.5 IE
		if ( stripos( $this->agent, 'microsoft internet explorer' ) !== false ) {
			$this->setBrowser( $this->BROWSER_IE );
			$this->setVersion( '1.0' );
			$aresult = stristr( $this->agent, '/' );
			if ( preg_match( '/308|425|426|474|0b1/i', $aresult ) ) {
				$this->setVersion( '1.5' );
			}
			return true;
		} elseif ( stripos( $this->agent, 'msie' ) !== false && stripos( $this->agent, 'opera' ) === false ) {
			// See if the browser is the odd MSN Explorer
			if ( stripos( $this->agent, 'msnb' ) !== false ) {
				$aresult = explode( ' ', stristr( str_replace( ';', '; ', $this->agent ), 'MSN' ) );
				$this->setBrowser( $this->BROWSER_MSN );
				$this->setVersion( str_replace( array( '(', ')', ';' ), '', $aresult[1] ) );
				return true;
			}
			$aresult = explode( ' ', stristr( str_replace( ';', '; ', $this->agent ), 'msie' ) );
			$this->setBrowser( $this->BROWSER_IE );
			$this->setVersion( str_replace( array( '(', ')', ';' ), '', $aresult[1] ) );
			return true;
		} elseif ( stripos( $this->agent, 'mspie' ) !== false || stripos( $this->agent, 'pocket' ) !== false ) {
			$aresult = explode( ' ', stristr( $this->agent, 'mspie' ) );
			$this->setPlatform( $this->PLATFORM_WINDOWS_CE );
			$this->setBrowser( $this->BROWSER_POCKET_IE );
			$this->setMobile( true );

			if ( stripos( $this->agent, 'mspie' ) !== false ) {
				$this->setVersion( $aresult[1] );
			} else {
				$aversion = explode( '/', $this->agent );
				$this->setVersion( $aversion[1] );
			}
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Opera or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Opera otherwise false
		 */
	public function checkBrowserOpera() {
		if ( stripos( $this->agent, 'opera mini' ) !== false ) {
			$resultant = stristr( $this->agent, 'opera mini' );
			if ( preg_match( '/\//', $resultant ) ) {
				$aresult  = explode( '/', $resultant );
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$aversion = explode( ' ', stristr( $resultant, 'opera mini' ) );
				$this->setVersion( $aversion[1] );
			}
			$this->browser_name = $this->BROWSER_OPERA_MINI;
			$this->setMobile( true );
			return true;
		} elseif ( stripos( $this->agent, 'opera' ) !== false ) {
			$resultant = stristr( $this->agent, 'opera' );
			if ( preg_match( '/Version\/(10.*)$/', $resultant, $matches ) ) {
				$this->setVersion( $matches[1] );
			} elseif ( preg_match( '/\//', $resultant ) ) {
				$aresult  = explode( '/', str_replace( '(', ' ', $resultant ) );
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$aversion = explode( ' ', stristr( $resultant, 'opera' ) );
				$this->setVersion( isset( $aversion[1] ) ? $aversion[1] : '' );
			}
			$this->browser_name = $this->BROWSER_OPERA;
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Chrome or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Chrome otherwise false
		 */
	public function checkBrowserChrome() {
		if ( stripos( $this->agent, 'Chrome' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'Chrome' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->setBrowser( $this->BROWSER_CHROME );
			return true;
		}
		return false;
	}


		/**
		 * Determine if the browser is WebTv or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is WebTv otherwise false
		 */
	public function checkBrowserWebTv() {
		if ( stripos( $this->agent, 'webtv' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'webtv' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->setBrowser( $this->BROWSER_WEBTV );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is NetPositive or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is NetPositive otherwise false
		 */
	public function checkBrowserNetPositive() {
		if ( stripos( $this->agent, 'NetPositive' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'NetPositive' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( str_replace( array( '(', ')', ';' ), '', $aversion[0] ) );
			$this->setBrowser( $this->BROWSER_NETPOSITIVE );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Galeon or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Galeon otherwise false
		 */
	public  function checkBrowserGaleon() {
		if ( stripos( $this->agent, 'galeon' ) !== false ) {
			$aresult  = explode( ' ', stristr( $this->agent, 'galeon' ) );
			$aversion = explode( '/', $aresult[0] );
			$this->setVersion( $aversion[1] );
			$this->setBrowser( $this->BROWSER_GALEON );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Konqueror or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Konqueror otherwise false
		 */
	public  function checkBrowserKonqueror() {
		if ( stripos( $this->agent, 'Konqueror' ) !== false ) {
			$aresult  = explode( ' ', stristr( $this->agent, 'Konqueror' ) );
			$aversion = explode( '/', $aresult[0] );
			$this->setVersion( $aversion[1] );
			$this->setBrowser( $this->BROWSER_KONQUEROR );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is iCab or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is iCab otherwise false
		 */
	public function checkBrowserIcab() {
		if ( stripos( $this->agent, 'icab' ) !== false ) {
			$aversion = explode( ' ', stristr( str_replace( '/', ' ', $this->agent ), 'icab' ) );
			$this->setVersion( $aversion[1] );
			$this->setBrowser( $this->BROWSER_ICAB );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is OmniWeb or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is OmniWeb otherwise false
		 */
	public function checkBrowserOmniWeb() {
		if ( stripos( $this->agent, 'omniweb' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'omniweb' ) );
			$aversion = explode( ' ', isset( $aresult[1] ) ? $aresult[1] : '' );
			$this->setVersion( $aversion[0] );
			$this->setBrowser( $this->BROWSER_OMNIWEB );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Phoenix or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Phoenix otherwise false
		 */
	public function checkBrowserPhoenix() {
		if ( stripos( $this->agent, 'Phoenix' ) !== false ) {
			$aversion = explode( '/', stristr( $this->agent, 'Phoenix' ) );
			$this->setVersion( $aversion[1] );
			$this->setBrowser( $this->BROWSER_PHOENIX );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Firebird or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Firebird otherwise false
		 */
	public function checkBrowserFirebird() {
		if ( stripos( $this->agent, 'Firebird' ) !== false ) {
			$aversion = explode( '/', stristr( $this->agent, 'Firebird' ) );
			$this->setVersion( $aversion[1] );
			$this->setBrowser( $this->BROWSER_FIREBIRD );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Netscape Navigator 9+ or not (last updated 1.7)
		 * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
		 *
		 * @return boolean True if the browser is Netscape Navigator 9+ otherwise false
		 */
	public function checkBrowserNetscapeNavigator9Plus() {
		if ( stripos( $this->agent, 'Firefox' ) !== false && preg_match( '/Navigator\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->setVersion( $matches[1] );
			$this->setBrowser( $this->BROWSER_NETSCAPE_NAVIGATOR );
			return true;
		} elseif ( stripos( $this->agent, 'Firefox' ) === false && preg_match( '/Netscape6?\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->setVersion( $matches[1] );
			$this->setBrowser( $this->BROWSER_NETSCAPE_NAVIGATOR );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko) (last updated 1.7)
		 *
		 * @return boolean True if the browser is Shiretoko otherwise false
		 */
	public  function checkBrowserShiretoko() {
		if ( stripos( $this->agent, 'Mozilla' ) !== false && preg_match( '/Shiretoko\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->setVersion( $matches[1] );
			$this->setBrowser( $this->BROWSER_SHIRETOKO );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat) (last updated 1.7)
		 *
		 * @return boolean True if the browser is Ice Cat otherwise false
		 */
	public function checkBrowserIceCat() {
		if ( stripos( $this->agent, 'Mozilla' ) !== false && preg_match( '/IceCat\/([^ ]*)/i', $this->agent, $matches ) ) {
			$this->setVersion( $matches[1] );
			$this->setBrowser( $this->BROWSER_ICECAT );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Nokia or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Nokia otherwise false
		 */
	public function checkBrowserNokia() {
		if ( preg_match( '/Nokia([^\/]+)\/([^ SP]+)/i', $this->agent, $matches ) ) {
			$this->setVersion( $matches[2] );
			if ( stripos( $this->agent, 'Series60' ) !== false || strpos( $this->agent, 'S60' ) !== false ) {
				$this->setBrowser( $this->BROWSER_NOKIA_S60 );
			} else {
				$this->setBrowser( $this->BROWSER_NOKIA );
			}
			$this->setMobile( true );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Firefox or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Firefox otherwise false
		 */
	public function checkBrowserFirefox() {
		if ( stripos( $this->agent, 'safari' ) === false ) {
			if ( preg_match( '/Firefox[\/ \(]([^ ;\)]+)/i', $this->agent, $matches ) ) {
				$this->setVersion( $matches[1] );
				$this->setBrowser( $this->BROWSER_FIREFOX );
				return true;
			} elseif ( preg_match( '/Firefox$/i', $this->agent, $matches ) ) {
				$this->setVersion( '' );
				$this->setBrowser( $this->BROWSER_FIREFOX );
				return true;
			}
		}
		return false;
	}

		/**
		 * Determine if the browser is Firefox or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Firefox otherwise false
		 */
	public function checkBrowserIceweasel() {
		if ( stripos( $this->agent, 'Iceweasel' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'Iceweasel' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->setBrowser( $this->BROWSER_ICEWEASEL );
			return true;
		}
		return false;
	}
		/**
		 * Determine if the browser is Mozilla or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Mozilla otherwise false
		 */
	public function checkBrowserMozilla() {
		if ( stripos( $this->agent, 'mozilla' ) !== false && preg_match( '/rv:[0-9].[0-9][a-b]?/i', $this->agent ) && stripos( $this->agent, 'netscape' ) === false ) {
			$aversion = explode( ' ', stristr( $this->agent, 'rv:' ) );
			preg_match( '/rv:[0-9].[0-9][a-b]?/i', $this->agent, $aversion );
			$this->setVersion( str_replace( 'rv:', '', $aversion[0] ) );
			$this->setBrowser( $this->BROWSER_MOZILLA );
			return true;
		} elseif ( stripos( $this->agent, 'mozilla' ) !== false && preg_match( '/rv:[0-9]\.[0-9]/i', $this->agent ) && stripos( $this->agent, 'netscape' ) === false ) {
			$aversion = explode( '', stristr( $this->agent, 'rv:' ) );
			$this->setVersion( str_replace( 'rv:', '', $aversion[0] ) );
			$this->setBrowser( $this->BROWSER_MOZILLA );
			return true;
		} elseif ( stripos( $this->agent, 'mozilla' ) !== false && preg_match( '/mozilla\/([^ ]*)/i', $this->agent, $matches ) && stripos( $this->agent, 'netscape' ) === false ) {
			$this->setVersion( $matches[1] );
			$this->setBrowser( $this->BROWSER_MOZILLA );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Lynx or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Lynx otherwise false
		 */
	public function checkBrowserLynx() {
		if ( stripos( $this->agent, 'lynx' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'Lynx' ) );
			$aversion = explode( ' ', ( isset( $aresult[1] ) ? $aresult[1] : '' ) );
			$this->setVersion( $aversion[0] );
			$this->setBrowser( $this->BROWSER_LYNX );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Amaya or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Amaya otherwise false
		 */
	public function checkBrowserAmaya() {
		if ( stripos( $this->agent, 'amaya' ) !== false ) {
			$aresult  = explode( '/', stristr( $this->agent, 'Amaya' ) );
			$aversion = explode( ' ', $aresult[1] );
			$this->setVersion( $aversion[0] );
			$this->setBrowser( $this->BROWSER_AMAYA );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Safari or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Safari otherwise false
		 */
	public function checkBrowserSafari() {
		if ( stripos( $this->agent, 'Safari' ) !== false && stripos( $this->agent, 'iPhone' ) === false && stripos( $this->agent, 'iPod' ) === false ) {
			$aresult = explode( '/', stristr( $this->agent, 'Version' ) );
			if ( isset( $aresult[1] ) ) {
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$this->setVersion( $this->VERSION_UNKNOWN );
			}
			$this->setBrowser( $this->BROWSER_SAFARI );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is iPhone or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is iPhone otherwise false
		 */
	public function checkBrowseriPhone() {
		if ( stripos( $this->agent, 'iPhone' ) !== false ) {
			$aresult = explode( '/', stristr( $this->agent, 'Version' ) );
			if ( isset( $aresult[1] ) ) {
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$this->setVersion( $this->VERSION_UNKNOWN );
			}
			$this->setMobile( true );
			$this->setBrowser( $this->BROWSER_IPHONE );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is iPod or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is iPod otherwise false
		 */
	public function checkBrowseriPad() {
		if ( stripos( $this->agent, 'iPad' ) !== false ) {
			$aresult = explode( '/', stristr( $this->agent, 'Version' ) );
			if ( isset( $aresult[1] ) ) {
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$this->setVersion( $this->VERSION_UNKNOWN );
			}
			$this->setMobile( true );
			$this->setBrowser( $this->BROWSER_IPAD );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is iPod or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is iPod otherwise false
		 */
	public function checkBrowseriPod() {
		if ( stripos( $this->agent, 'iPod' ) !== false ) {
			$aresult = explode( '/', stristr( $this->agent, 'Version' ) );
			if ( isset( $aresult[1] ) ) {
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$this->setVersion( $this->VERSION_UNKNOWN );
			}
			$this->setMobile( true );
			$this->setBrowser( $this->BROWSER_IPOD );
			return true;
		}
		return false;
	}

		/**
		 * Determine if the browser is Android or not (last updated 1.7)
		 *
		 * @return boolean True if the browser is Android otherwise false
		 */
	public function checkBrowserAndroid() {
		if ( stripos( $this->agent, 'Android' ) !== false ) {
			$aresult = explode( ' ', stristr( $this->agent, 'Android' ) );
			if ( isset( $aresult[1] ) ) {
				$aversion = explode( ' ', $aresult[1] );
				$this->setVersion( $aversion[0] );
			} else {
				$this->setVersion( $this->VERSION_UNKNOWN );
			}
			$this->setMobile( true );
			$this->setBrowser( $this->BROWSER_ANDROID );
			return true;
		}
		return false;
	}

		/**
		 * Determine the user's platform (last updated 1.7)
		 */
	public function checkPlatform() {
		if ( stripos( $this->agent, 'windows' ) !== false ) {
			$this->platform = $this->PLATFORM_WINDOWS;
		} elseif ( stripos( $this->agent, 'iPad' ) !== false ) {
			$this->platform = $this->PLATFORM_IPAD;
		} elseif ( stripos( $this->agent, 'iPod' ) !== false ) {
			$this->platform = $this->PLATFORM_IPOD;
		} elseif ( stripos( $this->agent, 'iPhone' ) !== false ) {
			$this->platform = $this->PLATFORM_IPHONE;
		} elseif ( stripos( $this->agent, 'mac' ) !== false ) {
			$this->platform = $this->PLATFORM_APPLE;
		} elseif ( stripos( $this->agent, 'android' ) !== false ) {
			$this->platform = $this->PLATFORM_ANDROID;
		} elseif ( stripos( $this->agent, 'linux' ) !== false ) {
			$this->platform = $this->PLATFORM_LINUX;
		} elseif ( stripos( $this->agent, 'Nokia' ) !== false ) {
			$this->platform = $this->PLATFORM_NOKIA;
		} elseif ( stripos( $this->agent, 'BlackBerry' ) !== false ) {
			$this->platform = $this->PLATFORM_BLACKBERRY;
		} elseif ( stripos( $this->agent, 'FreeBSD' ) !== false ) {
			$this->platform = $this->PLATFORM_FREEBSD;
		} elseif ( stripos( $this->agent, 'OpenBSD' ) !== false ) {
			$this->platform = $this->PLATFORM_OPENBSD;
		} elseif ( stripos( $this->agent, 'NetBSD' ) !== false ) {
			$this->platform = $this->PLATFORM_NETBSD;
		} elseif ( stripos( $this->agent, 'OpenSolaris' ) !== false ) {
			$this->platform = $this->PLATFORM_OPENSOLARIS;
		} elseif ( stripos( $this->agent, 'SunOS' ) !== false ) {
			$this->platform = $this->PLATFORM_SUNOS;
		} elseif ( stripos( $this->agent, 'OS\/2' ) !== false ) {
			$this->platform = $this->PLATFORM_OS2;
		} elseif ( stripos( $this->agent, 'BeOS' ) !== false ) {
			$this->platform = $this->PLATFORM_BEOS;
		} elseif ( stripos( $this->agent, 'win' ) !== false ) {
			$this->platform = $this->PLATFORM_WINDOWS;
		}

	}
}


