jQuery(document).ready(function($) {
	
	/**
	 * but only if there is at least one page of new posts to load.
	 */
		// Insert the "Related Posts" link.
		$('.entry-content').append('<p id="sra-related-posts"><a>See Related Articles</a></p><div id="respo"></div>');
			
		// Remove the traditional navigation.
		$('.navigation').remove();
	
	
	/**
	 * Load new posts when the link is clicked.
	 */
 
        //$.ajaxSetup({cache:false});
        $("#sra-related-posts a").click(function(){
            //var post_link = window.location.href;
			//alert(post_link);
			var pido = parseInt(sra_related.poid);
			var data = {
				'action': 'my_related_posts',
				'pido': pido
			};
		jQuery.post(sra_related.ajax_url, data, function(response) {
			//alert('Got this from the server: ' + response);
			$("#sra-related-posts").remove();
			$("#respo").html(response);
		});
            //$(this).text("Related Articles loading");
           // $(this).load(ajaxurl);
		   
        return true;
        });
 });