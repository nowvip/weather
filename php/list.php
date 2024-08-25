<?php
// 初始化 cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.weather.com.cn/pubm/zhaowen.htm');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// 执行 cURL 请求
$jsonData = curl_exec($ch);

if ($jsonData === FALSE) {
    die('Error fetching JSON data: ' . curl_error($ch));
}

// 关闭 cURL 资源
curl_close($ch);

// 解析 JSON 数据
$data = json_decode($jsonData, true);

if ($data === NULL) {
    die('Error decoding JSON data');
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
