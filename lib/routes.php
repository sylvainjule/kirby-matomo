<?php

return array(
    array(
        'pattern' => 'matomo-panel/get-widget-content',
        'action'  => function() {
        	$widget = get('widget');
        	$method = get('method');
        	$period = get('period');
        	$date   = get('date');
        	$limit  = get('limit');
        	$lang   = get('lang');

        	try {
        		$matomo  = new Matomo();
                if(!in_array($method, ['Referrers.getReferrerType', 'Referrers.getWebsites', 'Referrers.getSocials', 'DevicesDetection.getType', 'Referrers.getKeywords', 'Actions.getPageUrls'], true)) {
                    throw new \InvalidArgumentException('Unsupported Matomo widget method');
                }
                $content = $matomo->apiWidget($widget, $method, $period, $date, $limit, $lang);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
    array(
        'pattern' => 'matomo-panel/get-bulk-widgets',
        'action'  => function() {
        	$widgets = get('widgets');
        	$period = get('period');
        	$date   = get('date');
        	$limit  = get('limit');
        	$lang   = get('lang');

        	try {
        		$matomo  = new Matomo();
        		$content = $matomo->apiBulkWidgets($widgets, $period, $date, $limit, $lang);

        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
    array(
        'pattern' => 'matomo-panel/get-realtime-number',
        'action'  => function() {
        	try {
        		$matomo  = new Matomo();
        		$content = $matomo->apiRealtime();
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
    array(
        'pattern' => 'matomo-panel/get-chart-data',
        'action'  => function() {
        	$method = get('method');
        	$period = get('period');
        	$date   = get('date');

        	try {
        		$matomo  = new Matomo();
                if(!in_array($method, ['VisitsSummary.getUniqueVisitors', 'VisitTime.getVisitInformationPerServerTime'], true)) {
                    throw new \InvalidArgumentException('Unsupported Matomo chart method');
                }
                $content = $matomo->apiChart($method, $period, $date);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
    array(
        'pattern' => 'matomo-panel/get-overview-content',
        'action'  => function() {
        	$method = get('method');
        	$period = get('period');
        	$date   = get('date');

        	try {
        		$matomo  = new Matomo();
                if($method !== 'VisitsSummary.get') {
                    throw new \InvalidArgumentException('Unsupported Matomo overview method');
                }
                $content = $matomo->apiOverview($method, $period, $date);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
    array(
        'pattern' => 'matomo-panel/get-bulk-summary',
        'action'  => function() {
        	try {
        		$matomo  = new Matomo();
        		$content = $matomo->apiBulkSummary();
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
    array(
        'pattern' => 'matomo-panel/get-page-metrics',
        'action'  => function() {
        	$period    = get('period');
        	$uri       = get('uri');
        	$lang      = array(
        		'multilang' => get('multilang'),
        		'overview'  => get('overview'),
        		'default'   => get('default'),
        		'current'   => get('current'),
        	);

        	try {
        		$matomo  = new Matomo();
        		$content = $matomo->apiPageMetrics($period, $uri, $lang);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
            catch (\Throwable $e) {
        		$error = array(
		            'status' => 'error',
		            'plugin' => 'matomo',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    ),
);
