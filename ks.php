<?php
$userIdFile = 'userid.txt';  // 包含用户ID的文件，每行一个ID

// 从文件中读取用户ID
$userIds = file($userIdFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (!$userIds) {
    echo '用户ID文件为空。' . PHP_EOL;
    exit;
}

// 选择一个随机用户ID
$randomUserId = $userIds[array_rand($userIds)];

$cookies = [
    'did' => 'web_a92953c8111a475ca61ff3427d1ff74a',
    'didv' => '1705578890000',
    'kpf' => 'PC_WEB',
    'kpn' => 'KUAISHOU_VISION',
];

$headers = [
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36',
    'Content-Type' => 'application/json',
];

$data = '{"operationName":"visionProfilePhotoList","variables":{"userId":"' . $randomUserId . '","pcursor":"1.70168125806E12","page":"profile"},"query":"fragment photoContent on PhotoEntity {\n  __typename\n  id\n  duration\n  caption\n  originCaption\n  likeCount\n  viewCount\n  commentCount\n  realLikeCount\n  coverUrl\n  photoUrl\n  photoH265Url\n  manifest\n  manifestH265\n  videoResource\n  coverUrls {\n    url\n    __typename\n  }\n  timestamp\n  expTag\n  animatedCoverUrl\n  distance\n  videoRatio\n  liked\n  stereoType\n  profileUserTopPhoto\n  musicBlocked\n}\n\nfragment recoPhotoFragment on recoPhotoEntity {\n  __typename\n  id\n  duration\n  caption\n  originCaption\n  likeCount\n  viewCount\n  commentCount\n  realLikeCount\n  coverUrl\n  photoUrl\n  photoH265Url\n  manifest\n  manifestH265\n  videoResource\n  coverUrls {\n    url\n    __typename\n  }\n  timestamp\n  expTag\n  animatedCoverUrl\n  distance\n  videoRatio\n  liked\n  stereoType\n  profileUserTopPhoto\n  musicBlocked\n}\n\nfragment feedContent on Feed {\n  type\n  author {\n    id\n    name\n    headerUrl\n    following\n    headerUrls {\n      url\n      __typename\n    }\n    __typename\n  }\n  photo {\n    ...photoContent\n    ...recoPhotoFragment\n    __typename\n  }\n  canAddComment\n  llsid\n  status\n  currentPcursor\n  tags {\n    type\n    name\n    __typename\n  }\n  __typename\n}\n\nquery visionProfilePhotoList($pcursor: String, $userId: String, $page: String, $webPageArea: String) {\n  visionProfilePhotoList(pcursor: $pcursor, userId: $userId, page: $page, webPageArea: $webPageArea) {\n    result\n    llsid\n    webPageArea\n    feeds {\n      ...feedContent\n      __typename\n    }\n    hostName\n    pcursor\n    __typename\n  }\n}\n"}';

$options = [
    'http' => [
        'header' => "User-Agent: {$headers['User-Agent']}\r\n" .
                    "Content-Type: {$headers['Content-Type']}\r\n" .
                    'Cookie: ' . http_build_query($cookies, '', '; '),
        'method' => 'POST',
        'content' => $data,
    ],
];

$context = stream_context_create($options);
$response = file_get_contents('https://www.kuaishou.com/graphql', false, $context);

$data_json = json_decode($response, true);
$data_list = $data_json['data']['visionProfilePhotoList']['feeds'];

// 检查是否有可用的视频
if (!empty($data_list)) {
    // 从列表中选择一个随机视频
    $randomVideo = $data_list[array_rand($data_list)];

    $videoUrl = $randomVideo['photo']['photoUrl'];
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    if ($type === 'video') {
        // 直接在浏览器输出视频
        header('Location: ' . $videoUrl);
        exit;
    } elseif ($type === 'text') {
        // 直接输出视频链接
        echo '视频链接: ' . $videoUrl . PHP_EOL;
    } else {
        // 输出随机选择的视频链接
        echo '随机视频链接: ' . $videoUrl . PHP_EOL;
    }
} else {
    echo '没有可用的视频。' . PHP_EOL;
}
?>