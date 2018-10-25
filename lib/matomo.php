<?php

class Matomo {

	/* Should we load the snippet?
	---------------------------------*/

	public static function allowed() {
		$visitor = new Kirby\Http\Visitor();

		// if deactivated or missing option
		if(!option('sylvainjule.matomo.id') || !option('sylvainjule.matomo.url') || !option('sylvainjule.matomo.active')) {
			return false;
		} 
		// if debugging the plugin, return true before checking the blacklist / user status
		if(option('sylvainjule.matomo.debug')) {
			return true;
		}
		// if IP is blacklisted (= localhost by default)
		if(in_array($visitor->ip(), option('sylvainjule.matomo.blacklist'))) {
			return false;
		}
		// if users tracking is disabled and an I'm logged in
		if(!option('sylvainjule.matomo.trackUsers') && site()->user()) {
			return false;
		} 

		return true;
	}


	/* Matomo API calls
	---------------------------------*/

	protected $url   = null;
	protected $id    = null;
	protected $token = null;

	public function __construct() {
    	$this->url   = option('sylvainjule.matomo.url');
		$this->id    = option('sylvainjule.matomo.id');
		$this->token = option('sylvainjule.matomo.token');
    }

	public function apiWidget($widget, $method, $period, $date, $limit) {
		$url  = $this->url;
		$url .= "?module=API&method=" . $method;
		$url .= "&idSite=". $this->id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP";
		$url .= $limit ? "&filter_limit=" . $limit : '';
		$url .= "&token_auth=". $this->token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public function apiChart($method, $period, $date) {
		$url  = $this->url;
		$url .= "?module=API&method=". $method;
		$url .= "&idSite=". $this->id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP&token_auth=". $this->token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public function apiOverview($method, $period, $date) {
		$url  = $this->url;
		$url .= "?module=API&method=". $method;
		$url .= "&idSite=". $this->id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP&token_auth=". $this->token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public function apiRealtime() {
		$url  = $this->url;
		$method = 'Live.getCounters';

		$url .= "?module=API&method=" . $method;
		$url .= "&idSite=". $this->id ."&lastMinutes=3";
		$url .= "&format=PHP&token_auth=". $this->token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public function apiBulkSummary() {
		$url  = $this->url;
		$method = 'Live.getCounters';

		$url .= "?module=API&method=API.getBulkRequest";
		$url .= "&token_auth=". $this->token ."&format=PHP";

		$url .= "&urls[0]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=day&date=today");

		$url .= "&urls[1]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=day&date=last7");

		$url .= "&urls[2]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=month&date=today");

		$url .= "&urls[3]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=year&date=today");

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

}