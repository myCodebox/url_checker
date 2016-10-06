<?php

	$file = rex_file::get(rex_path::addon(rex_package::getName(),'CHANGELOG.md'));
	$Parsedown = new Parsedown();

	$content =  $Parsedown->text($file);

	$fragment = new rex_fragment();
	$fragment->setVar('title', $this->i18n('changelog'), false);
	$fragment->setVar('body', $content, false);
	echo $fragment->parse('core/page/section.php');
