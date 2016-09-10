<?php

/**
 * Checks whether the client browser is supported.
 * If not supported the bar with notice will be shown. 
 */


$config = array(
	'debugMode' => false,
	'helpFilePath' => $_SERVER['DOCUMENT_ROOT'] . '/Extensions/er/Forms/er_terminal/er_help/er_main/er_help.frm',
	'jsFileWebPath' => './external/oldies/oldies.js'	
);



class FileException extends Exception {};
class IOError extends FileException {}; // Sub-class of FileException
class InternalException extends Exception {};


/**
 * Extracts browser version and name using USER_AGENT variable
 * @author ksb
 *
 */
class Browser 
{	
	private $_agent = '';
	private $_browser_name = '';
	private $_version = '';	
	private $_is_mobile = false;
	private $_is_tablet = false;
	const BROWSER_UNKNOWN = 'unknown';
	const VERSION_UNKNOWN = 'unknown';
	
	const BROWSER_OPERA = 'Opera';
	const BROWSER_OPERA_MINI = 'Opera Mini'; 
	const BROWSER_IE = 'Internet Explorer'; 
	const BROWSER_FIREFOX = 'Firefox';
	const BROWSER_POCKET_IE = 'Pocket Internet Explorer';
	const BROWSER_MSN = 'MSN Browser';
	const BROWSER_CHROME = 'Chrome';
	
	
	/**
	 * Class constructor
	 * @param string $userAgentStr represents user agent
	 * @return Browser
	 */
	public function __construct($userAgentStr = ''){
		$this->reset();
		if (!empty($userAgentStr)){
			$this->setUserAgent($userAgentStr);
		} else {
			$this->determine();
		}
		return $this;
	}
	
	/**
	 * Reset all properties
	 * @return Browser
	 */
	public function reset(){
		$this->_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->_browser_name = self::BROWSER_UNKNOWN;
		$this->_version = self::VERSION_UNKNOWN;
		$this->_is_mobile = false;
		$this->_is_tablet = false;
		return $this;
	}
	
	/**
	 * Sets user agent string
	 * @param string $agent_string
	 * @throws InvalidArgumentException
	 * @return Browser
	 */
	public function setUserAgent($agent_string){
		if (is_string($agent_string)){
			$this->reset();
			$this->_agent = $agent_string;			
			$this->determine();
		} else {
			throw new InvalidArgumentException('$agent_string should be a string type');
		}
		return $this;
	}
	
	/**
	 * Get agent string
	 * @return string
	 */
	public function getUserAgent(){
		return $this->_agent;
	}
	
	/**
	 * Sets browser name
	 * @param string $browser
	 * @throws InvalidArgumentException
	 * @return Browser
	 */
	public function setBrowser ($browser){
		if (is_string($browser)){
			$this->_browser_name = trim($browser);
		} else {
			throw new InvalidArgumentException('$browser should be a string type');
		}
		return $this;
	}
	
	/**
	 * Sets browser version
	 * @param string $version
	 * @throws InvalidArgumentException
	 * @return Browser
	 */
	public function setVersion ($version){
		if (is_string($version)){
			$this->_version = trim($version);
		} else {
			throw new InvalidArgumentException('$version should be a string type');
		}
		return $this;
	}
	
	/**
	 * Get browser name
	 * @return string browser name
	 */
	public function getBrowser() {
		return $this->_browser_name;
	}
	
	/**
	 * Get browser version string
	 * @return string browser version
	 */
	public function getVersion(){
		return $this->_version;
	}
	
	/**
	 * Extracts major version of the browser
	 * @return string
	 */
	public function getMajorVersion(){
		$version = $this->getVersion();
	    $dot_pos = strpos($version, '.');
	    if ($dot_pos !== FALSE) {
	    	$version = substr($version, 0, $dot_pos);
	    }
	    return $version;
	}
	
	/**
	 * Returns string with a summary of the details of the browser
	 * @return string string with a summary of the browser
	 */
	public function __toString()
	{
		return "Browser Name: {$this->getBrowser()}\n " . 
		"Browser Version: {$this->getVersion()} \n " .
		"Browser User Agent String: {$this->getUserAgent()} \n";
	}
	
	
	/**
	 * Set the Browser to be mobile
	 * @param boolean $value is the browser a mobile browser or not
	 * @return Browser
	 */
	protected function setMobile($value = true)
	{
		$this->_is_mobile = $value;
		return $this;
	}
	
	
	/**
	 * Set the Browser to be tablet
	 * @param string $value
	 * @return Browser
	 */
	protected function setTablet($value = true)
	{
		$this->_is_tablet = $value;
		return $this;
	}
	
	/**
	 * Determine if the browser is Internet Explorer or not
	 * @return boolean True if the browser is Internet Explorer otherwise false
	 */
	protected function checkBrowserInternetExplorer()
	{
		//  Test for IE11
		if( stripos($this->_agent,'Trident/7.0; rv:11.0') !== false ) {
			$this->setBrowser(self::BROWSER_IE);
			$this->setVersion('11.0');
			return true;
		}
		// Test for v1 - v1.5 IE
		else if (stripos($this->_agent, 'microsoft internet explorer') !== false) {
			$this->setBrowser(self::BROWSER_IE);
			$this->setVersion('1.0');
			$aresult = stristr($this->_agent, '/');
			if (preg_match('/308|425|426|474|0b1/i', $aresult)) {
				$this->setVersion('1.5');
			}
			return true;
		} // Test for versions > 1.5
		else if (stripos($this->_agent, 'msie') !== false && stripos($this->_agent, 'opera') === false) {
			// See if the browser is the odd MSN Explorer
			if (stripos($this->_agent, 'msnb') !== false) {
				$aresult = explode(' ', stristr(str_replace(';', '; ', $this->_agent), 'MSN'));
				if (isset($aresult[1])) {
					$this->setBrowser(self::BROWSER_MSN);
					$this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
					return true;
				}
			}
			$aresult = explode(' ', stristr(str_replace(';', '; ', $this->_agent), 'msie'));
			if (isset($aresult[1])) {
				$this->setBrowser(self::BROWSER_IE);
				$this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
				if(stripos($this->_agent, 'IEMobile') !== false) {
					$this->setBrowser(self::BROWSER_POCKET_IE);
					$this->setMobile(true);
				}
				return true;
			}
		} // Test for versions > IE 10
		else if(stripos($this->_agent, 'trident') !== false) {
			$this->setBrowser(self::BROWSER_IE);
			$result = explode('rv:', $this->_agent);
			if (isset($result[1])) {
				$this->setVersion(preg_replace('/[^0-9.]+/', '', $result[1]));
				$this->_agent = str_replace(array("Mozilla", "Gecko"), "MSIE", $this->_agent);
			}
		} // Test for Pocket IE
		else if (stripos($this->_agent, 'mspie') !== false || stripos($this->_agent, 'pocket') !== false) {
			$aresult = explode(' ', stristr($this->_agent, 'mspie'));
			if (isset($aresult[1])) {
				$this->setBrowser(self::BROWSER_POCKET_IE);
				$this->setMobile(true);
				if (stripos($this->_agent, 'mspie') !== false) {
					$this->setVersion($aresult[1]);
				} else {
					$aversion = explode('/', $this->_agent);
					if (isset($aversion[1])) {
						$this->setVersion($aversion[1]);
					}
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Determine if the browser is Opera or not (last updated 1.7)
	 * @return boolean True if the browser is Opera otherwise false
	 */
	protected function checkBrowserOpera()
	{
		if (stripos($this->_agent, 'opera mini') !== false) {
			$resultant = stristr($this->_agent, 'opera mini');
			if (preg_match('/\//', $resultant)) {
				$aresult = explode('/', $resultant);
				if (isset($aresult[1])) {
					$aversion = explode(' ', $aresult[1]);
					$this->setVersion($aversion[0]);
				}
			} else {
				$aversion = explode(' ', stristr($resultant, 'opera mini'));
				if (isset($aversion[1])) {
					$this->setVersion($aversion[1]);
				}
			}
			$this->_browser_name = self::BROWSER_OPERA_MINI;
			$this->setMobile(true);
			return true;
		} else if (stripos($this->_agent, 'opera') !== false) {
			$resultant = stristr($this->_agent, 'opera');
			if (preg_match('/Version\/(1*.*)$/', $resultant, $matches)) {
				$this->setVersion($matches[1]);
			} else if (preg_match('/\//', $resultant)) {
				$aresult = explode('/', str_replace("(", " ", $resultant));
				if (isset($aresult[1])) {
					$aversion = explode(' ', $aresult[1]);
					$this->setVersion($aversion[0]);
				}
			} else {
				$aversion = explode(' ', stristr($resultant, 'opera'));
				$this->setVersion(isset($aversion[1]) ? $aversion[1] : "");
			}
			if (stripos($this->_agent, 'Opera Mobi') !== false) {
				$this->setMobile(true);
			}
			$this->_browser_name = self::BROWSER_OPERA;
			return true;
		} else if (stripos($this->_agent, 'OPR') !== false) {
			$resultant = stristr($this->_agent, 'OPR');
			if (preg_match('/\//', $resultant)) {
				$aresult = explode('/', str_replace("(", " ", $resultant));
				if (isset($aresult[1])) {
					$aversion = explode(' ', $aresult[1]);
					$this->setVersion($aversion[0]);
				}
			}
			if (stripos($this->_agent, 'Mobile') !== false) {
				$this->setMobile(true);
			}
			$this->_browser_name = self::BROWSER_OPERA;
			return true;
		}
		return false;
	}
	/**
	 * Determine if the browser is Chrome or not (last updated 1.7)
	 * @return boolean True if the browser is Chrome otherwise false
	 */
	protected function checkBrowserChrome()
	{
		if (stripos($this->_agent, 'Chrome') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'Chrome'));
			if (isset($aresult[1])) {
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
				$this->setBrowser(self::BROWSER_CHROME);
				//Chrome on Android
				if (stripos($this->_agent, 'Android') !== false) {
					if (stripos($this->_agent, 'Mobile') !== false) {
						$this->setMobile(true);
					} else {
						$this->setTablet(true);
					}
				}
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Determine if the browser is Firefox or not 
	 * @return boolean True if the browser is Firefox otherwise false
	 */
	protected function checkBrowserFirefox()
	{
		if (stripos($this->_agent, 'safari') === false) {
			if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $this->_agent, $matches)) {
				$this->setVersion($matches[1]);
				$this->setBrowser(self::BROWSER_FIREFOX);
				//Firefox on Android
				if (stripos($this->_agent, 'Android') !== false) {
					if (stripos($this->_agent, 'Mobile') !== false) {
						$this->setMobile(true);
					} else {
						$this->setTablet(true);
					}
				}
				return true;
			} else if (preg_match("/Firefox$/i", $this->_agent, $matches)) {
				$this->setVersion("");
				$this->setBrowser(self::BROWSER_FIREFOX);
				return true;
			}
		}
		return false;
	}
	
	

	/**
	 * Protected routine to calculate and determine what the browser is in use
	 * @return Browser
	 */
	protected function determine(){
		$this->checkBrowsers();
		return $this;
	}
	
	/**
	 * Protected routine to determine the browser type
	 * @return boolean True if the browser was detected otherwise false
	 */
	protected function checkBrowsers()
	{
		return (		
				// Notice! The order of calls is important!
				$this->checkBrowserInternetExplorer() ||
				$this->checkBrowserOpera() ||				
				$this->checkBrowserFirefox() ||
				$this->checkBrowserChrome()
				);
	}
	
}

/**
 * Detector of unsupprted browsers 
 * @author ksb
 *
 */
abstract class UnsupportedBrowserDetector {
	/**
	 * Incapsulates the logic of detection 
	 * @param string $browserName
	 * @param integer $majorVersion
	 * @throws InvalidArgumentException
	 * @return boolean true if unsupported | false if supported
	 */
	public static function isUnsupported($browserName, $majorVersion){
		if (!is_string($browserName)){
			throw new InvalidArgumentException('$browserName argument is not a string type');
		}		
	    if (!is_integer($majorVersion)){
	   	 	throw new InvalidArgumentException('$majorVersion argument is not an integer type');
	    }
	    $isUnsupported = true;
	    
	    switch($browserName) {
	    	case 'Internet Explorer':
	    		if ($majorVersion >= 10) {
	    			$isUnsupported = false;
	    		}
	    		break;
	    	case 'Firefox':
	    		if ($majorVersion >= 30) {
	    			$isUnsupported = false;
	    		}
	    		break;
	    	case 'Chrome':
	    		if ($majorVersion >= 35) {
	    			$isUnsupported = false;
	    		}
	    		break;
	    	case 'Opera':
	    		if ($majorVersion >= 20) {
	    			$isUnsupported = false;
	    		}
	    		break;
	    	default:
	    		$isUnsupported = true;
	    		break;
	    }
	    
	    return $isUnsupported;
	}
	
}

/**
 * Represents of frm help file
 * @author ksb
 *
 */
class HelpFile {
	
	const DEF_START_LABEL = '<!-- <help> -->';
	const DEF_END_LABEL = '<!-- </help> -->';
	
	private $_filePath = '';
	private $_startLabels = array();
	private $_endLabels = array();
	private $_extractedText = '';
	
	
	/**
	 * Reses all properties to theirs default
	 * @return HelpFile
	 */
	public function reset(){
		$this->_filePath = '';
		$this->_startLabels = array(self::DEF_START_LABEL);
		$this->_endLabels = array(self::DEF_END_LABEL);
		$this->_extractedText = '';
		return $this;
	}

	/**
	 * Class constructor
	 * @param string $filePath path to the help file
	 * @param array $startLabels
	 * @param array $endLabels
	 * @return HelpFile
	 */
	public function __construct($filePath, array $startLabels = array(), array $endLabels = array()){
		$this->reset();
		$this->setFilePath($filePath);
		return $this;
	}
	
	/**
	 * 
	 * @param unknown $filePath
	 * @throws InvalidArgumentException
	 * @return HelpFile
	 */
	public function setFilePath ($filePath){
		if (!is_string($filePath) || empty($filePath)){
			throw new InvalidArgumentException('$filePath is not a string or empty');
		}
		$this->reset();
	  	$this->_filePath = $filePath;
	  	$this->extractHelpText();
	  	return $this;
	}
	
	/**
	 * Sets start possible start labels.
	 * @param string[]|array $labels
	 * @throws InvalidArgumentException
	 * @return HelpFile
	 */
	public function setStartLabels(array $labels){
		if (!count ($labels)){
			throw new InvalidArgumentException('$labels array is empty');
		}
		$this->_startLabels = $labels;
		return $this;
	}
	
	/**
	 * Sets end possible start labels.
	 * @param string[]|array $labels
	 * @throws InvalidArgumentException
	 * @return HelpFile
	 */
	public function setEndLabels(array $labels){
		if (!count ($labels)){
			throw new InvalidArgumentException('$labels array is empty');
		}
		$this->_endLabels = $labels;
		return $this;
	}
	
	/**
	 * Start labels
	 * @return string[]|array[]
	 */
	public function getStartLabels(){
		return $this->_startLabels;
	}
	
	/**
	 * End labels
	 * @return string[]|array
	 */
	public function getEndLabels(){
		return $this->_endLabels;
	}
	
	/**
	 * Get extracted text
	 * @return string extracted text
	 */
	public function getText(){
		return $this->_extractedText;
	}
	
	public function __toString() {
		return $this->getText();
	}
	
	/**
	 * Read the file
	 * @throws IOError
	 * @return string
	 */
	protected function readFile(){
		$res = file_get_contents($this->_filePath);
		if ($res === false){
			throw new IOError("Error of reading the file: {$this->_filePath}");
		}
		return $res; 
	}

	
	/**
	 * Extracts text from the file
	 * @throws FileException
	 * @throws InvalidArgumentException
	 * @throws InternalException
	 * @return string
	 */
	protected function extractHelpText(){
		$text = $this->readFile();
		if (!strlen($text)){
			throw new FileException('The file is empty! ' . $this->_filePath);
		}
		$startPos = 0;		
		foreach ($this->_startLabels as $label) {
			if (!is_string($label)){
				throw new InvalidArgumentException('$label is not a string type');
			}
			$startPos = strpos($text, $label);
			if ($startPos !== false){
				$startPos += strlen($label);
				break;
			}
		}
		$endPos = 0;		
		foreach ($this->_endLabels as $label) {
			if (!is_string($label)){
				throw new InvalidArgumentException('$label is not a string type');
			}
			$endPos = strrpos($text, $label);
			if ($endPos !== false){
				break;
			}
		}		
		$helpTextlen = $endPos - $startPos;  
		if ($helpTextlen < 0){
			throw new InternalException("helpTextlen < 0: {$helpTextlen}");
		}
		$res = substr($text, $startPos, $helpTextlen);
		if (!$res){
			throw new InternalException('Cannot extract the text');
		}				
		$this->_extractedText = trim(str_replace(array("\r\n", "\r", "\n"), ' ', $res));
		return $this->_extractedText;
	}	
	
}


abstract class View {}

/**
 * IeBarView show IE styled panel
 * @author ksb
 *
 */
class IeBarView extends View {
	
	private $_jsFilePath = '';
	
	/**
	 * Reset properties
	 * @return IeBarView
	 */
	public function reset(){
		$this->_jsFilePath = '';
		return $this;
	}
	
	/**
	 * Set web path to the js file
	 * @param string $jsFile
	 * @return IeBarView
	 */
	public function setJsFilePath($jsFile){
		if (!is_string($jsFile)){
			throw new InvalidArgumentException('jsFile is not a string');
		}
		$this->_jsFilePath = $jsFile;
		return $this;
	}
	
	/**
	 * IeBarView constructor
	 * @return IeBarView
	 */
	public function __construct(){
		$this->reset();
		return $this;
	}
	
	/**
	 * Echoes result to client
	 * @param string $text
	 */
	public function render($text){
		echo $this->renderToString($text);
	}
	
	/**
	 * Renders result to string
	 * @param string $text
	 * @return string
	 */
	public function renderToString($text){
		$res = '<script type="text/javascript">' . "\r\n";
		$res .= 'var infoText = ' . $this->textToJs($text) . ';' . "\r\n";
		$res .= '</script>' . "\r\n";
		$res .= '<script type="text/javascript" src="' . $this->_jsFilePath .'"></script>';
		return $res;
	}
	
	/**
	 * Prepares text to send to js var
	 * @param string $text
	 * @return string
	 */
	protected function textToJs ($text){
		if (version_compare(phpversion(), '5.4', '<')) {
            $text = json_encode($text); 
		} else {
			$text = json_encode($text, JSON_UNESCAPED_UNICODE);
		}
		return $text; 
	}
	
}


/**
 * Application controller
 * @author ksb
 *
 */
class Application {
	
	private $_config = array();
	
	/**
	 * Initialise application. Setup exception handler
	 * @return Application
	 */
	protected function init(){
		error_reporting(E_ALL);
		$config = $this->getConfig();
		if ($config['debugMode']){
			ini_set('display_errors', 'on');
		} else {
			ini_set("display_errors", 0);
			ini_set("log_errors", 1);
		}
		@set_exception_handler(array($this, 'exception_handler'));
		return $this;
	}
	
	/**
	 * Get current config
	 * @return array config
	 */
	public function getConfig(){
		return $this->_config;
	}
	
	/**
	 * Class constructor
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->_config = $config;
	}

	/**
	 * Application exception handler
	 * @param Exception $exception
	 */
	public function exception_handler(Exception $exception) {
		if ($this->_config['debugMode']){
			print "Exception Caught: ". $exception->getMessage() ."\n";
		} 		
	}
	
	/**
	 * Run the application instance
	 * @return Application
	 */
	public function run(){
		$this->init();
		$browser = new Browser();
		$isUnsupported = UnsupportedBrowserDetector::isUnsupported($browser->getBrowser(), (int) $browser->getMajorVersion());
		$config = $this->getConfig();
		if ($config['debugMode']){
		 	$isUnsupported = true;
		}
		if ( $isUnsupported ){
			$helpFile = new HelpFile($config['helpFilePath']);
			$view = new IeBarView();
			$view->setJsFilePath($config['jsFileWebPath']);
			$view->render($helpFile->getText());
		}
		return $this;
	}
}

//======================================== client code ================================================================


// Only for corporate local network
if (isset($_SERVER['REMOTE_ADDR']) && (preg_match('/10\.10\.1[0-1]\.[0-9]{1,3}[0-9]{0,1}[0-9]{0,1}/', $_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] === '10.171.3.13')) {
	$app = new Application($config);
	$app->run();
	//throw new Exception('blahblah Exception');
}
