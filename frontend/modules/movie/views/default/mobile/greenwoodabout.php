<?php
/**
 * Created by PhpStorm.
 * User: 44844
 * Date: 2019/8/18
 * Time: 15:36
 */
use common\models\Pay\Wechat;

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>青木文化传媒</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="./layui/css/layui.css">
    <script src="./layui/layui.js"></script>
    <style>
        .footer{
            position: fixed;
            bottom: 0px;
            width: 100%;
            height: 30px;
            padding: 5px;
            border-top: 1px solid #801e99;
            text-align: center;
            color: white;
            background: #031871;

            line-height: 30px;
        }
    </style>
</head>
<body>
<div >

    <div class="layui-row" >
            <div >
                <img class="layui-col-xs12" src="http://images.ahwes.com/GreenWood.jpg" />
            </div>

    </div>


</div>
</body>
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>

<script type="text/javascript">

    wx.config({
        //debug: true,
        appId: '<?php echo $data["appId"];?>',
        timestamp: <?php echo $data["timestamp"];?>,
        nonceStr: '<?php echo $data["nonceStr"];?>',
        signature: '<?php echo $data["signature"];?>',
        jsApiList: [
            'checkJsApi',//判断当前客户端版本是否支持指定JS接口
            'updateAppMessageShareData',//分享到朋友圈
            'updateTimelineShareData',
            'onMenuShareAppMessage',//老版本分享接口。
            'onMenuShareTimeline'//老版本分享接口。
        ]
    });

wx.ready(function () {

    wx.updateAppMessageShareData({
        title: '青木文化传媒', // 分享标题
        desc: '您身边的视频专家', // 分享描述
        link: 'https://www.ahwes.com/movie-default-greenwoodabout.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
         imgUrl: 'https://www.ahwes.com/xinpian/images/growinglogo.png', // 分享图标
        success: function () {
            // 设置成功
        }
    });

    wx.updateTimelineShareData({
        title: '青木文化传媒', // 分享标题
        link: 'https://www.ahwes.com/movie-default-greenwoodabout.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
         imgUrl: 'https://www.ahwes.com/xinpian/images/growinglogo.png', // 分享图标
        success: function () {
            // 设置成功
        }
    });

    wx.onMenuShareAppMessage({
        title: '青木文化传媒', // 分享标题
        desc: '您身边的视频专家', // 分享描述
        link: 'https://www.ahwes.com/movie-default-greenwoodabout.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: 'https://www.ahwes.com/xinpian/images/growinglogo.png', // 分享图标
        type: '', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
// 用户点击了分享后执行的回调函数
        }
    });

    wx.onMenuShareTimeline({
        title: '青木文化传媒', // 分享标题
        link: 'https://www.ahwes.com/movie-default-newabout.html', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: 'https://www.ahwes.com/xinpian/images/growinglogo.png', // 分享图标
        success: function () {
            // 设置成功
        }
    });

});

</script>
</html>
