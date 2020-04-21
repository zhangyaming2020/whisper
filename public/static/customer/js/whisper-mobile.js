// 客服的id
var kf_id = 0;
var kf_name = '';
// 是否点击显示表情的标志
var flag = 1;
// 发送锁  标识
var sendLock = 0;
// 是否显示默认的聊天记录
var commChat = 1;

// 连接服务器
if(config != undefined && config.socket != undefined){

    // 创建一个Socket实例
    var socket = new WebSocket('ws://' + config.socket);

    // 加锁
    lockInput();
    //showSystem({content: '连接中...'});
    document.getElementById('title').innerText = '连接中...';

    // 打开Socket
    socket.onopen = function(res) {
        console.log('握手成功');
        // 登录
        var login_data = '{"type":"userInit", "uid": ' + config.uid + ', "name" : "' + config.name +
            '", "avatar" : "' + config.avatar + '", "group" : ' + config.group + '}';
        socket.send(login_data);

        // 解锁
        unlockInput();
    };

    // 监听消息
    socket.onmessage = function(res) {
        var data = eval("("+res.data+")");
        switch(data['message_type']){
            // 服务端ping客户端
            case 'ping':
                socket.send('{"type":"ping"}');
                break;
            // 已经被分配了客服
            case 'connect':
                kf_id = data.data.kf_id;
                kf_name = data.data.kf_name;
                showSystem({content: '客服 ' + data.data.kf_name + ' 为您服务'});
                document.getElementById('title').innerHTML = '与 ' + kf_name + ' 交流中';
                if(1 == commChat){
                    showChatLog();
                }
                unlockInput();
                break;
            // 排队等待
            case 'wait':
                lockInput();
                document.getElementById('title').innerHTML = '请稍后再来';
                showSystem(data.data);
                break;
            // 监测聊天数据
            case 'chatMessage':
                showMsg(data.data);
                break;
            // 问候语
            case 'helloMessage':
                showMsg(data.data, 1);
                break;
            // 转接
            case 'relinkMessage':
                commChat = 2;
                document.getElementById('title').innerHTML = '正在转接中...';
                break;
        }
    };

    // 监听错误
    socket.onerror = function(err){
        showSystem({content: '连接失败'});
        document.getElementById('title').innerText = '连接失败';
    };
}

// 图片 文件上传
layui.use(['upload'], function () {
    var upload = layui.upload;

    // 执行实例
    var uploadInstImg = upload.render({
        elem: '#up-image' // 绑定元素
        , accept: 'images'
        , exts: 'jpg|jpeg|png|gif'
        , url: '/index/upload/uploadImg' // 上传接口
        , done: function (res) {

            sendMsg('img[' + res.data.src + ']');
        }
        , error: function () {
            // 请求异常回调
        }
    });

    $('.layui-upload-file').hide();
});

$(function(){
	
	// 修复IOS下输入法遮挡问题
	$('input[type="text"],textarea').on('click', function () {
		var target = this;
		setTimeout(function(){
			target.scrollIntoViewIfNeeded();
		}, 200);
	});

    // 监听输入改变发送按钮
    $("#msg").bind('input porpertychange', function(){

        if($("#msg").val().length > 0){
            $(".layim-send").removeClass('layui-disabled');
        }else{
            $(".layim-send").addClass('layui-disabled');
        }
    });

    // 点击发送
    $("#send").click(function(){
        sendMsg();
    });

    // 点击表情
    $('#up-face').click(function(e){
        e.stopPropagation();
        if(1 == flag){
            showFaces();
            $('#face-box').show();
            flag = 2;
        }else{
            $('#face-box').hide();
            flag = 1;
        }
    });

    // 监听点击旁边关闭表情
    document.addEventListener("click",function(){
        if(2 == flag){
            document.getElementById('face-box').style.display = 'none';
            flag = 1;
        }
    });
});

// 发送消息
function sendMsg(sendMsg){

    if(1 == sendLock){
        return false;
    }

    var msg = (typeof(sendMsg) == 'undefined') ? $('#msg').val() : sendMsg;
    if('' == msg){
        return false;
    }

    var _html = $("#chat-list").html();
    var time = getTime();
    var content = replaceContent(msg);

    _html += '<li class="layim-chat-system"><span>' + time + '</span></li>'
    _html += '<li class="layim-chat-li layim-chat-mine">';
    _html += '<div class="layim-chat-user">';
    _html += '<img src="' + config.avatar + '"><cite>我</cite></div>';
    _html += '<div class="layim-chat-text">' + content + ' </div></li>';

    $('#chat-list').html(_html);

    // 发送消息
    socket.send(JSON.stringify({
        type: 'chatMessage',
        data: {to_id: kf_id, to_name: kf_name, content: msg, from_name: config.name,
            from_id: config.uid, from_avatar: config.avatar}
    }));

    // 储存我发出的信息
    var key = kf_id + '-' + config.uid;
    if(typeof(Storage) !== "undefined"){
        var localMsg = getCache(key);
        if(localMsg == null || localMsg.length == 0){
            localMsg = [];
        }
        localMsg.push({type: 'mine', name: '我', time: time, content: content, avatar: config.avatar});

        cacheChat({key: key, data: localMsg});
    }

    $('#msg').val('');
    $(".layim-send").addClass('layui-disabled');

    // 滚动条自动定位到最底端
    wordBottom();
}

// 展示发送来的消息
function showMsg(info, flag){

    var _html = $("#chat-list").html();
    var content = replaceContent(info.content);

    _html += '<li class="layim-chat-system"><span>' + info.time + '</span></li>';
    _html += '<li class="layim-chat-li">';
    _html += '<div class="layim-chat-user">';
    _html += '<img src="' + info.avatar + '"><cite>' + info.name + '</cite></div>';
    _html += '<div class="layim-chat-text">' + content + '</div></li>';

    $('#chat-list').html(_html);

    // 滚动条自动定位到最底端
    wordBottom();

    // 储存我收到的信息
    var key = kf_id + '-' + config.uid;
    if(typeof(Storage) !== "undefined" && typeof(flag) == "undefined"){
        var localMsg = getCache(key);
        if(localMsg == null || localMsg.length == 0){
            localMsg = [];
        }
        localMsg.push({type: 'other', name: info.name, time: info.time, content: content, avatar: info.avatar});

        cacheChat({key: key, data: localMsg});
    }
}

// 获取时间
function getTime(){
    var myDate = new Date();
    var hour = myDate.getHours();
    var minute = myDate.getMinutes();
    if(hour < 10) hour = '0' + hour;
    if(minute < 10) minute = '0' + minute;

    return hour + ':' + minute;
}

// 缓存聊天数据 [本地存储策略]
function cacheChat(obj){
    if(typeof(Storage) !== "undefined"){
        localStorage.setItem(obj.key, JSON.stringify(obj.data));
    }
}

// 从本地缓存中，拿出数据
function getCache(key){
    return JSON.parse(localStorage.getItem(key));
}

// 对话框定位到最底端
function wordBottom() {
    // 滚动条自动定位到最底端
    var box = $(".layim-chat-main");
    box.scrollTop(box[0].scrollHeight);
}

// 锁住输入框
function lockInput(){
    sendLock = 1;
    document.getElementById('msg').setAttribute('readonly', 'readonly');
}

// 解锁输入框
function unlockInput(){
    sendLock = 0;
    document.getElementById('msg').removeAttribute('readonly');
}

// 展示表情数据
function showFaces(){
    var alt = getFacesIcon();
    var _html = '<ul class="layui-layim-face">';
    var len = alt.length;
    for(var index = 0; index < len; index++){
        _html += '<li title="' + alt[index] + '" onclick="checkFace(this)"><img src="/static/customer/images/face/'+ index + '.gif" /></li>';
    }
    _html += '</ul>';

    document.getElementById('face-box').innerHTML = _html;
}

// 选择表情
function checkFace(obj){
    var msg = document.getElementById('msg').value;
    document.getElementById('msg').value = 	msg + ' face' + obj.title + ' ';
    document.getElementById('face-box').style.display = 'none';
    $(".layim-send").removeClass('layui-disabled');
    flag = 1;
}

// 系统消息
function showSystem(info){

    $("#chat-list").append('<li class="layim-chat-system"><span>' + info.content + '</span></li>');
}

// 展示本地聊天缓存
function showChatLog(){

    var chatLog = getCache(kf_id + '-' + config.uid);
    if(chatLog == null || chatLog.length == 0){
        return ;
    }

    var _html = '';
    var len = chatLog.length;
    for(var i = 0; i < len; i++){
        var item = chatLog[i];

        if('mine' == item.type){

            _html += '<li class="layim-chat-system"><span>' + item.time + '</span></li>'
            _html += '<li class="layim-chat-li layim-chat-mine">';
            _html += '<div class="layim-chat-user">';
            _html += '<img src="' + item.avatar + '"><cite>'+ item.name + '</cite></div>';
            _html += '<div class="layim-chat-text">' + item.content + ' </div></li>';

        }else if('other' == item.type){

            _html += '<li class="layim-chat-system"><span>' + item.time + '</span></li>';
            _html += '<li class="layim-chat-li">';
            _html += '<div class="layim-chat-user">';
            _html += '<img src="' + item.avatar + '"><cite>' + item.name + '</cite></div>';
            _html += '<div class="layim-chat-text">' + item.content + '</div></li>';
        }
    }

    document.getElementById('chat-list').innerHTML = _html;

    // 滚动条自动定位到最底端

    wordBottom();
}

// 退出
function loginOut(){
    window.history.go(-1);
}