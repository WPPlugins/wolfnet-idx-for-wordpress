<?php

/**
 * WolfNet Template
 *
 * This class represents the page template and associated functions
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Template
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;

    protected $url;
    protected $version;


    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->url = $plugin->url;
        $this->version = $plugin->version;
    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. Any JavaScript files (and their
     * dependencies) which are needed by the plugin for public interfaces are registered in this
     * method.
     * @return void
     */
    public function scripts()
    {
    	// Legacy hook
        do_action($this->plugin->preHookPrefix . 'enqueueResources');

        $scripts = array(
            'wolfnet-swipe',
            'wolfnet-thumbnail-scroller',
            'wolfnet-scrolling-items',
            'wolfnet-quick-search',
            'wolfnet-smartsearch',
            'wolfnet-listing-grid',
            'wolfnet-toolbar',
        );

		// Include map scripts only on non-header/footer action requests
		if (
			!has_action('wp_ajax_wolfnet_content_header') && !has_action('wp_ajax_wolfnet_content_footer') &&
			(!isset($_SERVER['REQUEST_URI']) || !preg_match('/(header|footer)[^\/]*\.php/', $_SERVER['REQUEST_URI']))
		) {
			array_push($scripts, 'wolfnet-maptracks');
			array_push($scripts, 'wolfnet-map-driver');
			//array_push($scripts, 'wolfnet-maptracks-theme');
		}

        foreach ($scripts as $script) {
            wp_enqueue_script($script);
        }

    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. Any CSS files which are needed
     * by the plugin for public areas are registered in this method.
     * @return void
     */
    public function styles()
    {
        $styles = array(
            'wolfnet',
            'wolfnet-agent',
            'icomoon',
            'google-fonts',
            //'wolfnet-maptracks-theme-css',
        );

		$widgetTheme = $this->plugin->views->getWidgetTheme();
		if (strlen($widgetTheme)) {
			array_push($styles, 'wolfnet-' . $widgetTheme);
		}

		array_push($styles, 'wolfnet-theme-custom');

        foreach ($styles as $style) {
            wp_enqueue_style($style);
        }

        // Legacy hook
        do_action($this->plugin->postHookPrefix . 'enqueueResources');

    }


    /**
     * This method is a callback for the 'wp_enqueue_scripts' hook. This will load CSS files
     * which are needed for the plugin after all the other CSS includes in the event that we
     * need to override styles.
     * @return void
     */
    public function publicStyles()
    {
        if (strlen($this->plugin->views->getPublicCss())) {
            $styles = array(
                'wolfnet-custom',
            );

            foreach ($styles as $style) {
                wp_enqueue_style($style);
            }

            // Legacy hook
            do_action($this->plugin->postHookPrefix . 'enqueueResources');

        }

    }


    public function registerScripts()
    {
        $scripts = array(
            'tooltipjs' => array(
                $this->url . 'js/jquery.tooltip.min.js',
                array('jquery'),
            ),
            'imagesloadedjs' => array(
                $this->url . 'js/jquery.imagesloaded.min.js',
                array('jquery'),
            ),
			'jquerywnt' => array(
				$this->url . 'js/jquerywnt.min.js',
				array('jquery'),
			),
            'wolfnet' => array(
                $this->url . 'js/wolfnet.min.js',
                array('jquery', 'tooltipjs'),
            ),
            'wolfnet-admin' => array(
                $this->url . 'js/wolfnetAdmin.min.js',
                array('jquery-ui-dialog', 'jquery-ui-tabs', 'jquery-ui-datepicker', 'jquery', 'wolfnet'),
            ),
            'wolfnet-search-manager' => array(
                $this->url . 'js/jquery.wolfnetSearchManager.min.js',
                array('jquery'),
            ),
            'wolfnet-scrolling-items' => array(
                $this->url . 'js/jquery.wolfnetScrollingItems.min.js',
                array('wolfnet'),
            ),
            'wolfnet-quick-search' => array(
                $this->url . 'js/jquery.wolfnetQuickSearch.min.js',
                array('jquery', 'wolfnet'),
            ),
            'wolfnet-smartsearch' => array(
                $this->url . 'js/jquery.wolfnetSmartsearch.min.js',
                array('jquery'),
            ),
            'wolfnet-listing-grid' => array(
                $this->url . 'js/jquery.wolfnetListingGrid.min.js',
                array('jquery', 'tooltipjs', 'imagesloadedjs', 'wolfnet'),
            ),
            'wolfnet-swipe' => array(
                $this->url . 'js/wolfnetSwipe.min.js',
                array('jquery'),
            ),
            'wolfnet-thumbnail-scroller' => array(
                $this->url . 'js/jquery.wolfnetThumbnailScroller.min.js',
                array('jquery', 'wolfnet-swipe', 'wolfnet'),
            ),
            'wolfnet-toolbar' => array(
                $this->url . 'js/jquery.wolfnetToolbar.min.js',
                array('jquery', 'wolfnet'),
            ),
            'wolfnet-shortcode-builder' => array(
                $this->url . 'js/jquery.wolfnetShortcodeBuilder.min.js',
                array('jquery-ui-widget', 'jquery-effects-core', 'wolfnet-admin'),
            ),
			'wolfnet-agent-office' => array(
				$this->url . 'js/wolfnetAgentOffice.min.js',
				array('jquery', 'wolfnet'),
			),
			'google-maps' => array(
				'https://maps.googleapis.com/maps/api/js?client=gme-wolfnettechnologies&v=3.26&channel='
					. $this->getGoogleMapChannel(),
                array(),
                $this->version,
            ),
            'wolfnet-maptracks' => array(
				'https://common.wolfnet.com/js/maptracks/v3/maptracks.bundle.min.js?v=3.0.7',
				array('jquerywnt', 'google-maps'),
                $this->version,
            ),
			'wolfnet-map-driver' => array(
                $this->url . 'js/jquery.wolfnetMaptracksDriver.min.js',
                array('jquery'),
                $this->version,
            ),
			// Uncomment if the MapTracks theme JS is ever needed
			/*
			'wolfnet-maptracks-theme' => array(
				'https://common.wolfnet.com/js/maptracks/themes/2_5/theme.js?v=1.2.3',
				$this->version,
				true,
			),
			*/

        );

        foreach ($scripts as $script => $data) {
            $params   = array($script);
            if (is_array($data) && count($data) > 0) {
                $params[] = $data[0];
                $params[] = (count($data) > 1) ? $data[1] : array();
                $params[] = (count($data) > 2) ? $data[2] : $this->version;
                $params[] = (count($data) > 3) ? $data[3] : false;

                call_user_func_array('wp_register_script', $params);

                if ($script == 'wolfnet') {
                    wp_localize_script('wolfnet', 'wolfnet_ajax', $this->localizedScriptData());
                }

            }

        }

    }


    public function registerStyles()
    {
        global $wp_scripts;
        $jquery_ui = $wp_scripts->query('jquery-ui-core');

		$google_fonts = array(
			'Lato:400,700',
			'Montserrat:400,700',
			'Open+Sans:400,700',
			'Roboto:400,700',
		);

        $styles = array(
            'wolfnet' => array(
                $this->url . 'css/wolfnet.min.css'
            ),
            'wolfnet-agent' => array(
                $this->url . 'css/wolfnetAgentPages.min.css'
            ),
            'wolfnet-admin' => array(
                $this->url . 'css/wolfnetAdmin.min.css',
            ),
            'wolfnet-custom' => array(
                admin_url('admin-ajax.php') . '?action=wolfnet_css',
            ),
			'wolfnet-theme-custom' => array(
				$this->url . 'css/wolfnet.theme.custom.php?' . $this->plugin->views->getThemeStyleArgs(),
			),
			'wolfnet-jquery-ui' => array(
				$this->url . 'lib/jquery-ui/themes/wolfnet-wp/jquery-ui.min.css',
				$this->url . 'lib/jquery-ui/themes/wolfnet-wp/jquery-ui.theme.min.css',
			),
            'jquery-ui' => array(
                'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_ui->ver
                    . '/themes/smoothness/jquery-ui.css'
            ),
            'icomoon' => array(
                $this->url . 'lib/icomoon/style.css'
            ),
            'google-fonts' => array(
                'https://fonts.googleapis.com/css?family=' . implode('|', $google_fonts),
            ),
			// Uncomment if the MapTracks theme CSS is ever needed (in combination with theme JS)
			/*
			'wolfnet-maptracks-theme-css' => array(
				'https://common.wolfnet.com/js/maptracks/themes/2_5/theme.css?v=1.2.3',
			),
			*/
        );

		// Add widget theme styles
		$widgetThemes = $this->plugin->widgetTheme->getThemeOptions();
		foreach ($widgetThemes as $widgetTheme) {
			$styles[$widgetTheme['styleName']] = array(
				$this->url . 'css/' . $widgetTheme['styleFile']
			);
		}

        foreach ($styles as $style => $data) {
            $params   = array($style);
            $params[] = $data[0];
            $params[] = (count($data) > 1) ? $data[1] : array();
            $params[] = (count($data) > 2) ? $data[2] : $this->version;
            $params[] = (count($data) > 3) ? $data[3] : 'screen';

            call_user_func_array('wp_register_style', $params);

        }

    }


    /**
     * This method is a callback for the 'wp_footer' hook. Currently this method is used to display
     * market disclaimer information if necessary for the request.
     * @return void
     */
    public function footer()
    {
    	// Legacy hook
        do_action($this->plugin->preHookPrefix . 'footerDisclaimer');

        /* If it has been established that we need to output the market disclaimer do so now in the
         * site footer, otherwise do nothing. */
        if (array_key_exists('wolfnet_includeDisclaimer', $_REQUEST) &&
            array_key_exists('keyList', $_REQUEST)) {
            echo '<div class="wolfnet_marketDisclaimer">';
            foreach ($_REQUEST['keyList'] as $key) {
                try {
                    $disclaimer = $this->plugin->api->sendRequest(
                    	sanitize_key($key),
                    	'/core/disclaimer',
                    	'GET',
                    	array(
                        	'type'=>'search_results', 'format'=>'html'
                        )
                    );
                    echo $disclaimer['responseData']['data'];
                } catch (Wolfnet_Exception $e) {
                    echo $this->plugin->displayException($e);
                }

            }
            echo '</div>';
        }
        // TODO: Add a filter point here. Allow developers to filter the disclaimer content for formatting purposes.

        do_action($this->plugin->postHookPrefix . 'footerDisclaimer'); // Legacy hook

    }


    /**
     * This method is a callback for the 'template_redirect' hook. This method intercepts the
     * WordPress request cycle when specific URI are requested. Specifically this method exposes
     * URIs for getting header and footer HTML for the site.
     * @return void
     */
    public function templateRedirect()
    {
        $pagename = (array_key_exists('pagename', $_REQUEST)) ? sanitize_text_field($_REQUEST['pagename']) : '';
        $pagename = str_replace('-', '_', $pagename);
        $prefix   = 'wolfnet_';

        // Legacy hook
        do_action($this->plugin->preHookPrefix . 'manageRewritePages');

        if (substr($pagename, 0, strlen($prefix)) == $prefix) {
            global $wp_query;

            if ($wp_query->is_404) {
                $wp_query->is_404 = false;
                $wp_query->is_archive = true;
            }

            status_header(200);

			function wnt_remove_maptracks () {
				wp_dequeue_script('wolfnet-maptracks');
			}

            switch ($pagename) {

                case 'wolfnet_content':
					add_action('wp_print_scripts', 'wnt_remove_maptracks');
                    $this->plugin->ajax->remoteContent();
                    break;

                case 'wolfnet_content_header':
					add_action('wp_print_scripts', 'wnt_remove_maptracks');
                    $this->plugin->ajax->remoteContentHeader();
                    break;

                case 'wolfnet_content_footer':
					add_action('wp_print_scripts', 'wnt_remove_maptracks');
                    $this->plugin->ajax->remoteContentFooter();
                    break;

            }

        }

        // Legacy hook
        do_action($this->plugin->postHookPrefix . 'manageRewritePages');

    }


    public function getWpHeader()
    {
        $wntClass = 'wnt-wrapper';

        ob_start();

        get_header();
        $header = ob_get_clean();
        $htmlTags = array();
        $hasHtmlTags = preg_match_all("(<html([^\>]*)>)", $header, $htmlTags, PREG_PATTERN_ORDER);

        if ($hasHtmlTags > 0) {
            foreach ($htmlTags[0] as $tag) {
                $classRegex = "/(?<=class\=[\"|\'])([^\"|\']*)/";
                $currentClassArray=array();
                $hasClassAttr = preg_match($classRegex, $tag, $currentClassArray);

                if ($hasClassAttr > 0) {
                    $currentClasses = ($hasClassAttr > 0) ? $currentClassArray[0] : "";
                    $newTag = preg_replace($classRegex, $currentClasses . ' ' . $wntClass, $tag);
                } else {
                    $newTag = str_replace('>', ' class="' . $wntClass . '">', $tag);
                }

                $header = str_replace($tag, $newTag, $header);

            }
        }

        return $header;

    }


    public function getWpFooter()
    {
        ob_start();
        get_footer();
        $footer = ob_get_clean();

        return $footer;

    }


    public function localizedScriptData()
    {
        global $wp_version;

        return array(
            'ajaxurl'        => admin_url('admin-ajax.php'),
            'loaderimg'      => admin_url('/images/wpspin_light.gif'),
            'buildericon'    => $this->url . 'img/paw-gold.svg',
            'houseoverIcon'  => $this->url . 'img/houseover.png',
            'useDialogClass' => (version_compare($wp_version, '3.6')>0) ? "true" : "false",
        );
    }


	private function getGoogleMapChannel()
	{
		$api_key = $this->plugin->keyService->getDefault();
		$channel = 'unknown_wp';

		if ($api_key) {
			try {
				$channel = $this->plugin->data->getMarketName($api_key) . '_wp';
			} catch (Exception $e) {
			}
		}

		return $channel;

	}

}
