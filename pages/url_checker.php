<?php

	$content = '';
	$message = '';




	$func = rex_request('func', 'string');

	$addon 	= $this->getName();
	$plugin = rex_be_controller::getCurrentPagePart(2);

	$addonObj = rex_addon::getAddon($addon);
	$aAuthor = $addonObj->getAuthor($plugin);

	$pluginObj = rex_addon::getPlugin($plugin);
	$pAuthor = $pluginObj->getAuthor();




	/* GET all Links */
	$all_links = '';
	if (rex_post('url_checker-collect', 'boolean')) {
		if(url_checker::db_empty($addon) ) {
			url_checker::clear_database($addon);
		}
		$all_links =  url_checker::init($addon);
		$message .= rex_view::info($this->i18n("url_checker/collect_info", $all_links));
	}
	if (rex_post('url_checker-clean', 'boolean')) {
		if(url_checker::clear_database($addon)) {
			$message .= rex_view::info($this->i18n("url_checker/clean_info"));
		}
	}




	if ($func == '') {
		$link_count = url_checker::db_empty($addon);

		if(url_checker::db_empty($addon)) {
			$button_confirm = 'data-confirm="'.$this->i18n('url_checker/recollect_confirm').'"';
			$button_title 	= $this->i18n("url_checker/recollect");
			$button_clean 	= '<button
									class="btn btn-danger"
									data-confirm="'.$this->i18n('url_checker/clean_confirm').'"
									type="submit"
									name="url_checker-clean"
									value="1"
									title="'.$this->i18n("url_checker/clean").'">
									<i class="rex-icon fa-trash-o"></i> '.$this->i18n("url_checker/clean").'
								</button>';

			$footer			= '<div class="btn-group-md clearfix">
									<button
										type="button"
										class="btn btn-primary pull-right"
										data-backdrop="true"
										data-toggle="modal"
										data-target="#url_test_window">
											<i class="rex-icon fa-gear"></i> '.$this->i18n("url_checker/run_test", $link_count).'
									</button>
								</div>
								<!-- Modal -->
								<div class="modal fade bs-example-modal-lg" id="url_test_window" tabindex="-1" role="dialog" aria-labelledby="url_test_window">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="url_test_window_Label">Url Checker <span class="modal-counter">0</span>/'.$link_count.'</h4>
												<div class="progress">
													<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
														<span class="sr-only">0% Complete</span>
													</div>
												</div>
											</div>
											<div class="modal-body"><div class="modal-body-inner"></div></div>
											<div class="modal-footer">
												<div class="testing_now pull-left"></div>
												<button id="url_checker-run_test" type="button" class="btn btn-success">
													'.$this->i18n("url_checker/start_test").'
												</button>
											</div>
										</div>
									</div>
								</div>';

		} else {
			$button_confirm = $button_clean = $footer = '';
			$button_title 	= $this->i18n("url_checker/collect");
		}



		$heading = '<div class="panel-title pull-left">'.$this->i18n('url_checker').'</div>
					<form method="post" action="'.rex_url::currentBackendPage().'" class="pull-right">
						<div class="btn-group-xs pull-right">
							'.$button_clean.'
							<button
								class="btn btn-info"
								type="submit"
								name="url_checker-collect"
								value="1"
								title="'.$button_title.'" '.$button_confirm.'><i class="rex-icon fa-gear"></i> '.$button_title.'
							</button>
						</div>
					</form>';



		#$list = rex_list::factory('SELECT id, link, origin_name, origin_clang, origin_id, status, createdate, updatedate FROM '.rex::getTable($addon.'_'.$plugin).' ORDER BY id', 20);
		$list = rex_list::factory('SELECT * FROM '.rex::getTable($addon).' ORDER BY id', 50);
		$list->addTableAttribute('class', 'table-hover');
		$list->addTableColumnGroup(array(20,'*',240,50,200));


		$thIcon = '#';
		$tdIcon = '<a href="###link###" target="_blank"><i class="rex-icon fa fa-external-link"></i></a>';
		$list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);


		$list->removeColumn('id');
		$list->removeColumn('origin_name');
		$list->removeColumn('createuser');
		$list->removeColumn('createdate');
		$list->removeColumn('updateuser');
		$list->removeColumn('updatedate');
		$list->removeColumn('revision');


		$list->setColumnLabel('link', $this->i18n('url_checker/link'));
		$list->setColumnLayout('link', ['<th>###VALUE###</th>', '<td data-title="'.$list->getColumnLabel('link').'"><a href="###link###" target="_blank">###VALUE###</a></td>']);
		$list->setColumnSortable('link');
		$list->setColumnFormat('link', 'custom', 'get_short_link');
		if( !function_exists('get_short_link') ){ function get_short_link($params){
			$list = $params['list'];
			$href = $list->getValue('link');
			$max = 50;
			$short = substr($href, 0, $max) . ((strlen($href) > $max ) ? ' ...' : '');
			$link = sprintf(
				'<a href="%s" title="%s" target="_blank" data-toggle="tooltip" data-placement="top">%s</a>',
				$href, $href, $short
			);
			return $list->getColumnLink("status", $link, $params);
		}}


		$list->setColumnLabel('origin_clang', 'Lang');
		$list->setColumnSortable('origin_clang');
		$list->setColumnFormat('origin_clang', 'custom', 'get_origin_clang');
		if( !function_exists('get_origin_clang') ){ function get_origin_clang($params){
			$list = $params["list"];
			return sprintf('<small>%s</small>',strtoupper(rex_clang::get($list->getValue("origin_clang"))->getCode()));
		}}


		$list->setColumnLabel('origin_id', $this->i18n('url_checker/origin'));
		$list->setColumnSortable('origin_id');
		$list->setColumnFormat('origin_id', 'custom', 'get_origin');
		if( !function_exists('get_origin') ){ function get_origin($params){
			$list = $params["list"];
			$origin_name = $list->getValue("origin_name");

			if( $origin_name == 'content/edit' ) {
				$name = rex_article::get($list->getValue("origin_id"))->getValue('name');
				$params = array(
					'page' 			=> 'content/edit',
					'category_id' 	=> rex_article::get($list->getValue("origin_id"))->getCategoryId(),
					'article_id' 	=> $list->getValue("origin_id"),
					'clang' 		=> $list->getValue("origin_clang"),
					'mode' 			=> 'edit',
				);
			}
			else {
				// page=funding_db/funding_db/overview&func=edit&id=1
				$addon = explode('/', $origin_name);
				$name = rex_package::get( $addon[0] )->i18n('funding_db');
				$params = array(
					'page' 			=> $origin_name,
					'func' 			=> 'edit',
					'id' 			=> $list->getValue("origin_id"),
				);
			}

			$max = 30;
			$short = substr($name, 0, $max) . ((strlen($name) > $max ) ? ' ...' : '');
			$linkname = sprintf(
				'<span title="%s" target="_blank" data-toggle="tooltip" data-placement="top">%s</span>',
				$name, $short
			);

			return $list->getColumnLink('origin_id', $linkname, $params);
		}}


		$list->setColumnLabel('status', $this->i18n('url_checker/status'));
		$list->setColumnSortable('status');
		$list->setColumnFormat('status', 'custom', 'get_status');
		if( !function_exists('get_status') ){ function get_status($params){
			$list = $params["list"];
			return rex_api_url_checker_api::getStatusOutput($list->getValue("status"), $list->getValue("id"));
		}}


		$content .= $list->get();


		$fragment = new rex_fragment();
		$fragment->setVar('heading', $heading, false);
		$fragment->setVar('content', $content, false);
		$fragment->setVar('footer', $footer, false);
		$content = $fragment->parse('core/page/section.php');
	}


	echo $message;
	echo $content;
