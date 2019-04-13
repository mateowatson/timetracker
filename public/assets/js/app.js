import $ from 'jquery'

console.log($('#run-migration').get());

$('#run-migration').submit(function(event) {
	event.preventDefault()

	$('#run-migration-status').html('Migrating database...')
	$.ajax({
		method: 'POST',
		url: '/migration',
		success: function(data) {
			console.log(data);
			$('#run-migration-status').html(data)
		},
		error: function(error, errorType, errorThown) {
			console.log(error);
			$('#run-migration-status').html(errorType + ': ' + errorThown)
		}
	});
})