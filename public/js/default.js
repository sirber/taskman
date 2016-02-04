$(function(){
    /* Layout base actions */
	$("#user_logout").on("click", function() {
       window.location.href = base_url + "/user/logout";
    });
	
	$("#user_profile").on("click", function() {
       window.location.href = base_url + "/user/view/" + user_id;
    });
	
});