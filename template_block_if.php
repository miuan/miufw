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
  
  require_once 'template_block.php';
  
      ///////////////////////////////////////////////////////////////
      //
      //    TERMINOLOGY
      //
      // start-entity: <@
      // end-entity: @>
      // if-block are:  <@ if ... @>
      //                <@ elif ... @>
      //                <@ else @>
      //                <@ endif @>
      //
      // if-condition are:  if ...
      //                    elif ...
      // example:
      //    if myval == "1"
      //
      // inside-bloc are: block betwen two if-blocks
      // example:
      //    <@ if ...  @>
      //      <!-- this is inside-block -->
      //    <@ endif @>
      //
      ///////////////////////////////////////////////////////////////
  
  class CMIUFW_TEMPLATE_BLOCK_IF extends CMIUFW_TEMPLATE_BLOCK
  {
    public static $cs_syntax_if = 'if';
    public static $cs_syntax_elif = 'elif';
    public static $cs_syntax_else = 'else';
    public static $cs_syntax_endif = 'endif';
    public static $cs_syntax_is = 'is';
    public static $cs_syntax_not = 'not';
    public static $cs_syntax_defined = 'defined';
    
  
    private $fb_if_condition_if = false;
    private $fb_any_inside_block = false;
  
    protected function pareseInit()
    {
      $this->fb_if_condition_if = false;
      $this->fb_any_inside_block = false;
      parent::parseInit();
    }
  
    protected function parseIsEndBlock($array_args)
    {
      
      // current type is endif exit
      if($array_args[0] == self::$cs_syntax_endif)
      {
          if(!$this->fb_if_condition_if)
          {
            // TODO: 
            if($this->fs_str_args_prev == "")
              $template_html = $this->error("Unexpcet if-condition: \'$type\'") . $this->fb_if_condition_if;
            /*else
              $template_html = error("In \'$str_if_conditio_prev\'. Unexpcet if-condition: \'$type\'") . $this->fb_if_Condition_if;*/
          }
          return true;
      }
      return false;
    }
    
    protected function parseBody($array_args, $str_body)
    {
    // 5.
        // any inside-block has been add
        // or if-condition false
        if($this->fb_any_inside_block || !$this->getIfTrue($array_args))
        {
          // this remove inside-block
          //$this->mRemoveInsideBlock();
          return false;
        }
        else
        {
          $this->fb_any_inside_block = true;
          return true;
        }
    }
    
    private function getIfTrue($array_args)
    {
      // array args is wrong
      if(!is_array($array_args) || count($array_args) < 1)
        return false;
     
      $array_pos = 0;
      
      // get type  
      $type = $array_args[$array_pos];
      if($type == self::$cs_syntax_else)
        return true;
    
      // $type is "if" or "elif"
      // because function parseIsBlockForParse getting only this option
    
      if(count($array_args) < 2)
      {
        $this->error("format of if is wrong.");
        return false;
      }

      if($this->isDefinedValue($array_args[1]))
      {
        return true;
      }
      // get condition
      //$str_condition
      return false;
    }
    
    protected function parseIsBlockForParse($array_args, $str_body = null)
    {
      // array args is wrong
      if(!is_array($array_args) || count($array_args) < 1)
        return false;
      
      // get type
      $type = $array_args[0];
      
      return $type == self::$cs_syntax_if
              ||  $type == self::$cs_syntax_elif
              ||  $type == self::$cs_syntax_else
              ||  $type == self::$cs_syntax_endif;
    }
  }

?>