<?php

/*
  Copyright (c) 2009 milan medlik (milan.medlik@gmail.com)

  Permission is hereby granted, free of charge, to any person
  obtaining a copy of this software and associated documentation
  files (the "Software"), to deal in the Software without
  restriction, including without limitation the rights to use,
  copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the
  Software is furnished to do so, subject to the following
  conditions:

  The above copyright notice and this permission notice shall be
  included in all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
  OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
  HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
  WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
  OTHER DEALINGS IN THE SOFTWARE.
  */
  
require_once 'config.php';

// load function from configuration
if($MIUFW_CFG->DB_TYPE == 'mysql')
  require_once 'db_utils_mysql.php';
else if($MIUFW_CFG->DB_TYPE == 'pgsql')
  require_once 'db_utils_pgsql.php';
else
  echo "wrong database configuration";


  class MIUFW_DBUTILS extends DB_UTILS_BASE
  {
    private $fs_table = '';
    private $fo_result = null;
    
    public function __construct($table)
    {
      $this->fs_table = $table;
    }
  
    public function db_delete($where)
    {
		  $query = "delete from ". $this->fs_table ." where ".$where;

		  /* execute query */
		  $this->query( $query) 
				or die( 'db_delete : error query "'.$query.'" '. pg_last_error());
	  }
    
    /* update table in db 
	 * table - table name
    * update - datas for update (exampl. text = 'hi my frend')
	 * where - where for update
    */
    function db_update($update, $where)
	  {
		  $query = "update ".$this->fs_table." set ".$update;
		
		  if( $where != "") 
        $query = $query . ' where '. $where;
		
		  $this->query( $query);
	  }
	  
	  /* insert to db data
	 * columns - array with names of column
	 * values - array with values
	 * the array columns and valuses is must same length */
	  function db_insert($columns, $values)
	  {
		  $query = "insert into ".$this->fs_table." (";

		  if( !is_array($columns) || !is_array($values)) return False;
		  else if( ($cnt = count($columns)) == 0 || $cnt != count( $values)) return False;
		  $cnt = 0;
		  /* set the columns name */
		  foreach( $columns as $col)
		  {
			 if( $cnt++ > 0 ) $query =  $query. ",";
			 $query = $query . $col;
		  }
		
		  $query = $query .")values(";

		  $cnt = 0;
		  /* set the valuses */
		  foreach( $values as $col)
		  {
			 if( $cnt++ > 0 ) $query =  $query. ",";
			 $query = $query ."'". $col."'";
		  }

		  $query = $query .")";
  		$this->query( $query);
	   }
	  
	  function cselect($select = '*', $where = '')
		{
			/* compile query */
			$query = 'select '.$select.' from '.$this->fs_table;

			if( $where != "") 
        $query = $query . ' where '. $where;
			
      /* connect to db */
		  if( self::$fo_connect == null) 
        $this->connect();
      
      /* execute query */
      $this->fo_result = call_user_func(self::$FUNC_QUERY, $query);
		}
		
		/* execute query is Ok? */
		 function isOk()
		{
			if( $this->fo_result && $this->numRows() > 0) 
        return true;
			else 
        return false;
		}
		
		/* get result query to row */
		 function getRow()
		{
			if( $this->isOk())
				return call_user_func(self::$FUNC_FETCH_ROW, $this->fo_result);
			else 
        return false;
		}
		
		/* get result query to array */
		 function getArray()
		{
			if( $this->isOk())
				return call_user_func(self::$FUNC_FETCH_ARRAY, $this->fo_result);
			else 
        return false;
		}
		
		public function getTable()
		{
		  if( !$this->isOk())
		    return null;
		    
		  $table = array();  
      while($array = $this->getArray())
      {
        array_push($table, $array);
      }
      
      return $table;
    }
		
		/* get rows in result */
		function numRows()
		{
			if( $this->fo_result)
				return call_user_func(self::$FUNC_NUM_ROWS, $this->fo_result);
			return 0;
		}
    
    function query($query)
    {
      /* connect to db */
		  if( self::$fo_connect == null) 
        $this->connect();
      
      /* execute query */
      $this->fo_result = call_user_func(self::$FUNC_QUERY, $query);
    }
    
		/* destructor */
		 function __destruct()
		{
			if( $this->fo_result) 
        call_user_func(self::$FUNC_FREE_RESULT, $this->fo_result);
		}
		
	  public function close()
  	{
		  if( self::$fo_connect != null)
      { 
			   call_user_func(self::$FUNC_CLOSE, self::$fo_connect);
			   self::$fo_connect = null;
			}
	  }
	  
	  public static function getTableSimple($table_name, $select = '*', $where = '')
	  {
      $db = new MIUFW_DBUTILS($table_name);
      $db->cselect($select, $where);
      return $db->getTable();
    }
  
  }

?>