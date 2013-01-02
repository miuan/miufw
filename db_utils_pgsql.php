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
  
  if( !class_exists('DB_UTILS_BASE')):
  
  abstract class DB_UTILS_BASE
  {
    static $fo_connect = null;
    
    static $FUNC_QUERY = 'pg_query';
    static $FUNC_NUM_ROWS = 'Pg_num_rows';
    static $FUNC_FETCH_ROW = 'Pg_fetch_row';
    static $FUNC_FETCH_ARRAY = 'Pg_fetch_array';
    static $FUNC_FREE_RESULT = 'Pg_free_result';
    static $FUNC_CLOSE = 'Pg_close';
    
    /* connect do db */
    function connect()
    {
      global $MIUFW_CFG;
      
      if( self::$fo_connect != null)
        $this->close();
      
      $connection_string = "port={$MIUFW_CFG->DB_PORT} dbname={$MIUFW_CFG->DB_NAME} user={$MIUFW_CFG->DB_LOGIN}";
      
      if($MIUFW_CFG->DB_PASS != '')
        $connection_string .= " password={$MIUFW_CFG->DB_PASS}";
      else
        self::$fo_connect = Pg_Connect($connection_string);  
    }
    
    /* use children close */
    function close()
    {
    }
    
	}
  endif;

?>