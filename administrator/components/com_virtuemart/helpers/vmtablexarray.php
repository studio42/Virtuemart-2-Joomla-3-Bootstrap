<?php
/**
 * Xref table abstract class to create tables specialised doing xref
 *
 * The pkey is the Where key in the load function,
 * the skey is the select key in the load function
 *
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

defined('_JEXEC') or die();

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

class VmTableXarray extends VmTable {

	/** @var int Primary key */

	protected $_autoOrdering = false;
	protected $_orderable = false;

	protected $_pvalue = '';

//    function setOrderable($key='ordering', $auto=true){
//    	$this->_orderingKey = $key;
//    	$this->_orderable = 1;
//    	$this->_autoOrdering = $auto;
//    	$this->$key = 0;
//    }

	function setSecondaryKey($key,$keyForm=0){
		$this->_skey 		= $key;
		$this->$key			= array();
		$this->_skeyForm	= empty($keyForm)? $key:$keyForm;

    }

	function setOrderableFormname($orderAbleFormName){
		$this->_okeyForm = $orderAbleFormName;
	}


	/**
	* swap the ordering of a record in the Xref tables
	* @param  $direction , 1/-1 The increment to reorder by
	*/
	function move($direction, $where='', $orderingkey=0) {

    	if(empty($this->_skey) ) {
    		vmError( 'No secondary keys defined in VmTableXarray '.$this->_tbl );
    		return false;
    	}
		$skeyId = JRequest::getInt($this->_skey, 0);
		// Initialize variables
		$db		= JFactory::getDBO();
		$cid	= JRequest::getVar( $this->_pkey , array(), 'post', 'array' );
		$order	= JRequest::getVar( 'order', array(), 'post', 'array' );

		$query = 'SELECT `id` FROM `' . $this->_tbl . '` WHERE $this->_pkey = '.(int)$cid[0].' AND `virtuemart_category_id` = '.(int)$skeyId ;
		$this->_db->setQuery( $query );
		$id = $this->_db->loadResult();
		$keys = array_keys($order);
		// TODO next 2 lines not used ????
		if ($direction >0) $idToSwap = $order[$keys[array_search($id, $keys)]+1];
		else $idToSwap =  $order[$keys[array_search($id, $keys)]-1];

		if (isset( $cid[0] )) {

			$query = 'UPDATE `'.$this->_tbl.'` '
			. ' SET `'.$this->_orderingKey.'` = `'.$this->_orderingKey.'` + '. $direction
			. ' WHERE `'.$this->_pkey.'` = ' . (int)$cid[0].
			' AND `'.$this->_skey.'`  = ' . (int)$skeyId
			;
			$this->_db->setQuery( $query );

			if (!$this->_db->query())
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError( 500, get_class( $this ).':: move '. $err );
			}
		}
	}
    /**
     * Records in this table are arrays. Therefore we need to overload the load() function.
     * TODO, this function is giving back the array, not the table, it is not working like the other table, so we should change that
     * for the 2.2. at least.
	 * @author Max Milbers
     * @param int $id
     */
    function load($oid=null,$overWriteLoadName=0,$andWhere=0,$tableJoins= array(),$joinKey = 0){

    	if(empty($this->_skey) ) {
    		vmError( 'No secondary keys defined in VmTableXarray '.$this->_tbl );
    		return false;
    	}

    	if(empty($this->_db)) $this->_db = JFactory::getDBO();

		if($this->_orderable){
			$orderby = 'ORDER BY `'.$this->_orderingKey.'`';
		} else {
			$orderby = '';
		}

		$q = 'SELECT `'.$this->_skey.'` FROM `'.$this->_tbl.'` WHERE `'.$this->_pkey.'` = "'.(int)$oid.'" '.$orderby;
		$this->_db->setQuery($q);

		$result = $this->_db->loadResultArray();
// 		vmdebug('my q ',$q,$result);
		$error = $this->_db->getErrorMsg();
		if(!empty($error)){
			vmError(get_class( $this ).':: load'.$error  );
			return false;
		} else {
			if(empty($result)) return array();
			if(!is_array($result)) $result = array($result);

			return $result;
		}

    }

    /**
     * This binds the data to this kind of table. You can set the used name of the form with $this->skeyForm;
     *
     * @author Max Milbers
     * @param array $data
     */
	public function bind($data, $ignore = array()){

		if(!empty($data[$this->_pkeyForm])){
			$this->_pvalue = $data[$this->_pkeyForm];
		}

		if(!empty($data[$this->_skeyForm])){
			$this->_svalue = $data[$this->_skeyForm];
		}

		if($this->_orderable){
			$orderingKey = $this->_orderingKey;
			if(!empty($data[$orderingKey])){
				$this->$orderingKey = $data[$this->_orderingKey];
			}
		}

		return true;

	}

    /**
     *
     * @author Max Milbers, George Kostopoulos
     * @see libraries/joomla/database/JTable#store($updateNulls)
     */
    public function store($updateNulls = false) {

    	$returnCode = true;
		$this->setLoggableFieldsForStore();
		$db = JFactory::getDBO();

        $pkey = $this->_pkey;
        $skey = $this->_skey;
        $tblkey = $this->_tbl_key;

        // We select all database rows based on our _pkey
        $q  = 'SELECT * FROM `'.$this->_tbl.'` WHERE `'.$this->_pkey.'` = "'. $this->_pvalue.'" ';
        $this->_db->setQuery($q);
        $objList = $this->_db->loadObjectList();

        // We convert the database object list that we got in a more friendly array
        $oldArray = null;
        if($objList) {
            foreach($objList as $obj){
                $oldArray[] = array($pkey=>$obj->$pkey, $skey=>$obj->$skey);
            }
        }

        // We make another database object list with the values that we want to insert into the database
        $newArray = array();
		if(!empty($this->_svalue)){
	            if(!is_array($this->_svalue)) $this->_svalue = array($this->_svalue);
	            foreach($this->_svalue as $value) $newArray[] = array($pkey=>$this->_pvalue, $skey=>$value);
		}

        // Inserts and Updates
        if(count($newArray)>0){
            $myOrdering = 1;

            foreach ($newArray as $newValue) {
                // We search in the existing (old) rows to find one of the new rows we want to insert
                $result = $this->array_msearch($oldArray, $newValue);

                // We start creating the row we will insert or update
                $obj = new stdClass;
                $obj->$pkey = $newValue[$pkey];
                $obj->$skey = $newValue[$skey];

                if($this->_autoOrdering){
                    $oKey = $this->_orderingKey;
                    $obj->$oKey = $myOrdering++;
                }

                // If the new row does not exist in the old rows, we will insert it
                if( $result === false ) {
                    $returnCode = $this->_db->insertObject($this->_tbl, $obj, $pkey);
                }
                else {
                    // If the new row exists in the old rows, we will update it
                    $obj->$tblkey = $objList[$result]->$tblkey;
                    $returnCode = $this->_db->updateObject($this->_tbl, $obj, $tblkey);
                }
            }
        }
        else {
            // There are zero new rows, so the user asked for all the rows to be deleted
            $q  = 'DELETE FROM `'.$this->_tbl.'` WHERE `' . $pkey.'` = "'. $this->_pvalue .'" ';
            $this->_db->setQuery($q);

            if(!$this->_db->query()){
                $returnCode = false;
                vmError(get_class( $this ).':: store '.$this->_db->getErrorMsg());
            }
        }


        // Deletions
        if(!empty($oldArray)) {
            for ($i = 0; $i < count($oldArray); $i++) {
                $result = $this->array_msearch($newArray, $oldArray[$i]);

                // If no new row exists in the old rows, we will delete the old rows
                if( $result === false ) {
                    // If the old row does not exist in the new rows, we will delete it
                    $q  = 'DELETE FROM `'.$this->_tbl.'` WHERE `' . $tblkey.'` = "'. $objList[$i]->$tblkey .'" ';
                    $this->_db->setQuery($q);
                    if(!$this->_db->Query()){
                        $returnCode = false;
                        vmError(get_class( $this ).':: store'.$this->_db->getErrorMsg());
                    }
                }
             }
        }

 	return $returnCode;

    }

    /**
     *
     * Searches in an array of arrays to find a specific array we want
     *
     * @author George Kostopoulos
     * @param source array of arrays that we will search
     * @param the target array we want to find
     */
    protected function array_msearch($parents, $searched) {
        if (empty($searched) || empty($parents)) {
            return false;
        }

        foreach ($parents as $key => $value) {
            $exists = true;
            foreach ($searched as $skey => $svalue) {
                $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
            }
            if($exists){ return $key; }
         }

        return false;
    }


    function deleteRelation(){
    	$q  = 'DELETE FROM `'.$this->_tbl.'` WHERE `'.$this->_pkey.'` = "'. $this->_pvalue.'" ';
    	$this->_db->setQuery($q);
    	if(!$this->_db->Query()){
    		vmError(get_class( $this ).':: store'.$this->_db->getErrorMsg(),'Couldnt delete relations');
    		return false;
    	}

    	return true;
    }

}