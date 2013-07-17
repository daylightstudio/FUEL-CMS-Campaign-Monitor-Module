<?php
require_once(FUEL_PATH.'libraries/Fuel_base_controller.php');
class Dashboard extends Fuel_base_controller {
	
	public $nav_selected = 'tools/campaign_monitor|tools/campaign_monitor/:any';
	public $view_location = 'campaign_monitor';
	private $_cm = NULL;
	private $_config = array();
	
	function __construct()
	{
		parent::__construct();
		$this->_validate_user('tools/campaign_monitor');
	}
	
	function index()
	{
		if (!is_ajax())
		{
			$vars['summaries'] = $this->fuel->campaign_monitor->render(TRUE);
			$crumbs = array(lang('campaign_monitor_titlebar'));
			$this->fuel->admin->set_titlebar($crumbs);
			$this->fuel->admin->render('_admin/campaign_monitor', $vars, '', CAMPAIGN_MONITOR_FOLDER);
		}
		else
		{
			$this->fuel->campaign_monitor->render();
		}
		
	}
	

}

/* End of file dashboard.php */
/* Location: ./codeigniter/application/modules/campaign_monitor/controllers/dashboard.php */