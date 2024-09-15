<?php
// 获取 JSON 数据
$url = 'https://m.weibo.cn/api/container/getIndex?containerid=231522type%3D1%26t%3D10%26q%3D%23%E9%A2%84%E6%8A%A5%E5%A4%A9%E5%A4%A9%E7%9C%8B%23&isnewpage=1&luicode=10000011&lfid=100103type%3D1%26q%3D%23%E9%A2%84%E6%8A%A5%E5%A4%A9%E5%A4%A9%E7%9C%8B%23&page_type=searchall';
$json = file_get_contents($url);

// 检查获取数据是否成功
if ($json === false) {
    die('Error fetching JSON data.');
}

// 解析 JSON 数据
$data = json_decode($json, true);

// 检查解析是否成功
if ($data === null) {
    die('Error decoding JSON data.');
}

//print_r($data) ;

// 提取第一个 card_group 元素
$cardGroup = $data['data']['cards'][10]['card_group'][0]['mblog'];

// 提取 created_at, 原始日期时间字符串 "Sat Sep 07 18:50:01 +0800 2024"
$createdAt = $cardGroup['created_at'];

// 创建 DateTime 对象
$dateTime = DateTime::createFromFormat('D M d H:i:s O Y', $createdAt);

// 检查是否成功解析
if ($dateTime === false) {
    die('Error parsing date time.');
}

// 格式化为目标格式
$createdAt = $dateTime->format('Y-m-d H:i:s');


// 提取source
$source = $cardGroup['page_info']['content1'];

// 检查 source 并提取 mp4_720p_mp4
if ($source === '河南气象的微博视频') {
    $mp4Url = $cardGroup['page_info']['urls']['mp4_720p_mp4'];
    //echo "Created At:" .$createdAt. "\n";
    //echo "Source: " . $source . "\n";
    //echo $mp4Url ."\n";

    // 创建数据字符串
    $dataString = 'getLbDatas({"data":[{"url":"https://mirror.ghproxy.com/https://raw.githubusercontent.com/nowvip/weather/main/videos/hatq.mp4","title":"河南卫视新闻联播天气预报","updateTime":"' . $createdAt . '"}]})';
    
    // 文件路径
    $file = 'php/hatq_json.txt';
    
    // 将数据写入文件
    file_put_contents($file, $dataString);

    
    // 在 GitHub Actions 中输出 video_url 变量
    // 使用 json_encode() 输出 URL，确保其完整性
    echo json_encode(['video_url' => $mp4Url]);

    echo "updateTime:" . $createdAt;
    //echo 'getLbDatas({"data":[{"url":"'. $mp4Url .'","updateTime":"'.$createdAt.'"}]})';
} else {
    //echo "Source does not match.\n";
    // 创建数据字符串
    $dataString = 'getLbDatas({"data":[{"url":"https://mirror.ghproxy.com/https://raw.githubusercontent.com/nowvip/weather/main/videos/hatq.mp4","title":"河南卫视新闻联播天气预报","updateTime":""}]})';
    echo json_encode(['video_url' => '']);
}
?>
