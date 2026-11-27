<?php

use Kirby\Http\Remote;

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

	protected $url    = null;
	protected $id     = null;
	protected $token  = null;
	protected $basicAuth     = null;
	protected $requestParams = [];

	protected $methodsMap = array (
		'referrerType' => 'Referrers.getReferrerType',
		'websites'     => 'Referrers.getWebsites',
		'socials'      => 'Referrers.getSocials',
		'devicesType'  => 'DevicesDetection.getType',
		'keywords'     => 'Referrers.getKeywords',
		'popularPages' => 'Actions.getPageUrls',
	);

    public function __construct() {
	    $this->url = option('sylvainjule.matomo.url');
	    $this->id = option('sylvainjule.matomo.id');
	    $this->token = option('sylvainjule.matomo.token');
	    $this->basicAuth = option('sylvainjule.matomo.basicAuth');

	    $this->requestParams['token_auth'] = $this->token;
	}

	public function apiWidget($widget, $method, $period, $date, $limit, $lang) {
		$url  = $this->url;
		$url .= "?module=API&method=" . rawurlencode((string) $method);
		$url .= "&idSite=". $this->id ."&period=". rawurlencode((string) $period) ."&date=" . rawurlencode((string) $date);
		$url .= "&format=JSON&language=". rawurlencode((string) $lang);
		$url .= $limit ? "&filter_limit=" . rawurlencode((string) $limit) : '';

        $content = $this->sendRequest($url);
        return $content;
	}

	public function apiBulkWidgets($widgets, $period, $date, $limit, $lang) {
        $widgets = json_decode($widgets, true);
        if(!is_array($widgets)) {
            throw new \InvalidArgumentException('Invalid Matomo widgets list');
        }
        foreach ($widgets as $widget) {
            if(!is_string($widget) || !isset($this->methodsMap[$widget])) {
                throw new \InvalidArgumentException('Unknown Matomo widget');
            }
        }
        if($widgets === []) return [];

		$url  = $this->url;

		$url .= "?module=API&method=API.getBulkRequest";
		$url .= "&format=JSON";

		$i = 0;
		foreach($widgets as $widget) {
			$url .= "&urls[". $i ."]=";
			$url .= urlencode("method=". $this->methodsMap[$widget] ."&idSite=". $this->id ."&period=". rawurlencode((string) $period) ."&date=" . rawurlencode((string) $date) ."&language=". rawurlencode((string) $lang) ."&filter_limit=" . rawurlencode((string) $limit));
			$i++;
		}

        $content = $this->sendRequest($url);
        return $content;
	}

	public function apiChart($method, $period, $date) {
		$url  = $this->url;
		$url .= "?module=API&method=". rawurlencode((string) $method);
		$url .= "&idSite=". $this->id ."&period=". rawurlencode((string) $period) ."&date=" . rawurlencode((string) $date);
		$url .= "&format=JSON";

        $content = $this->sendRequest($url);
        return $content;
	}

	public function apiOverview($method, $period, $date) {
		$url  = $this->url;
		$url .= "?module=API&method=". rawurlencode((string) $method);
		$url .= "&idSite=". $this->id ."&period=". rawurlencode((string) $period) ."&date=" . rawurlencode((string) $date);
		$url .= "&format=JSON";

        $content = $this->sendRequest($url);
        return $content;
	}

	public function apiRealtime() {
		$url  = $this->url;
		$method = 'Live.getCounters';

		$url .= "?module=API&method=" . rawurlencode((string) $method);
		$url .= "&idSite=". $this->id ."&lastMinutes=3";
		$url .= "&format=JSON";

        $content = $this->sendRequest($url);
		return $content;
	}

	public function apiBulkSummary() {
		$url  = $this->url;

		$url .= "?module=API&method=API.getBulkRequest";
		$url .= "&format=JSON";

		$url .= "&urls[0]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=day&date=today");

		$url .= "&urls[1]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=day&date=last7");

		$url .= "&urls[2]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=month&date=today");

		$url .= "&urls[3]=";
		$url .= urlencode("method=VisitsSummary.getVisits&idSite=". $this->id ."&period=year&date=today");

        $content = $this->sendRequest($url);
        return $content;
	}

	public function apiPageMetrics($period, $uri, $lang) {
		$url  = $this->url;
		$url .= "?module=API&method=Actions.getPageUrls";
		$url .= "&idSite=". $this->id ."&period=". rawurlencode((string) $period) ."&date=today";
		$url .= "&format=JSON";
		$url .= '&flat=1';
		$url .= filter_var($lang['multilang'], FILTER_VALIDATE_BOOLEAN) ? '&expanded=1' : '';

        $content = $this->sendRequest($url);
		$content = $this->filterPageMetrics($content, $uri, $lang);

		return $content;
	}

	/* Filter page metrics
	---------------------------------*/

	public function filterPageMetrics($content, $uri, $lang) {
		$site  = site();
		$kirby = kirby();
		$multilang   = filter_var($lang['multilang'], FILTER_VALIDATE_BOOLEAN);
		$overview    = filter_var($lang['overview'], FILTER_VALIDATE_BOOLEAN);
		$currentLang = $lang['current'];
		$defaultLang = $lang['default'];
		$isDefault   = $currentLang == $defaultLang;

		// set the correct language and find the page
		if($multilang && $overview) $site->visit($site->homePage(), $currentLang);
		$page = $multilang && $overview ? $site->index()->findBy('uri', $uri) : null;

		// get the current page stats
		$current = $this->filterWithUri($content, $uri, $currentLang, $isDefault);

		// return it if there's no other language or if they don't need to be displayed
		if(!$multilang || !$overview || !$page) {
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

				$languageCode = $language->code();
				$isDefault    = $languageCode == $defaultLang;
				$languageUri  = $page->uri($languageCode);

				$languageMetrics = $this->filterWithUri($content, $languageUri, $languageCode, $isDefault);
				array_push($metrics, $languageMetrics);
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
				$duration = $totalVisits > 0 ? $totalDurations / $totalVisits : 0;

				// find the average bounce %
				$totalBounces = $this->multiplyArraysByKey($visits, $bounces);
				$bounce = $totalVisits > 0 ? $totalBounces / $totalVisits : 0;

				// find the average exit %
				$totalExits = $this->multiplyArraysByKey($visits, $exits);
				$exit = $totalVisits > 0 ? $totalExits / $totalVisits : 0;

				$all = array(
					'visits'   => $totalVisits,
					'duration' => $duration,
					'bounce'   => $bounce,
					'exit'     => $exit,
				);
			}

			$current = empty($current) && !empty($all) ? ['visits' => 0, 'duration' => 0, 'bounce' => 0, 'exit' => 0] : $current;

			// return the array
			return array(
				'current' => $current,
				'all'     => $all,
			);

		}
	}

	public function filterWithUri($content, $uri, $lang, $isDefault) {
	    if($uri == '/' || $uri == '/' . site()->homePage()->uri()) {
	    	$possibleLabels = ['/', '/index', '/home'];
	    }
	    else {
	    	$possibleLabels = kirby()->multilang() ? ['/'. $lang .'/'. $uri, '/'. $uri, $uri] : ['/'. $uri, $uri];
	    }

		// check if the $content contains one of the possible labels
		$filteredContent = array_filter($content, function($entry) use($possibleLabels) {
			if(!is_array($entry) || !isset($entry['label'])) return false;
			$label = preg_replace('/\?.*/', '', $entry['label']);
			return in_array($label, $possibleLabels);
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
		$duration = $totalVisits > 0 ? $totalDurations / $totalVisits : 0;

		// find the average bounce %
		$totalBounces = $this->multiplyArraysByKey($visits, $bounces);
		$bounce = $totalVisits > 0 ? $totalBounces / $totalVisits : 0;

		// find the average exit %
		$totalExits = $this->multiplyArraysByKey($visits, $exits);
		$exit = $totalVisits > 0 ? $totalExits / $totalVisits : 0;

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

	private function sendRequest($url) {
	    $response = Remote::request($url, [
	        'method'    => 'POST',
	        'data'      => $this->requestParams,
	        'basicAuth' => $this->basicAuth,
	        'headers'   => [
	            'Content-Type' => 'application/x-www-form-urlencoded',
	        ],
	        'timeout'   => 30,
	    ]);

	    if($response->code() < 200 || $response->code() >= 300) {
	        throw new \RuntimeException(
	            'Matomo API returned HTTP ' . $response->code()
	        );
	    }

	    $data = json_decode($response->content() ?? '', true, 512, JSON_THROW_ON_ERROR);
        if(!is_array($data)) {
            throw new \RuntimeException('Unexpected Matomo API response');
        }
        if(($data['result'] ?? null) === 'error') {
            throw new \RuntimeException('Matomo API returned an error');
        }
        return $data;
	}

}
