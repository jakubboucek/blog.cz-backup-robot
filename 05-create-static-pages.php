<?php
date_default_timezone_set('Europe/Prague');
include_once __DIR__ . '/include.php';
include_once __DIR__ . '/parser.php';

$articles_content_file = "$output_dir/04/articles_content.json";
$output_dir = "$output_dir/05";
$build_dir = "$output_dir/build";

$detail_template_file = "$templates_dir/detail.html";
$list_template_file = "$templates_dir/list.html";

if( ! file_exists( $output_dir ) ) {
	mkdir( $output_dir, 0777, TRUE );
}
if( ! file_exists( $build_dir ) ) {
	mkdir( $build_dir, 0777, TRUE );
}

$articles_content = json_decode( file_get_contents( $articles_content_file ), TRUE );
$articles_content_count = count($articles_content);
echo "Articles: " . $articles_content_count . "\n";

echo "Building static articles:\n";

$detail_template = file_get_contents( $detail_template_file );
$list_template = file_get_contents( $list_template_file );

$articles = array();
$index_list = array();
$i = 1;
foreach( $articles_content as $link ) {
	$info = $link['info'];
	echo " - build article " . $i++ . "/$articles_content_count ($info[archive_name]-" . substr($info['article_url_key'], 0, 30) . "…): ";

	$masks = array(
		'%TITLE%',
		'%DATE%',
		'%CONTENT%'
	);
	$replacements = array(
		$link[ 'title' ],
		$link[ 'date' ],
		$link[ 'content' ],
	);

	$output_file_name = "$info[archive_name]-$info[article_url_key].html";
	$output_file_path = "$build_dir/$output_file_name";

	file_put_contents($output_file_path,
		str_replace($masks, $replacements, $detail_template)
	);

	$index_list[] = "<span class=\"date\">" . substr($link['date'],0,10) . "</span> <a href=\"$output_file_name\">$link[title]</a>";

	echo "OK\n";
}
echo "Build static articles done, obtained " . count( $articles ) . " articles.\n";

file_put_contents("$build_dir/index.html",
	str_replace('%LIST%', join('</li><li>', $index_list), $list_template)
);

function replaceMonths($month) {
	$months = explode(',', ',ledna,února,března,dubna,května,června,července,srpna,září,října,listopadu,prosince');
	$key = array_search($month, $months);
	if($key) return $key;
	die("ERROR: unknown month $month!!!");
}
