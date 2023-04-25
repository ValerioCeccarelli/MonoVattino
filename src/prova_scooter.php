<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>MonoVattino</title>
</head>

<body>
    <?php 
    
    if (!isset($_COOKIE['jwt'])) {
        echo 'Hello guest<br>yuo need to <a href="account/login.php">login</a> or <a href="account/register.php">register</a>!';
    }else {
        echo 'Hello user<br>you can <a href="account/logout.php">logout</a> or <a href="account/profile.php">view your profile</a>!';
    }
    
    ?>

    <button onclick="get_scooter()">Get Scooter</button>
    <button onclick="reserve_scooter()">Reserve Scooter</button>
    <button onclick="end_scooter()">End Scooter</button>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
    function get_scooter() {
        url = 'http://localhost/scooter/scooter.php';
        const data = {
            longitude: 1,
            latitude: 1,
            radius: 30
        };

        // fetch(url, {
        //         method: 'GET',
        //         headers: {
        //             'Content-Type': 'application/json'
        //         }

        //         // body: JSON.stringify(data)
        //     })
        //     .then(response => response.json())
        //     .then(data => console.log(data))
        //     .catch((error) => {
        //         console.error('Error:', error);
        //     });

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function(data) {
                console.log(data);
            }
        });
    }

    function reserve_scooter() {
        const url = 'http://localhost/scooter/scooter.php';
        const data = {
            scooter_id: 2,
            action: 'reserve'
        };

        // fetch(url, {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json'
        //         },
        //         body: JSON.stringify(data)
        //     })
        //     .then(response => response.json())
        //     .then(data => console.log(data))
        //     .catch((error) => {
        //         console.error('Error:', error);
        //     });
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data) {
                console.log(data);
            }
        });
    }

    function end_scooter() {
        const url = 'http://localhost/scooter/scooter.php';
        const data = {
            scooter_id: 2,
            action: 'end',
            longitude: 10,
            latitude: 10
        };

        // fetch(url, {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json'
        //         },
        //         body: JSON.stringify(data)
        //     })
        //     .then(response => response.json())
        //     .then(data => console.log(data))
        //     .catch((error) => {
        //         console.error('Error:', error);
        //     });
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data) {
                console.log(data);
            }
        });
    }
    </script>
</body>

</html>