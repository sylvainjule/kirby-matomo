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
        		$content = Matomo::apiWidget($widget, $method, $period, $date, $limit);
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
        		$content = Matomo::apiRealtime();
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
        		$content = Matomo::apiChart($method, $period, $date);
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
        		$content = Matomo::apiOverview($method, $period, $date);
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
        		$content = Matomo::apiBulkSummary();
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
