<?php

// 
//  MarkdownTemplate.class.php
//  
//  Created by Brent Shaffer on 2009-10-03
// 

class Doctrine_Template_Markdown extends Doctrine_Template
{    
  /**
   * Array of default markdown options
   */  
  protected $_options = array(  'fields'  =>  array() );


  /**
   * Constructor for Markdown Template
   *
   * @param array $options 
   * @return void
   * @author Brent Shaffer
   */
  public function __construct(array $options = array())
  {  
    if (!isset($options['fields'])) 
    {
      throw new sfException("Required parameter 'fields' not set in Doctrine Markdown");
    }
    
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    
    // Set markdown field names if they are not set by the user
    foreach ( $this->_options['fields'] as $key => $value) 
    {
      if (is_int($key)) 
      {
        unset($this->_options['fields'][$key]);
        $this->_options['fields'][$value.'_html'] = $value;
      }
    }
  }

  /**
   * Add the database columns to hold the parsed HTML.
   * 
   *
   * @return void
   * @author Brent Shaffer
   */
  public function setTableDefinition()
  { 
    $this->addListener(new Doctrine_Template_Listener_Markdown($this->_options));
    $object = $this->getInvoker();

    foreach ($this->_options['fields'] as $htmlCol => $markdownCol) 
    {
        if (!$object->getTable()->hasColumn($markdownCol)) {
            throw new sfException(sprintf('Column "%s" must exist on model "%s" in order to use it for markdown', $markdownCol, get_class($object)));
        }
        
        if (!$object->getTable()->hasColumn($htmlCol)) {
            // automatically create the html column if not created
            $definition = $object->getTable()->getColumnDefinition($markdownCol);
            $this->hasColumn($htmlCol, $definition['type'], $definition);
        }
    }
  }

}
