<html>
<body>
    <label id="notifications"></label>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
<script>
  $( document ).ready(function() {

        var socket = io.connect('http://localhost:8080');

        socket.on('notification', function (data) {

            console.log(data);

        });

    });
</script>
</body>
</html>