
jQuery('<div/>', {
    id: 'overlay'
}).appendTo('body');

jQuery('<div/>', {
    id: 'overlayinner'
}).appendTo('#overlay');

jQuery('<div/>', {
    id: 'overlayheader'
}).appendTo('#overlayinner');

jQuery('<div/>', {
    id: 'overlaycontent'
}).appendTo('#overlayinner');

jQuery('<div/>', {
    class: 'btn',
    id: 'overlayok',
    text: 'ok'
}).appendTo('#overlayinner');

jQuery('<h3/>', {
    text: 'Same Great Company, Staff and Products! '
}).appendTo('#overlaycontent');

jQuery('<p/>', {
    text: 'Our site has been overhauled and our name has changed.  We have enriched the shopping experience, but you can count on us to deliver the same great service you\'ve come to know and love.'
}).appendTo('#overlaycontent');

jQuery('<p/>', {
    text: 'Thank you for allowing us this opportunity to re-introduce ourselves.  Now that that\'s out of the way, click \'OK\' below and you can get on to what you came for!'
}).appendTo('#overlaycontent');

$('#overlayok').click(function() {
	$('#overlay').hide('slow');
});
