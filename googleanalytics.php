<?php

class Google_Analytics extends GP_Plugin
{
    var $ga_id;

    function __construct()
    {
        parent::__construct();

        $this->add_action( 'gp_footer' );
        // Google Analytics Tracking ID
        $this->ga_id= '';
    }

    function gp_footer()
    {
        $footer = <<<FOOTER
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '$this->ga_id']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
FOOTER;
        echo $footer;
    }
}

GP::$plugins->google_analytics = new Google_Analytics;