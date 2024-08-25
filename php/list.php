<?php
// 使用 cURL 获取 JSON 数据
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.weather.com.cn/pubm/zhaowen.htm');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$jsonData = curl_exec($ch);

if ($jsonData === FALSE) {
    die('Error fetching JSON data: ' . curl_error($ch));
}

// 关闭 cURL 资源
curl_close($ch);

// 打印原始 JSON 数据
echo "Raw JSON Data:\n";
echo $jsonData;
echo "\n";

// 解析 JSON 数据
$data = json_decode($jsonData, true);

if ($data === NULL) {
    die('Error decoding JSON data: ' . json_last_error_msg());
}

// 处理 JSON 数据
foreach ($data['data'] as $item) {
    $url = $item['url'];
    $title = $item['title'];
    $updateTime = $item['updateTime'];
    
    echo "URL: $url\n";
    echo "Title: $title\n";
    echo "Update Time: $updateTime\n";
}
?>
