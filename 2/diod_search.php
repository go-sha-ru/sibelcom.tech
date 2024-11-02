<?php
$sites = [
    'https://www.promelec.ru',
    'https://www.chipdip.ru/',
    'https://www.chip1stop.com/',
];

$url = 'https://www.promelec.ru/directrequest/search/search/index/';
$data = ['query' => 'BAS521,115'];

$url2 = 'https://www.chipdip.ru/search/suggest?tmpl=%2Fsearch&q=bas521%2C115';


$optionsPost = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
    ],
];
function prepareHeaders($headers) {
    $flattened = array();

    foreach ($headers as $key => $header) {
        if (is_int($key)) {
            $flattened[] = $header;
        } else {
            $flattened[] = $key.': '.$header;
        }
    }

    return implode("\r\n", $flattened);
}
$headers = [
    'Content-Type' => 'application/json',
    'X-Requested-With' => 'XMLHttpRequest'
];
$optionsGet = [
    'http' => [
        'header' => prepareHeaders($headers),
        'method' => 'GET',
    ],
];

$headers = [
    'Content-Type' => 'text/html',
    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 YaBrowser/24.10.0.0 Safari/537.36',
//    'content' => http_build_query($data),
];

$data = [
    'searchForm' => 'searchForm',
    'headerKeywordSearch' => 'BAS521,115'
];

$optionsGet2 = [
    'http' => [
        'header' => prepareHeaders($headers),
        'method' => 'GET',
    ],
];
$ret = [];


$context = stream_context_create($optionsPost);
$result = file_get_contents($url, false, $context);
$data = json_decode($result, true);
$count = preg_replace("/\D+/", "", $data['goods'][0]['quantity']['inStock']);
$ret[$sites[0] . $data['goods'][0]['url']] = $count;

$context = stream_context_create($optionsGet);
$result = file_get_contents($url2, false, $context);
$data = json_decode($result, true);
$html = $data['ExtSections'][0]['Rows'][0]['Columns'][4]['Html'];
$count = preg_replace("/\D+/", "", $html);

$ret[$sites[1] . $data['ExtSections'][0]['Rows'][0]['Url']] = $count;
arsort($ret);

foreach ($ret as $url => $count) {
    echo "$url $count" . PHP_EOL;
}