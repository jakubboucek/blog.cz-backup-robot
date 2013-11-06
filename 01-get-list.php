<?php

include_once __DIR__ . '/include.php';
include_once __DIR__ . '/parser.php';

$url = "$blog_url/archiv";

$output_dir = "$output_dir/01";
$cache_file = "$output_dir/cache.html";

if( ! file_exists( $output_dir ) ) {
	mkdir( $output_dir, 0777, TRUE );
}

if( file_exists( $cache_file ) ) {
	$content = file_get_contents( $cache_file );
}
else {
	$content = file_get_contents($url);
	file_put_contents( $cache_file, $content );
}

$doc = new DOMDocument();
@$doc->loadHTML( '<?xml encoding="UTF-8">' . $content );
$doc->preserveWhiteSpace = true;
$doc->encoding = 'UTF-8';
$xpath = new DOMXPath( $doc );
$parser = new XPathParser( $xpath );

$links = $parser->getNodeList('//div[@id="archive"]/ul/li');

$archives = array();
foreach($links as $link) {
	$childNodes = $link->childNodes;
	$month = array(
		'name' => trim($childNodes->item( 1 )->textContent),
		'href' => trim($childNodes->item( 1 )->getAttribute( 'href' )),
		'count' => intval(trim(str_replace(array('(',')'),array(), $childNodes->item( 2 )->textContent))),
	);
	$archives[] = $month;
}

echo "Archives: " . count($archives) . "\n";

file_put_contents(
	"$output_dir/archives.json",
	json_encode($archives, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$archive_links = array();

foreach( $archives as $archive ) {
	for( $i = 1; $i <= ceil( $archive[ 'count' ] / $blog_items_per_list_page ); $i++ ) {
		$link = array(
			'url' => absUrl( $archive[ 'href' ], $blog_url ) . ($i>1 ? "/$i" : ''),
			'name' => str_replace( '/', '', $archive[ 'href' ] ) . "-$i",
			'archive_name'=>str_replace( '/', '', $archive[ 'href' ] ),
			'page'=>$i,
		);
		$archive_links[] = $link;
	}
}

echo "Archive links: " . count($archive_links) . "\n";
file_put_contents(
	"$output_dir/archive_links.json",
	json_encode($archive_links, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);
