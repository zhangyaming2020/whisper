// 客服的id
var kf_id = 0;
var kf_name = '';
// 是否点击显示表情的标志
var flag = 1;
// 发送锁  标识
var sendLock = 0;
// 窗口大小标识
var size = 1;
// 是否显示默认的聊天记录
var commChat = 1;

// 连接服务器
if(config != undefined && config.socket != undefined){

    // 创建一个Socket实例
    var socket = new WebSocket('ws://' + config.socket);

    // 加锁
    lockTextarea();
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
        unlockTextarea();
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
                unlockTextarea();
                break;
            // 排队等待
            case 'wait':
                lockTextarea();
                if('暂时没有客服上班,请稍后再咨询。' == data.data.content){
                    socket.close();
                }
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


// 点击表情
document.getElementById('up-face').onclick = function(e){
    e.stopPropagation();
    if(1 == flag){
        showFaces();
        document.getElementById('face-box').style.display = 'block';
        flag = 2;
    }else{
        document.getElementById('face-box').style.display = 'none';
        flag = 1;
    }
};

// 监听点击旁边关闭表情
document.addEventListener("click",function(){
    if(2 == flag){
        document.getElementById('face-box').style.display = 'none';
        flag = 1;
    }
});

// 点击发送消息
document.getElementById('send').onclick = function(){
    sendMsg();
    document.getElementById('msg').value = '';
    // 滚动条自动定位到最底端
    wordBottom();
};

// 改变窗体事件
window.onresize = function(){
    if(1 == size){
        size = 2;
        document.getElementById('face-box').style.top = '58%';
        document.getElementById('chat-content-box').style.height = '75%';
    }else if(2 == size){
        size = 1;
        document.getElementById('face-box').style.top = '190px';
        document.getElementById('chat-content-box').style.height = '60%';
    }
};

// 图片 文件上传
layui.use(['upload', 'layer'], function () {
    var upload = layui.upload;
    var layer = layui.layer;

    // 执行实例
    var uploadInstImg = upload.render({
        elem: '#up-image' // 绑定元素
        , accept: 'images'
        , exts: 'jpg|jpeg|png|gif'
        , url: '/index/upload/uploadImg' // 上传接口
        , done: function (res) {

            sendMsg('img[' + res.data.src + ']');
            showBigPic();
        }
        , error: function () {
            // 请求异常回调
        }
    });

});

// 获取时间
function getTime(){
    var myDate = new Date();
    var hour = myDate.getHours();
    var minute = myDate.getMinutes();
    if(hour < 10) hour = '0' + hour;
    if(minute < 10) minute = '0' + minute;

    return hour + ':' + minute;
}

// 展示系统消息
function showSystem(msg){
    var _html = document.getElementById('chat-list').innerHTML;
    _html += '<div class="whisper-chat-system"><span>' + msg.content + '</span></div>';

    document.getElementById('chat-list').innerHTML = _html;
}


// 发送信息
function sendMsg(sendMsg){

    if(1 == sendLock){
        return false;
    }

    var msg = (typeof(sendMsg) == 'undefined') ? document.getElementById('msg').value : sendMsg;
    if('' == msg){
        return false;
    }

    var _html = document.getElementById('chat-list').innerHTML;
    var time = getTime();
    var content = replaceContent(msg);

    _html += '<div class="chat-mine">';
    _html += '<div class="author-name">我 ';
    _html += '<small class="chat-date">' + time + '</small>';
    _html += '</div><div class="chat-text">' + content + '</div></div>';

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
        localMsg.push({type: 'mine', name: '我', time: time, content: content});

        cacheChat({key: key, data: localMsg});
    }

    document.getElementById('chat-list').innerHTML = _html;

    // 滚动条自动定位到最底端
    wordBottom();

    showBigPic();
}

// 展示发送来的消息
function showMsg(info, flag){
    // 清除系统消息
    document.getElementsByClassName('whisper-chat-system').innerHTML = '';

    var _html = document.getElementById('chat-list').innerHTML;
    var content = replaceContent(info.content);
    _html += '<div class="chat-other">';
    _html += '<div class="author-name">' + info.name + ' ';
    _html += '<small class="chat-date">' + info.time + '</small>';
    _html += '</div><div class="chat-text">' + content + '</div></div>';

    document.getElementById('chat-list').innerHTML = _html;

    showBigPic();
    // 滚动条自动定位到最底端
    wordBottom();
    // 储存我收到的信息
    var key = kf_id + '-' + config.uid;
    if(typeof(Storage) !== "undefined" && typeof(flag) == "undefined"){
        var localMsg = getCache(key);
        if(localMsg == null || localMsg.length == 0){
            localMsg = [];
        }
        localMsg.push({type: 'other', name: info.name, time: info.time, content: content});

        cacheChat({key: key, data: localMsg});
    }
}

// 展示表情数据
function showFaces(){
    var alt = getFacesIcon();
    var _html = '<ul>';
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
    document.getElementById('msg').focus();
    document.getElementById('face-box').style.display = 'none';
    flag = 1;
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

            _html += '<div class="chat-mine">';
            _html += '<div class="author-name">' + item.name + ' ';
            _html += '<small class="chat-date">' + item.time + '</small>';
            _html += '</div><div class="chat-text">' + item.content + '</div></div>';
        }else if('other' == item.type){

            _html += '<div class="chat-other">';
            _html += '<div class="author-name">' + item.name + ' ';
            _html += '<small class="chat-date">' + item.time + '</small>';
            _html += '</div><div class="chat-text">' + item.content + '</div></div>';
        }
    }

    document.getElementById('chat-list').innerHTML = _html;

    showBigPic();
    // 滚动条自动定位到最底端
    wordBottom();
}

// 锁住输入框
function lockTextarea(){
    sendLock = 1;
    document.getElementById('msg').setAttribute('readonly', 'readonly');
}

// 解锁输入框
function unlockTextarea(){
    sendLock = 0;
    document.getElementById('msg').removeAttribute('readonly');
}

// 双击图片
function showBigPic(){
    layui.use('jquery', function(){
        var $ = layui.jquery;

        $(".layui-whisper-photos").on('click', function () {
            var src = this.src;
            layer.photos({
                photos: {
                    data: [{
                        "alt": "大图模式",
                        "src": src
                    }]
                }
                , shade: 0.5
                , closeBtn: 2
                , anim: 0
                , resize: false
                , success: function (layero, index) {

                }
            });
        });
    });
}

// 对话框定位到最底端
function wordBottom(){
    var ex = document.getElementById("chat-list");
    ex.scrollTop = ex.scrollHeight;
}