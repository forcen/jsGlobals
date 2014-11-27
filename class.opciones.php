<?php
/**

	class.opciones.php
	
	Faustino Forcén
	www.forcen.com
		
	ff	291210	first public release.
*/


/**
	opcionesClass
	
	An utility class to handle configuration data relying on the magic methods of PHP.
	
	
	Example:
	
	$strConfig = {
		"list":{"src":"sql","data":"SELECT dom_id AS ID, dom_label AS LABEL FROM aux_domains ORDER BY LABEL"},
		"interface":{"firstselect":"none"},
		"events":{"onchange":"doChecking"}
	};
	
	
	$objConfig = new opcionesClass($strConfig);
	
	if(isset($objConfig.list)) {
		switch($objConfig.list['src'] && is_array($objConfig.list)) {
			case 'sql':
					// check that $objConfig.list['data'] is a query and execute it
				break;
			case 'values':
					// extract the value list from $objConfig.list['data']
				break;
			default:
				break;
		}
	}
	
*/

class opcionesClass {
	protected $arrDatos;
	protected $boolOrder;

	/**
	  * @param $strDatos is an optional json_encoded array
	  */
	public function __construct($strDatos = NULL) {
		if(!empty($strDatos)) {
			$this->arrDatos = (array) json_decode($strDatos);
		} else {
			$this->arrDatos = array();
		}
	}

	/**
	  * @param $strName is the name of the option/var to set
	  * @param $varValue can be an string or an array
	  */
	public function __set($strName, $varValue) {
 		if(isset($this->arrDatos[$strName]) && is_array($this->arrDatos[$strName])) {
			$this->arrDatos[$strName][] = $varValue;
		} else {
			$this->arrDatos[$strName] = $varValue;
		}	
 	}
	
	/**
	  * @param $strName is the name of the option/var to read
	  * @return the value of the option/var or NULL is the option/var is not set
	  */	
	public function __get($strName) {
  		if(isset($this->arrDatos[$strName])) {
			return $this->arrDatos[$strName];	
  		} else {
			return NULL;
		}
 	}

	/**
	  * @param the name of the option/var to check
	  * @return true if the option/var is defined in the object, false otherwise
	  */
	public function __isset($strName) {
		return array_key_exists($strName, $this->arrDatos); 
	}
	
	/**
	  * @param $strName is the name of the option/var to unset
	  */
	public function  __unset($strName) {
		if(isset($this->arrDatos[$strName])) {
			unset($this->arrDatos[$strName]);  
		}
	}
	
	/**
	  * this method will be called when you echo/print the object
	  *
	  * @return a json_encoded string with all of the option/vars in the object
	  */
	public function __toString() {
 		if(is_array($this->arrDatos)) {
			if($this->boolOrder) {
				ksort($this->arrDatos);	
			}
			$strResultado = json_encode($this->arrDatos, JSON_FORCE_OBJECT);
 		} else {
			$strResultado = '';
		}
		return $strResultado;
	}
	
	/**
	  * an utility to add double quotes to the content of a variable.
	  * it serves no other purpose than to help to write cleaner code.
	  *
	  * @param an string to enclose in quotes.
	 */
	public function text($strName) {
		return '"' . $strName . '"';	
	}
	
	/**
	  * a setter to force the ordering of arrDatos array before exporting
	  * 
	  * @param an optional boolean. true by default.
	  */
	public function order($boolOrder = true) {
		$this->boolOrder = $boolOrder;
	}
}
