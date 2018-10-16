<?php

class Matomo {

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

	public static function apiWidget($widget, $method, $period, $date, $limit) {
		$url   = option('sylvainjule.matomo.url');
	  	$id    = option('sylvainjule.matomo.id');
	  	$token = option('sylvainjule.matomo.token');

		$url .= "?module=API&method=" . $method;
		$url .= "&idSite=". $id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP";
		$url .= $limit ? "&filter_limit=" . $limit : '';
		$url .= "&token_auth=". $token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public static function apiChart($method, $period, $date) {
		$url   = option('sylvainjule.matomo.url');
	  	$id    = option('sylvainjule.matomo.id');
	  	$token = option('sylvainjule.matomo.token');

		$url .= "?module=API&method=". $method;
		$url .= "&idSite=". $id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP&token_auth=". $token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public static function apiOverview($method, $period, $date) {
		$url   = option('sylvainjule.matomo.url');
	  	$id    = option('sylvainjule.matomo.id');
	  	$token = option('sylvainjule.matomo.token');

		$url .= "?module=API&method=". $method;
		$url .= "&idSite=". $id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP&token_auth=". $token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public static function apiRealtime() {
		$url   = option('sylvainjule.matomo.url');
	  	$id    = option('sylvainjule.matomo.id');
	  	$token = option('sylvainjule.matomo.token');
	  	$method = 'Live.getCounters';

		$url .= "?module=API&method=" . $method;
		$url .= "&idSite=". $id ."&lastMinutes=3";
		$url .= "&format=PHP&token_auth=". $token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public static function apiBulkSummary() {
		$url   = option('sylvainjule.matomo.url');
	  	$id    = option('sylvainjule.matomo.id');
	  	$token = option('sylvainjule.matomo.token');
	  	$method = 'Live.getCounters';

		$url .= "?module=API&method=API.getBulkRequest";
		$url .= "&token_auth=". $token ."&format=PHP";

		$url .= "&urls[0]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $id ."&period=day&date=today");

		$url .= "&urls[1]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $id ."&period=day&date=last7");

		$url .= "&urls[2]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $id ."&period=month&date=today");

		$url .= "&urls[3]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $id ."&period=year&date=today");

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

}