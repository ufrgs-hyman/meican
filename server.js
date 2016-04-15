var server = require('http').createServer(handler);
var io = require('socket.io')(server);

server.listen(8080);

function handler (req, res) {};

io.on('connection', function (socket) {

    console.log("new client connected");

    refresher = setInterval(function() {
        console.log("ping");
        socket.emit("notification", { data: 'ping'});
    }, 2000);
});
