// jshint ignore:start

// 1
function getJsonLinks(url, callback) {
    jQuery.ajax({
        url: url,
        cache: false,
		type: 'POST',
		dataType: 'json',
        success: function(data) {
			var max = Object.keys(data.message).length;
            callback(data, max); // testLink
        }
    });
}

// 2
function testLink(data, max) {
	$.each( data.message, function( key, val) {
		if (typeof rex.url_checker_json_curl !== "undefined" && rex.url_checker_json_curl != "") {
			if (typeof rex.url_checker_tbl !== "undefined" && rex.url_checker_tbl != "") {
				getJsonCurl(rex.url_checker_json_curl, {
					url:val.link,
					id:val.id,
					tbl:rex.url_checker_tbl},
					max,
					outputToWindow
				);
			}
		}
	});
}

// 3
function getJsonCurl(url, data, max, callback) {
    jQuery.ajax({
        url: url,
        cache: false,
		async: true,
		type: 'POST',
		dataType: 'json',
		data: data,
        success: function(data) {
            callback(data, max); // outputToWindow
		}
    });
}

// 4
function outputToWindow(data, max) {


	// add status test output
	var res = data.message;
	$('#url_test_window .modal-body-inner')
		.append('<div class="tested_url '+res.color+'">'+res.code+' '+res.status+' - '+res.url+'</div>');

	// count items
	var i = $('.tested_url').size();

	// add info text
	$('.testing_now').html('<small class="text-muted">Testing now: '+res.url+'</small>');

	// set prigress-bar
	var prz = parseFloat( (i/max)*100 );
	$('.progress-bar')
		.css('width', prz+'%')
		.attr('aria-valuenow', prz);

	// test of the last entry
	if( i < max) {
		$('.progress-bar').removeClass('active');
	} else {
		$('.progress-bar').removeClass('active');
		$('#url_test_window .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
		$('*').css('cursor','auto');
		$('#url_test_window').on('hidden.bs.modal', function(){
			$.pjax.reload('#rex-js-page-main', {'page':rex.page});
		});
	}
	// console.debug(i+' '+prz);

	// add counter
	$('#url_test_window .modal-counter').text(i);

	// add status text
	$('#td_'+res.id).html(res.html);

	// auto scroll down
	$("#url_test_window .modal-body").animate({ scrollTop: $('#url_test_window .modal-body-inner').height() }, 0);
}



// INIT
$(document).on('rex:ready', function (event, container) {
	$('#url_test_window').modal({
		keyboard: false, backdrop: 'static', show: false,
	}).on('show.bs.modal', function(){
		$('html').css('overflow', 'hidden');
	}).on('hide.bs.modal', function(){
		$('html').css('overflow', 'scroll');
	});

	container.find('#url_checker-run_test').click(function (event, container) {
		if (typeof rex.url_checker_json_links !== "undefined" && rex.url_checker_json_links != "") {
			getJsonLinks(
				rex.url_checker_json_links,
				testLink
			);
		}
	});
});


// jshint ignore:end

/*
jQuery.extend({
	setRequest: function(url, mydata = null, type = 'POST') {
		var result = null;
		$.ajax({
			url: url,
			type: type,
			data: mydata,
			dataType: 'json',
			cache: false,
			async: false,
			success: function(data) { result = data; }
		});
		return result;
	}
});

// http://jshint.com/docs/
// jshint ignore:start
$(document).on('rex:ready', function (event, container) {

	$('#url_test_window').modal({
		keyboard: false,
		backdrop: 'static',
		show: false,
	}).on('show.bs.modal', function(){
		$('html').css('overflow', 'hidden');
	}).on('hide.bs.modal', function(){
		$('html').css('overflow', 'scroll');
	});

	container.find('#url_checker-run_test').click(function (event, container) {

		if (typeof rex.url_checker_json_links !== "undefined" && rex.url_checker_json_links != "") {
			var res_links = $.setRequest(rex.url_checker_json_links);
			var max = Object.keys(res_links.message).length;
		}
		if (typeof rex.url_checker_tbl !== "undefined" && rex.url_checker_tbl != "") {
			var url_checker_tbl = rex.url_checker_tbl;
		}

		var i = 1;
		$('.progress-bar').addClass('active');
		$.each( res_links.message, function( key, val) {
			if (typeof rex.url_checker_json_curl !== "undefined" && rex.url_checker_json_curl != "") {
				$('.testing_now').html('<small class="text-muted">Testing now: '+val.link+'</small>');
				var res_curl = $.setRequest(rex.url_checker_json_curl, {url:val.link, id:val.id, tbl:url_checker_tbl});

				var res = res_curl.message;
				var prz = parseFloat( (i/max)*100 );

				$('#url_test_window .modal-counter').text(i++);
				$('#url_test_window .modal-body-inner').append('<div class="tested_url '+res.color+'">'+res.code+' '+res.status+' - '+res.url+'</div>');
				$('#td_'+res.id).html(res.html);
				$('.progress-bar')
					.css('width', prz+'%')
					.attr('aria-valuenow', prz);
				$("#url_test_window .modal-body").animate({ scrollTop: $('#url_test_window .modal-body-inner').height() }, 0);
			}
		});

		$('.progress-bar').removeClass('active');
		$('#url_test_window .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
		$('*').css('cursor','auto');

		$('#url_test_window').on('hidden.bs.modal', function(){
			$.pjax.reload('#rex-js-page-main', {'page':rex.page});
		});

		return false;
    });
});
// jshint ignore:end
*/
