<style>
    <?php require get_stylesheet_directory() . '/map/css/bootstrap.min.css';?>  

    /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
    #map {
        height: 500px;
        width: auto;
        transform: translateY(-25px); 

    }   

    .directions-button {
        background: #00adef;    
        width: 100%;
        color: #fff;
        text-align: center;
        padding: 10px;  
        font-size: 16px; 
        font-weight: bold
        vertical-align: bottom;
        border-radius: 5px;              
    }

    #geo-locations-list {
        height: 500px;           
        background-color: inherit;
    }

    .address{
        transform: translateY(-30px);   
    }
    
    .popup_window{        
        height: auto;
    }    

    a.popup_directions {
        background: #00adef;
        width: 100%;
        color: #fff;
        text-align: center;
        padding: 3px;
        font-size: 12px;
        font-weight: bold vertical-align: bottom;
        border-radius: 5px;
    }    

    .location-title{
        font-weight: bold;
        white-space: nowrap;
        font-size: 18px; 

    }

    .location-info-container{
        padding-top: 3%;
        padding-bottom: 10%;
        align-content: center;;
    }

    @media only screen and (max-width: 769px) {
        #geo-locations-list {
            height: 500px;        
            margin-bottom:60%;
            background-color: inherit;
            justify-content: center;
            transform: translateX(7%);
        }
        .location-info-container {
            margin-bottom: 5%;
            text-align: center;
        }        
    }
    
</style>
<script async defer>
    //array that holds html for map pop up windows
    var locations = [];
    //array that holds lattitude for map markers
    var lat = [];
    //array that holds longitude for map markers
    var lng = [];
</script>
<div class="col-md-8 col-md-push-4">
    <div id="map">    
    </div>
</div>

<div class="col-md-4 col-md-pull-8">
    <div id="geo-locations-list">
        <?php 
        $args = array(
            'posts_per_page'    => -1,
            'post_type'         => 'locations',
            'orderby' => 'title',
            'order'   => 'ASC',
            'post_status' => ('publish')
        );
        $wp_query = new WP_Query($args);
        if( $wp_query->have_posts() ) {
            while( $wp_query->have_posts() ) {
                $wp_query->the_post();

                //location information switch
                $use_location_data=get_field('use_location_data');
                if($use_location_data){

                    //map variables
                    $map_location=get_field('location_on_a_map');                
                    $lat=$map_location['lat'];
                    $lng=$map_location['lng'];  

                    //post title
                    $title=get_the_title(); 

                    //address variables
                    $address_1=get_field('address_1');     
                    $address_2=get_field('address_2');
                    $city=get_field('city');
                    $state=get_field('state');
                    $zip=get_field('zipcode');

                    //contact variables
                    $phone_number=get_field('phone_number');

                    //address display
                    $address_display = '';
                    $address_display .= ($address_1 ? $address_1 . '<br>' : '');
                    $address_display .= ($address_2 ? $address_2 . '<br/>' : '');
                    $address_display .= ($city ? $city . ', ' : '');
                    $address_display .= ($state ? $state . ' ' : '');
                    $address_display .= ($zip ? $zip . ' ' : '');
                    ?>
                    <div class="location-info-container">
                        <div class="row">
                            <div class="col-md-6 col-xs-pull-1">
                            <?php
                                 $map_info = "<div class='address'>" ."<span class='location-title'>" . $title . "</span><br>". $address_display . "<br><a class='phone-numbers' href='tel:1-".$phone_number."'>" . $phone_number . "</a></div>";
                                 
                                 echo $map_info;
                            ?>
                            </div>
                            <div class="col-md-6 col-xs-pull-1">
                                <?php
                                   
                                $address_link = "<a href='http://maps.google.com/maps?q=" . $address_1 . "," . $city . "," . $state . "&z=17' target='_blank' class='directions-button'>Get Directions</span></a>";
                                echo $address_link;
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php $popup_window="<div class='popup_window'><strong>".$title."</strong><br>".$address_display."<br>"."<a href='http://maps.google.com/maps?q=" . $address_1 . "," . $city . "," . $state . "&z=17' target='_blank' class='popup_directions'>Get Directions</span></a>"."</div>";?>
                    <script>
                        locations.push("<?php echo $popup_window;?>");
                        lat.push("<?php echo $lat;?>");
                        lng.push("<?php echo $lng;?>");
                    </script>
                    <?php                  
                    
                }                

            }                
                wp_reset_postdata();
        }

        ?>
    </div>
</div>


<script async defer>
    var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {        
        zoom: 9,
        //sets center of map to coordinates here
        center: new google.maps.LatLng(27.7949197, -82.331301),
        mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        var infowindow = new google.maps.InfoWindow();
            var bounds = new google.maps.LatLngBounds();
        for(i=0; i<locations.length; i++){
            var marker = '';
                
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat[i], lng[i]),
                    animation: google.maps.Animation.DROP,
                    map: map
                    
                });

                 // process multiple info windows
                (function(marker, i) {
                    // add click event
                    google.maps.event.addListener(marker, 'click', function() {
                        infowindow.setContent(locations[i]);
                        infowindow.open(map, marker);
                    });
                })(marker, i);

                //extend the bounds to include each marker's position
                bounds.extend(marker.position);

                //now fit the map to the newly inclusive bounds
                map.fitBounds(bounds);
        }
    }
</script>

<!-- add google maps api key here -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=API_KEY_HERE&callback=initMap" async defer></script>