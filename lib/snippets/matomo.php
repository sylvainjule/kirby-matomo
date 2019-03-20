<?php if(Matomo::allowed()) : ?>

	<script type="text/javascript">
	  	var _paq = window._paq || [];
		_paq.push(['trackPageView']);
	  	_paq.push(['enableLinkTracking']);
	    (function() {
	    	var u='<?php echo rtrim(option('sylvainjule.matomo.url'), '/') . '/' ?>';
	    	_paq.push(['setTrackerUrl', u+'matomo.php']);
	    	_paq.push(['setSiteId', <?php echo option('sylvainjule.matomo.id') ?>]);
	    	var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
	    	g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
	  	})();
	</script>

<?php else : ?>

	<!-- Kirby Matomo -->
	
<?php endif; ?>