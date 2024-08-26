<?php
$arr_urls = array(
    '朝闻预报' => 'https://www.weather.com.cn/pubm/zhaowen.htm',
    '午间预报' => 'https://www.weather.com.cn/pubm/wujian.htm',
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
    //$title = $item['title'];
    
    //echo $urlname . ' '. $itemDate ."," . "$url\n";
    //echo "Update Time: $updateTime\n";

    $content .= $urlname . ' '. $itemDate ."," . "$url\n";
    
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


/*
// 获取环境变量
$origin_url = getenv('ORIGIN_URL');

if (empty($origin_url)) {
    die('The Origin URL is not set or is empty');
}

// 从 源URL 获取文件内容
$origin_text = file_get_contents($origin_url);

// 检查是否成功获取内容
if ($origin_text !== false) {
    // 输出内容
    echo $origin_text;

    // 去掉$content开头和结尾的空白行，以防止多余的换行
    $content = trim($content);
    
    // 正则表达式匹配并替换“天气节目,#genre#”分组内容
    $pattern = '/(天气预报,#genre#\R)([\s\S]*?)(?=\R\S.*#genre#|\z)/';
    $replacement = '$1' . $content;
    
    //echo $replacement;
    
    $origin_text = preg_replace($pattern, $replacement, $origin_text);
    
    
    // 输出结果
    echo $origin_text;
    
    // 要写入的文件路径
    $filePath = __DIR__ . '/result.txt';
    
    // 使用 file_put_contents() 函数写入内容，并覆盖原有内容
    $result = file_put_contents($filePath, $origin_text);
    
    // 检查是否成功写入
    if ($result !== false) {
        echo "更新文件写入成功";
    } else {
        echo "更新文件写入失败"; 
    }

}else {
        die('Failed to get content from the Origin URL'); 
}

*/
    
/*
// 要写入的文件路径
$filePath = __DIR__ . '/result.txt';
#echo $filePath;

// 将内容追加到文件中
$result = file_put_contents($filePath, $content);

if ($result === false) {
    echo "Failed to write to file: " . print_r(error_get_last(), true);
} else {
    echo "URL has been written to $filePath\n";
}
*/
?>
