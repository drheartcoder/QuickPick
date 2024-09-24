var express = require('express');
var path    = require('path');
var app     = express();
var http    = require('http').Server(app);
var io      = require('socket.io')(http);
var dotenv  = require('dotenv').config({path: '../.env'});
var fs      = require('fs');
var buf     = new Buffer(10240);

var _       = require('lodash');
var mysql   = require("mysql");
var async   = require('async');
var moment  = require('moment');
var Base64  = require('js-base64').Base64;
var geodist = require('geodist');

// Define the port to run on
app.set('port', process.env.NODE_SERVER_PORT);

// using the 
app.use('/public', express.static(path.join(__dirname + '/public')));

// Listen for requests
http.listen(process.env.NODE_SERVER_PORT, function () {
  console.log('Hello, listening on *:'+process.env.NODE_SERVER_PORT);
});

// Serving the html page on the browser.
app.get('/', function(req, res){
  res.sendFile(__dirname + '/map.html');
});

app.get('/trackall', function (req, res) {
  res.sendFile(__dirname + '/multiple_vehicle.html');
});

// Storing the data in the database.
var DB_HOST     = process.env.DB_HOST;
var DB_USERNAME = process.env.DB_USERNAME;
var DB_PASSWORD = process.env.DB_PASSWORD;
var DB_DATABASE = process.env.DB_DATABASE;

var pool = mysql.createPool({
  connectionLimit: 100,
  host: DB_HOST,
  user: DB_USERNAME,
  password: DB_PASSWORD,
  database: DB_DATABASE,
  debug: false,
  multipleStatements: true
});

// Connecting to socket with io connection.
var arr_admin_socket    = []
var arr_socket          = [];
var arr_user_socket     = [];
var arr_ongoing_vehicle = [];
var arr_ongoing_rides   = [];


  io.on('connection', function (socket) {

      /*
      |check for connected riders data if data found replace old socket id with new one
      */
      if(socket.handshake.query.rider_id!=undefined){
        if(arr_user_socket.length > 0) {
          arr_user_socket.forEach(function (value, index) {
            if (value != undefined) {
              if (value.rider_id != undefined) {
                if (String(value.rider_id) == String(socket.handshake.query.rider_id)) {
                    
                    /*also check for ongoing ride if the ride is break or not*/

                    if(arr_ongoing_rides.length > 0) {
                      arr_ongoing_rides.forEach(function (inner_value, index) {
                        if (inner_value != undefined) {
                          if (inner_value.rider_id != undefined) {
                            if (String(inner_value.rider_id) == String(value.rider_id)) {
                                arr_user_socket[index].id = socket.id;            
                            }
                          }
                        }
                      });
                    }
                }
              }
            }
          });
        }
      }
      /*
      |check for connected driver data if data found replace old socket id with new one
      */
      if(socket.handshake.query.driver_id!=undefined){
        if(arr_ongoing_vehicle.length > 0) {
          arr_ongoing_vehicle.forEach(function (value, index) {
            if (value != undefined) {
              if (value.driver_id != undefined) {
                if (String(value.driver_id) == String(socket.handshake.query.driver_id)) {
                  arr_ongoing_vehicle[index].id = socket.id
                }
              }
            }
          });
        }
      }
  });

  /*---------------------------------------------------------------------------------------------------
  | After rider enter source and destination and category then this listener will call and list all the rider request
  ----------------------------------------------------------------------------------------------------*/
  io.on('track_all_vehicles', function (data) {
      
      if(arr_user_socket.length > 0) {
        arr_user_socket.forEach(function (value, index) {
          if (value != undefined) {
            if (value.rider_id != undefined) {
              if (String(value.rider_id) == String(data.rider_id)) {
                arr_user_socket.splice(index, 1);
              }
            }
          }
        });
      }

      // arr_user_socket.push(data);


      arr_user_socket.push({
            id           : data.id,
            rider_id     : data.rider_id,
            rider_name   : data.rider_name,
            rider_email  : data.rider_email,
            rider_mobile : data.rider_mobile,
            rider_type   : data.rider_type,
            rider_image  : data.rider_image,
            service_id   : data.service_id,
            source_lat   : data.source_lat,
            source_lng   : data.source_lng
      });
  });

  /*---------------------------------------------------------------------------------------------------
  | After rider book specific driver then this listener will call and request will send to driver
  ----------------------------------------------------------------------------------------------------*/
  io.on('request_to_book_driver', function (data) {      
    
    if(data!=undefined){
      pool.getConnection(function (err, db_connect) {
          
          if (err) {
            console.log("Error in connection database");
            return;
          }
          
          var curr_date = moment(Date.now()).format('YYYY-MM-DD HH:mm:ss');

          var insert_query = 'INSERT '+ 
                               'INTO '+ 
                                    'rider_to_driver_request('+
                                    'driver_id,'+
                                    'rider_id,'+
                                    'vehicle_id,'+
                                    'category_id,'+
                                    'promo_code_id,'+
                                    'date,'+
                                    'source_location,'+
                                    'destination_location,'+
                                    'source_lat,'+
                                    'source_lng,'+
                                    'destination_lat,'+
                                    'destination_lng,'+
                                    'is_service_used,'+
                                    'request_status) '+  
                                'VALUES ('+
                                  parseInt(data.driver_id) + ','+ 
                                  parseInt(data.rider_id) + ','+ 
                                  parseInt(data.vehicle_id) + ','+ 
                                  parseInt(data.service_id) + ','+ 
                                  parseInt(data.promo_code_id) +',"'+
                                  curr_date + '","'+ 
                                  data.source_location + '","'+
                                  data.destination_location + '",'+ 
                                  data.source_lat + ',' + 
                                  data.source_lng + ',' + 
                                  data.destination_lat + ',' + 
                                  data.destination_lng + ',"'+ 
                                  data.is_service_used +'"' + ',"'+ 
                                  data.request_status +'" ' +')';
          
          

          db_connect.query(insert_query, function (err, results) {
                if (err) throw err;
                
                if(results!=undefined && results.affectedRows>0){
                  
                  var obj_response = 
                                        {
                                            status               : 'success',
                                            request_id           : parseInt(results.insertId),
                                            rider_id             : parseInt(data.rider_id),
                                            rider_name           : '',
                                            rider_email          : '',
                                            rider_mobile         : '',
                                            rider_image          : '',
                                            rider_type           : '',
                                            driver_id            : parseInt(data.driver_id),
                                            source_location      : data.source_location,
                                            destination_location : data.destination_location,
                                            source_lat           : data.source_lat,
                                            source_lng           : data.source_lng,
                                            destination_lat      : data.destination_lat,
                                            destination_lng      : data.destination_lng,
                                        };

                  if(arr_user_socket.length > 0) {
                    arr_user_socket.forEach(function (value, index) {
                      if (value != undefined) {
                        if (value.rider_id != undefined) {
                          if (String(value.rider_id) == String(data.rider_id)) {
                              obj_response.rider_name   = value.rider_name;
                              obj_response.rider_email  = value.rider_email;
                              obj_response.rider_mobile = value.rider_mobile;
                              obj_response.rider_image  = value.rider_image;
                              obj_response.rider_type   = value.rider_type;
                          }
                        }
                      }
                    });
                  }
                  
                  if(arr_ongoing_vehicle.length > 0) {
                    arr_ongoing_vehicle.forEach(function (currentValue, index) {
                      if (currentValue != undefined) {
                        if (currentValue.driver_id != undefined) {
                          if (String(currentValue.driver_id) == String(data.driver_id)) {

                            /*change driver status to busy if new request received form rider*/
                            
                            arr_ongoing_vehicle[index].driver_status = 'BUSY';

                            if (io.sockets != undefined) {
                              if (io.sockets.connected[currentValue.id] != undefined) {
                                  io.sockets.connected[currentValue.id].emit('send_receive_request_to_driver', obj_response);                                
                                  console.log('send driver request socket id : '+ currentValue.id);
                              }
                              else{
                                console.log('socket_id not found -');
                              }
                            }
                            else{
                                console.log('socket_id not found --');
                            }
                          }
                        }
                      }
                    });
                  }
                }                    
          });
      });
    }
  });

  /*---------------------------------------------------------------------------------------------------
  | After rider book specific driver and driver perform action then this listener will call
  ----------------------------------------------------------------------------------------------------*/
  io.on('process_request_by_driver', function (data) {
    
    console.log('process_request_by_driver');
    //console.log(data);

    if(data!=undefined){
      pool.getConnection(function (err, db_connect) {
            
        if (err) {
          console.log("Error in connection database");
          return;
        }

        var status     = 'ERROR';
        var request_id = data.request_id;
        var rider_id   = data.rider_id;
        var driver_id  = data.driver_id;

        var obj_response = 
                                  {
                                      status           : status,
                                      rider_id         : parseInt(rider_id),
                                      driver_id        : parseInt(driver_id),
                                      request_id       : parseInt(request_id),
                                      ride_id          : 0
                                  };

        var update_query = "UPDATE rider_to_driver_request SET request_status = '"+data.request_status+"',reason = '"+data.reason+"' WHERE id = "+data.request_id;  
        
        db_connect.query(update_query, function (err, results) {
            
          if (err) throw err;
          
          if(results!=undefined && results.affectedRows>0){
            if(data.request_status == "ACCEPT_BY_DRIVER")
            { 
               status = 'ACCEPT_BY_DRIVER';
            }
            else if(data.request_status == "REJECT_BY_DRIVER")
            {
               status = 'REJECT_BY_DRIVER';
            }
            else if(data.request_status == "TIMEOUT")
            {
               status = 'TIMEOUT';
            }
            obj_response.status = status;

            if(data.request_status == "ACCEPT_BY_DRIVER"){
                /*if request accept_by_driver then make entry in ride table as to be picked*/
                process_accept_request(data,obj_response)
            } 
            else
            {
                /*emit data to rider */
                if(arr_user_socket.length > 0) {
                  arr_user_socket.forEach(function (currentValue, index) {
                    if (currentValue != undefined) {
                      if (currentValue.rider_id != undefined) {
                        if (String(currentValue.rider_id) == String(data.rider_id)) {
                            if (io.sockets != undefined) {
                            if (io.sockets.connected[currentValue.id] != undefined) {
                               io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
                               console.log('booking reply from driver socket id : '+ currentValue.id);
                            }
                            else{
                              console.log('socket_id not found -');
                            }
                          }
                          else{
                              console.log('socket_id not found --');
                          }
                        }
                      }
                    }
                  });
                }
                /*emit data to driver */
                if(arr_ongoing_vehicle.length > 0) {
                  arr_ongoing_vehicle.forEach(function (currentValue, index) {
                    if (currentValue != undefined) {
                      if (currentValue.driver_id != undefined) {
                        if (String(currentValue.driver_id) == String(data.driver_id)) {
                            if (io.sockets != undefined) {
                              if (io.sockets.connected[currentValue.id] != undefined) {
                                 io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
                                 console.log('ride_data_send to driver socket id : '+ currentValue.id);
                              }
                              else{
                                console.log('socket_id not found -');
                              }
                            }
                            else{
                                console.log('socket_id not found --');
                            }
                        }
                      }
                    }
                  });
                }
            }
          }   
          else {
            
            /*emit data to rider */
            if(arr_user_socket.length > 0) {
              arr_user_socket.forEach(function (currentValue, index) {
                if (currentValue != undefined) {
                  if (currentValue.rider_id != undefined) {
                    if (String(currentValue.rider_id) == String(data.rider_id)) {
                        if (io.sockets != undefined) {
                          if (io.sockets.connected[currentValue.id] != undefined) {
                             io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
                             console.log('booking reply from driver socket id : '+ currentValue.id);
                          }
                          else{
                            console.log('socket_id not found -');
                          }
                        }
                        else{
                            console.log('socket_id not found --');
                        }
                    }
                  }
                }
              });
            }
            
            /*emit data to driver */
            if(arr_ongoing_vehicle.length > 0) {
              arr_ongoing_vehicle.forEach(function (currentValue, index) {
                if (currentValue != undefined) {
                  if (currentValue.driver_id != undefined) {
                    if (String(currentValue.driver_id) == String(data.driver_id)) {
                      if (io.sockets != undefined) {
                        if (io.sockets.connected[currentValue.id] != undefined) {
                           io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
                           console.log('ride_data_send to driver socket id : '+ currentValue.id);
                        }
                        else{
                          console.log('socket_id not found -');
                        }
                      }
                      else{
                          console.log('socket_id not found --');
                      }
                    }
                  }
                }
              });
            }
          }
        });
      });
    }
  });

  io.on('track_current_ride_driver',function(data){
      if(data!=undefined && arr_ongoing_vehicle!=undefined){
        if(arr_ongoing_vehicle.length > 0) {
          arr_ongoing_vehicle.forEach(function (value, index) {
            if (value != undefined) {
              if (value.driver_id != undefined) {
                if (String(value.driver_id) == String(data.driver_id)) {
                    if (io.sockets != undefined) {
                      if (io.sockets.connected[data.socket_id] != undefined) {
                        io.sockets.connected[data.socket_id].emit('get_current_ride_driver_info', value);
                      }
                    }
                }
              }
            }
          });
        }
      }
  });

  function process_accept_request(data,obj_response){
    console.log('process_accept_request function');
    /*console.log(data);
    console.log(obj_response);*/

    var request_id = data.request_id;

    if(request_id!=undefined){
        
        var unique_id = (Date.now() * Math.floor(Math.random() * 49));
        var ride_unique_id = 'RIDE-'+unique_id;

        var obj_ride = 
                        {
                            rider_to_driver_request_id : parseInt(request_id),
                            ride_unique_id             : ride_unique_id,
                            driver_fair_charge         : 10,
                            distance                   : 10,
                            admin_commission           : 0,
                            is_promo_code_applied      : '0',
                            promo_code                 : '',
                            promo_percentage           : 0,
                            promo_max_amount           : 0,
                            applied_promo_code_charge  : 0,
                            total_amount               : 0,
                            final_amount               : 0,
                            ride_date                  : moment(Date.now()).format('YYYY-MM-DD HH:mm:ss'),
                            ride_start_time            : moment(Date.now()).format('HH:mm:ss'),
                            ride_end_time              : moment(Date.now()).format('HH:mm:ss'),
                            payment_status             : 'UNPAID',
                            status                     : 'TO_BE_PICKED',
                        };
        console.log('ride object');
        /*console.log(obj_ride);*/
        pool.getConnection(function (err, db_connect) {
          async.waterfall([
            function (callback) {
                console.log('select promo code');
                var select_query = 'SELECT id,promo_code_id FROM rider_to_driver_request WHERE rider_to_driver_request.id = ' + parseInt(request_id);
                db_connect.query(select_query, function (err, results) {
                  if (err) throw err;
                  if (results != undefined) {
                    callback(null, results);
                  } else {
                    callback('error', 'Error while accessing the records');
                  }
                });
            },
            function (query_result, callback) {
              console.log('promo code callback');
              /*console.log(query_result);*/

              if(query_result[0]!=undefined){
                  promo_code_id = query_result[0].promo_code_id;
                  if(promo_code_id!=undefined && promo_code_id!=0){
                    var select_query = 'SELECT id,code,percentage,max_amount FROM promo_offer WHERE promo_offer.id = ' + parseInt(promo_code_id);
                    db_connect.query(select_query, function (err, results) {
                      if (err) throw err;
                      if (results != undefined) {
                        callback(null, results,obj_ride);
                      } else {
                        callback('error', 'Error while accessing the records');
                      }
                    });
                  }
                  else{
                    callback(null, null ,obj_ride);
                  }

              }            
            }
            ,function(query_result,obj_ride,callback){
                console.log('promo code other values callback');
                /*console.log(query_result);*/

                if(query_result!=null && query_result[0]!=undefined){
                  obj_ride.is_promo_code_applied = '1';
                  obj_ride.promo_code            = query_result[0].code;
                  obj_ride.promo_percentage      = parseFloat(query_result[0].percentage);
                  obj_ride.promo_max_amount      = parseFloat(query_result[0].max_amount);
                } 
                callback(null, obj_ride);
            },
            function(obj_ride,callback){
                console.log('admin_commission callback');
                /*console.log(obj_ride);*/
                var select_query = 'SELECT id,percentage FROM admin_commission WHERE admin_commission.id = ' + parseInt(1);
                db_connect.query(select_query, function (err, results) {
                  if (err) throw err;
                  if (results[0] != undefined) {
                    obj_ride.admin_commission = results[0].percentage;
                    callback(null, obj_ride);
                  }
                });
            },
            function(obj_ride,callback){
                console.log('insert ride query callback');
                /*console.log(obj_ride);*/
                if(obj_ride!=undefined){

                    var insert_query = 'INSERT INTO ride'+
                                                      '(rider_to_driver_request_id,'+
                                                      'ride_unique_id,'+
                                                      'driver_fair_charge,'+
                                                      'distance,'+
                                                      'admin_commission,'+
                                                      'is_promo_code_applied,'+
                                                      'promo_code,'+
                                                      'promo_percentage,'+
                                                      'promo_max_amount,'+
                                                      'total_amount,'+
                                                      'final_amount,'+
                                                      'date,'+
                                                      'start_time,'+
                                                      'end_time,'+  
                                                      'payment_status,'+
                                                      'status)'+
                                                      'VALUES('+ 
                                                                parseInt(obj_ride.rider_to_driver_request_id)+',"'+
                                                                obj_ride.ride_unique_id+'",'+ 
                                                                parseFloat(obj_ride.driver_fair_charge)+','+ 
                                                                parseFloat(obj_ride.distance)+','+ 
                                                                parseFloat(obj_ride.admin_commission)+',"'+
                                                                obj_ride.is_promo_code_applied+'","'+
                                                                obj_ride.promo_code+'",'+
                                                                parseFloat(obj_ride.promo_percentage)+','+ 
                                                                parseFloat(obj_ride.promo_max_amount)+','+ 
                                                                parseFloat(obj_ride.total_amount)+','+ 
                                                                parseFloat(obj_ride.final_amount)+',"'+ 
                                                                obj_ride.ride_date+'","'+
                                                                obj_ride.ride_start_time+'","'+
                                                                obj_ride.ride_end_time+'","'+
                                                                obj_ride.payment_status+'","'+
                                                                obj_ride.status +'" ' +')';
                    
                    db_connect.query(insert_query, function (err, results) {
                      if (err) throw err;
                      if (results != undefined) {
                        console.log('ride entry in database');
                        callback(null, results);
                      } else {
                        callback('error', 'Error while accessing the records');
                      }
                    });
                }
            },
            function(query_result,callback){
                console.log('insert ride callback');
                /*console.log(query_result);*/
                var ride_id = 0;
                if(query_result!=undefined && query_result.affectedRows>0){
                  if(query_result.insertId!=undefined){
                    ride_id = query_result.insertId;
                  }
                }
                console.log('ride_id : '+ ride_id);

                if(ride_id!=0){
                    obj_response.ride_id = parseInt(ride_id);
                    /*emit data to rider */
                    if(arr_user_socket.length > 0) {
                      arr_user_socket.forEach(function (currentValue, index) {
                        if (currentValue != undefined) {
                          if (currentValue.rider_id != undefined) {
                            if (String(currentValue.rider_id) == String(obj_response.rider_id)) {
                                /*console.log(obj_response);
                                console.log(currentValue);*/
                                if (io.sockets != undefined) {
                                  if (io.sockets.connected[currentValue.id] != undefined) {
                                     io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
                                     console.log('ride_data_send to rider socket id : '+ currentValue.id);
                                  }
                                  else{
                                    console.log('socket_id not found -');
                                  }
                                }
                                else{
                                    console.log('socket_id not found --');
                                }
                            }
                          }
                        }
                      });
                    }

                    /*emit data to driver */
                    if(arr_ongoing_vehicle.length > 0) {
                      arr_ongoing_vehicle.forEach(function (currentValue, index) {
                        if (currentValue != undefined) {
                          if (currentValue.driver_id != undefined) {
                            if (String(currentValue.driver_id) == String(obj_response.driver_id)) {
                              /*console.log(obj_response);
                              console.log(currentValue);*/
                                if (io.sockets != undefined) {
                                  if (io.sockets.connected[currentValue.id] != undefined) {
                                     io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
                                     console.log('ride_data_send to driver socket id : '+ currentValue.id);
                                  }
                                  else{
                                    console.log('socket_id not found -');
                                  }
                                }
                                else{
                                    console.log('socket_id not found --');
                                }
                            }
                          }
                        }
                      });
                    }

                }
                else{

                    if(arr_user_socket.length > 0) {
                      arr_user_socket.forEach(function (currentValue, index) {
                        if (currentValue != undefined) {
                          if (currentValue.rider_id != undefined) {
                            if (String(currentValue.rider_id) == String(obj_response.rider_id)) {
                                /*io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
                                console.log('ride_data_send to driver socket id : '+ currentValue.id);*/
                                if (io.sockets != undefined) {
                                  if (io.sockets.connected[currentValue.id] != undefined) {
                                     io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
                                     console.log('ride_data_send to rider socket id : '+ currentValue.id);
                                  }
                                  else{
                                    console.log('socket_id not found -');
                                  }
                                }
                                else{
                                    console.log('socket_id not found --');
                                }
                            }
                          }
                        }
                      });
                    }

                    /*emit data to driver */
                    if(arr_ongoing_vehicle.length > 0) {
                      arr_ongoing_vehicle.forEach(function (currentValue, index) {
                        if (currentValue != undefined) {
                          if (currentValue.driver_id != undefined) {
                            if (String(currentValue.driver_id) == String(obj_response.driver_id)) {
                                /*io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
                                console.log('ride_data_send to driver socket id : '+ currentValue.id);*/
                                if (io.sockets != undefined) {
                                  if (io.sockets.connected[currentValue.id] != undefined) {
                                     io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
                                     console.log('ride_data_send to driver socket id : '+ currentValue.id);
                                  }
                                  else{
                                    console.log('socket_id not found -');
                                  }
                                }
                                else{
                                    console.log('socket_id not found --');
                                }
                            }
                          }
                        }
                      });
                    }
                }
            }
            ],function (err, result) {
            if (err) throw err;
            console.log('in error block');
            return {ride_id : 0 , status:'error'};
          });
          
        });
    }
  }

  /*---------------------------------------------------------------------------------------------------
  | After rider select specific category then this listener will through nearby drivers to rider
  ----------------------------------------------------------------------------------------------------*/
  io.on('show_on_map', function (data) {
    console.log('show_on_map driver data : ');
    console.log(data);

    if (data != undefined) {

      /*delete existing value of vehicle id from the array.*/
      if(arr_ongoing_vehicle.length > 0) {
        arr_ongoing_vehicle.forEach(function (currentValue, index) {
          if (currentValue != undefined) {
            if (currentValue.driver_id != undefined) {
              if (String(currentValue.driver_id) == String(data.driver_id)) {
                arr_ongoing_vehicle.splice(index, 1);
              }
            }
          }
        });
      }

      arr_ongoing_vehicle.push(data);
      
      /*console.log('arr_ongoing_vehicle');
      console.log(arr_ongoing_vehicle);*/


      // Store lat lng inside the database against vehicle id and ride id.
      // storeLocationInfo(data);

      // Send data to each socket.
      if(arr_socket.length > 0) {
        arr_socket.forEach(function (value, index) {
          if (value != undefined) {
            if (String(value.vehicle_id) != undefined && String(data.vehicle_id) != undefined) {
              if (String(value.vehicle_id) == String(data.vehicle_id)) {
                if (io.sockets != undefined) {
                  if (io.sockets.connected[value.id] != undefined) {
                    io.sockets.connected[value.id].emit('get_vehicle_info', data);
                  }
                }
              }
            }
          }
        });
      }

      // Send all data to socket.
      if(arr_user_socket.length > 0) {
        arr_user_socket.forEach(function (value, index) {
          if (value != undefined) {
            if (io.sockets != undefined) {
              if (io.sockets.connected[value.id] != undefined) {

                // Match driver status and service id of driver and user ride requirement.
                if((data.service_id != undefined) && (data.service_id == value.service_id)
                    && (data.driver_status != undefined) && (data.driver_status == "AVAILABLE")) {
                    
                        var calculated_distance = getDistance(value.source_lat, value.source_lng, data.lat, data.lng);
                        var obj_tmp = {};
                        
                        obj_tmp['lat']          = data.lat;
                        obj_tmp['lng']          = data.lng;
                        obj_tmp['vehicle_id']   = data.vehicle_id;
                        obj_tmp['driver_id']    = data.driver_id;
                        obj_tmp['driver_name']  = data.driver_name;
                        obj_tmp['min_distance'] = calculated_distance;
                        
                        if(calculated_distance <= 15) {
                          obj_tmp['marker_status'] = 'SHOW';
                        } else {
                          obj_tmp['marker_status'] = 'REMOVE';
                        }
                        
                        // Emit the data which is sent to the user.
                        /*console.log('show driver to rider');
                        console.log(obj_tmp);*/
                        io.sockets.connected[value.id].emit('get_all_vehicle_info', obj_tmp);
                }

                // // Implement a query to get the drivers.
                // var query =  'SELECT '+
                //             'users.id AS driver_id,'+
                //             'CONCAT( users.first_name," ",users.last_name) AS driver_name,'+
                //             'role_users.role_id AS role_user_id,'+
                //             'roles.slug as user_role,'+
                //             'driver_car_relation.vehicle_id as vehicle_id,'+
                //             'vehicle.vehicle_type_id as vehicle_type_id,'+
                //             'driver_fair_charges.fair_charge as fair_charge '+
                //         'FROM '+'users '+
                //         'JOIN '+
                //             'role_users ON role_users.user_id = users.id '+
                //         'JOIN '+
                //             'roles ON roles.id = role_users.role_id '+
                //         'JOIN '+
                //             'driver_car_relation ON driver_car_relation.driver_id = users.id '+
                //         'JOIN '+
                //             'driver_available_status ON driver_available_status.driver_id = users.id '+
                //         'JOIN '+
                //             'vehicle ON vehicle.id = driver_car_relation.vehicle_id '+
                //         'JOIN '+
                //             'vehicle_type ON vehicle_type.id = vehicle.vehicle_type_id '+
                //         'JOIN '+
                //            'driver_fair_charges ON driver_fair_charges.driver_id = users.id '+
                //         'WHERE '+
                //             'users.is_active = "1" AND '+
                //             'roles.slug = "driver" AND '+
                //             'driver_available_status.status = "AVAILABLE" AND '+
                //             'driver_car_relation.is_car_assign = "1"';
                //             //+' AND vehicle_type.id = '+ value.service_id;
                
                // pool.getConnection(function (err, db_connect) {

                //   if (err) {
                //     console.log("Error in connection database");
                //     //res.json({ "code": 100, "status": "Error in connection database" });
                //     return;
                //   }

                //   db_connect.query(query, function (err, results) {
                //     if (err) throw err;

                //     var arr_drivers = [];
                //     _.each(results, function (inner_value, index) {
                //         // Compare source lat lng and driver's lat lng.
                //         var calculated_distance = getDistance(value.source_lat, value.source_lng, data.lat, data.lng);
                //         if(calculated_distance <= 15) {
                //              var obj_tmp = {};
                //              obj_tmp['lat']         = data.lat; 
                //              obj_tmp['lng']         = data.lng;
                //              obj_tmp['vehicle_id']  = data.vehicle_id;
                //              obj_tmp['driver_id']   = inner_value['driver_id'];
                //              obj_tmp['driver_name'] = inner_value['driver_name'];
                //              obj_tmp['fair_charge'] = inner_value['fair_charge'];
                //              obj_tmp['min_distance']= calculated_distance;
                //              arr_drivers.push(obj_tmp);         
                //         }
                //     });

                //     // Emit the data which is sent to the user.
                //     io.sockets.connected[value.id].emit('get_all_vehicle_info', arr_drivers);

                //});

                //});

                // io.sockets.connected[value.id].emit('get_all_vehicle_info', data);
              }
            }
          }
        });
      }
    }
  });

  /*---------------------------------------------------------------------------------------------------
  | After accept ride request from driver maintain ride status in blow listener
  ----------------------------------------------------------------------------------------------------*/
  io.on('driver_live_trip_tracking', function (data) {
    
    if (data != undefined) {
      /*delete existing value of vehicle id from the array.*/
      /*will used to show on admin map*/
      if(arr_ongoing_rides.length > 0) {
        arr_ongoing_rides.forEach(function (value, index) {
          if (value != undefined) {
            if (value.driver_id != undefined) {
              console.log(String(value.driver_id) == String(data.driver_id));
              if (String(value.driver_id) == String(data.driver_id)) {
                arr_ongoing_rides.splice(index, 1);
              }
            }
          }
        });
      }
      arr_ongoing_rides.push(data);
      
      console.log('arr_ongoing_rides :' , arr_ongoing_rides);
      /*Send all drivers to admin socket */
      
      if(arr_admin_socket.length > 0) {
        arr_admin_socket.forEach(function (value, index) {
          if (value != undefined) {
            if (io.sockets != undefined) {
              if (io.sockets.connected[value.socket_id] != undefined) {

                if((data != undefined) && (data.ride_status != undefined)) {
                    
                    var obj_tmp = {};

                    obj_tmp['ride_id']     = data.ride_id;
                    obj_tmp['rider_id']    = data.rider_id;
                    obj_tmp['driver_id']   = data.driver_id;
                    obj_tmp['ride_status'] = data.ride_status;
                    obj_tmp['lat']         = data.lat;
                    obj_tmp['lng']         = data.lng;

                    if(arr_ongoing_vehicle.length > 0) {
                      arr_ongoing_vehicle.forEach(function (value, index) {
                        if (value != undefined) {
                          if (value.driver_id != undefined) {
                            if (String(value.driver_id) == String(data.driver_id)) {
                              obj_tmp['vehicle_id']     = value.vehicle_id;
                              obj_tmp['vehicle_name']   = value.vehicle_name; 
                              obj_tmp['driver_name']    = value.driver_name;
                              obj_tmp['driver_email']   = value.driver_email;
                              obj_tmp['driver_profile'] = value.driver_profile;
                              obj_tmp['driver_mobile']  = value.driver_mobile;
                              obj_tmp['fair_charge']    = value.fair_charge;
                            }
                          }
                        }
                      });
                    }
                    io.sockets.connected[value.socket_id].emit('get_all_vehicle_info', obj_tmp);
                }
              }
            }
          }
        });
      }


      if(data.ride_status == 'IN_TRANSIT'){
          
          var file_path = __dirname + '/public/ride/'+data.ride_id+'.json';

          fs.exists(file_path, (exists) => {
            if (exists) {
                
                var arr_final_lat_lng = [];

                var obj_previous_lat_lng = JSON.parse(fs.readFileSync(file_path, 'utf8'));

                if(obj_previous_lat_lng.length>0){
                  obj_previous_lat_lng.forEach(function(value,index){
                      arr_final_lat_lng.push(value);
                  });
                }

                var obj_lat_lng = {
                                    'lat': parseFloat(data.lat),
                                    'lon': parseFloat(data.lng)
                                  };

                arr_final_lat_lng.push(obj_lat_lng);
                var file  = fs.openSync(file_path, 'w');
                fs.writeSync(file, JSON.stringify(arr_final_lat_lng));
            } else {

              var arr_data    = [];

              var obj_lat_lng = {
                                  'lat': parseFloat(data.lat),
                                  'lon': parseFloat(data.lng)
                                };

              arr_data.push(obj_lat_lng);
              var file  = fs.openSync(file_path, 'w');
              fs.writeSync(file, JSON.stringify(arr_data));
            }
          });     
      } 
      else if(data.ride_status == 'COMPLETED'){
         
          var file_path = __dirname + '/public/ride/'+data.ride_id+'.json';
          fs.exists(file_path, (exists) => {

            if (exists) {

                var arr_final_lat_lng = [];
                var ride_id = parseInt(data.ride_id);

                var last_index = final_distance = 0;

                var str_obj_previous_lat_lng = fs.readFileSync(file_path, 'utf8');
                var obj_previous_lat_lng = JSON.parse(fs.readFileSync(file_path, 'utf8'));
                
                if(obj_previous_lat_lng.length>0){
                  last_index = (obj_previous_lat_lng.length - 1);
                  obj_previous_lat_lng.forEach(function(value,index){
                    if(index<=last_index){
                      var next_index = index + 1;
                      if(next_index>last_index){
                        next_index = last_index;
                      }
                      var current_lat_lon = value;
                      var next_lat_lon    = obj_previous_lat_lng[next_index];

                      var current_dist = geodist(current_lat_lon, next_lat_lon,{exact: true, unit: 'km'});
                      final_distance   = parseFloat(final_distance) + parseFloat(current_dist.toFixed(2));
                    }
                  })
                }

                pool.getConnection(function (err, db_connect) {
                    var insert_query = 'INSERT '+ 
                               'INTO '+ 
                                    'ride_coordinate('+
                                    'ride_id,'+
                                    'coordinates )'+
                                'VALUES ('+
                                  parseInt(ride_id) + ',\''+ 
                                  str_obj_previous_lat_lng +'\' ' +')';
                    
                    db_connect.query(insert_query, function (err, results) {
                        if (results != undefined) {
                          /* UNLINK FILE */
                          fs.unlink(file_path, function(err) {
                             if (err) {
                                return console.error(err);
                             }
                            console.log("File deleted successfully!");
                          });
                          
                          var update_query = "UPDATE ride SET status = '"+data.ride_status+"', distance = "+final_distance+" WHERE id = "+ride_id;
                          db_connect.query(update_query, function (err, results) {
                              if(results!=undefined && results.affectedRows>0){
                                //after status updated emit to driver and rider
                                if(arr_user_socket.length > 0) {
                                  arr_user_socket.forEach(function (currentValue, index) {
                                    if (currentValue != undefined) {
                                      if (currentValue.rider_id != undefined) {
                                        if (String(currentValue.rider_id) == String(data.rider_id)) {
                                            if (io.sockets != undefined) {
                                              if (io.sockets.connected[currentValue.id] != undefined) {
                                                 io.sockets.connected[currentValue.id].emit('complete_trip_by_driver', data);
                                                 console.log('ride_data_send to rider socket id : '+ currentValue.id);
                                              }
                                              else{
                                                console.log('socket_id not found -');
                                              }
                                            }
                                            else{
                                                console.log('socket_id not found --');
                                            }
                                        }
                                      }
                                    }
                                  });
                                }
                                /*emit data to driver */
                                if(arr_ongoing_vehicle.length > 0) {
                                  arr_ongoing_vehicle.forEach(function (currentValue, index) {
                                    if (currentValue != undefined) {
                                      if (currentValue.driver_id != undefined) {
                                        if (String(currentValue.driver_id) == String(data.driver_id)) {
                                            if (io.sockets != undefined) {
                                              if (io.sockets.connected[currentValue.id] != undefined) {
                                                 io.sockets.connected[currentValue.id].emit('complete_trip_by_driver', data);
                                                 console.log('ride_data_send to driver socket id : '+ currentValue.id);
                                              }
                                              else{
                                                console.log('socket_id not found -');
                                              }
                                            }
                                            else{
                                                console.log('socket_id not found --');
                                            }
                                        }
                                      }
                                    }
                                  });
                                }
                                //Remove driver array from arr_ongoing_rides
                                if(arr_ongoing_rides.length > 0) {
                                  arr_ongoing_rides.forEach(function (value, index) {
                                    if (value != undefined) {
                                      if (value.driver_id != undefined) {
                                        if (String(value.driver_id) == String(data.driver_id)) {
                                          arr_ongoing_rides.splice(index, 1);
                                        }
                                      }
                                    }
                                  });
                                }      
                              }
                          });

                        }
                    });
                });
                return;
            } else {
              console.log("file not found!");
            } 
          });
      } 
      else if(data.ride_status == 'CANCELED'){
        var ride_id = parseInt(data.ride_id);

        pool.getConnection(function (err, db_connect) {
            var update_query = "UPDATE ride SET status = '"+data.ride_status+"',reason = '"+data.reason+"' WHERE id = "+data.ride_id;  
            db_connect.query(update_query, function (err, results) {
                if(results!=undefined && results.affectedRows>0){
                    //after status updated emit to driver and rider
                    if(arr_user_socket.length > 0) {
                      arr_user_socket.forEach(function (currentValue, index) {
                        if (currentValue != undefined) {
                          if (currentValue.rider_id != undefined) {
                            if (String(currentValue.rider_id) == String(data.rider_id)) {
                                if (io.sockets != undefined) {
                                  if (io.sockets.connected[currentValue.id] != undefined) {
                                     io.sockets.connected[currentValue.id].emit('canceled_trip_by_driver', data);
                                     console.log('ride_data_send to rider socket id : '+ currentValue.id);
                                  }
                                  else{
                                    console.log('socket_id not found -');
                                  }
                                }
                                else{
                                    console.log('socket_id not found --');
                                }
                            }
                          }
                        }
                      });
                    }
                    /*emit data to driver */
                    if(arr_ongoing_vehicle.length > 0) {
                      arr_ongoing_vehicle.forEach(function (currentValue, index) {
                        if (currentValue != undefined) {
                          if (currentValue.driver_id != undefined) {
                            if (String(currentValue.driver_id) == String(data.driver_id)) {
                                if (io.sockets != undefined) {
                                  if (io.sockets.connected[currentValue.id] != undefined) {
                                     io.sockets.connected[currentValue.id].emit('canceled_trip_by_driver', data);
                                     console.log('ride_data_send to driver socket id : '+ currentValue.id);
                                  }
                                  else{
                                    console.log('socket_id not found -');
                                  }
                                }
                                else{
                                    console.log('socket_id not found --');
                                }
                            }
                          }
                        }
                      });
                    }
                    //Remove driver array from arr_ongoing_rides
                    if(arr_ongoing_rides.length > 0) {
                      arr_ongoing_rides.forEach(function (value, index) {
                        if (value != undefined) {
                          if (value.driver_id != undefined) {
                            if (String(value.driver_id) == String(data.driver_id)) {
                              arr_ongoing_rides.splice(index, 1);
                            }
                          }
                        }
                      });
                    }      
                }
            });    
        });
      } 
      
      if(arr_user_socket.length > 0) {
        arr_user_socket.forEach(function (value, index) {
          if (value != undefined) {
            if (io.sockets != undefined) {
              if (io.sockets.connected[value.id] != undefined) {
                if((data.rider_id != undefined) && (data.rider_id == value.rider_id) && (data.ride_status != undefined)) {
                  
                  var ride_id = data.ride_id;
                  var obj_response = 
                                      {
                                          /*socket_id            : data.socket_id,*/
                                          ride_id              : data.ride_id,
                                          rider_id             : data.rider_id,
                                          driver_id            : data.driver_id,
                                          ride_status          : data.ride_status,
                                          lat                  : data.lat,
                                          lng                  : data.lng,
                                          source_location      : '',
                                          source_lat           : '',
                                          source_lng           : '',
                                          destination_location : '',
                                          destination_lat      : '',
                                          destination_lng      : ''

                                      };
                  pool.getConnection(function (err, db_connect) {
                    
                    var select_query = 'SELECT rider_to_driver_request_id FROM ride WHERE ride.id = ' + parseInt(ride_id);
                    db_connect.query(select_query, function (err, results) {
                        if(results[0]!=undefined){
                            var rider_to_driver_request_id = results[0].rider_to_driver_request_id;
                            var select_query = 'SELECT source_location,source_lat,source_lng,destination_location,destination_lat,destination_lng FROM rider_to_driver_request WHERE rider_to_driver_request.id = ' + parseInt(rider_to_driver_request_id);
                            db_connect.query(select_query, function (err, results) {
                                if(results[0]!= undefined){
                                  obj_response.source_location      = results[0].source_location;
                                  obj_response.source_lat           = results[0].source_lat;
                                  obj_response.source_lng           = results[0].source_lng;
                                  obj_response.destination_location = results[0].destination_location;
                                  obj_response.destination_lat      = results[0].destination_lat;
                                  obj_response.destination_lng      = results[0].destination_lng;
                                  io.sockets.connected[value.id].emit('get_current_ride_driver_info',obj_response);
                                }
                            });
                        }
                    });
                  });
                }
                /*else{
                  console.log('socket not found');
                }*/
              }
              /*else
              {
                console.log('socket not found');
              }*/
            }
          }
        });
      }
    }
  });

  /*---------------------------------------------------------------------------------------------------
  | After ride complete rider and driver will give rating to each other 
  ----------------------------------------------------------------------------------------------------*/
  io.on('store_review_details', function (data) {
    if(data!=undefined){

      var rider_id = driver_id = 0;
      
      var db_user_type = '';

      if(data.user_type == 'RIDER'){
        db_user_type = 'rider';
        rider_id     = parseInt(data.from_user_id);
        driver_id    = parseInt(data.to_user_id);
      }
      else if(data.user_type == 'DRIVER'){
        db_user_type = 'driver';
        driver_id    = parseInt(data.from_user_id);
        rider_id     = parseInt(data.to_user_id);
      }
      pool.getConnection(function (err, db_connect) {
          
        var insert_query = 'INSERT '+ 
                             'INTO '+ 
                                  'review('+
                                  'from_user_id,'+
                                  'to_user_id,'+
                                  'ride_id,'+
                                  'user_type,'+
                                  'rating,'+
                                  'rating_msg) '+  
                              'VALUES ('+
                                parseInt(data.from_user_id) + ','+ 
                                parseInt(data.to_user_id) + ','+ 
                                parseInt(data.ride_id) +',"'+
                                db_user_type + '",'+ 
                                parseFloat(data.rating) +',"'+
                                data.rating_msg +'" ' +')';
        db_connect.query(insert_query, function (err, results) {
          if(results!=undefined && results.affectedRows>0){
            var obj_response =  
                              {
                                status:'success',
                                msg   : 'Review details store successfully.'
                              };
            //after status updated emit to driver and rider
            if(arr_user_socket.length > 0) {
              arr_user_socket.forEach(function (currentValue, index) {
                if (currentValue != undefined) {
                  if (currentValue.rider_id != undefined) {
                    if (String(currentValue.rider_id) == String(rider_id)) {
                      if (io.sockets != undefined) {
                        if (io.sockets.connected[currentValue.id] != undefined) {
                          io.sockets.connected[currentValue.id].emit('store_review_result', obj_response);
                        }
                      }
                    }
                  }
                }
              });
            }
            /*emit data to driver */
            if(arr_ongoing_vehicle.length > 0) {
              arr_ongoing_vehicle.forEach(function (currentValue, index) {
                if (currentValue != undefined) {
                  if (currentValue.driver_id != undefined) {
                    if (String(currentValue.driver_id) == String(driver_id)) {
                      if (io.sockets != undefined) {
                        if (io.sockets.connected[currentValue.id] != undefined) {
                           io.sockets.connected[currentValue.id].emit('store_review_result', obj_response);
                        }
                      }  
                    }
                  }
                }
              });
            }

          } else {

            var obj_response =  
                              {
                                status:'error',
                                msg   : 'Problem occured while storing review details.'
                              };

            //after status updated emit to driver and rider
            if(arr_user_socket.length > 0) {
              arr_user_socket.forEach(function (currentValue, index) {
                if (currentValue != undefined) {
                  if (currentValue.rider_id != undefined) {
                    if (String(currentValue.rider_id) == String(rider_id)) {
                      if (io.sockets != undefined) {
                        if (io.sockets.connected[currentValue.id] != undefined) {
                          io.sockets.connected[currentValue.id].emit('store_review_result', obj_response);
                        }
                      }
                    }
                  }
                }
              });
            }
            /*emit data to driver */
            if(arr_ongoing_vehicle.length > 0) {
              arr_ongoing_vehicle.forEach(function (currentValue, index) {
                if (currentValue != undefined) {
                  if (currentValue.driver_id != undefined) {
                    if (String(currentValue.driver_id) == String(driver_id)) {
                      if (io.sockets != undefined) {
                        if (io.sockets.connected[currentValue.id] != undefined) {
                           io.sockets.connected[currentValue.id].emit('store_review_result', obj_response);
                        }
                      }  
                    }
                  }
                }
              });
            }
          }
        });
      });
    }
  });
  
  io.on('send_vehicle_info_to_track', function (data) {

      if(arr_socket.length > 0) {
        arr_socket.forEach(function (value, index) {
          if (value != undefined) {
            if (value.driver_id != undefined) {
              if (String(value.driver_id) == String(data.driver_id)) {
                 arr_socket.splice(index, 1);
              }
            }
          }
        });
      }      

      arr_socket.push({
            id         : data.id,
            vehicle_id : data.vehicle_id,
            driver_id  : data.driver_id,
      });
  });
  
  io.on('admin_track_vehicle_info', function (data) {
     arr_admin_socket.push({
            socket_id : data.id,
            admin_id  : data.admin_id
      });
  });

  io.on('show_all_driver',function (data) {
      if(data!=undefined){

          /*delete existing value of vehicle id from the array.*/
          /*if(arr_ongoing_vehicle.length > 0) {
            arr_ongoing_vehicle.forEach(function (currentValue, index) {
              if (currentValue != undefined) {
                if (currentValue.driver_id != undefined) {
                  if (String(currentValue.driver_id) == String(data.driver_id)) {
                    arr_ongoing_vehicle.splice(index, 1);
                  }
                }
              }
            });
          }
          arr_ongoing_vehicle.push(data);*/
        // console.log(arr_ongoing_vehicle);

      }     
  });

  // When ride is stoped the call the event to remove the vehicle from the ongoing vehicle list and fire a function to calculate the total ride distance.
  io.on('stop_vehicle_tracking', function (data) {
    if (data.driver_id != undefined) {
      arr_ongoing_vehicle.forEach(function (currentValue, index_inside) {
        if (currentValue != undefined) {
          if (currentValue.driver_id != undefined) {
            if (String(currentValue.driver_id) == String(data.driver_id)) {
              /*console.log('index_inside ->', index_inside);
              console.log('Before ->',arr_ongoing_vehicle);*/
              arr_ongoing_vehicle = arr_ongoing_vehicle.splice(index_inside, 1);
              //console.log('After ->', arr_ongoing_vehicle);
            }
          }
        }
      });      
    }
  });

  io.on('socket_logout', function (data) {
    
    // console.log('data receveid');

    // console.log(data);
    
    if (data.driver_id != undefined && data.rider_id != undefined) {

      // console.log('before rider remove',arr_user_socket);

      /*logout rider data*/
      if(data.rider_id!=0){
        if(arr_user_socket.length>0){  
          arr_user_socket.forEach(function (value, index) {
            if (value != undefined) {
              if (value.rider_id != undefined) {
                if (parseInt(value.rider_id) == parseInt(data.rider_id)) {
                  // console.log('index : ' ,index);
                  arr_user_socket.splice(index, 1);
                }
              }
            }
          });
        }      
      }
      
      
      // console.log('after rider remove',arr_user_socket);

      // console.log('before driver remove',arr_ongoing_vehicle);
      /*logout driver data*/
      if(data.driver_id!=0){
        if(arr_ongoing_vehicle.length>0){  
          arr_ongoing_vehicle.forEach(function (value, index) {
            if (value != undefined) {
              if (value.driver_id != undefined) {
                if (parseInt(value.driver_id) == parseInt(data.driver_id)) {
                  arr_ongoing_vehicle.splice(index, 1);
                }
              }
            }
          });
        }

      // console.log('after driver remove',arr_ongoing_vehicle);
        
        if(arr_ongoing_rides.length > 0) {
          arr_ongoing_rides.forEach(function (value, index) {
            if (value != undefined) {
              if (value.driver_id != undefined) {
                if (parseInt(value.driver_id) == parseInt(data.driver_id)) {
                   arr_ongoing_rides.splice(index, 1);
                }
              }
            }
          });
        }      
      }

    }
  });

  io.on('disconnect', function () {
    
    /*remove admin socket data*/
    var socket_index = findSocket(socket.id);
    if (socket_index != -1) {
      arr_socket = arr_socket.splice(socket_index, 1);
    }

    /*remove user socket data*/
    var socket_index = findUserSocket(socket.id);
    if (socket_index != -1) {
      arr_user_socket = arr_user_socket.splice(socket_index, 1);
    }

    /*remove driver socket data*/
    var socket_index = findOngoingVehicleSocket(socket.id);
    if (socket_index != -1) {
      arr_ongoing_vehicle = arr_ongoing_vehicle.splice(socket_index, 1);
    }
  });


function findSocket(socket_id) {
  arr_socket.forEach(function (currentValue, index) {
    if (currentValue != undefined) {
      if (currentValue.vehicle_id != undefined) {
        if (currentValue.id == socket_id) {
          return index;
        }
      }
    }
  });
  return -1;
}


function findUserSocket(socket_id) {
  arr_user_socket.forEach(function (currentValue, index) {
    if (currentValue != undefined) {
      if (currentValue.rider_id != undefined) {
        if (currentValue.id == socket_id) {
          return index;
        }
      }
    }
  });
  return -1;
}

function findOngoingVehicleSocket(socket_id) {
  arr_ongoing_vehicle.forEach(function (currentValue, index) {
    if (currentValue != undefined) {
      if (currentValue.driver_id != undefined) {
        if (currentValue.id == socket_id) {
          return index;
        }
      }
    }
  });
  return -1;
}

function getDistance(slat, slng, clat, clng) {

  if(slat!=undefined && slng!=undefined && clat!=undefined && clng!=undefined){

    var R = 6378137; // Earth’s mean radius in meter

    var dLat = rad(clat - slat);
    var dLong = rad(clng - slng);

    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(rad(clat)) * Math.cos(rad(clat)) *
      Math.sin(dLong / 2) * Math.sin(dLong / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c;
    var k = d / 1000; // returns the distance in meter
    return parseFloat(k.toFixed(2)); /*fix 2 digts after decimal*/
  }
  else{
    return 20.0;
  }  
}

function rad(x) {
  return x * Math.PI / 180;
};


function storeLocationInfo(location_data) {
  /* var location_data = {
                            'vehicle_id': 4,
                            'lat': 19.293000000000266,
                            'lng': 73.09470000000104,
                            'location_description': 'Mumbai, India. lat-> 19.293000000000266 , lng -> 73.09470000000104',
                            'ride_id': 2
                          };*/
  pool.getConnection(function (err, db_connect) {

    if (err) {
      console.log('Error in connection database.');
      return;
      //res.json({ "code": 100, "status": "Error in connection database" });
      //return;
    }

    if (location_data == undefined) {
      console.log('Error in getting the location data.');
      return;
      // res.json({ "code": 100, "status": "Error in getting the location data." });
      // return;
    }

    async.waterfall([
        function (callback) {
            var query_rides = 'SELECT * FROM rides WHERE rides.ride_id = ' + parseInt(location_data.ride_id) + ' AND rides.vehicle_id = ' + parseInt(location_data.vehicle_id);
            //console.log(query_rides);
            db_connect.query(query_rides, function (err, results) {
              if (err) throw err;
              if (results != undefined) {
                callback(null, results,asd);
              } else {
                callback('error', 'Error while accessing the records');
              }
            });
        },
        function (query_result, callback) {            
          if (query_result != undefined && query_result.length > 0) {
              
            //   _.each(query_result, function (data, index) {
                
            //     console.log(obj_location          = JSON.parse(data['locations']));
                
            //     /*var obj_location          = JSON.parse(data['locations']);
            //     var obj_current_locations = { 'lat': location_data.lat, 'lng': location_data.lng };
            //     var locations             = JSON.stringify(obj_location.push(obj_current_locations));

            //     console.log(locations);*/

            //     /*let ride_id    = parseInt(location_data.ride_id);
            //     let vehicle_id = parseInt(location_data.vehicle_id);

            //     'UPDATE rides SET locations = '+locations+' WHERE rides.ride_id = '+parseInt(ride_id)+ ' AND ' +' WHERE rides.vehicle_id = '+parseInt(vehicle_id);*/

            // });
            
            // return;
            // here update the result for existing query.

            var arr_current_locations = [{ 'lat': location_data.lat, 'lng': location_data.lng }];
            var locations = JSON.stringify(arr_current_locations);

            console.log(locations);

            /*var query_updating = 'INSERT INTO rides VALUES(' + parseInt(location_data.ride_id) + ',' + location_data.vehicle_id + ',' + locations + 'ONGOING' + ')';
            db_connect.query(query_updating, function (err, results) {
              if (err) throw err;
              if (results != undefined) {
                callback(null, results);
              } else {
                callback('error', 'Error while creating the records.');
              }
            });*/

          } else {
            // Creating a record in a database.
            var arr_current_locations = [{ lat: location_data.lat, lng: location_data.lng }];

            var locations = JSON.stringify(arr_current_locations);
            console.log(locations);

            var query_inserting = 'INSERT INTO rides(ride_id,vehicle_id,locations,status)  VALUES(' + parseInt(location_data.ride_id) + ',' + parseInt(location_data.vehicle_id) + ',' + '"hesllo"' + ',"ONGOING" ' +')';
            // console.log(query_inserting);

            db_connect.query(query_inserting, function (err, results) {
              if (err) throw err;
              if (results != undefined) {
                callback(null, results);
              } else {
                callback('error', 'Error while creating the records.');
              }
            });
          }
        }
    ], function (err, result) {
        if (err) throw err;
        console.log('Main Callback --> ' + result);
    });

    console.log('Program End');

    /*if(location_data.ride_id != undefined && location_data.vehicle_id != undefined ) {
      console.log('connected as id ' + db_connect.threadId);
      var query_rides = 'SELECT * FROM rides WHERE rides.ride_id = ' + location_data.ride_id + ' AND rides.vehicle_id = ' + location_data.ride_id;

      db_connect.query(query_rides, function (err, results) {
        if (err) throw err;
        var current_locations = { 'lat': location_data.lat, 'lng': location_data.lng };
        current_locations = current_locations.json({ 'lat': location_data.lat, 'lng': location_data.lng });

        if ( results != undefined && results.length > 0) {
          var query_inserting = 'INSERT INTO rides VALUES(' + location_data.ride_id + ',' + location_data.vehicle_id + ',' + location_data.vehicle_id + ')';
        }
      });
    }
    */

    return;
  });
}
