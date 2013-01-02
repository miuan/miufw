<?php
  require 'template_base.php';
  
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
  
  ///////////////////////////////////////////////////////////////
  //
  //    TERMINOLOGY
  //
  // start-entity: <@
  // end-entity: @>
  // block are : <@ ... @>
  // block-attribute are: text locket between start-entity and end-entity
  // example in block <@ example_block @> :
  //   example_block
  //
  // inside-bloc are: block betwen two blocks
  // example:
  //    <@ ...  @>
  //      <!-- this is inside-block -->
  //    <@ ... @>
  //
  ///////////////////////////////////////////////////////////////
  class CMIUFW_TEMPLATE_BLOCK extends CMIUFW_TEMPLATE_BASE
  {
    

    // define start and end entities
    public static $cs_entity_start;
    public static $cs_entity_end;
    
    public static $len_entity_start = 0;
    public static $len_entity_end = 0;
    
    protected $fs_html_template_src = '';
    protected $fs_html_template_desc = '';
    private $fi_pos_args_start = 0;
    private $fi_pos_args_end = 0;
    private $fi_pos_current_block_start = 0;
    private $fi_pos_current_block_end = 0;
    protected $fi_pos_prev_block_end = 0;
    protected $fs_text = '';
    
    // previosly args
    protected $fs_str_args_prev = "";
    
    // was set $fs_html_template_desc value
    private $fb_set_desc = false;
    
    function __construct($template_html_src, $keys)  
    {
      parent::__construct($keys);
      
      if($template_html_src != '' && $this->fs_html_template_src == '')
      {
        $this->fs_html_template_src = $template_html_src;
        // set src to desc for returning
        // in mbGetBlockPosition start entity not found
        //  "// 1.
        //   // get first start-entity"
        //$this->fs_html_template_desc = $template_html_src;
      }
      
      $this->fb_set_desc = false;
      
      if(self::$len_entity_start == 0)
      {
        self::$cs_entity_start = self::$cs_syntax_entity_start . self::$cs_syntax_entity;
        self::$len_entity_start = strlen(self::$cs_entity_start);
      }
      
      if(self::$len_entity_end == 0)
      {
        self::$cs_entity_end = self::$cs_syntax_entity . self::$cs_syntax_entity_end;
        self::$len_entity_end = strlen(self::$cs_entity_end);
      }
    }
    
    function mRemoveInsideBlock()
    {
      /*
      $num_byte_for_remove = $this->fi_pos_current_block_start - $this->fi_pos_prev_block_end;
      $this->fs_html_template = substr_replace($this->fs_html_template, '', $this->fi_pos_prev_block_end, $num_byte_for_remove);
          
      // set new current position
      $this->fi_pos_current_block_start -= $num_byte_for_remove;
      $this->fi_pos_current_block_end -= $num_byte_for_remove;
      $this->fi_pos_args_start -= $num_byte_for_remove;
      $this->fi_pos_args_end -= $num_byte_for_remove;*/
    }
    
    // set pos for current block
    function mbGetBlockPosition()
    {
      if($this->fi_pos_current_block_start != 0)
        $this->fi_pos_current_block_start++;
        
      // get first start-entity
      $pos_entity_start = strpos($this->fs_html_template_src, self::$cs_entity_start, $this->fi_pos_current_block_start);
      // in template_html are not any special block
      if($pos_entity_start == 0)
        return false;
       
      // get position entity-end of first if-block
      $pos_entity_end = strpos($this->fs_html_template_src, self::$cs_entity_end, $pos_entity_start);
        
      // get if-condition start, end
      //    and get if-block start, end
      $this->fi_pos_args_start = $pos_entity_start + self::$len_entity_start +1;
      $this->fi_pos_args_end = $pos_entity_end;
      $this->fi_pos_current_block_start = $pos_entity_start;
      $this->fi_pos_current_block_end = $pos_entity_end + self::$len_entity_end;
    
      return true;
    }
    
    // init parse
    function parseInit()
    {
      //$this->fs_html_template_desc = "";
      $this->fs_str_args_prev = "";
      $this->fi_pos_prev_block_end = 0;
      $this->fi_pos_current_block_end = 0;
      $this->fs_text = '';
      //$this->debug("init:" . $this->fs_html_template_desc);
    }
    
    // add string to desc
    protected function descAdd($str_add)
    {
      $this->fb_set_desc = true;
      $this->fs_html_template_desc .= $str_add;
    }
    
    // get string from desc
    function descGet()
    {
      if($this->fb_set_desc)
        return $this->fs_html_template_desc;
      else
        return $this->fs_html_template_src;
    }
    
    function parse()
    {    
      // get entities
      // $str_entity_start = miufwEntityStart();
      // $str_entity_end = miufwEntityEnd();
      // $str_entity_endif = miufwEntityEndif();*/
      
      // get lens of entity-start and entity-end
      /* $len_entity_start = strlen($str_entity_start);
      $len_entity_end = strlen($str_entity_end);*/
      $this->parseInit();
      $result = $this->parseTemplate();
      $this->parseFinallize();
      
      return $result;   
    }
    
    private function parseTemplate()
    {
      $b_add_body = false;
      
      $array_args = array();
      $block_for_parse = false;
      $str_block = '';
      $end_of_file = false;
      while(!$end_of_file)
      { 
        // send previosly end position
        $this->fi_pos_prev_block_end = $this->fi_pos_current_block_end; 
         
        // 1.
        // get first start-entity 
        if(!$this->mbGetBlockPosition())
        {
          // add body befor exit from this cycle
          // from previously parseBody
          // add inside-block
          //if($b_add_body)
          //  $this->descAdd($this->fs_text);
          $this->debug("return:" . $this->fs_html_template_desc);
          $this->debug($this->fi_pos_args_end);
          $this->debug($this->fs_text);
          $end_of_file = true;
          $this->fs_text = '';
          // add text from end pos block to end document
          //$this->descAdd(substr($this->fs_html_template_src, $this->fi_pos_prev_block_end));
          // return $this->descGet();//$this->fs_html_template_desc;
        } 
        else
          $this->fs_text = substr($this->fs_html_template_src, $this->fi_pos_prev_block_end, $this->fi_pos_current_block_start - $this->fi_pos_prev_block_end);
        
      
        // TODO: print where 
        if(!$end_of_file && $this->fi_pos_args_end == 0)
        {
          $this->debug("error return:" . $this->fs_html_template_desc);
          return $this->error("Had been expect symbol: \'". self::$cs_entity_end. "\'") . $this->fs_html_template_desc;
        }
        // 2. 
        // get previous inside block
        
        // get is block for parsing
        $block_for_parse = $this->parseIsBlockForParse($array_args);
       
        // 4. (1)
        // get next if-block and set as current
        // getting now because function parseBody must know
        // end position inside block
        //if(!$this->mbGetBlockPosition())
        //{
        //  $this->parseFinallize();
        //  return $this->fs_html_template_desc;
        //}
       
        if($block_for_parse)
        {
          // and remove current if-blocke
          //$this->fs_html_template = substr_replace($this->fs_html_template, '', $this->fi_pos_current_block_start, $this->fi_pos_current_block_end - $this->fi_pos_current_block_start);
          // 3.
          if($this->parseIsEndBlock($array_args) && !$end_of_file)
          {
            //$this->debug($array_args);
            //$this->debug("break:" . $this->fs_html_template_desc);
            //$this->debug($this->fi_pos_current_block_start);
            $b_add_body = true;
            //break;
          }
          else
          // any operation
            $b_add_body = $this->parseBody($array_args, $this->fs_text);
          //$this->fi_pos_current_block_end = $this->fi_pos_current_block_start;
        }
        else
        {
          // add this bodie
          $b_add_body = true;
          // current block is not for parse
          // add the block to desc for after parse...   
          $this->descAdd($str_block);
          //$this->debug($str_block);
        }
        
        // add body befor exit from this cycle
        // from previously parseBody
        // add inside-block
        if($b_add_body)
          $this->descAdd($this->fs_text);
        
        // get current args  
        $str_block = substr($this->fs_html_template_src, $this->fi_pos_current_block_start, $this->fi_pos_current_block_end - $this->fi_pos_current_block_start); 
        $str_args =  substr($str_block , self::$len_entity_start, -self::$len_entity_end);
        $array_args = explode(' ', trim($str_args));
        
      
      
        //if($end_of_file)
        //  return $this->descGet();  
        
        // set previos
        $this->fs_str_args_prev = $str_args;
      }
      
      //echo "$str_args";
      //return $this->parseTemplate();
      
      $this->descAdd(substr($this->fs_html_template_src, $this->fi_pos_prev_block_end));
      return $this->descGet();//$this->fs_html_template_desc;
    }
    
    
    protected function parseIsEndBlock($array_args)
    {
      return true;
    }
    
    protected function parseFinallize()
    {
      
    }
    
    protected function parseBody($array_args, $str_body)
    {
      return true;
    }
    
    protected function parseIsBlockForParse($args_array, $str_body)
    {
      return true;
    }
  }


?>