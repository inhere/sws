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

/**
 * SwSocket
 * @param options
 * @constructor
 */
function SwSocket(options) {
  /**
   * 选项
   * @type {object}
   */
  this.options = Object.assign({
    defaultRoute: null,
    noFoundRoute: null,
    reconnect: true
  }, options)

  /**
   * server 地址
   * @type {string}
   */
  this.url = null

  /**
   * WS 连接
   * @type {WebSocket}
   */
  this.ws = null

  // 所在房间
  this.room = ""

  // 接收文件时用于暂存接收文件
  this.fileData = {}

  /**
   * routes map
   * @type {Object}
   */
  this.routes = {}
}

//继承自事件处理器，提供绑定事件和触发事件的功能
SwSocket.prototype = new EventEmitter()

/*************************服务器连接部分***************************/

/**
 *
 * @param {string} host
 * @param {int}    port
 * @param {string} protocol
 */
SwSocket.prototype.connect = function (host, port, protocol = 'ws') {
  this.connectByUrl(protocol + '://' + host + (port ? ':' + port : ''))
}

// e.g `ws://domain.com/chat`
SwSocket.prototype.reconnect = function () {
  if (this.url) {
    this.createByUrl(this.url)
  }
}

SwSocket.prototype.connectByUrl = function (url, room) {
  let ws, that = this

  this.url = url
  ws = this.ws = new WebSocket(url)
  room = this.room = room || ""

  ws.onopen = function (event) {
    debug("OPEN: 连接服务器成功", event)

    ws.send(JSON.stringify({
      "eventName": "__join",
      "data": {
        "room": room
      }
    }))
    
    that.fire("opened", ws)
  }

  ws.onmessage = function (event) {
    let data = event.data
    that.fire("message", event.data, ws)

    debug("DATA: 收到服务端数据", event)

    try {
      data = JSON.parse(data)

      // if (data.command) {
      //   that.fire(data.command, data, ws)
      // } else {
      //   that.fire("json_message", data, ws)
      // }

      that.dispatch(data)
    } catch (err) {
      that.fire("text_message", event.data, ws)
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

  this.dispatch = function (data) {
    try {
      if (data.command) {
        let cmd = data.command

        if (this.routes[cmd]) {
          let handler = this.routes[cmd]
          handler(data, this)
        } else {
          this.fire('notFound', cmd)
        }
      } else {
        this.fire("json_message", data, this)
      }
    } catch (err) {
      console.log(err)
      this.fire('error', 'dispatch ERROR:' + err.message)
    }
  }
}

SwSocket.prototype.send = function (data) {
  let ws = this.ws

  if (!ws) {
    this.fire('error', 'please connect to server before send message.')
    return
  }

  if (ws.readyState === ws.CLOSED) {
    this.fire('error', 'connection has been closed')
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

SwSocket.prototype.readImage = function (evt) {
  let reader = new FileReader()

  reader.onload = function(event) {
    let contents = event.target.result
    let a = new Image()
    a.src = contents

    document.body.appendChild(a)
  }

  reader.readAsDataURL(evt.data)
}

SwSocket.prototype.destroy = function () {
  let ws = this.ws

  if (ws) {
    ws.onmessage = ws.onclose = ws.onerror = null;
  }

  this.clearEvents()
}

SwSocket.prototype.command = function (route, cb) {
  this.routes[route] = cb
}

SwSocket.prototype.commands = function (routes) {
  const app = this

  Object.keys(routes).forEach(function (route) {
    app.routes[route] = routes[route]
  })
}


/*
 function heartbeat() {
 this.isAlive = true;
 }

 wss.on('connection', function connection(ws) {
 ws.isAlive = true;
 ws.on('pong', heartbeat);
 });

 const interval = setInterval(function ping() {
 wss.clients.forEach(function each(ws) {
 if (ws.isAlive === false) return ws.terminate();

 ws.isAlive = false;
 ws.ping('', false, true);
 });
 }, 30000);
 */