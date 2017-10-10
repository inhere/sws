function startTime() {
  let today = new Date();
  let h = today.getHours();
  let m = today.getMinutes();
  let s = today.getSeconds();// 在小于10的数字前加一个‘0’
  m = checkTime(m);
  s = checkTime(s);
  document.getElementById('txt').innerHTML = h + ":" + m + ":" + s;
  t = setTimeout(function () {
    startTime()
  }, 500);
}

function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}

// swsApp
// sws
const sws = {
  ws: null,
  url: null,
  error: null,
  isConnected: false,
  events: {
    open: null,
    message: function (data) {
      console.log('Received data: ', data)
    },
    close: null,
    error: function (err) {
      console.error('Error occurs: ' + err)
    },
    notFound: function (data) {
      console.log('notFound', data)
    },
    dataError: function (data) {
      console.log('dataError', data)
    },
  },

  //
  create(host, port, protocol = 'ws') {
    const url = protocol + '://' + host + (port ? ':' + port : '')

    return this.createByUrl(url)
  },
  // e.g `ws://domain.com/chat`
  createByUrl(url) {
    const self = this

    this.url = url
    this.isConnected = false

    try {
      const ws = new WebSocket(url)

      ws.addEventListener('open', function (evt) {
        console.info("OPEN: 连接服务器成功", evt)

        self.isConnected = true
        self.fireEvent('open')
      })

      ws.addEventListener('close', function (evt) {
        console.log("CLOSE", evt)
        self.isConnected = false
        self.fireEvent('close')
      })

      ws.addEventListener('error', function (evt) {
        console.error('ERROR: ' + evt.data)
        this.error = evt.data
        self.fireEvent('error', evt.data)
      })

      ws.addEventListener('message', function (evt) {
        console.log("DATA", evt)
        self.fireEvent('message', evt.data, self)
      })

      this.ws = ws
    } catch (ex) {
      console.log(ex)
    }

    return this
  },
  close() {
    if (!this.isConnected) {
      // this.error = 'the ws server is not connecting'
      return
    }

    if (this.ws.readyState === this.ws.CLOSED) {
//        this.error = 'connect is closed'
      return
    }

    this.ws.close()
  },
  addListener(name, handler) {
    this.events[name] = handler
  },
  fireEvent(name, ...args) {
    if (this.events[name]) {
      let cb = this.events[name]
      cb(...args)
    }
  },
  sendJson(text) {
    let data = {
      '_cmd': '/chat',
      text: text,
      id: 'clientID',
      date: Date.now()
    }

    this.send(JSON.stringify(data))
  },
  send: function (data) {
    if (!this.isConnected) {
      this.error = 'please connect to server before send message.'
      this.fireEvent('error', this.error)
      return
    }

    if (this.ws.readyState === this.ws.CLOSED) {
      this.error = 'connect is closed'
      this.fireEvent('error', this.error)
      return
    }

    this.ws.send(data)
  }
}

const vm = new Vue({
  el: '#app',
  data: function () {
    return {
      connection: null,
      disabled: {
        connect: false,
        disconnect: true,
        send: true
      },
      alertMsg: null,
      alertType: 'info',
      showAlert: false,
      message: '',
      wsUrl: 'ws://127.0.0.1:9501',
      messages: [],
      logs: []
    }
  },
  methods: {
    connect() {
      if (!this.wsUrl) {
        this.showMsg('Please input a ws server address')
        return
      }

      let that = this
      let app = sws.createByUrl(this.wsUrl)

      if (app.error) {
        this.showMsg(app.error, 'danger')
        return
      }

      app.addListener('message', this.onMessage)
      app.addListener('error', function (msg) {
        that.showMsg(msg, 'danger')
        that.disconnect()
      })

      this.connection = app

      // do something
      this.printLog('connect', 'Success connect to the server ' + this.wsUrl)
      this.disabled = {
        connect: true,
        disconnect: false,
        send: false,
        clear: false
      }
    },
    disconnect() {
      if (!this.connection) {
        this.showMsg('The connection has lost.', 'danger')
        return
      }

      this.connection.close()

      // do something
      // this.showMsg('Connection has been closed.')
      this.printLog('disconnect', 'disconnect from the server ' + this.wsUrl)
      this.disabled = {
        connect: false,
        disconnect: true,
        send: true
      }
    },
    onMessage(data, app) {
      console.log(data, app)

      this.printLog('received', data)

      let item = {
        role: 'server',
        time: new Date().toLocaleString(),
        data: data
      }

      this.messages.push(item)
    },
    send() {
      if (!this.connection) {
        this.showMsg('Please connect to ws server before send message.', 'danger')
        return
      }

      if (!this.message) {
        this.showMsg('Please input a message want to send.', 'warning')
        return
      }

      let msg = this.message.trim()

      if (!msg) {
        this.showMsg('Cannot send a empty message.', 'warning')
        return
      }

//        time = new Date(msg.date);
//        var timeStr = time.toLocaleTimeString();

      let item = {
        role: 'client',
        time: new Date().toLocaleString(),
        data: msg
      }

      this.messages.push(item)
      this.printLog('send', msg)

      console.log('send msg:' + msg)
      this.connection.send(msg)

      this.message = null
    },
    clear() {
      this.messages = []
    },
    printLog(tag, msg) {
      let time = new Date().toLocaleString()
      let txt = ' [' + tag + '] ' + msg
      console.log(time + txt)

      this.logs.push({
        time: time,
        msg: txt
      })
    },
    clearLog() {
      this.logs = []
    },
    /**
     *
     * @param msg
     * @param type allow info, success, warning or danger
     */
    showMsg(msg, type = 'info') {
      if (msg) {
        this.alertMsg = msg
        this.alertType = type
        this.showAlert = true
      } else {
        this.alertMsg = null
        this.showAlert = false
      }
    }
  }
})
