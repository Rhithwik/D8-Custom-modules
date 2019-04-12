(function ($, Drupal) {
  /* Drupal.behaviors.pvtmsg = { */
    /* attach: function (context, settings) {
						
			
			
    } */
  /* }; */
			$(document).ready(function () {
				var urlParams = new URLSearchParams(window.location.search);
				var auth = urlParams.get('auth');
				//alert(auth);
				$("#thread-members-input").val(auth);
/* 				var txtBox=document.getElementById("edit-message-0-value" );
				txtBox.focus(); */
				//$('#edit-message-0-value').trigger("click");
				console.log(auth);
				
			});
})(jQuery, Drupal);