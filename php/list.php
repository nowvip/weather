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

// 使用正则表达式提取 JSON 数据部分
preg_match('/getLbDatas\((\{.*\})\)/', $jsonData, $matches);

if (isset($matches[1])) {
    $jsonStr = $matches[1];
} else {
    die('Error extracting JSON data');
}

// 解析 JSON 数据
$data = json_decode($jsonStr, true);

if ($data === NULL) {
    die('Error decoding JSON data: ' . json_last_error_msg());
}

// 获取当前 UTC 日期
$currentDateUTC = date('Y-m-d');

// 将当前 UTC 日期转换为北京时间
$datetime = new DateTime($currentDateUTC . ' 00:00:00', new DateTimeZone('UTC'));
$datetime->setTimezone(new DateTimeZone('Asia/Shanghai'));
$currentDateBeijing = $datetime->format('Y-m-d');

// 处理 JSON 数据并输出符合条件的数据
foreach ($data['data'] as $item) {
    $updateTime = $item['updateTime'];
    $itemDate = explode(' ', $updateTime)[0]; // 提取日期部分

    if ($itemDate === $currentDateBeijing) {
        $url = $item['url'];
        $title = $item['title'];
        
        echo "URL: $url\n";
        echo "Title: $title\n";
        echo "Update Time: $updateTime\n";
        break;
    }
}
?>
