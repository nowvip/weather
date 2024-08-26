<?php
$arr_urls = array(
    '1台' => 'https://www.weather.com.cn/pubm/zhaowen.htm',
    '1台 12:30' => 'https://www.weather.com.cn/pubm/wujian.htm',
    '2台 08:00' => 'https://www.weather.com.cn/pubm/diyiyinxiang.htm',
    '4台' => 'https://www.weather.com.cn/pubm/cctv4.htm',
    '5台 13:00' => 'https://www.weather.com.cn/pubm/tiyu.htm',
    '联播预报' => 'https://www.weather.com.cn/pubm/video_lianbo_2021.htm',
);

$content='';
foreach($arr_urls as $urlname => $urls){
// 使用 cURL 获取 JSON 数据
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urls);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$jsonData = curl_exec($ch);

if ($jsonData === FALSE) {
    die('Error fetching JSON data: ' . curl_error($ch));
}

// 关闭 cURL 资源
curl_close($ch);

// 打印原始 JSON 数据
//echo "Raw JSON Data:\n";
//echo $jsonData;
//echo "\n";

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


// 处理 JSON 数据并输出符合条件的数据
foreach ($data['data'] as $item) {
    $updateTime = $item['updateTime'];
    //$itemDate = explode(' ', $updateTime)[0]; // 提取日期部分
    $itemDate = substr($updateTime, 5, 5);//从第6个字符开始，提取5个字符,提取月份和日期部分
    
    $url = $item['url'];
    $title = $item['title'];
    //echo $title .'\n';
    // 使用正则表达式匹配时间格式
    if (preg_match('/\d{2}:\d{2}/', $title, $matches)) {
        $time = $matches[0];        
        $time = ' ' . $time;
        //echo $time '\n';
    } else {
        //echo "未找到时间";
        $time='';
    }


    //echo $urlname . $time .' '. $itemDate ."," . "$url\n";
    //echo "Update Time: $updateTime\n";

    $content .= $urlname . $time . ' '. $itemDate ."," . "$url\n";
    
    break;
    
}

}

echo $content;

// 要写入的文件路径
$filePath_tq = __DIR__ . '/tq.txt';

// 使用 file_put_contents() 函数写入内容，并覆盖原有内容
$result_tq = file_put_contents($filePath_tq, $content);

// 检查是否成功写入
if ($result_tq !== false) {
    echo "仅更新文件写入成功";
} else {
    echo "仅更新文件写入失败"; 
}

?>
