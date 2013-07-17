<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Campaign Monitor
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/campaign_monitor
 */

// --------------------------------------------------------------------


class Fuel_campaign_monitor extends Fuel_advanced_module {
	
	protected $_cm = NULL;
	
	/**
	 * Constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();

		if (!extension_loaded('curl')) 
		{
			$this->_add_error(lang('error_no_curl_lib'));
		}
		
		if (empty($params))
		{
			$params['name'] = 'campaign_monitor';
		}
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the page analysis object
	 *
	 * Accepts an associative array as input, containing backup preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params)
	{
		parent::initialize($params);
		$this->set_params($this->_config);
		$this->CI->load->library('cache');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Connects to the Campaign Monitor API and returns the CampaignMonitor object
	 *
	 * @access	public
	 * @return	object
	 */	
	function connect()
	{
		if (!isset($this->_cm))
		{
			require_once(CAMPAIGN_MONITOR_PATH.'libraries/CMBase.php');
			$this->_cm = new CampaignMonitor($this->config('api_key'));
		}
		return $this->_cm;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the client ID specified in the config
	 *
	 * @access	public
	 * @return	int
	 */	
	function client_id()
	{
		$cm = $this->connect();
		$clients = $cm->userGetClients();
		if (isset($clients['anyType'], $clients['anyType']['Client']))
		{
			foreach($clients['anyType']['Client'] as $client)
			{
				if ($client['Name'] == $this->config('client_name'))
				{
					$client_id = $client['ClientID']; 
					return $client_id;
				}
			}
		}
		return NULL;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the campaign data for a given client
	 *
	 * @access	public
	 * @param	int The client ID
	 * @param	int The number of campaigns to return (optional)
	 * @return	array
	 */	
	function campaigns($client_id, $limit = NULL)
	{
		if (empty($limit))
		{
			$limit = $this->config('num_campaigns');
		}
		$cache_id = 'campaigns';
		$cm = $this->connect();
		$campaigns = $cm->clientGetCampaigns($client_id); 
		
		if (isset($campaigns['anyType'], $campaigns['anyType']['Campaign']))
		{
			return array_slice($campaigns['anyType']['Campaign'], 0, $limit);
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a data summary for a particicular campaign
	 *
	 * @access	public
	 * @param	int The campaign ID
	 * @return	array
	 */
	function campaign_summary($campaign_id)
	{
		$cm = $this->connect();
		$summaries = $cm->campaignGetSummary($campaign_id);
		if (isset($summaries['anyType']))
		{
			return $summaries['anyType'];
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a data summary for multiple campaigns
	 *
	 * @access	public
	 * @return	array
	 */
	function summaries()
	{
		$summaries = array();
		
		$cache_id = fuel_cache_id();
		if ($this->config('use_cache'))
		{
			$cached_file = $this->CI->cache->get($cache_id, $this->config('cache_folder'));
			if (!empty($cached_file)) $summaries = $cached_file;
		}
		
		if (empty($summaries))
		{
			$client_id = $this->client_id();
			if (!empty($client_id))
			{
				$campaigns = $this->campaigns($client_id);
				if (!empty($campaigns))
				{
					foreach($campaigns as $campaign)
					{
						$summary = $this->campaign_summary($campaign['CampaignID']);
						if (!empty($summary))
						{
							$summaries[$campaign['Name']] = $summary;
						}
					}
				}
				if ($this->config('use_cache'))
				{
					$this->CI->cache->save($cache_id, $summaries, $this->config('cache_folder'), $this->config('cache_ttl'));
				}
			}
		}
		
		return $summaries;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Renders the campaign information into HTML
	 *
	 * @access	public
	 * @param	boolean Determines whether to return it as a string or to output it (optional)
	 * @return	mixed (string or void)
	 */
	function render($return = FALSE)
	{
		$vars['num_campaigns'] = $this->config('num_campaigns');
		$vars['account_name'] = $this->config('account_name');
		$vars['client_name'] = $this->config('client_name');

		$vars['summaries'] = $this->summaries();
		if ($return)
		{
			return $this->CI->load->module_view(CAMPAIGN_MONITOR_FOLDER, '_admin/dashboard', $vars, TRUE);
		}
		else
		{
			$this->CI->load->module_view(CAMPAIGN_MONITOR_FOLDER, '_admin/dashboard', $vars);
		}
		
	}
}

/* End of file Fuel_page_analysis.php */
/* Location: ./modules/fuel/libraries/Fuel_page_analysis.php */