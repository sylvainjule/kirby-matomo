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

        	try {
        		$matomo  = new Matomo();
        		$content = $matomo->apiWidget($widget, $method, $period, $date, $limit);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
        	catch (Exception $e) {
        		$error = array(
		            'status' => 'error',
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
        	catch (Exception $e) {
        		$error = array(
		            'status' => 'error',
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
        		$content = $matomo->apiChart($method, $period, $date);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
        	catch (Exception $e) {
        		$error = array(
		            'status' => 'error',
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
        		$content = $matomo->apiOverview($method, $period, $date);
        		if(empty($content)) {
				    $content = array('status' => 'empty');
				}
        		return $content;
        	}
        	catch (Exception $e) {
        		$error = array(
		            'status' => 'error',
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
        	catch (Exception $e) {
        		$error = array(
		            'status' => 'error',
		            'error'  => $e->getMessage()
		        );
		        return $error;
        	}
        }
    )
);
