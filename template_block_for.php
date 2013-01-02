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
  
  class CMIUFW_TEMPLATE_BLOCK_FOR extends CMIUFW_TEMPLATE_BLOCK
  {
    public static $cs_syntax_for = 'for';
    public static $cs_syntax_endfor = 'endfor';
    
    private $fb_was_for = false;
    private $fi_num_repeat = 0;
    private $fs_array_name = '';
    
    
    protected function pareseInit()
    {
      $this->fb_was_fo = false;
      parent::parseInit();
    }
    
    protected function parseIsEndBlock($array_args)
    {
      // get name of array for iterate for
      if(!is_array($array_args) || count($array_args) < 1)
        return false; 
        
      // current type is endif exit
      if($array_args[0] == self::$cs_syntax_endfor)
      {
        //$this->debug( $str_body);
          if(!$this->fb_was_for)
          {
            // TODO: 
            //$template_html = $this->error("Unexpcet : \'$array_args[0]\' anywhere used \'". self::$cs_syntax_for ."\'") . $this->fb_if_condition_if;
          }
          return true;
      }
      return false;
    }
    
    protected function parseBody($array_args, $str_body)
    {   
      // get name of array for iterate for
      if(!is_array($array_args))
        return false;  
      
      // set default num repeat
      $this->fi_num_repeat = 0;
      $b_array_name_defined = false;
      if($this->fa_keys != null && is_array($this->fa_keys))
      { 
        // array name is defined
        if( count($array_args) > 1)
        {
          $this->fs_array_name = $array_args[1];
          $b_array_name_defined = true;
          
          // fa_keys defined
          // and key with name array_name exists in fa_keys
          // array_name is array
          // ->  set num repeat from array_name
          
          // get array
          $a = $this->valueGet($this->fs_array_name);
          $this->fi_num_repeat = count($a);
          
          /*if(array_key_exists($this->fs_array_name, $this->fa_keys))
          {
            $test_array = $this->fa_keys[$this->fs_array_name]; 
            if(is_array($test_array))
              $this->fi_num_repeat = count($test_array); 
          }*/
        }
        else
        {
          // use global array
          $this->fs_array_name = '';
          $this->fi_num_repeat = count($this->fa_keys);  
        }
      }
      
      $this->fb_was_for = true;
      $s_replace_key = '<% ' . $this->fs_array_name ;
      // add multiple inside-block
      for($num_repeat = 0; $num_repeat != $this->fi_num_repeat; $num_repeat++)
      {
        $s_replace_text = '<% ';
        if($b_array_name_defined)
          $s_replace_text .= $this->fs_array_name . '@' . (string)$num_repeat ;
        else
          $s_replace_text .= (string)$num_repeat;
      
        //$this->debug($this->fs_html_template_desc); 
        $this->fs_html_template_desc .= str_replace( $s_replace_key, $s_replace_text, $str_body);
        //$this->debug($num_repeat);
      }
      
      return false;
    }
    
    protected function parseIsBlockForParse($array_args, $str_body = null)
    {
      // array args is wrong
      if(!is_array($array_args) || count($array_args) < 1)
        return false;
      
      // get type
      $type = $array_args[0];
      
      return $type == self::$cs_syntax_for
              || $type == self::$cs_syntax_endfor;
    }
  }
  
  
  
  
?>