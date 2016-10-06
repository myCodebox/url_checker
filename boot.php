<?php

	if (!rex::isBackend()) { // Frontend

	} else { // Backend
		// add js
		rex_view::addJsFile( $this->getAssetsUrl('js/url_checker.js') );
		rex_view::addCSSFile( $this->getAssetsUrl('css/url_checker.css') );

		// set api link url
		//$url = rex::getServer().'redaxo/'.rex_url::currentBackendPage(['rex-api-call'=>'url_checker_api', 'type'=>'links'], false);
		$url = rex_url::currentBackendPage(['rex-api-call'=>'url_checker_api', 'type'=>'links'], false);
		rex_view::setJsProperty('url_checker_json_links', $url);

		// set api curl url
		$url = rex_url::currentBackendPage(['rex-api-call'=>'url_checker_api', 'type'=>'curl'], false);
		rex_view::setJsProperty('url_checker_json_curl', $url);

		// set api tbl
		$tbl = $this->getName();
		rex_view::setJsProperty('url_checker_tbl', $tbl);

		// add header api link output
		rex_extension::register('PAGE_HEADER','rex_api_url_checker_api::setJsonLinkUrlHeader');
	}
