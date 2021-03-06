<?php

// 
//  MarkdownListener.class.php
//  
//  Created by Brent Shaffer on 2009-10-03
// 

class Doctrine_Template_Listener_Markdown extends Doctrine_Record_Listener
{
  /**
   * Array of markdown options
   */  
  protected $_options = array();


  /**
   * Constructor for Markdown Template
   *
   * @param array $options 
   * @return void
   * @author Brent Shaffer
   */  
  public function __construct(array $options)
  {
    $this->_options = $options;
  }

  /**
   * parse the markdown in the specified fields when an object is created
   *
   * @param Doctrine_Event $event
   * @return void
   * @author Brent Shaffer
   */
  public function preInsert(Doctrine_Event $event)
  {
    $parser = $this->getMarkdownParser();
    $object = $event->getInvoker();
    foreach ($this->_options['fields'] as $parsedField => $markdownField) 
    {
      // allows editing of $htmlField directly, otherwise it's cleared
      if ($object[$markdownField] != '') {
        $object[$parsedField] = $parser->transform($object[$markdownField]);
      }
    }
  }

  /**
   * parse the markdown in the specified fields when an object is updated
   *
   * @param Doctrine_Event $event
   * @return void
   * @author Brent Shaffer
   */
  public function preUpdate(Doctrine_Event $event)
  {
    $parser = $this->getMarkdownParser();
    $object = $event->getInvoker();
    foreach ($this->_options['fields'] as $htmlField => $markdownField) 
    {
      if (array_key_exists($markdownField, $object->getModified()))
      {
        // allows editing of $htmlField directly, otherwise it's cleared
        if (!($object[$markdownField] == '' && $object->isNew())) {
          $object[$htmlField] = $parser->transform($object[$markdownField]);
        }
      }
    }
  }
  
  private function getMarkdownParser()
  {
    $class  = sfConfig::get('app_sfDoctrineMarkdownPlugin_parser_class', 'sfMarkdownParser');
    return new $class();
  }
}
