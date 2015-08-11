$(function() {
  $('.error').hide();
  $('input.text-input').css({backgroundColor:"#FFFFFF"});
  $('input.text-input').focus(function(){
    $(this).css({backgroundColor:"#FFDDAA"});
  });
  $('input.text-input').blur(function(){
    $(this).css({backgroundColor:"#FFFFFF"});
  });

  $(".button").click(function() {
		// validate and process form
		// first hide any error messages
    $('.error').hide();
		
	var name = $("input#name").val();
		if (name == "") {
      $("label#name_error").show();
      $("input#name").focus();
      return false;
    }

		var dataString = 'shortlinkname='+ name;
		//alert (dataString);return false;
		
		$.ajax({
      type: "POST",
      url: "bin/process.php",
      data: dataString,
      success: function() {
        $('#newlink').html("<div id='message'></div>");
        $('#message').html("<h2>Short Link Added!</h2>")
        .hide()
        .fadeout(1500, function() {
          $('#message').append("<img id='checkmark' src='./images/validYes.png' />");
        });
      }
     });
    return false;
	});
});