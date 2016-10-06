<?php

	$file = rex_file::get(rex_path::addon(rex_package::getName(),'license.md'));
	$Parsedown = new Parsedown();

	$content =  $Parsedown->text($file);

	$search  = './assets';
	$replace = '../assets/addons/less_compiler';
	$content = str_replace($search, $replace, $content);

	$fragment = new rex_fragment();
	$fragment->setVar('title', $this->i18n('license'), false);
	$fragment->setVar('body', $content, false);
	echo $fragment->parse('core/page/section.php');

?>

<style>
	img {
		width: 100%;
		max-width: 200px;
		float: left;
		margin: 0 1em 1em 0;
	}
</style>
