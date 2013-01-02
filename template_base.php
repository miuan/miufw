<?php 
  
  require_once 'template_syntax.php';
  
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
  
  if(!class_exists('CMIUFW_TEMPLATE_BASE')):
  class CMIUFW_TEMPLATE_BASE extends CMIUFW_TEMPLATE_SYNTAX
  {
    protected $fa_keys = null;
    
    
    static function error($err)
    {
      return '<div class="error">ERROR: ' . $err . '</div>';
    }
    
    public static function debug($debug)
    {
      require_once( 'config.php');
      global $MIUFW_CFG;
      if($MIUFW_CFG->DEBUG)
      {
        echo '<div class="debug">';
        var_dump($debug);
        echo '</div>';
      }
    }
    
    function __construct($keys)
    {
      if($keys != null && is_array($keys))
        $this->fa_keys = $keys;
    }
    
    function isDefinedValue($key)
    {
      if($this->fa_keys != null && is_array($this->fa_keys)
            && array_key_exists($key, $this->fa_keys)) 
      {
        return !empty($this->fa_keys[$key]);
      }
      return false;
    }
    
    public function valueGet($s_key_name)
    {
      return $this->valueFromKeys($this->fa_keys, $s_key_name);
    }
    
    
    private function valueFromKeys($a_keys, $s_key_name)
    {
      if($a_keys == null || !is_array($a_keys))
        return null;
      
      // get first symbol @  
      $pos_array_start = strpos($s_key_name, CMIUFW_TEMPLATE_SYNTAX::$cs_syntax_entity);
      // s key name is no @
      // and s_key_name containet in keys
      if($pos_array_start == 0)
        if(array_key_exists($s_key_name, $a_keys))
          return $a_keys[$s_key_name];
      else
      {
        // get string after symbol @
        $s_key_name_new = substr($s_key_name, $pos_array_start + 1);
        // get sting prev symbol @
        $s_key_name = substr($s_key_name, 0, $pos_array_start);
        
        if(array_key_exists($s_key_name, $a_keys))
          $a_keys_new = $a_keys[$s_key_name];
        
        if(is_array($a_keys_new))
          return valueFromKeys($a_keys_new, $s_key_name_new);
      }
    
      return null;
    }
  }
  endif;
?>