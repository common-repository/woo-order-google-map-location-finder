<?php
/*
=== Woo order google map location finder ===
Plugin Name: Woo order google map location finder
Contributors: Shamim MS
Plugin URI: http://ownawebbrand.com/woo-order-google-map-location-finder
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GGHCYVAYXBXY6
Tags: google map order locator for woocommerce, admin order location, woocommerce local delivery location finder,order delivery location finder,Googlemap location, googlemap order location, woocommerce order google map location finder,
woocommerce customer Locator
Description: Woo order google map location finder helps to find delivery  location of customer who make the order.It is working with WooCommerce only.
Author: Shamim MS 
Author URI: http://ownawebbrand.com/
Version: 1.1
*/
//wogmlf
add_action('woocommerce_after_order_notes', 'wogmlf_my_custom_checkout_field');

function wogmlf_my_custom_checkout_field( $checkout ) {
	echo '<meta name="viewport" content="initial-scale=1.0, user-scalable=no">';
	echo '<div id="my_custom_checkout_field" ><!--<h3>'.__('My Field').'</h3>-->';
				
	woocommerce_form_field( 'my_lat_long', array( 
		'type' 			=> 'text', 
		'class' 		=> array('my-field-class orm-row-wide input-text'), 
		'label' 		=> __('Your Location'), 
		'placeholder' 	=> __('geo location'),
		), $checkout->get_value( 'my_lat_long' ));
	
	echo '</div><div id="map_go"></div>';
	
	?>
	<style>
		p#my_lat_long_field {display: none;}
		#map_go {
			width: 400px;
			height: 400px;
		  }
	</style>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDc3rlwwWUHNOFL-jOOk9WilLBmhHtNDHU&signed_in=true&callback=initMap"
        async defer>
    </script>
	<script type="text/javascript">
		
		
    function initMap() {
		directionsService = new google.maps.DirectionsService();
		directionsDisplay = new google.maps.DirectionsRenderer();
		
		
		var AE = new google.maps.LatLng(25,55);
		var IT = new google.maps.LatLng(42.745334, 12.738430);

		var noStreetNames = [{
			featureType: "road",
			elementType: "labels",
			stylers: [{
				visibility: "off"}]}];

		hideLabels = new google.maps.StyledMapType(noStreetNames, {
			name: "hideLabels"
		});

		//var curloc = {};
		

		var showPosition = function(position) {
			var userLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
			
			var marker = new google.maps.Marker({
				position: userLatLng,
				title: 'Your Location',
				draggable: true,
				map: map
			});

			var infowindow = new google.maps.InfoWindow({
				content: '<div id="infodiv" style="width: 300px">'+userLatLng+'</div>'
			});
			map.setCenter(marker.getPosition());
			google.maps.event.addListener(marker, 'dragend', function() {
				//$('input[name=my_lat_long]').val(position.coords.latitude+','+position.coords.longitude);
				var infowindow = new google.maps.InfoWindow({
				content: '<div id="infodiv" style="width: 300px">'+marker.getPosition()+'</div>'
			});
				//infowindow.open(map, marker)
				map.setCenter(marker.getPosition());
			});
			
			google.maps.event.addListener(map, 'idle', function() {
					 var infowindow = new google.maps.InfoWindow({
				content: '<div id="infodiv" style="width: 300px">'+marker.getPosition()+'</div>'
			});
				//$('input[name=my_lat_long]').val(position.coords.latitude+','+position.coords.longitude);
				var srr = marker.getPosition();
				$('input[name=my_lat_long]').val(srr);
				//infowindow.open(map, marker)
				map.setCenter(marker.getPosition())

				});
			
			google.maps.event.addListener(marker, 'mouseover', function() {
					var infowindow = new google.maps.InfoWindow({
				content: '<div id="infodiv" style="width: 300px">'+marker.getPosition()+'</div>'
			});
				//infowindow.open(map, marker)
			});

			google.maps.event.addListener(marker, 'mouseout', function() {
				t = setTimeout(function() {
					infowindow.close()
				}, 3000);
			});

			google.maps.event.addListener(infowindow, 'domready', function() {
				$('#infodiv').on('mouseenter', function() {
					clearTimeout(t);
				}).on('mouseleave', function() {
					t = setTimeout(function() {
						infowindow.close()
					}, 1000);
				})
			});

			var input = document.getElementById('nptsearch');
			var autocomplete = new google.maps.places.Autocomplete(input);

			autocomplete.bindTo('bounds', map);

			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				infowindow.close();
				var place = autocomplete.getPlace();
				if (place.geometry.viewport) {
					map.fitBounds(place.geometry.viewport);
				} else {
					map.setCenter(place.geometry.location);
					map.setZoom(7);
				}

				var image = new google.maps.MarkerImage(
				place.icon, new google.maps.Size(71, 71), new google.maps.Point(0, 0), new google.maps.Point(17, 34), new google.maps.Size(35, 35));
				marker.setIcon(image);
				marker.setPosition(place.geometry.location);

				infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
				infowindow.open(map, marker);
			});

			map.setCenter(marker.getPosition());
		}

		navigator.geolocation.getCurrentPosition(showPosition);
		//alert(coords.latitude);
		//alert(marker.position);
		var myOptions = {
			zoom: 13,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: AE
		}
		
		map = new google.maps.Map(document.getElementById("map_go"), myOptions);
		directionsDisplay.setMap(map);

		map.mapTypes.set('hide_street_names', hideLabels);

		function offsetCenter(latlng, offsetx, offsety) {
			var scale = Math.pow(2, map.getZoom());
			var nw = new google.maps.LatLng(
			map.getBounds().getNorthEast().lat(), map.getBounds().getSouthWest().lng());

			var worldCoordinateCenter = map.getProjection().fromLatLngToPoint(latlng);
			var pixelOffset = new google.maps.Point((offsetx / scale) || 0, (offsety / scale) || 0)

			var worldCoordinateNewCenter = new google.maps.Point(
			worldCoordinateCenter.x - pixelOffset.x, worldCoordinateCenter.y + pixelOffset.y);

			var newCenter = map.getProjection().fromPointToLatLng(worldCoordinateNewCenter);

			map.setCenter(newCenter);
		}

		function addmarker(latilongi) {
			var marker = new google.maps.Marker({
				position: latilongi,
				title: 'new marker',
				draggable: true,
				map: map
			});

			var infowindow = new google.maps.InfoWindow({
				content: '<div id="infodiv2">infowindow!</div>'
			});
			//map.setZoom(15);
			map.setCenter(marker.getPosition())
			//infowindow.open(map, marker)
		}

		$(window).on('resize', function() {
			var currCenter = map.getCenter();
			google.maps.event.trigger(map, 'resize');
			map.setCenter(currCenter);
		})

		$('#btnlabels').toggle(function() {
			map.setZoom(15);
			map.setMapTypeId('hide_street_names')
		}, function() {
			map.setMapTypeId(google.maps.MapTypeId.ROADMAP)
		})

		$('#btnoffset').on('click', function() {
			offsetCenter(map.getCenter(), 0, -100)
		})

		$('#btnaddmarker').on('click', function() {
			addmarker(IT)
		})
	}	
	//});
	</script>
	 
	
	<?php
}
/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'wogmlf_my_custom_checkout_field_process');
function wogmlf_my_custom_checkout_field_process() {
	global $woocommerce;
	
	// Check if set, if its not set add an error. This one is only requite for companies
	//if ($_POST['billing_company'])
		//if (!$_POST['my_lat_long']) 
			//$woocommerce->add_error( __('Please enter your .') );
}
/**
 * Update the user meta with field value
 **/
add_action('woocommerce_checkout_update_user_meta', 'wogmlf_my_custom_checkout_field_update_user_meta');
function wogmlf_my_custom_checkout_field_update_user_meta( $user_id ) {
	if ($user_id && $_POST['my_lat_long']) update_user_meta( $user_id, 'my_field_name', sanitize_text_field(substr($_POST['my_lat_long'], 1, -1)) );
}
/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'wogmlf_my_custom_checkout_field_update_order_meta');
function wogmlf_my_custom_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['my_lat_long']) update_post_meta( $order_id, 'woo_display_order_location', sanitize_text_field(substr($_POST['my_lat_long'], 1, -1)));
}
/**
 * Add the field to order emails
 **/
add_filter('woocommerce_email_order_meta_keys', 'wogmlf_my_custom_checkout_field_order_meta_keys');
function wogmlf_my_custom_checkout_field_order_meta_keys( $keys ) {
	$keys[] = 'woo_display_order_location';
	return $keys;
}


// Add WooCommerce customer username to edit/view order admin page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'wogmlf_woo_display_order_username', 10, 1 );
function wogmlf_woo_display_order_username( $order ){
    global $post;
    
	$customer_user = get_post_meta( $post->ID, 'woo_display_order_location', true );
    echo '<p><strong style="display: block;">'.__('Customer location').':</strong></p>';
print '<style>
		  #map {
			width: 500px;
			height: 400px;
		  }
		</style>
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script>
      function initialize() {
        var mapCanvas = document.getElementById("map");
		var myLatlng = new google.maps.LatLng(' . $customer_user. ');
        var mapOptions = {
          center: new google.maps.LatLng(' . $customer_user. '),
          zoom: 15,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
		var marker = new google.maps.Marker({
			position: myLatlng,
			title:"Order Location"
		});

		var map = new google.maps.Map(mapCanvas, mapOptions);
		// To add the marker to the map, call setMap();
		marker.setMap(map);
      }
      google.maps.event.addDomListener(window, "load", initialize);
    </script>
 
    <div id="map"></div>';
}

?>