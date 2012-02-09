<?php

/**
* 
*/
class sfMarkdownParser extends MarkdownExtra_Parser
{
  protected $options;

  public function __construct($options = array())
  {
    $this->options = array_merge(array(
      'no_markup'   => sfConfig::get('app_sfDoctrineMarkdownPlugin_no_markup'),
      'no_entities' => sfConfig::get('app_sfDoctrineMarkdownPlugin_no_entities'),
    ), $options);
    
    $this->no_markup   = $this->options['no_markup'];
    $this->no_entities = $this->options['no_entities'];

    parent::__construct();
  }
  
  function doHardBreaks($text) {
    # Do hard breaks:
    return preg_replace_callback('/ {2,}\n|\n{1}/', 
      array(&$this, '_doHardBreaks_callback'), $text);  
  }
}
