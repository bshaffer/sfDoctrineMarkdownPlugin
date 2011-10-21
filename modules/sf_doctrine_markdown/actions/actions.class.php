<?php

/**
* sf_doctrine_markdown Actions
*/
class sf_doctrine_markdownActions extends sfActions
{
  public function executePreview(sfWebRequest $request)
  {
    $markdown = $request->getParameter('markdown'); 
    
    $parser = new MarkdownExtra_Parser();
    $markdown = $markdown ? $markdown : '_No Markdown To Preview!_';
    return $this->renderText($parser->transform($markdown));
  }
}
