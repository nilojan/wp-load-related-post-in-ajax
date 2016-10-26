jQuery(document).ready(function($) {
	/**
	 * the magic starts
	 */
		// Insert the "Related Posts" link.
		$('.entry-content').append('<p id="sra-related-posts"><a href="">See Related Articles</a></p><div id="respo"></div>');
			
		// Remove the traditional navigation.
		$('.navigation').remove();
	/**
	 * Load new posts when the link is clicked.
	 */
        $("#sra-related-posts a").click(function(){
			// get the post id as Int
			var pido = parseInt(sra_related.poid);
			// create data array with the action and the post id
			var data = {
				'action': 'my_related_posts',
				'pido': pido
			};
			// ajax request start
			jQuery.post(sra_related.ajax_url, data, function(response) {
				// hide the button
				console.log(response);
				$("#sra-related-posts").remove();
				// show the response on the respo div
				$("#respo").html(response);
			});		   
			return false;
        });
 });
