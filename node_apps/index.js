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

var dl  = require('delivery');

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
	res.sendFile(__dirname + '/index.html');
});

// app.timeout = 30000;

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
var arr_user_socket     = [];
var arr_ongoing_vehicle = [];
var arr_ongoing_rides   = [];


function send_available_vehicle_info_to_rider(){

	if(arr_user_socket.length > 0) {
		arr_user_socket.forEach(function (value, index) {
			if (value != undefined) {
				if (io.sockets != undefined) {
					if (io.sockets.connected[value.id] != undefined) {

						var arr_result = [];

						if(arr_ongoing_vehicle.length > 0) {
							arr_ongoing_vehicle.forEach(function (inner_value, index_index) {
								if (inner_value != undefined && io.sockets != undefined) {
									if (io.sockets.connected[inner_value.id] != undefined && inner_value.driver_id!=undefined && inner_value.service_id!=undefined) {
										if(String(value.service_id) == String(inner_value.service_id)  && inner_value.driver_status == "AVAILABLE") {

											var calculated_distance = getDistance(value.source_lat, value.source_lng, inner_value.lat, inner_value.lng);
											var obj_tmp = {};

											obj_tmp['lat']          = inner_value.lat;
											obj_tmp['lng']          = inner_value.lng;
											obj_tmp['vehicle_id']   = inner_value.vehicle_id;
											obj_tmp['driver_id']    = inner_value.driver_id;
											obj_tmp['driver_name']  = inner_value.driver_name;
											obj_tmp['min_distance'] = calculated_distance;
											obj_tmp['fair_charge']  = parseFloat(inner_value.fair_charge);

											if(calculated_distance <= 15) {
												obj_tmp['marker_status'] = 'SHOW';
											} else {
												obj_tmp['marker_status'] = 'REMOVE';
											}

											io.sockets.connected[value.id].emit('get_all_vehicle_info', obj_tmp);

							  // console.log('driver emiited to :--> ',value.id);
							  // console.log('driver emiited data :--> ',obj_tmp);

							  // arr_result.push(obj_tmp);
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
	else{
		console.log('no drivers available : '); 
	}
}

io.on('connection', function (socket) {

	var interval = setInterval(function () {
	  // send_available_vehicle_info_to_rider();
	},3000);

	/*
	|check for connected riders data if data found replace old socket id with new one
	*/
	if(socket.handshake.query.rider_id!=undefined)
	{
		if(arr_user_socket.length > 0)
		{
			arr_user_socket.forEach(function (value, index)
			{
				if (value != undefined)
				{
					if (value.rider_id != undefined)
					{
						if (String(value.rider_id) == String(socket.handshake.query.rider_id))
						{
							/*also check for ongoing ride if the ride is break or not*/

							if(arr_ongoing_rides.length > 0)
							{
								arr_ongoing_rides.forEach(function (inner_value, index)
								{
									if (inner_value != undefined)
									{
										if (inner_value.rider_id != undefined)
										{
											if (String(inner_value.rider_id) == String(value.rider_id))
											{
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

	var delivery = dl.listen(socket);
	delivery.on('receive.success',function(file){
		var params = file.params;
		console.log(params)

		var data = params;
		if(data!=undefined)
		{
			var curr_date = moment(Date.now()).format('YYYY-MM-DD HH:mm:ss');
			var unique_id = (Date.now() * Math.floor(Math.random() * 49));
			var ride_unique_id = 'QPB-'+unique_id;
			var calculated_distance = 0;
			var booking_id = 0;

			var obj_response = {
									booking_id : 0,
									booking_package_id : 0
								};
			// console.log("Params => ", data);

			var obj_booking = 
		  	{
		  		ride_unique_id 				: ride_unique_id,
		  		user_id             		: data.user_id,
		  		booking_type             	: data.booking_type,
		  		booking_date             	: curr_date,

		  		pickup_location				: data.pickup_location,
		  		pickup_lat					: data.pickup_lat,
		  		pickup_long					: data.pickup_long,
		  		drop_location 				: data.drop_location,
		  		drop_lat 					: data.drop_lat,
		  		drop_long 					: data.drop_long,
		  		distance 					: 0,
		  		
		  		admin_commission 			: 0,
		  		
		  		promo_code_id 				: data.promo_code_id,
		  		is_promo_code_applied 		: 'NO',
		  		promo_code 					: '',
		  		promo_percentage 			: 0,
		  		promo_max_amount 			: 0,

		  		card_id						: data.card_id
		  	};

		  	var obj_booking_package = 
		  	{
		  		booking_id 			: 0,
		  		package_type 		: data.package_type,
		  		package_length 		: data.package_length,

		  		package_breadth		: data.package_breadth,
		  		package_height		: data.package_height,
		  		package_volume		: 0,
		  		package_weight 		: data.package_weight,
		  		package_quantity 	: data.package_quantity
		  	};

			pool.getConnection(function (err, db_connect) {
		  		async.waterfall([
		  			function (callback) {
		  				// console.log(obj_booking.pickup_lat, obj_booking.pickup_long, obj_booking.drop_lat,obj_booking.drop_long);

	  					if(obj_booking.pickup_lat != 0 && obj_booking.pickup_long != 0 &&
	  						obj_booking.drop_lat != 0 && obj_booking.drop_long != 0){
	  						
	  						var calculated_distance = getDistance(obj_booking.pickup_lat, obj_booking.pickup_long, obj_booking.drop_lat, obj_booking.drop_long);

  							if (calculated_distance != undefined ) {
  								obj_booking.distance = calculated_distance;
  								// console.log(calculated_distance);
  								callback(null, obj_booking);
  							} else {
  								callback('error', 'Error while calculating distance');
  							}
	  					}else{
	  						callback(null, obj_booking);
	  					}
		  			}
		  			,function (obj_booking, callback) {
		  				// console.log("volume",obj_booking_package);
	  					if(obj_booking_package != undefined){
	  						if(obj_booking_package.package_length != undefined && obj_booking_package.package_length != 0 &&
	  							obj_booking_package.package_breadth != undefined && obj_booking_package.package_breadth != 0 &&
	  							obj_booking_package.package_height != undefined && obj_booking_package.package_height != 0)
		  					{
		  						var package_volume = obj_booking_package.package_length * obj_booking_package.package_breadth * obj_booking_package.package_height;
		  						if(package_volume != undefined && package_volume != 0)
		  						{
		  							obj_booking_package.package_volume = package_volume;
		  							callback(null, obj_booking_package);
		  						}else
		  						{
		  							callback('error', 'Invalid package volume generated');
		  						}
		  					}else
		  					{
								callback('error', 'Invalid package dimensions');
		  					}
	  					}else{
	  						callback(null, obj_booking);
	  					}
		  			}
		  			,function (obj_booking_package, callback) {
		  				// console.log("promo",obj_booking.distance);
	  					promo_code_id = data.promo_code_id;
	  					if(promo_code_id!=undefined && promo_code_id != 0){
	  						var select_query = 'SELECT id,code,percentage,max_amount FROM promo_offer WHERE promo_offer.id = ' + parseInt(promo_code_id);
	  						db_connect.query(select_query, function (err, results) {
	  							if (err) throw err;
	  							if (results != undefined) {
	  								callback(null, results);
	  							} else {
	  								callback('error', 'Error while accessing the records');
	  							}
	  						});
	  					}else{
	  						callback(null, obj_booking);
	  					}
		  			}
		  			,function(query_result,callback)
		  			{
		  				// console.log("Calculate promo",obj_booking);
		  				if(query_result != null && query_result[0] != undefined){
		  					obj_booking.is_promo_code_applied = 'YES';
		  					obj_booking.promo_code            = query_result[0].code;
		  					obj_booking.promo_percentage      = parseFloat(query_result[0].percentage);
		  					obj_booking.promo_max_amount      = parseFloat(query_result[0].max_amount);
		  				} 
		  				callback(null, obj_booking);
		  			}
		  			,function(obj_booking,callback){
		  				// console.log("Booking",obj_booking);

		  				if(obj_booking!=undefined){
							// callback(null, obj_booking);
		  					var insert_query = 'INSERT INTO booking_master'+
		  					'(booking_unique_id,'+
		  					'user_id,'+
		  					'booking_type,'+
		  					'booking_date,'+
		  					'pickup_location,'+
		  					'pickup_lat,'+
		  					'pickup_long,'+
		  					'drop_location,'+
		  					'drop_lat,'+
		  					'drop_long,'+
		  					'distance,'+
		  					'is_promo_code_applied,'+
		  					'promo_code,'+
		  					'promo_percentage,'+
		  					'promo_max_amount,'+  
		  					'card_id)'+
		  					'VALUES("'+ 
		  					obj_booking.ride_unique_id+'",'+
		  					obj_booking.user_id+',"'+
		  					obj_booking.booking_type+'","'+
		  					obj_booking.booking_date+'","'+
		  					obj_booking.pickup_location+'",'+
		  					obj_booking.pickup_lat+','+
		  					obj_booking.pickup_long+',"'+
		  					obj_booking.drop_location+'",'+ 
		  					obj_booking.drop_lat+','+ 
		  					obj_booking.drop_long+','+ 
		  					obj_booking.distance+',"'+ 
		  					obj_booking.is_promo_code_applied+'","'+
		  					obj_booking.promo_code+'",'+
		  					obj_booking.promo_percentage+','+
		  					obj_booking.promo_max_amount+','+
		  					obj_booking.card_id+')';
		  					// console.log(insert_query);
		  					db_connect.query(insert_query, function (err, results) {
		  						if (err) throw err;
		  						if (results != undefined) {
		  							// console.log(results);
		  							if(results.insertId !=undefined && results.insertId != 0)
		  							{
			  							var booking_id = results.insertId;
		  								callback(null, booking_id);
		  							}else
		  							{
		  								callback('error', 'Error while accessing the records');
		  							}

		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			}
		  			,function(booking_id, callback){
		  				// console.log("Booking result", booking_id);

		  				if(obj_booking!=undefined && booking_id != undefined){
							// callback(null, obj_booking);
		  					var insert_query = 'INSERT INTO booking_package'+
		  					'( booking_id,'+
		  					'package_type,'+
		  					'package_length,'+
		  					'package_breadth,'+
		  					'package_height,'+
		  					'package_volume,'+
		  					'package_weight,'+
		  					'package_quantity)'+
		  					'VALUES('+ 
		  					booking_id+',"'+
		  					obj_booking_package.package_type+'",'+
		  					obj_booking_package.package_length+','+
		  					obj_booking_package.package_breadth+','+
		  					obj_booking_package.package_height+','+
		  					obj_booking_package.package_volume+','+
		  					obj_booking_package.package_weight+','+ 
		  					obj_booking_package.package_quantity+' )';
		  					// console.log(insert_query);
		  					db_connect.query(insert_query, function (err, results) {
		  						if (err) throw err;
		  						if (results != undefined) {
		  							// console.log(results);
		  							callback(null, results);
		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			}
		  			,function(booking_package_result, callback){
		  				// console.log("Booking result", booking_id);

		  				if( booking_package_result != undefined ){
							// callback(null, obj_booking);
							var select_query = 'SELECT '+
											    '`D`.`id` AS `user_id`, '+
											    '`D`.`email`, '+
											    '`V`.`id` AS `vehicle_id`, '+
											    '`V`.`vehicle_name`, '+
											    '`VT`.`id` AS `vehicle_type_id`, '+
											    '`VT`.`vehicle_type` '+
											'FROM '+
											    '`users` AS `D` '+
											'JOIN `driver_car_relation` AS `DCR` ON `DCR`.`driver_id` = `D`.`id` '+
											'JOIN `vehicle` AS `V`ON `V`.`id` = `DCR`.`vehicle_id` '+
											'JOIN `vehicle_type` AS `VT` ON `VT`.`id` = `V`.`vehicle_type_id` '+
											'WHERE '+
											    '`D`.`account_status` = "approved" AND '+
											    '`V`.`is_active` = "1" AND '+
											    '`VT`.`is_active` = "1" AND '+
											    '`VT`.`deleted_at` = NULL AND '+
											    '`DCR`.`is_car_assign` = "1" AND '+
											    '('+
													'6371 * ACOS('+
														'COS(RADIANS('+obj_booking.pickup_lat+')) * COS(RADIANS(`latitude`)) * COS('+
															'RADIANS(`longitude`) - RADIANS('+obj_booking.pickup_long+')'+
														') + SIN(RADIANS('+obj_booking.pickup_lat+')) * SIN(RADIANS(`latitude`))'+
													')'+
												') < 20 AND '+
											    '`VT`.`vehicle_min_volume` <= '+obj_booking_package.package_volume+' AND '+
											    '`VT`.`vehicle_max_volume` >= '+obj_booking_package.package_volume;

		  					console.log(select_query);
		  					db_connect.query(select_query, function (err, results) {
		  						if (err) throw err;
		  						if (results != undefined) {
		  							console.log(results);
		  							return {ride_id : booking_id , data: results, status:'error'};
		  							//callback(null, results);
		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			}
		  		],function (err, result) {
		  			if (err) throw err;
		  				console.log('in error block');
		  				return {ride_id : 0 , status:'error'};
		  		});        
			});
		}

	});
	/*---------------------------------------------------------------------------------------------------
	| After user post load request then this listener will call and request will send to driver
	----------------------------------------------------------------------------------------------------*/
	socket.on('store_booking', function (data) {
		if(data!=undefined){
			var curr_date = moment(Date.now()).format('YYYY-MM-DD HH:mm:ss');
			var unique_id = (Date.now() * Math.floor(Math.random() * 49));
			var ride_unique_id = 'QPB-'+unique_id;
			var calculated_distance = 0;
			var booking_id = 0;

			var obj_response = {
									booking_id : 0,
									booking_package_id : 0
								};
			// console.log("Params => ", data);

			var obj_booking = 
		  	{
		  		ride_unique_id 				: ride_unique_id,
		  		user_id             		: data.user_id,
		  		booking_type             	: data.booking_type,
		  		booking_date             	: curr_date,

		  		pickup_location				: data.pickup_location,
		  		pickup_lat					: data.pickup_lat,
		  		pickup_long					: data.pickup_long,
		  		drop_location 				: data.drop_location,
		  		drop_lat 					: data.drop_lat,
		  		drop_long 					: data.drop_long,
		  		distance 					: 0,
		  		
		  		admin_commission 			: 0,
		  		
		  		promo_code_id 				: data.promo_code_id,
		  		is_promo_code_applied 		: 'NO',
		  		promo_code 					: '',
		  		promo_percentage 			: 0,
		  		promo_max_amount 			: 0,

		  		card_id						: data.card_id
		  	};

		  	var obj_booking_package = 
		  	{
		  		booking_id 			: 0,
		  		package_type 		: data.package_type,
		  		package_length 		: data.package_length,

		  		package_breadth		: data.package_breadth,
		  		package_height		: data.package_height,
		  		package_volume		: 0,
		  		package_weight 		: data.package_weight,
		  		package_quantity 	: data.package_quantity
		  	};

			pool.getConnection(function (err, db_connect) {
		  		async.waterfall([
		  			function (callback) {
		  				// console.log(obj_booking.pickup_lat, obj_booking.pickup_long, obj_booking.drop_lat,obj_booking.drop_long);

	  					if(obj_booking.pickup_lat != 0 && obj_booking.pickup_long != 0 &&
	  						obj_booking.drop_lat != 0 && obj_booking.drop_long != 0){
	  						
	  						var calculated_distance = getDistance(obj_booking.pickup_lat, obj_booking.pickup_long, obj_booking.drop_lat, obj_booking.drop_long);

  							if (calculated_distance != undefined ) {
  								obj_booking.distance = calculated_distance;
  								// console.log(calculated_distance);
  								callback(null, obj_booking);
  							} else {
  								callback('error', 'Error while calculating distance');
  							}
	  					}else{
	  						callback(null, obj_booking);
	  					}
		  			}
		  			,function (obj_booking, callback) {
		  				// console.log("volume",obj_booking_package);
	  					if(obj_booking_package != undefined){
	  						if(obj_booking_package.package_length != undefined && obj_booking_package.package_length != 0 &&
	  							obj_booking_package.package_breadth != undefined && obj_booking_package.package_breadth != 0 &&
	  							obj_booking_package.package_height != undefined && obj_booking_package.package_height != 0)
		  					{
		  						var package_volume = obj_booking_package.package_length * obj_booking_package.package_breadth * obj_booking_package.package_height;
		  						if(package_volume != undefined && package_volume != 0)
		  						{
		  							obj_booking_package.package_volume = package_volume;
		  							callback(null, obj_booking_package);
		  						}else
		  						{
		  							callback('error', 'Invalid package volume generated');
		  						}
		  					}else
		  					{
								callback('error', 'Invalid package dimensions');
		  					}
	  					}else{
	  						callback(null, obj_booking);
	  					}
		  			}
		  			,function (obj_booking_package, callback) {
		  				// console.log("promo",obj_booking.distance);
	  					promo_code_id = data.promo_code_id;
	  					if(promo_code_id!=undefined && promo_code_id != 0){
	  						var select_query = 'SELECT id,code,percentage,max_amount FROM promo_offer WHERE promo_offer.id = ' + parseInt(promo_code_id);
	  						db_connect.query(select_query, function (err, results) {
	  							if (err) throw err;
	  							if (results != undefined) {
	  								callback(null, results);
	  							} else {
	  								callback('error', 'Error while accessing the records');
	  							}
	  						});
	  					}else{
	  						callback(null, obj_booking);
	  					}
		  			}
		  			,function(query_result,callback)
		  			{
		  				// console.log("Calculate promo",obj_booking);
		  				if(query_result != null && query_result[0] != undefined){
		  					obj_booking.is_promo_code_applied = 'YES';
		  					obj_booking.promo_code            = query_result[0].code;
		  					obj_booking.promo_percentage      = parseFloat(query_result[0].percentage);
		  					obj_booking.promo_max_amount      = parseFloat(query_result[0].max_amount);
		  				} 
		  				callback(null, obj_booking);
		  			}
		  			,function(obj_booking,callback){
		  				// console.log("Booking",obj_booking);

		  				if(obj_booking!=undefined){
							// callback(null, obj_booking);
		  					var insert_query = 'INSERT INTO booking_master'+
		  					'(booking_unique_id,'+
		  					'user_id,'+
		  					'booking_type,'+
		  					'booking_date,'+
		  					'pickup_location,'+
		  					'pickup_lat,'+
		  					'pickup_long,'+
		  					'drop_location,'+
		  					'drop_lat,'+
		  					'drop_long,'+
		  					'distance,'+
		  					'is_promo_code_applied,'+
		  					'promo_code,'+
		  					'promo_percentage,'+
		  					'promo_max_amount,'+  
		  					'card_id)'+
		  					'VALUES("'+ 
		  					obj_booking.ride_unique_id+'",'+
		  					obj_booking.user_id+',"'+
		  					obj_booking.booking_type+'","'+
		  					obj_booking.booking_date+'","'+
		  					obj_booking.pickup_location+'",'+
		  					obj_booking.pickup_lat+','+
		  					obj_booking.pickup_long+',"'+
		  					obj_booking.drop_location+'",'+ 
		  					obj_booking.drop_lat+','+ 
		  					obj_booking.drop_long+','+ 
		  					obj_booking.distance+',"'+ 
		  					obj_booking.is_promo_code_applied+'","'+
		  					obj_booking.promo_code+'",'+
		  					obj_booking.promo_percentage+','+
		  					obj_booking.promo_max_amount+','+
		  					obj_booking.card_id+')';
		  					// console.log(insert_query);
		  					db_connect.query(insert_query, function (err, results) {
		  						if (err) throw err;
		  						if (results != undefined) {
		  							// console.log(results);
		  							if(results.insertId !=undefined && results.insertId != 0)
		  							{
			  							var booking_id = results.insertId;
		  								callback(null, booking_id);
		  							}else
		  							{
		  								callback('error', 'Error while accessing the records');
		  							}

		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			}
		  			,function(booking_id, callback){
		  				// console.log("Booking result", booking_id);

		  				if(obj_booking!=undefined && booking_id != undefined){
							// callback(null, obj_booking);
		  					var insert_query = 'INSERT INTO booking_package'+
		  					'( booking_id,'+
		  					'package_type,'+
		  					'package_length,'+
		  					'package_breadth,'+
		  					'package_height,'+
		  					'package_volume,'+
		  					'package_weight,'+
		  					'package_quantity)'+
		  					'VALUES('+ 
		  					booking_id+',"'+
		  					obj_booking_package.package_type+'",'+
		  					obj_booking_package.package_length+','+
		  					obj_booking_package.package_breadth+','+
		  					obj_booking_package.package_height+','+
		  					obj_booking_package.package_volume+','+
		  					obj_booking_package.package_weight+','+ 
		  					obj_booking_package.package_quantity+' )';
		  					// console.log(insert_query);
		  					db_connect.query(insert_query, function (err, results) {
		  						if (err) throw err;
		  						if (results != undefined) {
		  							// console.log(results);
		  							callback(null, results);
		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			}
		  			,function(booking_package_result, callback){
		  				// console.log("Booking result", booking_id);

		  				if( booking_package_result != undefined ){
							// callback(null, obj_booking);
							var select_query = 'SELECT '+
											    '`D`.`id` AS `user_id`, '+
											    '`D`.`email`, '+
											    '`V`.`id` AS `vehicle_id`, '+
											    '`V`.`vehicle_name`, '+
											    '`VT`.`id` AS `vehicle_type_id`, '+
											    '`VT`.`vehicle_type` '+
											'FROM '+
											    '`users` AS `D` '+
											'JOIN `driver_car_relation` AS `DCR` ON `DCR`.`driver_id` = `D`.`id` '+
											'JOIN `vehicle` AS `V`ON `V`.`id` = `DCR`.`vehicle_id` '+
											'JOIN `vehicle_type` AS `VT` ON `VT`.`id` = `V`.`vehicle_type_id` '+
											'WHERE '+
											    '`D`.`account_status` = "approved" AND '+
											    '`V`.`is_active` = "1" AND '+
											    '`VT`.`is_active` = "1" AND '+
											    '`VT`.`deleted_at` = NULL AND '+
											    '`DCR`.`is_car_assign` = "1" AND '+
											    '('+
													'6371 * ACOS('+
														'COS(RADIANS('+obj_booking.pickup_lat+')) * COS(RADIANS(`latitude`)) * COS('+
															'RADIANS(`longitude`) - RADIANS('+obj_booking.pickup_long+')'+
														') + SIN(RADIANS('+obj_booking.pickup_lat+')) * SIN(RADIANS(`latitude`))'+
													')'+
												') < 20 AND '+
											    '`VT`.`vehicle_min_volume` <= '+obj_booking_package.package_volume+' AND '+
											    '`VT`.`vehicle_max_volume` >= '+obj_booking_package.package_volume;

		  					console.log(select_query);
		  					db_connect.query(select_query, function (err, results) {
		  						if (err) throw err;
		  						if (results != undefined) {
		  							console.log(results);
		  							return {ride_id : booking_id , data: results, status:'error'};
		  							//callback(null, results);
		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			}
		  		],function (err, result) {
		  			if (err) throw err;
		  				console.log('in error block');
		  				return {ride_id : 0 , status:'error'};
		  		});        
			});
		}
	});

	/*---------------------------------------------------------------------------------------------------
	| After rider book specific driver then this listener will call and request will send to driver
	----------------------------------------------------------------------------------------------------*/
	socket.on('request_to_book_driver', function (data) {
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
							is_service_used      : data.is_service_used
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
							  //change driver status to busy if new request received form rider
							  arr_ongoing_vehicle[index].driver_status = 'BUSY';

							  if (io.sockets != undefined) {
							  	if (io.sockets.connected[currentValue.id] != undefined) {
							  		io.sockets.connected[currentValue.id].emit('send_receive_request_to_driver', obj_response);                                
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

	/*---------------------------------------------------------------------------------------------------
	| After rider enter source and destination and category then this listener will call and list all the rider request
	----------------------------------------------------------------------------------------------------*/
	socket.on('track_all_vehicles', function (data) {

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

		arr_user_socket.push({
			id                   : data.id,
			rider_id             : data.rider_id,
			rider_name           : data.rider_name,
			rider_email          : data.rider_email,
			rider_mobile         : data.rider_mobile,
			rider_type           : data.rider_type,
			rider_image          : data.rider_image,
			service_id           : data.service_id,
		  is_service_used      : data.is_service_used, // O normal special service or 1-> special service
		  source_location      : data.source_location,
		  destination_location : data.destination_location,
		  source_lat           : data.source_lat,
		  source_lng           : data.source_lng,
		  destination_lat      : data.destination_lat,
		  destination_lng      : data.destination_lng,
		});
	});

	/*---------------------------------------------------------------------------------------------------
	| After rider select specific category then this listener will through nearby drivers to rider
	----------------------------------------------------------------------------------------------------*/
	socket.on('show_on_map', function (data){

		if (data != undefined)
		{      

			// if(data.driver_id!=undefined && data.driver_id == 91){
			//     console.log('driver data :',data);
			// }

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

			//Send all data to rider socket.
			if(arr_user_socket.length > 0)
			{
				arr_user_socket.forEach(function (value, index)
				{
					if (value != undefined)
					{
						if (io.sockets != undefined)
						{
							if (io.sockets.connected[value.id] != undefined)
							{
					  			// Match driver status and service id of driver and user ride requirement.
					  			if( (data.service_id != undefined) && (data.service_id == value.service_id) && 
					  				(data.is_special_service != undefined) && (value.is_service_used == data.is_special_service) && 
					  				(data.driver_status != undefined) && (data.driver_status == "AVAILABLE"))
					  			{

					  				var calculated_distance = getDistance(value.source_lat, value.source_lng, data.lat, data.lng);
					  				var obj_tmp = {};

					  				obj_tmp['lat']          = data.lat;
					  				obj_tmp['lng']          = data.lng;
					  				obj_tmp['vehicle_id']   = data.vehicle_id;
					  				obj_tmp['driver_id']    = data.driver_id;
					  				obj_tmp['driver_name']  = data.driver_name;
					  				obj_tmp['min_distance'] = calculated_distance;
					  				obj_tmp['fair_charge']  = parseFloat(data.fair_charge);

					  				if(calculated_distance <= 15) {
					  					obj_tmp['marker_status'] = 'SHOW';
					  				} else {
					  					obj_tmp['marker_status'] = 'REMOVE';
					  				}
									// Emit the data which is sent to the user.
									io.sockets.connected[value.id].emit('get_all_vehicle_info', obj_tmp);
								}
							}
						}
					}
				});
			}
		}
	});

	/*---------------------------------------------------------------------------------------------------
	| After rider book specific driver then this listener will call and request will send to driver
	----------------------------------------------------------------------------------------------------*/
	socket.on('request_to_book_driver', function (data) {
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
							is_service_used      : data.is_service_used
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
							  //change driver status to busy if new request received form rider
							  arr_ongoing_vehicle[index].driver_status = 'BUSY';

							  if (io.sockets != undefined) {
							  	if (io.sockets.connected[currentValue.id] != undefined) {
							  		io.sockets.connected[currentValue.id].emit('send_receive_request_to_driver', obj_response);                                
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

	/*---------------------------------------------------------------------------------------------------
	| After rider book specific driver and driver perform action then this listener will call
	----------------------------------------------------------------------------------------------------*/
	socket.on('process_request_by_driver', function (data) {

		if(data!=undefined){

			pool.getConnection(function (err, db_connect) {

				if (err) {
					console.log("Error in connection database");
					return;
				}

				var status          = 'ERROR';
				var request_id      = data.request_id;
				var rider_id        = data.rider_id;
				var driver_id       = data.driver_id;
				var is_service_used = data.is_service_used;

				var obj_response = 
				{
					status           : status,
					rider_id         : parseInt(rider_id),
					driver_id        : parseInt(driver_id),
					request_id       : parseInt(request_id),
					is_service_used  : is_service_used,
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
				  //if request accept_by_driver then make entry in ride table as to be picked
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
									if (String(currentValue.driver_id) == String(data.driver_id)) {
										if (io.sockets != undefined) {
											if (io.sockets.connected[currentValue.id] != undefined) {
												io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
											}
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
								if (String(currentValue.driver_id) == String(data.driver_id)) {
									if (io.sockets != undefined) {
										if (io.sockets.connected[currentValue.id] != undefined) {
											io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);                                
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

	/*---------------------------------------------------------------------------------------------------
	| After accept ride request from driver maintain ride status in blow listener
	----------------------------------------------------------------------------------------------------*/
	socket.on('driver_live_trip_tracking', function (data) {

		if (data != undefined) {
			/*delete existing value of vehicle id from the array.*/

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
			arr_ongoing_rides.push(data);

			if(data.ride_status == 'IN_TRANSIT'){

				if(data.is_transit == 1){
					var ride_id = parseInt(data.ride_id);
					pool.getConnection(function (err, db_connect) {
						var update_query = "UPDATE ride SET status = 'IN_TRANSIT'  WHERE id = "+data.ride_id;  
						db_connect.query(update_query, function (err, results) {
							if(results!=undefined && results.affectedRows>0){
								console.log('IN_TRANSIT status updated to DB');  
							}
						});    
					});
				}
			} 
			else if(data.ride_status == 'COMPLETED'){

				var ride_id         = parseInt(data.ride_id);
				var final_distance  = parseFloat(data.distance);
				var coordinates     = data.coordinates;
				var is_service_used = data.is_service_used; 

				pool.getConnection(function (err, db_connect) {

					var insert_query = 'INSERT '+ 
					'INTO '+ 
					'ride_coordinate('+
					'ride_id,'+
					'coordinates )'+
					'VALUES ('+
					parseInt(ride_id) + ',\''+ 
					coordinates +'\' ' +')';

					db_connect.query(insert_query, function (err, results) {
				 	 // console.log('ride coordinate tbl insert : ',results);
				 	});

					var update_query = "UPDATE ride SET status = '"+data.ride_status+"', distance = "+final_distance+" WHERE id = "+ride_id;
					db_connect.query(update_query, function (err, results) {
						
						if(results!=undefined && results.affectedRows > 0)
						{
							if(is_service_used!=undefined && is_service_used == 0)
							{
								var driver_fair_charge = total_distance = final_amount = total_amount = admin_commission = promo_percentage = promo_max_amount = applied_promo_code_charge = 0;
								var select_query = 'SELECT id,driver_fair_charge, admin_commission,is_promo_code_applied,promo_percentage,promo_max_amount,distance FROM ride WHERE ride.id = ' + parseInt(ride_id);
								
								db_connect.query(select_query, function (err, results) {
									if (results != undefined) {
										//send invoice to driver and rider
										driver_fair_charge = parseFloat(results[0].driver_fair_charge);
										total_distance     = parseFloat(results[0].distance);
										
										//total amount is calculated based on distance and driver fair charge.
										total_amount       = (parseFloat(driver_fair_charge) * parseFloat(total_distance));

										admin_commission   = parseFloat(results[0].admin_commission);
										promo_percentage   = parseFloat(results[0].promo_percentage);
										promo_max_amount   = parseFloat(results[0].promo_max_amount);

										is_promo_code_applied   = results[0].is_promo_code_applied;
										
										if(is_promo_code_applied == 1){
											if(promo_percentage!=0 && total_amount!=0){
												applied_promo_code_charge = ((parseFloat(total_amount) * parseFloat(promo_percentage)) / 100);
												if(promo_max_amount!=0){
													if(applied_promo_code_charge > promo_max_amount){
														applied_promo_code_charge = parseFloat(promo_max_amount);
													}
												}
											}
										}

										//final amount is calculated based on total amount minus applied promo code charge.
										final_amount    = (parseFloat(total_amount) - parseFloat(applied_promo_code_charge));

										var obj_response = 
										{
											driver_fair_charge        : driver_fair_charge,
											total_distance            : total_distance,
											final_amount              : final_amount,
											applied_promo_code_charge : applied_promo_code_charge
										};

										var update_query = "UPDATE ride SET total_amount = "+total_amount+" , final_amount = "+final_amount+" , applied_promo_code_charge ="+applied_promo_code_charge+" WHERE id = "+ride_id;

										db_connect.query(update_query, function (err, results) {
											if(results!=undefined && results.affectedRows>0){

											}
										})


										// console.log('obj_response send to driver and rider : ',obj_response);
										// console.log('all rider object : ',arr_user_socket);
										// console.log('all driver object : ',arr_ongoing_vehicle);
										// console.log('current object received : ',data);

										//after status updated emit to driver and rider
										
										if(arr_user_socket.length > 0) {
											arr_user_socket.forEach(function (currentValue, index) {
												if (currentValue != undefined) {
													if (currentValue.rider_id != undefined) {
														if (String(currentValue.rider_id) == String(data.rider_id)) {
															if (io.sockets != undefined) {
																if (io.sockets.connected[currentValue.id] != undefined) {
																	io.sockets.connected[currentValue.id].emit('complete_trip_by_driver', obj_response);
																	console.log('emitted invoice event to rider : ',obj_response);
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
														if (String(currentValue.driver_id) == String(data.driver_id)) {
															if (io.sockets != undefined) {
																if (io.sockets.connected[currentValue.id] != undefined) {
																	io.sockets.connected[currentValue.id].emit('complete_trip_by_driver', obj_response);
																	console.log('emitted invoice event to driver :',obj_response);
																}
															}
														}
													}
												}
											});
										}
									}
								});
							}
						}
					});

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
				});
			}
			else if(data.ride_status == 'CANCELLED'){
				var ride_id = parseInt(data.ride_id);
				pool.getConnection(function (err, db_connect) {
					var update_query = "UPDATE ride SET status = 'CANCELED',reason = '"+data.reason+"' WHERE id = "+data.ride_id;  
					db_connect.query(update_query, function (err, results) {
						if(results!=undefined && results.affectedRows>0){
							console.log('CANCELLED status updated to DB');
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

			//emit data to rider application
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
										driver_name          : data.driver_name,
										ride_status          : data.ride_status,
										is_service_used      : is_service_used,
										lat                  : data.lat,
										lng                  : data.lng,
										source_location      : value.source_location,
										destination_location : value.destination_location,
										source_lat           : value.source_lat,
										source_lng           : value.source_lng,
										destination_lat      : value.destination_lat,
										destination_lng      : value.destination_lng
									};

									if (io.sockets != undefined) {
										if (io.sockets.connected[value.id] != undefined) {
											io.sockets.connected[value.id].emit('get_current_ride_driver_info',obj_response);
										}
									}

								}
							}
						}
					}
				});
			}

			//Show tracking to admin panel modules
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
														obj_tmp['service_id']     = value.service_id;
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

									io.sockets.connected[value.socket_id].emit('track_all_vehicles_by_admin', obj_tmp);
								}
							}
						}
					}
				});
			}
		}
	});

	/*---------------------------------------------------------------------------------------------------
	| After ride complete rider and driver will give rating to each other 
	----------------------------------------------------------------------------------------------------*/
	socket.on('store_review_details', function (data) {

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

	socket.on('admin_track_vehicle_info', function (data) {

		if(arr_admin_socket.length > 0) {
			arr_admin_socket.forEach(function (value, index) {
				if (value != undefined) {
					if (value.admin_id != undefined) {
						if (String(value.admin_id) == String(data.admin_id)) {
							arr_admin_socket.splice(index, 1);
						}
					}
				}
			});
		}      

		arr_admin_socket.push({
			socket_id : data.id,
			admin_id  : data.admin_id
		});
	});

	socket.on('socket_logout', function (data) {

		if (data.driver_id != undefined && data.rider_id != undefined) {

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

			/*logout driver data*/
			if(data.driver_id!=0){

				if(arr_ongoing_vehicle.length>0){  
					arr_ongoing_vehicle.forEach(function (value, index) {
						if (value != undefined) {
							if (value.driver_id != undefined) {
								if (parseInt(value.driver_id) == parseInt(data.driver_id)) {

									var obj_data = value;
									obj_data.marker_status = 'REMOVE';

									if(arr_user_socket.length > 0) {
										arr_user_socket.forEach(function (inner_value, index) {
											if (inner_value != undefined) {
												if (io.sockets != undefined) {

													console.log(io.sockets.connected[inner_value.id]);

													if (io.sockets.connected[inner_value.id] != undefined) {
														if(String(obj_data.service_id) == String(inner_value.service_id)) 
														{
															io.sockets.connected[inner_value.id].emit('get_all_vehicle_info', obj_data);
															console.log('emitted to rider : ',inner_value.id);
														}
														else{
															console.log('service id missmatch : '); 
														}
													}
													else{
														console.log('socket not found : '); 
													}
												}
											}
										});
									}

									arr_ongoing_vehicle.splice(index, 1);
								}
							}
						}
					});
				}

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

				/*remove user from admin panel tracking*/
				if(arr_admin_socket.length > 0) {
					arr_admin_socket.forEach(function (value, index) {
						if (value != undefined) {
							if (io.sockets != undefined) {
								if (io.sockets.connected[value.socket_id] != undefined) {
									io.sockets.connected[value.socket_id].emit('track_user_logout', data);
								}
							}
						}
					});
				}
			}
		}
	});

	socket.on('disconnect', function () {

		clearInterval(interval);

		/*remove admin socket data*/
		var socket_index = findAdminSocket(socket.id);
		if (socket_index != -1) {
			arr_admin_socket = arr_admin_socket.splice(socket_index, 1);
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
});

function process_accept_request(data,obj_response){
	var request_id = data.request_id;
	if(request_id!=undefined){

		var unique_id = (Date.now() * Math.floor(Math.random() * 49));
		var ride_unique_id = 'RIDE-'+unique_id;

		//is_service_used 0 means this is a normal service
		//is_service_used 1 means this is a special service

		if(obj_response.is_service_used == 0){

		  	var obj_ride = 
		  	{
		  		rider_to_driver_request_id : parseInt(request_id),
		  		ride_unique_id             : ride_unique_id,
		  		driver_fair_charge         : 0,
		  		distance                   : 0,
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

		  	pool.getConnection(function (err, db_connect) {
		  		async.waterfall([
		  			function (callback) {
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

		  				if(query_result!=null && query_result[0]!=undefined){
		  					obj_ride.is_promo_code_applied = '1';
		  					obj_ride.promo_code            = query_result[0].code;
		  					obj_ride.promo_percentage      = parseFloat(query_result[0].percentage);
		  					obj_ride.promo_max_amount      = parseFloat(query_result[0].max_amount);
		  				} 
		  				callback(null, obj_ride);
		  			},
		  			function(obj_ride,callback){

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
		  							callback(null, results);
		  						} else {
		  							callback('error', 'Error while accessing the records');
		  						}
		  					});
		  				}
		  			},
		  			function(query_result,callback){

		  				var ride_id = 0;
		  				if(query_result!=undefined && query_result.affectedRows>0){
		  					if(query_result.insertId!=undefined){
		  						ride_id = query_result.insertId;
		  					}
		  				}

		  				if(ride_id!=0){
		  					obj_response.ride_id = parseInt(ride_id);
		  					/*emit data to rider */
		  					if(arr_user_socket.length > 0) {
		  						arr_user_socket.forEach(function (currentValue, index) {
		  							if (currentValue != undefined) {
		  								if (currentValue.rider_id != undefined) {
		  									if (String(currentValue.rider_id) == String(obj_response.rider_id)) {
		  										if (io.sockets != undefined) {
		  											if (io.sockets.connected[currentValue.id] != undefined) {
		  												io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
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
		  									if (String(currentValue.driver_id) == String(obj_response.driver_id)) {
		  										if (io.sockets != undefined) {
		  											if (io.sockets.connected[currentValue.id] != undefined) {
		  												io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
		  											}
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
		  										if (io.sockets != undefined) {
		  											if (io.sockets.connected[currentValue.id] != undefined) {
		  												io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
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
		  									if (String(currentValue.driver_id) == String(obj_response.driver_id)) {
		  										if (io.sockets != undefined) {
		  											if (io.sockets.connected[currentValue.id] != undefined) {
		  												io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
		  											}
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
		else if(obj_response.is_service_used == 1){

			var obj_ride = 
			{
				rider_to_driver_request_id : parseInt(request_id),
				ride_unique_id             : ride_unique_id,
				driver_fair_charge         : 0,
				distance                   : 0,
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
			obj_ride.driver_fair_charge+','+ 
			obj_ride.distance+','+ 
			obj_ride.admin_commission+',"'+
			obj_ride.is_promo_code_applied+'","'+
			obj_ride.promo_code+'",'+
			obj_ride.promo_percentage+','+ 
			obj_ride.promo_max_amount+','+ 
			obj_ride.total_amount+','+ 
			obj_ride.final_amount+',"'+ 
			obj_ride.ride_date+'","'+
			obj_ride.ride_start_time+'","'+
			obj_ride.ride_end_time+'","'+
			obj_ride.payment_status+'","'+
			obj_ride.status +'" ' +')';

			pool.getConnection(function (err, db_connect) {

				db_connect.query(insert_query, function (err, query_result) {
					if (results != undefined) {
						var ride_id = 0;
						if(query_result!=undefined && query_result.affectedRows>0){
							if(query_result.insertId!=undefined){
								ride_id = query_result.insertId;
							}
						}

						if(ride_id!=0){
							obj_response.ride_id = parseInt(ride_id);
							/*emit data to rider */
							if(arr_user_socket.length > 0) {
								arr_user_socket.forEach(function (currentValue, index) {
									if (currentValue != undefined) {
										if (currentValue.rider_id != undefined) {
											if (String(currentValue.rider_id) == String(obj_response.rider_id)) {
												if (io.sockets != undefined) {
													if (io.sockets.connected[currentValue.id] != undefined) {
														io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
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
											if (String(currentValue.driver_id) == String(obj_response.driver_id)) {
												if (io.sockets != undefined) {
													if (io.sockets.connected[currentValue.id] != undefined) {
														io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
													}
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
												if (io.sockets != undefined) {
													if (io.sockets.connected[currentValue.id] != undefined) {
														io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
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
											if (String(currentValue.driver_id) == String(obj_response.driver_id)) {
												if (io.sockets != undefined) {
													if (io.sockets.connected[currentValue.id] != undefined) {
														io.sockets.connected[currentValue.id].emit('request_reply_from_driver', obj_response);
													}
												} 
											}
										}
									}
								});
							}
						}
					}
				});
			});
		}
	}
}

function findAdminSocket(socket_id) {
	arr_admin_socket.forEach(function (currentValue, index) {
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