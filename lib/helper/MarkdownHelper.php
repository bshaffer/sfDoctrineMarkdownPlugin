<?php

function markdown_preview_link($field, $linkName = 'Preview', $previewId = 'markdown_preview') {
    use_stylesheet('/sfDoctrineMarkdownPlugin/css/markdown.css');
    $submit = content_tag('a',$linkName, array('href' => '#', 'onclick' => sprintf('javascript:markdown_preview_%s(this);return false', $previewId)));
  
    $js = markdown_preview_function($field, $previewId);
        
    return $submit.$js;
}

function markdown_preview_function($field, $previewId = 'markdown_preview') {
    use_helper('JavascriptBase');
    $js = javascript_tag(sprintf(<<<EOF
      function markdown_preview_%s (e) {
        var markdown_text = $('form *[name=%s]').val();
        $('#%s').load('%s', { 'markdown': markdown_text }, function() { $(this).append("<a class=\"markdown-hide\" href='#' onclick='$(\"#markdown_preview\").hide()'>hide</a>") } );
      }
EOF
, $previewId, $field, $previewId, url_for('@markdown_preview')));

    return $js;
}

function markdown_preview(){
  return content_tag('div', image_tag('/sfDoctrineMarkdownPlugin/images/loader.gif'), array('id' => 'markdown_preview'));
}

// prevents conflicts with other libraries including markdown
function parse_as_markdown($text) {
#
# Initialize the parser and return the result of its transform method.
#
	# Setup static parser variable.
	static $parser;
	if (!isset($parser)) {
		$parser_class = sfConfig::get('app_sfDoctrineMarkdownPlugin_parser_class', 'Markdown_Parser');
		$parser = new $parser_class;
	}

	# Transform text using parser.
	return $parser->transform($text);
}


### WordPress Plugin Interface ###

/*
Plugin Name: Markdown Extra
Plugin URI: http://www.michelf.com/projects/php-markdown/
Description: <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://www.michelf.com/projects/php-markdown/">More...</a>
Version: 1.2.2
Author: Michel Fortin
Author URI: http://www.michelf.com/
*/

if (isset($wp_version)) {
	# More details about how it works here:
	# <http://www.michelf.com/weblog/2005/wordpress-text-flow-vs-markdown/>
	
	# Post content and excerpts
	# - Remove WordPress paragraph generator.
	# - Run Markdown on excerpt, then remove all tags.
	# - Add paragraph tag around the excerpt, but remove it for the excerpt rss.
	if (MARKDOWN_WP_POSTS) {
		remove_filter('the_content',     'wpautop');
        remove_filter('the_content_rss', 'wpautop');
		remove_filter('the_excerpt',     'wpautop');
		add_filter('the_content',     'mdwp_MarkdownPost', 6);
        add_filter('the_content_rss', 'mdwp_MarkdownPost', 6);
		add_filter('get_the_excerpt', 'mdwp_MarkdownPost', 6);
		add_filter('get_the_excerpt', 'trim', 7);
		add_filter('the_excerpt',     'mdwp_add_p');
		add_filter('the_excerpt_rss', 'mdwp_strip_p');
		
		remove_filter('content_save_pre',  'balanceTags', 50);
		remove_filter('excerpt_save_pre',  'balanceTags', 50);
		add_filter('the_content',  	  'balanceTags', 50);
		add_filter('get_the_excerpt', 'balanceTags', 9);
	}
	
	# Add a footnote id prefix to posts when inside a loop.
	function mdwp_MarkdownPost($text) {
		static $parser;
		if (!$parser) {
			$parser_class = MARKDOWN_PARSER_CLASS;
			$parser = new $parser_class;
		}
		if (is_single() || is_page() || is_feed()) {
			$parser->fn_id_prefix = "";
		} else {
			$parser->fn_id_prefix = get_the_ID() . ".";
		}
		return $parser->transform($text);
	}
	
	# Comments
	# - Remove WordPress paragraph generator.
	# - Remove WordPress auto-link generator.
	# - Scramble important tags before passing them to the kses filter.
	# - Run Markdown on excerpt then remove paragraph tags.
	if (MARKDOWN_WP_COMMENTS) {
		remove_filter('comment_text', 'wpautop', 30);
		remove_filter('comment_text', 'make_clickable');
		add_filter('pre_comment_content', 'Markdown', 6);
		add_filter('pre_comment_content', 'mdwp_hide_tags', 8);
		add_filter('pre_comment_content', 'mdwp_show_tags', 12);
		add_filter('get_comment_text',    'Markdown', 6);
		add_filter('get_comment_excerpt', 'Markdown', 6);
		add_filter('get_comment_excerpt', 'mdwp_strip_p', 7);
	
		global $mdwp_hidden_tags, $mdwp_placeholders;
		$mdwp_hidden_tags = explode(' ',
			'<p> </p> <pre> </pre> <ol> </ol> <ul> </ul> <li> </li>');
		$mdwp_placeholders = explode(' ', str_rot13(
			'pEj07ZbbBZ U1kqgh4w4p pre2zmeN6K QTi31t9pre ol0MP1jzJR '.
			'ML5IjmbRol ulANi1NsGY J7zRLJqPul liA8ctl16T K9nhooUHli'));
	}
	
	function mdwp_add_p($text) {
		if (!preg_match('{^$|^<(p|ul|ol|dl|pre|blockquote)>}i', $text)) {
			$text = '<p>'.$text.'</p>';
			$text = preg_replace('{\n{2,}}', "</p>\n\n<p>", $text);
		}
		return $text;
	}
	
	function mdwp_strip_p($t) { return preg_replace('{</?p>}i', '', $t); }

	function mdwp_hide_tags($text) {
		global $mdwp_hidden_tags, $mdwp_placeholders;
		return str_replace($mdwp_hidden_tags, $mdwp_placeholders, $text);
	}
	function mdwp_show_tags($text) {
		global $mdwp_hidden_tags, $mdwp_placeholders;
		return str_replace($mdwp_placeholders, $mdwp_hidden_tags, $text);
	}
}
