DROP TABLE IF EXISTS `%TABLE_PREFIX%url_checker`;


CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%url_checker` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`link` text NOT NULL,
	`origin_id` int(10) NOT NULL,
	`origin_clang` int(10) NOT NULL,
	`status` varchar(255) NOT NULL,
	`createuser` varchar(255) NOT NULL,
    `updateuser` varchar(255) NOT NULL,
    `createdate` datetime NOT NULL,
    `updatedate` datetime NOT NULL,
    `revision` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `rex_url_checker` (`id`, `link`, `origin_id`, `origin_clang`, `status`, `createuser`, `updateuser`, `createdate`, `updatedate`, `revision`) VALUES
(1, 'http://www.raum3.at', 5, 1, '', 'test', 'test', '2017-10-05 14:30:00', '2017-05-06 14:30:00', 0),
(2, 'http://www.codebox.at', 5, 1, '', 'test', 'test', '2017-10-05 14:30:00', '2017-05-06 14:30:00', 0),
(3, 'http://www.codebox.at/empty', 5, 1, '', 'test', 'test', '2017-05-06 14:30:00', '2017-05-06 14:30:00', 0);
