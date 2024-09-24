<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{isset($title) ? $title : config('app.project.name')}}</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    </head>
    <body>
    <div id="main">
       <div class="container-fluid">
            <div class="content">
                <h1 >{{ isset($title) ? $title : config('app.project.name') }}</h1>
                <p class="alert alert-info">This page is used by web developers to test node.js webservices for {{config('app.project.name')}} project</p>
            </div>
            <hr>
            <ol>
                <li>
                    Add Booking 
                    <input type="file" name="load_image" id="load_image" value="" >
                    <button class="btn btn-primary" onclick="javascript: return store_booking();">Post</button>
                </li>
                <hr>
            </ol>
            
            <hr>

        </div>
    </div>

    <script type="text/javascript" src="http://192.168.1.85/quickpick/node_apps/public/socket.io.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
    
    <script src="http://192.168.1.85/quickpick/node_apps/public/delivery.js"></script>

    <script type="text/javascript"> 

    var BASE_URL          = 'http://192.168.1.85/quickpick/node_apps/';
    
    var NODE_SERVER_URL   = '192.168.1.85:8800';
    var NODE_SERVER_PORT  = '8800';

    var stripe_port           = NODE_SERVER_PORT;
    var socket_connect = io(NODE_SERVER_URL);

    var delivery = new Delivery(socket_connect);


    function store_booking()
    {
        var file = $("input[type=file]")[0].files[0];

        var obj_request =   {
                                user_id             : 26
                                ,booking_type       : 'BUSINESS'   //RESIDENCE

                                ,pickup_location    : "The Invest Quotient, Shri Hari Narayan Kute Marg, Matoshree Nagar, Nashik, Maharashtra, 422002,India"
                                ,pickup_lat         : 19.990298
                                ,pickup_long        : 73.781809
                                ,drop_location      : "shri Samarth Baburao patil Maharaj temple, Deolali Pravara, Maharashtra 413716, India"
                                ,drop_lat           : 19.474978
                                ,drop_long          : 74.619044
                                ,distance           : 0.7

                                ,promo_code_id      : 4

                                ,package_type       : 'BOX'     // BOX/ CARTON/ CONTAINER
                                ,package_length     : 12.5
                                ,package_breadth    : 13.1
                                ,package_height     : 14.75
                                ,package_weight     : 100
                                ,package_quantity   : 1

                                ,card_id            : 1
                            };
        delivery.send(file, obj_request);
        // socket_connect.emit('store_booking', obj_request);
    }
    </script>
    </body>
</html>
