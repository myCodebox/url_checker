<?php

	class url_checker_ids
	{

		// Vars
		protected static $ids = [];

		public static function init($cat_id = 0)
		{
			self::collectIds($cat_id);
			return self::$ids;
		}

		// Init
		private static function collectIds($cat_id = 0)
		{
			if( $cat_id < 1 )
			{
				$art = rex_article::getRootArticles();
				self::getArtIds($art);
				self::collectIds(1);
			}
			else {
				$cat = rex_category::getRootCategories();
				self::getCatIds($cat);
			}
		}

		// get all categories
		private static function getCatIds($cat)
		{
			foreach($cat AS $c)
			{
				$art = $c->getArticles();
				self::getArtIds($art);
				if( $childen = $c->getChildren() )
				{
					self::getCatIds($childen);
				}
			}
		}

		// get articles id
		private static function getArtIds($art)
		{
			foreach($art AS $a)
			{
				self::$ids[] = $a->getValue('id');
			}
		}

	}
