<?php
$arr_urls = array(
    '1台' => 'https://www.weather.com.cn/pubm/zhaowen.htm',
    '2台' => 'https://www.weather.com.cn/pubm/diyiyinxiang.htm',
    '1台 12:30' => 'https://www.weather.com.cn/pubm/wujian.htm',
    '4台' => 'https://www.weather.com.cn/pubm/cctv4.htm',
    '5台' => 'https://www.weather.com.cn/pubm/tiyu.htm',
    '7台' => 'https://www.weather.com.cn/pubm/cctv7.htm',
    '联播 预报' => 'https://www.weather.com.cn/pubm/video_lianbo_2021.htm',
    //'河南 预报' => 'https://raw.githubusercontent.com/nowvip/weather/main/php/hatq_json.txt',
);

$content='';
foreach($arr_urls as $urlname => $urls){
// 使用 cURL 获取 JSON 数据
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urls);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 忽略证书验证

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
//preg_match('/getLbDatas\((\{.*\})\)/', $jsonData, $matches);
preg_match('/getLbDatas\(\s*(\{.*?\})\s*\)/s', $jsonData, $matches);
	
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
    if($updateTime){
         //$itemDate = explode(' ', $updateTime)[0]; // 提取日期部分
         $itemDate = substr($updateTime, 5, 5);//从第6个字符开始，提取5个字符,提取月份和日期部分
         $itemTime = substr($updateTime, 11, 5);//从第6个字符开始，提取5个字符,提取月份和日期部分
    }else{
	 $itemDate = '';
	 $itemTime = '';    
    }   
    $url = $item['url'];
    $title = $item['title'];
    //echo $title .'\n';
    $time='';
    // 使用正则表达式匹配时间格式
    if (preg_match('/\d{2}:\d{2}/', $title, $matches)) {
        $time = $matches[0];        
        $time = ' ' . $time;
    } else {       
        if (preg_match('/\d{2}:\d{2}/', $urlname, $matches)) {
	        $time = $matches[0];        
	        $time = '';        
        } elseif(strpos($urlname, '预报') === false ) {
        	$time= ' ' . $itemTime;
        }
    }

    //echo $urlname . $time .' '. $itemDate ."," . "$url\n";
    //echo "Update Time: $updateTime\n";

    $content .= $urlname . $time . ' '. $itemDate ."," . "$url\n";
    
    break;
    
}

}

//echo $content;

//排序
// 将内容按行拆分
$lines = explode("\n", $content);

// 定义一个函数用于将标题转换为时间戳
function parseDateTime($title) {
    if (preg_match('/(\d{2}:\d{2}) (\d{2}-\d{2})/', $title, $matches)) {
        $time = $matches[1];
        $date = $matches[2];
        $datetime = DateTime::createFromFormat('d-m H:i', $date . ' ' . $time);
        return $datetime ? $datetime->getTimestamp() : 0;
    } elseif (preg_match('/(\d{2}-\d{2})/', $title, $matches)) {
        $date = $matches[1];
        $datetime = DateTime::createFromFormat('d-m H:i', $date . ' 00:00');
        return $datetime ? $datetime->getTimestamp() : 0;
    } else {
        return 0;
    }
}

// 对内容进行排序
usort($lines, function($a, $b) {
    $titleA = explode(',', $a)[0];
    $titleB = explode(',', $b)[0];
    $timestampA = parseDateTime($titleA);
    $timestampB = parseDateTime($titleB);
    return $timestampB - $timestampA; // 从大到小排序
});

// 获取最大日期和次大日期
$dates = array();
foreach ($lines as $line) {
    $title = explode(',', $line)[0];
    if (preg_match('/(\d{2}-\d{2})/', $title, $matches)) {
        $date = $matches[1];
        if (!in_array($date, $dates)) {
            $dates[] = $date;
        }
    }
}
$dates = array_unique($dates);
rsort($dates); // 从大到小排序

// 确定最大日期和次大日期
$maxDate = isset($dates[0]) ? $dates[0] : '';
$secondMaxDate = isset($dates[1]) ? $dates[1] : '';

// 替换最大日期为“(今天)”和次大日期为“(昨天)”
$lines = array_map(function($line) use ($maxDate, $secondMaxDate) {
    $parts = explode(',', $line);

    // 检查数组长度，确保有两个部分
    if (count($parts) < 2) {
        return $line; // 如果数组长度不足2，返回原行
    }

    $title = $parts[0];
    $url = $parts[1];

    if (preg_match('/(\d{2}-\d{2})/', $title, $matches)) {
        $date = $matches[1];
        if ($date === $maxDate) {
            $title = preg_replace('/\d{2}-\d{2}/', '(今天)', $title);
        } elseif ($date === $secondMaxDate) {
            $title = preg_replace('/\d{2}-\d{2}/', '(昨天)', $title);
        }
    } else {
        // 对于没有日期的标题，直接标记为“(未知)”
        $title = $title . '(未知)';
    }

    // 特殊处理“联播 预报”的排序
    $priority = 2; // 默认优先级
    if (strpos($title, '预报') !== false) {
        if (strpos($title, '(今天)') !== false) {
            $priority = 1; // 高优先级，排在最上面
        } elseif (strpos($title, '(昨天)') !== false || strpos($title, '(未知)') !== false) {
            $priority = 3; // 低优先级，排在最下面
        }
    }

    return ['title' => $title, 'url' => $url, 'priority' => $priority];
}, $lines);

// 过滤掉空值
$lines = array_filter($lines);

// 按优先级排序，优先级1排在最上面，优先级3排在最下面
usort($lines, function($a, $b) {
    if (isset($a['priority']) && isset($b['priority'])) {
        if ($a['priority'] === $b['priority']) {
            return parseDateTime($b['title']) - parseDateTime($a['title']); // 相同优先级按时间排序
        }
        return $a['priority'] - $b['priority']; // 按优先级排序
    }
    return 0; // 如果没有优先级则不排序
});

// 重新组合内容
$content = implode("\n", array_map(function($line) {
    return $line['title'] . ',' . $line['url'];
}, $lines));


// 输出结果
echo $content ."\n";

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
