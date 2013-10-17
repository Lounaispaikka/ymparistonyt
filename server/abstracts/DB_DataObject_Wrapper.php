<?php
namespace Lougis;
/**
 * Extends the DataObject class with additional methods and functionality
 *
 * @authors 		Lounaispaikka: Pyry Liukas, Tapio Aali
 */
abstract class DB_DataObject_Wrapper extends \DB_DataObject {

    public function __construct($Id = null, $PkField = null) {
		if ( !empty($Id) ) {
			if ( !empty($PkField) ) {
				$this->get($PkField, $Id);
			} else {
				$this->get($Id);
			}
		}
		
	}
	
	/**
	 * Recognizes if the object is new or old and then inserts or updates.
	 */
	public function save() {
    	if ( $this->N == false ) {
			$KeyA = $this->keys();
			if ( empty($this->$KeyA[0]) ) {
				$nid = $this->getNextKey();
				$this->{$KeyA[0]} = $nid;
			}
			return $this->insert();
		} 
		return $this->update();
	}
	
	/**
	 * Checks if the object with the id exists in database.
	 *
	 * @param 	mixed 		$Id
	 * @param 	string 		$PkField	Name of the primary key field
	 * @return 	boolean
	 */
	public function exists( $Id, $PkField = null ) {
		
		$Search = clone($this);
		if ( empty($PkField) ) {
			$KeyA = $Search->keys();
			$PkField = $KeyA[0];
		}
		if ( !is_numeric($Id) ) $Id = "'".$Id."'"; 
		$Search->query("SELECT count(".$PkField.") AS keycount FROM ".$Search->tableName()." WHERE ".$PkField." = ".$Id);
		$Search->fetch();
		if ( $Search->keycount > 0 ) {
			$Search->free();
			return true;
		}
		$Search->free();
		return false;
		
	}
	
	public function toArray( $wrapper = null ) {
		
		$tableFields = $this->table();
		$toArray = ( !empty($wrapper) ) ? parent::toArray($wrapper) : parent::toArray();
		
		foreach($tableFields as $key => $dbotype) {
			switch(true) {
				case !empty($toArray[$key]) && ( $dbotype == DB_DATAOBJECT_INT || $dbotype == DB_DATAOBJECT_INT+DB_DATAOBJECT_NOTNULL ):
					$toArray[$key] = ( strpos($toArray[$key], '.') !== false ) ? doubleval($toArray[$key]) : intval($toArray[$key]);
				break;
				case !empty($toArray[$key]) && ( $dbotype == DB_DATAOBJECT_STR+DB_DATAOBJECT_BOOL || $dbotype == DB_DATAOBJECT_STR+DB_DATAOBJECT_BOOL+DB_DATAOBJECT_NOTNULL ):
					$toArray[$key] = ( $toArray[$key] == 't' ) ? true : false;
				break;
			}
		}
		return $toArray;
		
	}
	
    /**
     * Returns fields of the data object by category. If the category is not set it'll return the meta field of the object.
     * @param string $category
     * @return array
     */
    private function getItemsByCategory($category = null) {
        if(empty($category)) return $this->meta;
        $items = array();
        foreach($this->categories[$category] as $item) {
            $items[$item] = isset($this->meta[$item])? $this->meta[$item] : array();
        }
        return $items;
    }

    /**
     * Returns Ext items of the data object from the category given
     * @param string $category the wanted category
     * @param string $append a string to append to the ids of the fields
     * @return array
     */
	public function getExtItems($category = null, $append = '') {
        $set = $this->getItemsByCategory($category);
		$items = $this->parseExtItems($set, $append);
		return $items;
	}

	/**
     * Parses given set of items to a format that can be directly passed to Ext via JSON.
     * @param array $set - the set to be processed
     * @param string $append - a string to append to the ids of the items
     * @return array 
     */
    public function parseExtItems($set, $append = '') {
		$items = array();
		
		foreach($set as $key => $params) {
			
			$cls = substr($this->__table, strpos($this->__table, '.')+1);
			
			$item = array();

            if(isset( $this->$key )) $item['value'] = $this->$key;

			$item['id'] = $cls.'_'.$key.$append;
			$item['name'] = $key;
			$item['anchor'] = (isset($params['anchor']))? $params['anchor']: '97%';
			$item['fieldLabel'] = ( isset($params['label']) ) ? t($params['label']) : t(prettyName($key));
            if($item['fieldLabel']=='Hr') $item['fieldLabel'] = '';
            if( isset($params['emptyText'])) $item['emptyText'] = t( $params['emptyText'] );
            $item['qtip'] = t($params['qtip']);
            if( $params['type'] != 'checkbox' ) { $item['qtip'] = ( isset($params['qtip']) ) ? t($params['qtip']) : t($params['label']); }

            if(isset($params['length'])) $item['minLength'] = $params['length'];

			switch($params['type']) {
				case 'email':
					$item['vtype'] = 'email';
				break;
				case 'password':
					$item['vtype'] = 'password';
                    $item['inputType'] = 'password';
                    unset($item['value']);
				break;
                case 'image':
                    $item['xtype'] = 'fileuploadfield';
                    $item['buttonText'] = '...';
                    unset($item['value']);
                break;
                case 'hidden':
                    unset($item['fieldLabel']);
                    $item['xtype'] = 'hidden';
                break;
                case 'checkbox':
                    $item['xtype'] = 'checkbox';
                    $item['checked'] = ($params['defaultValue'] == true);
                    if(isset($item['value'])) $item['checked'] = ($item['value'] == "t"? true: false);

                    unset($item['value']);
                break;
                case 'combo':
                    $item['xtype'] = 'combo';
                    $item['editable'] = ( isset($params['editable']) ) ? $params['editable'] : false;
                    $item['autoSelect'] = ( isset($params['autoSelect']) ) ? $params['autoSelect'] : true;
                    $item['autoSelect'] = ( isset($params['autoSelect']) ) ? $params['autoSelect'] : true;
                    if(isset($params['triggerAction'])) $item['triggerAction'] = $params['triggerAction'];
                break;
                case 'url':
                    $item['vtype'] = 'url';
                break;
				case 'uniqueurl':
                    $item['vtype'] = 'uniqueurl';
                break;
                case 'page_id':
                    $item['xtype'] = 'hidden';
                break;
                case 'content_id':
                    $item['xtype'] = 'hidden';
                break;
                case 'textarea':
                    $item['xtype'] = 'textarea';
                    $item['height'] = 75;
                break;
				case 'datefield':
					$item['xtype'] = 'datefield';
					$item['format'] = 'Y-m-d';
					if ( $append != 'page' ) $item['value'] = date("Y-m-d");
				break;
                case 'string':
                    $item['xtype'] = 'textfield';
                break;
                case 'colorpicker':
                    $item['xtype'] = 'ext.ux.colorpicker';
                break;
                case 'box':
                    $item['xtype'] = 'box';
                break;
			}
			$params['required']? $item['allowBlank'] = false: $item['allowBlank'] = true;
            if (isset($params['hint'])) $item['emptyText'] = t($params['hint']);

    		$items[] = $item;
			
		}
		
		return $items;
	
	}
	
	
	/**
	 * Checks objects required attributed based on public $meta array
	 */
	public function checkValidity($values) {
        $firstPassword = null;
        foreach( $this->meta as $attribName => $params) {
			$attrib = $values[$attribName];
			switch( $params['type'] ) {
				case 'string':
					if ( isset($params['length']) && strlen( $attrib ) < $params['length'] ) {
                        throw new Exception( t("Attribute %1 length (%2) is too short", $attribName, strlen($attrib)));
                        }
				break;
				case 'email':
                    if (!$this->checkEmailAddressValidity($attrib)) {
                        throw new Exception(t("Email address is not valid"));
                    }
				break;
				case 'file':
					// checkFileValidity( param, param )
				break;
				case 'imagefile':
					// checkFileValidity( param, param )
					// is_image
					// 
				break;
                case 'password':
                    if(!isset($firstPassword)) $firstPassword = $attrib;
                    else if($firstPassword != $attrib && isset($params['length']) && strlen( $attrib ) < $params['length']) {
                        throw new Exception(t("Passwords do not match or the password is too short"));
                    }
                break;
                case 'url':
                    if (!$this->checkUrlValidity($attrib)) {
                        throw new Exception(t("The WWW address is not a valid URL!"));
                   } 
                break;
			}
		}
	}

    /**
     * An RFC822 compliant email address matcher
     * @param string $email
     * @return boolean 
     */
    public function checkEmailAddressValidity($email) {
        if(empty($email))
            return true;
        $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
        $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
        $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
                      '\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
        $quoted_pair = '\\x5c[\\x00-\\x7f]';
        $domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
        $quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
        $domain_ref = $atom;
        $sub_domain = "($domain_ref|$domain_literal)";
        $word = "($atom|$quoted_string)";
        $domain = "$sub_domain(\\x2e$sub_domain)*";
        $local_part = "$word(\\x2e$word)*";
        $addr_spec = "$local_part\\x40$domain";
        return preg_match("!^$addr_spec$!", $email) ? 1 : 0;
    }

    /**
     * URL validity checker. Accepts protocols http and https and urls without protocol.
     * @param string $url
     * @return boolean
     */
    public function checkUrlValidity( $url ) {
        if(empty($url)) return true;
        if(!(substr($url, 0, 7) == "http://" || substr($url, 0, 8) == "https://")) $url = "http://". $url;
        if(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) return true;
        return false;
    }
    /**
     * IP validity checker. Accepts ip addresses (XXX.XXX.XXX.XXX) and networks (XXX.XXX.XXX.XXX/XX)
     * @author Tapio Aali (tapio.aali@lounaispaikka.fi)
     * @param string $ip  the IP candidate to be validated
     * @return bool
     */
    public function checkIPValidity($ip) {
         $parts = explode("/", $ip);
         $ip = $parts[0];
         $long = ip2long($ip);
         if( ($long == -1) || ($long === FALSE) ) return false;
         else return true;
         //$regexp = "\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b";
         //$regexp = '/^((1?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(1?\d{1,2}|2[0-4]\d|25[0-5])$/';
         //return (bool)preg_match($regexp, $ip);
    }

    public function checkHostValidity($host) {
        $regexp = "^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])^";
        return preg_match($regexp, $host)? 1: 0;
    }

    public function checkFileValidity(  ) {
			
			// file_exists
			// upload ok
			// filename ok
			// filesize
			// filetype not dangerous (.sh, .exe etc)
	
	}
	
	/**
	 * Returns the next key of the given field field or primary key
	 *
	 * @param 	string 		$FieldName		Optional
	 * @return 	integer
	 */
	public function getNextKey( $FieldName = null, $WhereAdd = null ) {
		
		$KeyA = $this->keys();
		if ( empty($FieldName) ) $FieldName = $KeyA[0];
		$Maxname = 'max_'.$FieldName;
		$Search = clone($this);
		$Search->query("SELECT MAX({$FieldName}) AS {$Maxname} FROM {$this->__table}");
		if ( !empty($WhereAdd) ) $Search->whereAdd( $WhereAdd );
		$Search->fetch();
		$res = $Search->$Maxname;
		if ( empty($res) ) $res = 0;
		$nid = intval($res)+1;
		return $nid;
		
	}
	
	public function setNextKey( $FieldName = null, $WhereAdd = null ) {
		$KeyA = $this->keys();
		if ( empty($FieldName) ) $FieldName = $KeyA[0];
		$this->$FieldName = $this->getNextKey( $FieldName, $WhereAdd );
		return;
	}
	
	public function getNextOrderId( $WhereAdd = null ) {
		return $this->getNextKey('order_id', $WhereAdd);
	}
	
	public function setNextOrderId( $WhereAdd = null ) {
		return $this->setNextKey('order_id', $WhereAdd);
	}
	
	public function getNextSeqNum( $WhereAdd = null ) {
		return $this->getNextKey('seqnum', $WhereAdd);
	}
	
	public function setNextSeqNum( $WhereAdd = null ) {
		return $this->setNextKey('seqnum', $WhereAdd);
	}
	
	public function getLangText($DescClass, $PkField, $PkVal, $Field, $LgId = null) {
	    	
	    	global $Language;
	    	
	    	$KeyA = $this->keys();
	    	
	    	$Search = new $DescClass;
	    	$Search->lang_id = $LgId;
	    	if ( empty($LgId) ) $Search->lang_id = $Language->id;
	    	$Search->$PkField = $PkVal;
	    	$Search->find();
	    	$Search->fetch();
	    	if ( empty($Search->$Field) && $Language->id != DEFAULT_LANGUAGE ) {
	    		$Tr = $this->getObjectDescription( $DescClass, $PkField, $Language->id, $PkVal );
		    	return stripslashes($Tr->$Field);
	    	} else {
		    	return stripslashes($Search->$Field);
	    	}
	    	
	    }
    
	public function setLangText($Lang, $DescClass, $PkField, $PkVal, $Fields) {
    	
		if ( count($Fields) < 1 ) return false;
	    	$Desc = new $DescClass;
	    	$Desc->lang_id = $Lang;
	    	$Desc->$PkField = $PkVal;
	    	if ( $Desc->count() > 0 ) $Desc->fetch();
	    	foreach ($Fields as $FieldName => $Value) {
	    		$Desc->$FieldName = $Value;
	    	}
	    	print_r_html($Fields);
	    	$Desc->save();
	    	return true;
	    	
	}
	
	public function getArray() {
		
		return $this->toArray();
		
	}
	
    public function getObjectDescription( $DescTable, $DescPK, $Lid, $PkVal = null ) {
    	
    	if ( empty($PkVal) ) $PkVal = $this->id;
    	$Desc = new $DescTable;
    	$Desc->lang_id = $Lid;
    	$Desc->$DescPK = $PkVal;
    	if ( $Desc->count() > 0 ) {
    		$Desc->find();
    		$Desc->fetch();
    		return $Desc;
    	} else {
    		$Desc->lang_id = 'fi';
    		//if ( $Desc->count() == 0 ) throw new Exception(t('Alkuperäistä tekstiä ei ole olemassa. Teksikäännös mahdotonta! ').print_r_html($Desc, true));
    		if ( $Desc->count() == 0 ) {
    			$Desc->lang_id = 'en';
    			if ( $Desc->count() == 0 ) return new $DescTable;
    		}
    		$Desc->find();
    		$Desc->fetch();
    		$Darr = $Desc->toArray();
    		
    		$Trans = new $DescTable;
    		$Trans->$DescPK = $PkVal;
    		$Trans->lang_id = $Lid;
    		
    		$Skip = $Desc->keys();
    		foreach ($Darr as $key => $value) {
    			if ( !in_array($key, $Skip) && !empty($value) ) {
    				$Trans->$key = googleTranslate($value, $Lid, $Desc->lang_id);
    			}
    		}
    		$Trans->save();
    		return $Trans;
    		
    	}
    	
    	
    }

    /**
     * Wraps fields of the dataobject in MapScript compatible form using $setters and $booleanSetters variables.
     * @author Tapio Aali (tapio.aali@lounaispaikka.fi)
     * @return array
     */
    public function getMapScriptSetters() {
        $result = array();
        foreach($this->setters as $key => $value) {
            if(!empty($this->$key)) $result[$value] = $this->$key;
        }
        foreach($this->booleanSetters as $key => $value) {
            $result[$value] = ($this->$key == "t"? MS_TRUE: MS_FALSE);
        }
        return $result;
    }

}
?>