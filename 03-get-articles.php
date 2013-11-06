<?php
include_once __DIR__ . '/include.php';
include_once __DIR__ . '/parser.php';

$articles_links_file = "$output_dir/02/articles.json";
$output_dir = "$output_dir/03";
$cache_dir = "$output_dir/cache";

if( ! file_exists( $output_dir ) ) {
	mkdir( $output_dir, 0777, TRUE );
}
if( ! file_exists( $cache_dir ) ) {
	mkdir( $cache_dir, 0777, TRUE );
}

$articles_links = json_decode( file_get_contents( $articles_links_file ), TRUE );
$articles_links_count = count($articles_links);
echo "Articles links: " . $articles_links_count . "\n";

echo "Getting article contents:\n";

$articles = array();
$i = 1;
foreach( $articles_links as $link ) {
	echo " - grab content " . $i++ . "/$articles_links_count: ";

	$url_key = preg_replace('~^.+/~', '', $link[ 'url' ]);
	$cache_file = "$cache_dir/$link[archive_name]-$url_key.html";

	if( file_exists( $cache_file ) ) {
		echo " OK (already cached)\n";
	}
	else {
		$content = file_get_contents( $link[ 'url' ] );
		file_put_contents( $cache_file, $content );
		echo " OK\n";
		sleep( 2 );
	}

	$article = $link;
	$article[ 'article_cache_file' ] = $cache_file;
	$article[ 'article_url_key' ] = $url_key;

	$articles[] = $article;
}

echo "Getting article contents done.\n";

file_put_contents(
	"$output_dir/articles.json",
	json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);
