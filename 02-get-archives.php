<?php

include_once __DIR__ . '/include.php';
include_once __DIR__ . '/parser.php';

$archives_links_file = "$output_dir/01/archive_links.json";
$output_dir = "$output_dir/02";
$cache_dir = "$output_dir/cache";

if( ! file_exists( $output_dir ) ) {
	mkdir( $output_dir, 0777, TRUE );
}
if( ! file_exists( $cache_dir ) ) {
	mkdir( $cache_dir, 0777, TRUE );
}

$archive_links = json_decode( file_get_contents( $archives_links_file ), TRUE );
$archive_links_count = count($archive_links);
echo "Archive links: " . $archive_links_count . "\n";

echo "Getting archive contents:\n";

$i = 1;
foreach( $archive_links as $link ) {
	echo " - grab content " . $i++ . "/$archive_links_count: ";

	$cache_file = "$cache_dir/$link[name].html";

	if( file_exists( $cache_file ) ) {
		echo " OK (already cached)\n";
		continue;
	}
	$content = file_get_contents( $link[ 'url' ] );
	file_put_contents( $cache_file, $content );
	echo " OK\n";
}

echo "Getting archive contents done.\n";
echo "Parsing archive contents:\n";

$articles = array();
$i = 1;
foreach( $archive_links as $link ) {
	echo " - parse content " . $i++ . "/$archive_links_count: ";

	$cache_file = "$cache_dir/$link[name].html";

	$content = file_get_contents( $cache_file );

	$doc = new DOMDocument();
	@$doc->loadHTML( '<?xml encoding="UTF-8">' . $content );
	$doc->preserveWhiteSpace = true;
	$doc->encoding = 'UTF-8';
	$xpath = new DOMXPath( $doc );
	$parser = new XPathParser( $xpath );

	$article_nodes = $parser->getNodeList('//div[@id="mainInner"]/div/h3/a');

	echo $article_nodes->length . " items";

	foreach( $article_nodes as $article_node ) {

		$article = array(
			'url' => absUrl( $article_node->getAttribute( 'href' ), $blog_url ),
			'title' => $article_node->getAttribute( 'title' ),
			'archive_cache_file' => $cache_file,
			'archive_name' => $link[ 'archive_name' ],
			'archive_page' => $link[ 'page' ],
			'archive_url' => $link[ 'url' ],
		);
		$articles[] = $article;
	}

	unset( $parser, $xpath, $doc );
	echo "\n";
}
echo "Parsing archive contents done, obtained " . count( $articles ) . " items.\n";

file_put_contents(
	"$output_dir/articles.json",
	json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);
