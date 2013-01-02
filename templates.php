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



  require_once 'template_base.php';
  require_once 'template_block_if.php';
  require_once 'template_block_for.php';
  require_once 'config.php';
  
  
  class MIUFW_TEMPLATES extends CMIUFW_TEMPLATE_BASE
  {
    // file name
    private $fs_file_name = '';
    
    private $fs_template = '';
    
    public function __construct($keys = null)
    {
      parent::__construct($keys);
    }
    
    /**
     * replace values
     * a_value is array with values
     * key is string with key-name
     * template is loadet template               
     */         
    public static function replaceArray($a_value, $key, $template)
    {
      if($a_value != null && is_array($a_value))
      {
    //$i_count = count($a_value);
      foreach($a_value as $key_name => $value)
      {
        if($key != '')
          $s_key = (string)$key . '@' . $key_name;
        else
          $s_key = $key_name;
      
        if(is_array(($value)))
        {
          //var_dump($value);
          $template = self::replaceArray($value, $s_key, $template);
        }
        else 
        {
          //$s_key_replace = "<% " . $s_key . ' %>';
          //echo $s_key_replace;
          $replacements = array();
          $replacements[0] = $value;
          $patterns = array();
          $patterns[0] = "/<% " . $s_key . ' %>/';

          $template = preg_replace( $patterns, $replacements, $template);
          
          //$template = eregi_replace($s_key_replace, $value, $template);
        }
      }
      }
      return $template;
    }
    
    /**
     * load template by file name
     *  return - true when loaded template
     *        - or false if temlate not loaded...        
     */         
    public function load($s_file_name, $babsolute=false)
    {
      $this->fs_file_name = $s_file_name;
      
      global $MIUFW_CFG;
      if(!$babsolute)      
        $temp_name = $MIUFW_CFG->TEMPLATES_DIR . $this->fs_file_name;
      
      $filesize = filesize ($temp_name);
      
      /* open file */
      if($filesize > 0 && $fd = fopen($temp_name, "r")) 
      {
        /* read opened file */
        $template = fread ($fd, $filesize);
        
        /* replace keys from arrays */
        $this->fs_template = stripslashes($template);
        
        /* close file */
        fclose ($fd);
        return true;
      }
      /* return false if file nod opened */
      else return false; 
    }
    
    /**
     * parse loaded template
     * return - parset template file     
     */          
    public function parse()
    {
      //$template = miufwParseIf($template, $keys);
      $block_for = new CMIUFW_TEMPLATE_BLOCK_FOR($this->fs_template, $this->fa_keys);
      $template = $block_for->parse();
      
      //$block_for->debug($template);
      $block_if = new CMIUFW_TEMPLATE_BLOCK_IF($template, $this->fa_keys);
      $template = $block_if->parse();
      
      // add values  
      return self::replaceArray($this->fa_keys, '', $template);
    }
    
    /* function loadTemplate
     * $temp_name - file name with template *.temp *.tmp *.html
     * $keys - associative arrays with keys for replace
     * return - string with teplate and replaced keys
     *        - or false if temlate not loaded...     
     */               

   public static function parseSimple($keys = null)
    {
      global $MIUFW_CFG;
      
      // cal classical template
      $file_name = self::getThisScriptName();
      
      return self::parseFile($file_name.'.html', $keys);
    }
    
    public static function parseFile($file_name, $keys = null)
    {
      $ctemplate = new MIUFW_TEMPLATES($keys);
      $ctemplate->load($file_name);
      return $ctemplate->parse();
    }
    
    public static function parseSimpleWithTemplate($keys = null)
    {
      global $MIUFW_CFG;
  
      $array_args = array($MIUFW_CFG->template_body => self::parseSimple($keys));
      return self::parseFile($MIUFW_CFG->template_file_name, $array_args);
    }
  
     /**
   * get file name (this script)  
   */  
    public static function getThisScriptName()
    {
      $path = explode('/',$_SERVER['SCRIPT_NAME']);
      return $path[count($path)-1];
    }
}


?>