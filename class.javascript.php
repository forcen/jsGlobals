<?php

/**

	class.javascript.php
	
	Faustino Forcén
	www.forcen.com
	
	
	ff	291210	First public release
*/

require_once('class.opciones.php');

/**
	jsGlobalsClass
	
	An utility class to send vars from PHP to Javascript.
*/
class jsGlobalsClass extends opcionesClass{
/**
  * @return a js string with all of the option/vars in the object
  */
	public function __toString() {
 		$strResultado = '';
		if(is_array($this->arrDatos) && count($this->arrDatos)) {
			if($this->boolOrder) {
				ksort($this->arrDatos);	
			}
			foreach($this->arrDatos as $strName => $varValue) {
				if(is_array($varValue)) {
					$varValue = $this->toJS($varValue);
				}
				if(!empty($strResultado)) {
					$strResultado .= ",\n    ";					
				}
				$strResultado .= $strName . ' = ' . $varValue;
			}
			$strResultado = 'var ' . $strResultado . ";\n";
		}
		return $strResultado;
	}
	
/**
  * an utility method to transform a PHP array into a JS one
  *
  * @param an array
  * @return the same array in Javascript "format"
  */
	protected function toJS($array) {
		$arrDest = array();
		$total = count($array) - 1;
		$i = 0;
		foreach($array as $strKey => $varValue){
			if(is_array($varValue)) {
				$arrDest[$i] = '"' . $strKey . '":' . $this->toJS($varValue);
			} else {
				$arrDest[$i] = '"' . $strKey . '":' . preg_replace("/(\r\n|\n|\r)/", "", nl2br('"' . addslashes($varValue) . '"'));
			}
			if($i == 0) {
				$arrDest[$i] = '{' . $arrDest[$i];
			}
			if($i == $total) {
				$arrDest[$i] .= '}';
			}
			$i++;
		}
		return implode(',', $arrDest);
	}

}


/**
	jsLoaderClass
	
	An utility class to group a number of JS files into one, adding the globals required at the beginning
	of the resulting gile.
	
	One of the (multiple) tips for faster web pages is to have as little requests as possible. Using this
	class you can join several files into one just like this.
	
	<?php
		$objJSLoad = new jsLoaderClass();
		$objJSLoad->addFile($GLOBALS['BASE_PATH'] . '_dom/code.comms.js');
		$objJSLoad->addFile($GLOBALS['MODULE_PATH'] . '_dom/code.js');
		$objJSLoad->save($GLOBALS['MODULE_PATH'] . '_dom/cliente.js');
	?>
	<script type="text/javascript" src="./_dom/cliente.js"></script>

	NOTE that you need to have write access to the destination folder for this to work.

  */
  
class jsLoaderClass extends jsGlobalsClass{
	private $strOutput;
	private $arrFiles;

	/**
	  * This method sets the name of the generated file
	  *
	  * @param a relative or full path
	  */
	public function setOutput($strFilename) {
		$this->strOutput = $strFilename;
	}

	/**
	  * You must call this method for each file to include
	  *
	  * @param a relative or full path
	  * @return false it the file to add doesn't exists.
	  */
	public function addFile($strFilename) {
		$boolSuccess = false;
		if(!is_array($this->arrFiles)) {
			$this->arrFiles = array();
		}
		if(!in_array($strFilename, $this->arrFiles) && file_exists($strFilename)) {
			$this->arrFiles[] = $strFilename;
			$boolSuccess = true;
		}
		return $boolSuccess;
	}

	/**
	  * Saves all files, plus any defined globals, into the output file
	  *
	  * @return false if the output filename has not been defined
	  */
	public function save($strOutput = '') {
		$this->strOutput = $strOutput != '' ? $strOutput : $this->strOutput;
		if(!empty($this->strOutput)) {
			ob_start();
	
			// if we have defined any globals they'll be saved at the top of the file
			echo $this . "\n\n";
			
			if(is_array($this->arrFiles)) {
				foreach($this->arrFiles as $strOriginFilename) {			
					echo "/* " . $strOriginFilename . " */\n";
					
					include($strOriginFilename);
				
					echo "\n\n/* end of " . $strOriginFilename . "*/\n\n";			
				}
			}
			
			$this->saveFile($this->strOutput, ob_get_contents());
			ob_end_clean();
			return true;
		} else {
			return false;
		}
	}

	/**
	  * an utility function to save a file
	  * NOTE that you need to have write access to the destination folder for this to work.
	  *
	  * @param a relative or full path
	  * @param the content of the file
	  */
	private function saveFile($strFilename, $strContent) {
		//exec('touch ' . $strFilename);
		$fileOutput = fopen($strFilename, "w");
		if($fileOutput) {
			fwrite($fileOutput, $strContent);
			fclose($fileOutput);
			chmod($strFilename, 0666);
		}
	}
}
