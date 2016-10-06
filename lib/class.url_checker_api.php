<?php


	// 192.168.0.100/raum3/Smart_at/CMS/00/redaxo/index.php?rex-api-call=url_checker_api
	// index.php?rex-api-call=url_checker_api
	class rex_api_url_checker_api extends rex_api_function
	{

		// Pflicht-Methode
		public function execute()
		{
			$message = '';
			if(
				rex_get('rex-api-call') === 'url_checker_api'
				AND rex_request::requestMethod() == 'post'
				AND (
					rex_get('type') == 'links'
					OR rex_get('type') == 'curl'
					OR rex_get('type') == 'status'
				)
			) {
				// Code goes here
				if( rex_get('type') == 'links') {
					$message = self::getLinks();
				}
				if( rex_get('type') == 'curl') {
					$message = self::getCurl();
				}
			}
			$result = new rex_api_result(true, $message);
			return $result;
		}

		// get dato from db
		private function getLinks()
		{
			$arr = rex_sql::factory()->getArray('SELECT id, link FROM '.rex::getTable('url_checker'));
			return $arr;
		}

		// get dato from db
		private function getCurl()
		{
			$url 	= rex_post('url');
			$id 	= rex_post('id');
			$tbl 	= rex_post('tbl');

			if( $url != '' AND $id != '' ) {
				$data = self::check_url($url, $id);

				$sql = rex_sql::factory()
					->setTable(rex::getTablePrefix().$tbl)
					->setWhere([ 'id' => $id ])
					->setValue('status', $data['code']);

				try {
					$sql->update();
					return $data;
				} catch (rex_sql_exception $e) {
					return array('error'=>$e->getMessage() );
				}
			}
			return false;
		}

		// add header
		public function setJsonLinkUrlHeader()
		{
			if( rex_get('rex-api-call') == 'url_checker_api' AND rex_request::requestMethod() == 'post') {
				if( $api = rex_api_function::factory()) {
					$res = $api->getResult();
					header('Content-Type: application/json');
					echo ($res == '') ? json_encode(null): $res->toJSON();
					exit();
				}
			}
		}

		// check the url
		private static function check_url($url = NULL, $id = NULL) {
			$ch = curl_init();

			$options = array(
				CURLOPT_URL            	=> $url,
				CURLOPT_RETURNTRANSFER 	=> true,
				CURLOPT_HEADER         	=> true,
				CURLOPT_FOLLOWLOCATION 	=> true,
				CURLOPT_ENCODING       	=> '',
				CURLOPT_AUTOREFERER    	=> true,
				CURLOPT_CONNECTTIMEOUT 	=> 120,
				CURLOPT_TIMEOUT        	=> 120,
				CURLOPT_MAXREDIRS      	=> 10,
				CURLOPT_SSL_VERIFYPEER 	=> false,
			);

			@curl_setopt_array( $ch, $options );
			$response 	= curl_exec($ch);
			$httpUrl 	= curl_getinfo($ch, CURLOPT_URL);
			$httpCode 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);


			$data = array();
			if ( $httpCode != 200 ){
				if ( $httpCode == 0 ){
					$data = array(
						'code' => $httpCode,
						'status' => self::status_code($httpCode),
						'error' => curl_error($ch),
						#'html' => self::getStatusOutput($httpCode, $id),
					);
				} else {
					$data = array(
						'code' => $httpCode,
						'status' => self::status_code($httpCode),
						'error' => curl_error($ch),
						#'html' => self::getStatusOutput($httpCode, $id),
					);
				}
			} else {
				$data = array(
					'code' => $httpCode,
					'status' => self::status_code($httpCode),
					'error' => curl_error($ch),
					#'html' => self::getStatusOutput($httpCode, $id),
				);
			}

			curl_close($ch);

			$data['id'] = $id;
			$data['url'] = $url;
			$data['html'] = self::getStatusOutput($httpCode, $id);
			$data['color'] = self::status_code_csscolor($httpCode);
			return $data;
		}

		// get the url status
		public static function status_code($code = NULL)
		{
			if ($code !== NULL) {
				switch ($code) {
					case 100: $text = 'Continue'; break;
					case 101: $text = 'Switching Protocols'; break;
					case 200: $text = 'OK'; break;
					case 201: $text = 'Created'; break;
					case 202: $text = 'Accepted'; break;
					case 203: $text = 'Non-Authoritative Information'; break;
					case 204: $text = 'No Content'; break;
					case 205: $text = 'Reset Content'; break;
					case 206: $text = 'Partial Content'; break;
					case 300: $text = 'Multiple Choices'; break;
					case 301: $text = 'Moved Permanently'; break;
					case 302: $text = 'Moved Temporarily'; break;
					case 303: $text = 'See Other'; break;
					case 304: $text = 'Not Modified'; break;
					case 305: $text = 'Use Proxy'; break;
					case 400: $text = 'Bad Request'; break;
					case 401: $text = 'Unauthorized'; break;
					case 402: $text = 'Payment Required'; break;
					case 403: $text = 'Forbidden'; break;
					case 404: $text = 'Not Found'; break;
					case 405: $text = 'Method Not Allowed'; break;
					case 406: $text = 'Not Acceptable'; break;
					case 407: $text = 'Proxy Authentication Required'; break;
					case 408: $text = 'Request Time-out'; break;
					case 409: $text = 'Conflict'; break;
					case 410: $text = 'Gone'; break;
					case 411: $text = 'Length Required'; break;
					case 412: $text = 'Precondition Failed'; break;
					case 413: $text = 'Request Entity Too Large'; break;
					case 414: $text = 'Request-URI Too Large'; break;
					case 415: $text = 'Unsupported Media Type'; break;
					case 500: $text = 'Internal Server Error'; break;
					case 501: $text = 'Not Implemented'; break;
					case 502: $text = 'Bad Gateway'; break;
					case 503: $text = 'Service Unavailable'; break;
					case 504: $text = 'Gateway Time-out'; break;
					case 505: $text = 'HTTP Version not supported'; break;
					//default: $text = 'Unknown http status code "' . htmlentities($code) . '"'; break;
					default: $text = 'Unknown http status code'; break;
				}
			}
			return $text;
		}

		// get the url status color
		public static function status_code_csscolor($code = NULL)
		{
			if ($code !== NULL) {
				switch ($code) {
					case 100: case 101:
						$text = 'text-info';
						break;
					case 200:
						$text = 'text-success';
							break;
					case 201: case 202: case 203: case 204: case 205: case 206:
						$text = 'text-info';
							break;
					case 300: case 301: case 302: case 303: case 304: case 305:
						$text = 'text-warning';
							break;
					case 400: case 401: case 402: case 403: case 404: case 405: case 406: case 407:
					case 408: case 409: case 410: case 411: case 412: case 413: case 414: case 415:
					case 500: case 501: case 502: case 503: case 504: case 505:
						$text = 'text-danger';
							break;
					default:
						$text = 'text-info';
							break;
				}
			}
			return $text;
		}

		// get status output
		public static function getStatusOutput($code = NULL, $id = NULL)
		{

			if(is_numeric($code)) {
				$status_text = self::status_code($code);
				$status_icon = '<i class="rex-icon fa fa-circle '.self::status_code_csscolor($code).'"></i>';
				$textout = '<small class="text-muted">'.$status_icon.' '.$code.' <span>'.$status_text.'</span></small>';
			}
			else {
				$textout = '<span> - </span>';
			}

			$out = sprintf('<span %s class="td_all">%s</span>',
						(is_numeric($id)) ? 'id="td_'.$id.'"' : '',
						$textout
					);
			return $out;
		}


	}
