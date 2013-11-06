<?php

$blog_url = 'http://phoenixrise.blog.cz';
$blog_items_per_list_page = 15;

$output_dir = __DIR__ . '/output';
$templates_dir = __DIR__ . '/templates';

function absUrl( $url, $url_base ) {
	if( strpos( $url, '/') === 0) {
		return "$url_base$url";
	}
	else {
		return $url;
	}
}