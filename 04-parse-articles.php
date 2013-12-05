<?php
date_default_timezone_set('Europe/Prague');
include_once __DIR__ . '/include.php';
include_once __DIR__ . '/parser.php';

$articles_links_file = "$output_dir/03/articles.json";
$output_dir = "$output_dir/04";
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

echo "Parsing article contents:\n";

$articles = array();
$i = 1;
foreach( $articles_links as $link ) {
	echo " - parse content " . $i++ . "/$articles_links_count ($link[archive_name]-" . substr($link['article_url_key'], 0, 30) . "…): ";

	$content = file_get_contents( $link[ 'article_cache_file' ] );

	$doc = new DOMDocument();
	@$doc->loadHTML( '<?xml encoding="UTF-8">' . $content );
	$doc->preserveWhiteSpace = true;
	$doc->encoding = 'UTF-8';
	$xpath = new DOMXPath( $doc );
	$parser = new XPathParser( $xpath );

	$mainNode = $parser->getElementNode('//div[@id="mainInner"]');

	$title = $parser->getTextContent('//div[@class="article"]/h2', $mainNode);
	$dateNode = $parser->getElementNode('//div[@class="article"]/text()[2]', $mainNode);
	$categoryNode = $parser->getElementNode('//div[@class="article"]/a[contains(@href,\'rubrika\')]', $mainNode);
	$articleText = $parser->getElementNode('//div[@class="article"]/div[@class="articleText"]', $mainNode);

	$date = $m = NULL;
	if( $dateNode && preg_match("/^\\s*((\d+)\.\\s+(\\p{L}+)\\s+(\\d{4})\\sv\\s(\\d{1,2}):(\\d{2}))\\s*(\\||$)/u", $dateNode->textContent, $m ) ) {
		$date = mktime(
			$m[ 5 ],
			$m[ 6 ],
			0,
			replaceMonths($m[ 3 ]),
			$m[ 2 ],
			$m[ 4 ]
		);
	}
	else {
		if($dateNode) echo "ERROR preg: " . $dateNode->textContent . "\n";
		else echo "ERROR no date field";
	}

	$category = NULL;
	if( $categoryNode ) {
		$category = array(
			'name' => trim($categoryNode->textContent),
			'url_key' => preg_replace('~^.+/~', '', $parser->getAttributeValue( '@href', $categoryNode ) ),
		);
		if($category['url_key'] === "") die("ERROR: false detect category url_key\n");
	}

	$article = array(
		'info' => $link,
		'title' => $title,
		'date' => date('Y-m-d H:i:s', $date),
		'category' => $category,
		'content' => $articleText->ownerDocument->saveHTML( $articleText ),
	);

	$articles[] = $article;

	//if($i>100) break;

	unset( $parser, $xpath, $doc );
	echo "OK\n";
}
echo "Parsing article contents done, obtained " . count( $articles ) . " items.\n";

file_put_contents(
	"$output_dir/articles_content.json",
	json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

function replaceMonths($month) {
	$months = explode(',', ',ledna,února,března,dubna,května,června,července,srpna,září,října,listopadu,prosince');
	$key = array_search($month, $months);
	if($key) return $key;
	die("ERROR: unknown month $month!!!");
}
