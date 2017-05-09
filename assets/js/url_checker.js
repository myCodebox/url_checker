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
		$('html').css('overflow-y', 'hidden');
	}).on('hide.bs.modal', function(){
		$('html').css('overflow-y', 'scroll');
	});

	container.find('#url_checker-run_test').click(function (event, container) {
		if (typeof rex.url_checker_json_links !== "undefined" && rex.url_checker_json_links != "") {
			getJsonLinks(
				rex.url_checker_json_links,
				testLink
			);
		}
	});

	$('[data-toggle="tooltip"]').tooltip();
});
// jshint ignore:end
