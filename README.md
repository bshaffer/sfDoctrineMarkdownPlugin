sfDoctrineMarkdownPlugin
==============

Use the Markdown syntax for a field on your Doctrine models via a Doctrine behavior.

Installation
------------

### With git

    git submodule add git://github.com/bshaffer/sfDoctrineMarkdownPlugin.git plugins/sfDoctrineMarkdownPlugin
    git submodule init
    git submodule update

### With subversion

    svn propedit svn:externals plugins

In the editor that's displayed, add the following entry and then save

    sfDoctrineMarkdownPlugin https://svn.github.com/bshaffer/sfDoctrineMarkdownPlugin.git

Finally, update:

    svn up

# Setup

In your `config/ProjectConfiguration.class.php` file, make sure you have
the plugin enabled.

    $this->enablePlugins('sfDoctrineMarkdownPlugin');
    
Publish your assets

    ./symfony plugin:publish-assets

Clear your cache

    ./symfony cc

# Usage

This plugin is very simple, and provides you with the ability to add a markdown-enabled
field to your model. It also provides a symfony helper to allow calling the MarkDown libraries
in your template layer.

## The Behavior 

Activate the behavior like this:

    MyModel:
      actAs:
        MarkDown: { fields: [body] }
      columns:
        body:
          type:   clob
   
This is the bare minimum required to activate Markdown on your model.  The behavior will create
the column `body_html` and use this to render the html from the `body` field on create and
update.  This column will contain all the same options as the original column.  `body` must
be defined in your model's schema.

Optionally specify the column for the html to be persisted like so:

    MyModel:
      actAs:
        MarkDown: { fields: {body_rendered: body} }
      columns:
        body:
          type:   clob
        body_rendered:
          type:   clob
 
## The Helper 

Helper functions include:

 * _markdown_preview_link($field, $linkName = 'Preview')_
    - Takes the name of your markdown field (you must be outputing this in a symfony form)
    and provides a link which, when clicked opens a popup window with the value rendered as markdown 
 * _markdown($text)_
    - Render text as markdown
