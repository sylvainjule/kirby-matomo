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
		if(!option('sylvainjule.matomo.trackUsers') && kirby()->user()) {
			return false;
		}

		return true;
	}


	/* Matomo API calls
	---------------------------------*/

	protected $url   = null;
	protected $id    = null;
	protected $token = null;
	protected $methodsMap = array (
		'referrerType' => 'Referrers.getReferrerType',
		'websites'     => 'Referrers.getWebsites',
		'socials'      => 'Referrers.getSocials',
		'devicesType'  => 'DevicesDetection.getType',
		'keywords'     => 'Referrers.getKeywords',
		'popularPages' => 'Actions.getPageUrls',
	);

	public function __construct() {
    	$this->url   = option('sylvainjule.matomo.url');
		$this->id    = option('sylvainjule.matomo.id');
		$this->token = is_callable($this->token) ? $this->token() : option('sylvainjule.matomo.token');
    }

	public function apiWidget($widget, $method, $period, $date, $limit, $lang) {
		$url  = $this->url;
		$url .= "?module=API&method=" . $method;
		$url .= "&idSite=". $this->id ."&period=". $period ."&date=" . $date;
		$url .= "&format=PHP&language=". $lang;
		$url .= $limit ? "&filter_limit=" . $limit : '';
		$url .= "&token_auth=". $this->token;

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);

		return $content;
	}

	public function apiBulkWidgets($widgets, $period, $date, $limit, $lang) {
		$widgets = json_decode(stripslashes($widgets));

		$url  = $this->url;

		$url .= "?module=API&method=API.getBulkRequest";
		$url .= "&token_auth=". $this->token ."&format=PHP";

		$i = 0;
		foreach($widgets as $widget) {
			$url .= "&urls[". $i ."]=";
			$url .= urlencode("method=". $this->methodsMap[$widget] ."&idSite=". $this->id ."&period=". $period ."&date=" . $date ."&language=". $lang ."&filter_limit=" . $limit);
			$i++;
		}

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

	public function apiPageMetrics($period, $uri, $lang) {
		$url  = $this->url;
		$url .= "?module=API&method=Actions.getPageUrls";
		$url .= "&idSite=". $this->id ."&period=". $period ."&date=today";
		$url .= "&format=PHP&token_auth=". $this->token;
		$url .= $lang['multilang'] ? '&expanded=1' : '';

		$fetched = file_get_contents($url);
		$content = unserialize($fetched);
		$content = $this->filterPageMetrics($content, $uri, $lang);
		return $content;
	}

	/* Filter page metrics
	---------------------------------*/

	public function filterPageMetrics($content, $uri, $lang) {
		$site  = site();
		$kirby = kirby();
		$multilang   = $lang['multilang'];
		$overview    = $lang['overview'];
		$currentLang = $lang['current'];
		$defaultLang = $lang['default'];
		$isDefault   = $currentLang == $defaultLang;

		// set the correct language and find the page
		if($multilang) $site->visit($site->homePage(), $currentLang);
		$page   = $site->childrenAndDrafts()->find($uri);

		// re-create the uri from the public url
		$currentUrl  = $page->url(); // get the page url
		$currentUri  = str_replace(site()->url(), '', $currentUrl); // substract the site url to get the uri
		$currentUri  = '/' . ltrim($currentUri, '/'); // make sure it starts by a single forward slash

		// get the current page stats
		$current = $this->filterWithUri($content, $currentUri, $currentLang, $isDefault);

		// return it if there's no other language or if they don't need to be displayed
		if(!$multilang || !$overview) {
			return array('current' => $current);
		}
		// else, check all other languages and merge their metrics
		else {
			$metrics = array();

			// add the current language metrics
			array_push($metrics, $current);

			// loop through all language
			foreach($kirby->languages() as $language) {
				if($language->code() == $currentLang) continue;

				$tempCode    = $language->code();
				$tempDefault = $tempCode == $defaultLang;
				$tempUrl     = $page->url($tempCode);
				$tempUri     = str_replace(site()->url($tempCode), '', $tempUrl);
				$tempUri     = '/' . ltrim($tempUri, '/');

				$tempMetrics = $this->filterWithUri($content, $tempUri, $tempCode, $tempDefault);
				array_push($metrics, $tempMetrics);
			}

			// filter out empty languages
			$metrics = array_filter($metrics);

			if(!count($metrics)) {
				$all = array();
			}
			else {
				// merge the metrics
				$visits      = array_column($metrics, 'visits');
				$durations   = array_column($metrics, 'duration');
				$bounces     = array_column($metrics, 'bounce');
				$exits       = array_column($metrics, 'exit');

				// sum the visits
				$totalVisits = array_sum($visits);

				// find the average duration
				$totalDurations = $this->multiplyArraysByKey($visits, $durations);
				$duration = $totalDurations / $totalVisits;

				// find the average bounce %
				$totalBounces = $this->multiplyArraysByKey($visits, $bounces);
				$bounce = $totalBounces / $totalVisits;

				// find the average exit %
				$totalExits = $this->multiplyArraysByKey($visits, $exits);
				$exit = $totalExits / $totalVisits;

				$all = array(
					'visits'   => $totalVisits,
					'duration' => $duration,
					'bounce'   => $bounce,
					'exit'     => $exit,
				);
			}

			// return the array
			return array(
				'current' => $current,
				'all'     => $all,
			);

		}
	}

	public function filterWithUri($content, $uri, $lang, $isDefault) {
	    $possibleLabels = ($uri == '/' || $uri == '/' . site()->homePage()->uri()) ? array('/', '/index', '/home') : array($uri);

	    // check if an array exists for the given language
	    if($lang && !$isDefault) {
	    	$langArray = array_filter($content, function($entry) use($lang) {
				return $entry['label'] == $lang;
			});
	    	// if the array exists, make it the new $content
			if(count($langArray)) {
				$content = reset($langArray)['subtable'] ?? array();
			}
			// if the array isn't found, but the language isn't the default one
			else {
				return array();
			}
	    }

		// check if the $content contains one of the possible labels
		$filteredContent = array_filter($content, function($entry) use($possibleLabels) {
			$label = preg_replace('/\?.*/', '', $entry['label']);
			return in_array($label, $possibleLabels) ? true : false;
		});

		// filter out empty keys, just in case
		$filteredContent = array_filter($filteredContent);

		// return an empty array if there's no match
		if(!count($filteredContent)) return array();

		// get the columns we need
		$visits      = array_column($filteredContent, 'nb_visits');
		$durations   = array_column($filteredContent, 'avg_time_on_page');
		$bounces     = array_column($filteredContent, 'bounce_rate');
		$exits       = array_column($filteredContent, 'exit_rate');

		// sum the visits
		$totalVisits = array_sum($visits);

		// find the average duration
		$totalDurations = $this->multiplyArraysByKey($visits, $durations);
		$duration = $totalDurations / $totalVisits;

		// find the average bounce %
		$totalBounces = $this->multiplyArraysByKey($visits, $bounces);
		$bounce = $totalBounces / $totalVisits;

		// find the average exit %
		$totalExits = $this->multiplyArraysByKey($visits, $exits);
		$exit = $totalExits / $totalVisits;

		return array(
			'visits'   => $totalVisits,
			'duration' => $duration,
			'bounce'   => $bounce,
			'exit'     => $exit,
		);
	}

	public function multiplyArraysByKey($arr1, $arr2) {
		$total = 0;
		foreach($arr1 as $i => $value) {
		    if(isset($arr2[$i])) {
			    $total += floatval($value) * floatval($arr2[$i]);
			}
		}
		return $total;
	}

}
