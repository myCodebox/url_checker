<?php

	//use url_checker_ids;

	class url_checker
	{

		// Vars
		protected static $addon = [];
		protected static $links = [];
		protected static $ids = [];
		protected static $clang = [];
		protected static $link_count = 0;

		// init
		public static function init($addon = NULL)
		{
			self::$addon = $addon;

			// self::$ids = url_checker_ids::init();
			// self::$clang = rex_clang::getAllIds();
			// self::getArticle();

			self::getFundingDB('funding_db_overview', 'funding_db/funding_db/overview');

			return self::$link_count;
		}


		// get all db entries
		private static function getFundingDB($dbname, $addonpage )
		{
			if( !empty($dbname) && !empty($addonpage) ) {
				$sql = rex_sql::factory();
				$sql->setTable(rex::getTablePrefix().$dbname); // rex_foo_bar
				$sql->setWhere('homepage_de <> "" or homepage_en <> ""');
				$sql->select();

				$arr = '';
				if($sql->getRows()) { // nicht 0!
			    	while($sql->hasNext()) {
						$id = $sql->getValue('id');
						$de[] = $sql->getValue('homepage_de');
						$en[] = $sql->getValue('homepage_en');

						if( count($de) > 0 && $arr = self::clearUrls($de)  ) {
							self::$links[] = [
								'id' 		=> $id,
								'links' 	=> $arr,
								'clang' 	=> 1,
								'origin' 	=> $addonpage,
							];
							self::saveToDb($arr, $id, 1, $addonpage);
						}

						if( count($en) > 0 && $arr = self::clearUrls($en) ) {
							self::$links[] = [
								'id' 		=> $id,
								'links' 	=> $arr,
								'clang' 	=> 2,
								'origin' 	=> $addonpage,
							];
							self::saveToDb($arr, $id, 2, $addonpage);
						}

						$sql->next();
					}
				}
			}
			// echo '<pre>';
			// print_r($arr);
			// echo '</pre>';
			// exit;
		}

		// get all article
		private static function getArticle()
		{
			foreach(self::$ids AS $id)
			{
				foreach(self::$clang AS $clang)
				{
					$art = new rex_article_content($id, $clang);
					self::parseArticle(
						$id,
						$art->getArticle(),
						$clang
					);
				}
			}
		}

		// parse all article and get all href
		private static function parseArticle($id = NULL, $article = NULL, $clang = 1)
		{
			$regexp = "<a\s[^>]*href=(\"??)(https?[^\" >]*?)\\1[^>]*>(.*)<\/a>";
			if(preg_match_all("/$regexp/siU", $article, $matches))
			{
				if( $arr = self::clearUrls($matches[2]))
				{
					self::$links[] = [
						'id' => $id,
						'links' => $arr,
						'clang' => $clang,
						'origin' => 'content/edit',
					];
					self::saveToDb($arr, $id, $clang, 'content/edit');
				}
			}
		}

		// remove all localhost and host url's
		private static function clearUrls($links)
		{
			$arr = [];
			foreach($links AS $link){
				if(
					strpos($link, rex::getServer()) === false AND
					strpos($link, '://localhost') === false AND
					strpos($link, '://127.0.0.1') === false
				) {
					$arr[] = $link;
					self::$link_count++;
				}
			}
			return (count($arr) > 0 ) ? $arr : false;
		}

		// save all to the database
		private static function saveToDb($links, $page_id, $clang, $page_name)
		{
			foreach($links AS $link)
			{
				$sql = rex_sql::factory();
				$sql->setTable(rex::getTablePrefix().self::$addon)
					->setValue('link',$link)
					->setValue('origin_name',$page_name)
					->setValue('origin_id',$page_id)
					->setValue('origin_clang',$clang)
					->addGlobalUpdateFields()
					->addGlobalCreateFields();

				try {
					$sql->insert();
				} catch (rex_sql_exception $e) {
					echo rex_view::warning($e->getMessage());
				}
			}
		}

		// clear database
		public static function clear_database($addon)
		{
			if( self::db_empty($addon) )
			{
				$sql = rex_sql::factory();
				$sql->setTable(rex::getTablePrefix().$addon);
				try {
					$sql->delete();
					return true;
				} catch (rex_sql_exception $e) {
					echo rex_view::warning($e->getMessage());
				}
			}
			return false;
		}

		// Is database empty?
		public static function db_empty($addon = NULL)
		{
			if( !is_null($addon) )
			{
				$sql = rex_sql::factory();
				$sql->setTable(rex::getTablePrefix().$addon)
					->select();
				return ( $sql->getRows() > 0) ? $sql->getRows() : false;
			}
			return false;
		}

	}
