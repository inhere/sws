/**
 * Created by inhere on 2017-09-06.
 * @link https://github.com/LingyuCoder/SkyRTC-client/blob/master/SkyRTC-client.js
 */

function debug(...args) {
  console.info(...args)
}

/**********************************************************
 *                       事件处理器
 **********************************************************/

function EventEmitter() {
  this.events = {}
}

//绑定事件函数
EventEmitter.prototype.on = function (eventName, callback) {
  this.events[eventName] = this.events[eventName] || []
  this.events[eventName].push(callback)
}

//触发事件函数
EventEmitter.prototype.fire  = function (eventName, _) {
  let events = this.events[eventName],
    args = Array.prototype.slice.call(arguments, 1),
    i, m

  if (!events) {
    return false
  }

  for (i = 0, m = events.length; i < m; i++) {
    events[i].apply(null, args)
  }

  return true
}

EventEmitter.prototype.clearEvents = function () {
  this.events = {}
}

/**********************************************************
 *                   流及信道建立部分                     
 **********************************************************/

/*******************基础部分*********************/
function SwSocket(options) {
  /**
   *
   * @type {*}
   */
  this.options = options || {}

  /**
   * @type {string}
   */
  this.url = null

  /**
   *
   * @type {WebSocket}
   */
  this.ws = null

  //所在房间
  this.room = ""

  //接收文件时用于暂存接收文件
  this.fileData = {}
}

//继承自事件处理器，提供绑定事件和触发事件的功能
SwSocket.prototype = new EventEmitter()

/*************************服务器连接部分***************************/

SwSocket.prototype.connect = function (host, port, protocol = 'ws') {
  this.connectByUrl(protocol + '://' + host + (port ? ':' + port : ''))
}

SwSocket.prototype.connectByUrl = function (url, room) {
  let ws,
    that = this
  room = room || ""

  this.url = url
  ws = this.ws = new WebSocket(url)
  
  ws.onopen = function (event) {
    debug("OPEN: 连接服务器成功", event)

    ws.send(this.jsonEncode({
      "eventName": "__join",
      "data": {
        "room": room
      }
    }))
    
    that.fire("ws_opened", ws)
  }

  ws.onmessage = function (event) {
    let data = event.data
    that.fire("message", event.data, ws)

    debug("DATA: 收到服务端数据", event)

    try {
      data = JSON.parse(data)
    } catch (err) {
      that.fire("text_message", event.data, ws)
    }

    if (data.event) {
      that.fire(data.event, data, ws)
    } else {
      that.fire("json_message", data, ws)
    }
  }

  ws.onerror = function (event) {
    debug("ERROR: 发生错误", event)
    that.fire("error", event.reason ? event.reason : 'error on the webSocket')
    this.destroy()
  }

  ws.onclose = function (event) {
    debug("CLOSE: 关闭连接", event)
    that.fire('closed', ws, event)
  }

  this.on('_peers', function (data) {
    //获取所有服务器上的
    that.connections = data.connections
    that.me = data.you
    that.fire("get_peers", that.connections)
    that.fire('connected', ws)
  })
}

SwSocket.prototype.send = function (data) {
  let ws = this.ws

  if (!ws) {
    this.fire('error', 'please connect to server before send message.')
    return
  }

  if (ws.readyState === ws.CLOSED) {
    this.fire('error', 'connect is closed')
    return
  }

  ws.send(data)
}

SwSocket.prototype.close = function () {
  let ws = this.ws

  if (!ws) {
    this.fire('error', 'the ws server is not connecting')
    return
  }

  ws.close()
}

SwSocket.prototype.jsonEncode = function (data) {
  return JSON.stringify(data)
}

/**
 * @param data {string}
 */
SwSocket.prototype.jsonDecode = function (data) {
  return JSON.parse(data)
}

SwSocket.prototype.destroy = function () {
  let ws = this.ws

  if (ws) {
    ws.onmessage = ws.onclose = ws.onerror = null;
  }
}