jQuery(document).on('ready', function($){
    
	//Postbox
    postboxes.save_state = function(){
        return;
    };
    postboxes.save_order = function(){
        return;
    };
    postboxes.add_postbox_toggles();

    //Select all
    jQuery('input[name="select-all"]').click(function(event) {

    	var selectallpostid = jQuery(this).attr('data-selectallpostid');
	    if(this.checked) {
	        // Iterate each checkbox
	        jQuery('input[data-postid="'+selectallpostid+'"]').each(function() {
	            this.checked = true;                        
	        });
	    }else{
	        // Iterate each checkbox
	        jQuery('input[data-postid="'+selectallpostid+'"]').each(function() {
	            this.checked = false;                        
	        });
	    }
	});
	jQuery('input[name="select-all-less-administrator"]').click(function(event) {   
    	var selectalllapostid = jQuery(this).attr('data-selectalllapostid');
	    if(this.checked) {
	    	jQuery('input[name="select-all"]').checked = false;
	        // Iterate each checkbox
	        jQuery('input[data-postid="'+selectalllapostid+'"]').not('input[data-role="administrator"]').each(function() {
	            this.checked = true;                        
	        });
	    }
	});

	//Search/Filter
    jQuery(".hpfsr-filter").keyup(function(){
 
        // Retrieve the input field text and reset the count to zero
        var filter = jQuery(this).val(), count = 0;
        
        // Retrieve the input class for get currrent post type
        var box_id = jQuery(this).attr("id");
        console.log(box_id);
 
        // Loop through the comment list
        jQuery("#"+box_id+" ul li.hpfsr-post-name span").each(function(){
 
            // If the list item does not contain the text phrase fade it out
            if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
                jQuery(this).closest("li").fadeOut();
 
            // Show the list item if the phrase matches and increase the count by 1
            } else {
                jQuery(this).closest("li").show();
                count++;
            }
        });
 
        // Update the count
        var numberItems = count;
        jQuery("#filter-count-"+box_id+" strong").text(count);
    });
	
});