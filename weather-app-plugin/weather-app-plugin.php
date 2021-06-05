<?php
/**
 * Plugin Name: Weather Forcast
 * Description: This plugin will let you use a shortcode to show the weather in your area
 **/
 
    function weather_forcast_admin_menu_option(){
        add_menu_page('Weather Location Settings', 'Weather Location', 'manage_options', 'weather-forcast-admin-menu', 'weather_forcast_page', 'dashicons-location-alt', 200 );
    }

    add_action('admin_menu','weather_forcast_admin_menu_option');

    function weather_forcast_page(){

        if(array_key_exists('submit_location_update', $_POST)){

            update_option('weather_forcast', $_POST['location_city']);
            ?>
            <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><strong><h4>Settings have been saved.</h4></strong></div>
            <?php
        }

        $location_city = get_option('weather_forcast', 'none');

        ?>
        <div class="wrap">
            <h2>Location for Weather Display</h2>
            <form method="post" action="">
            <label for="location_city">Location City</label>
            <textarea name="location_city" class="large-text"><?php print $location_city; ?></textarea>
            <input type="submit" name="submit_location_update" class="button button-primary" value="UPDATE LOCATION">
            </form>
        </div>
        <?php
    }

    function weather_forcast_display(){
        $location_city = get_option('weather_forcast', 'none');

        $apiKey = "0cb29173d72ed702569b4ea6397a235c";
        $cityId = $location_city;
        $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . $cityId . "&units=imperial&appid=" . $apiKey;
        print $googleApiUrl;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_close($ch);
        // var_dump($response);
        $data = json_decode($response);
        $currentTime = time();
        ?>
            
<body>
    <div class="report-container">
        <h2><?php echo $data->name; ?> Weather Status</h2>
        <div class="time">
            <div><?php echo date("l g:i a", $currentTime); ?></div>
            <div><?php echo date("F jS, Y",$currentTime); ?></div>
            <div><?php echo ucwords($data->weather[0]->description); ?></div>
        </div>
        <div class="weather-forecast">
            <img
                src="http://openweathermap.org/img/w/<?php echo $data->weather[0]->icon; ?>.png"
                class="weather-icon" /><div>Max Temp: <?php echo $data->main->temp_max; ?>°F</div>
                <div>Min Temp: <?php echo $data->main->temp_min; ?>°F</div>
        </div>
        <div class="time">
            <div>Humidity: <?php echo $data->main->humidity; ?> %</div>
            <div>Wind: <?php echo $data->wind->speed; ?> mph</div>

        </div>
    </div>
        <?php


    }
    add_shortcode('weather' , 'weather_forcast_display' );

add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );

function prefix_add_my_stylesheet() {
    wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

?>