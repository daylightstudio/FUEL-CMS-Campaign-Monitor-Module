<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/campaign_monitor'] = 'Campaign Monitor';


/*
|--------------------------------------------------------------------------
| TOOL SETTING: Validation settings
|--------------------------------------------------------------------------
*/
$config['campaign_monitor'] = array();

// account name is sub domain (e.g. http://mycompany.createsend.com) would be "mycompany"
$config['campaign_monitor']['account_name'] = '';

// Campaign Monitor API key
$config['campaign_monitor']['api_key'] = '';

// client name you want to display reports for
$config['campaign_monitor']['client_name'] = '';

// number of campaigns to list
$config['campaign_monitor']['num_campaigns'] = 5;

// use caching... Recommended
$config['campaign_monitor']['use_cache'] = TRUE;

 //default time to live = 600 seconds 10 mins
$config['campaign_monitor']['cache_ttl'] = 600;

//used as the subfolder name for caching
$config['campaign_monitor']['cache_folder'] = 'campaign_monitor';


