<?php

/**
* 
*/
class sfMarkdownParser extends MarkdownExtra_Parser
{
  function doHardBreaks($text) {
    # Do hard breaks:
    return preg_replace_callback('/ {2,}\n|\n{1}/', 
      array(&$this, '_doHardBreaks_callback'), $text);  
  }
}
