<script type="text/javascript">
    function validatemail() {
        var pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (!pattern.test($('#email').val())) {
            alert("Please Enter Valid Email");
            return false;
        } else {
            $.ajax({
                url: 'index.php?route=module/newsletter/jsonnewsletter',
                type: 'post',
                data: 'email=' + $('#email').val(),
                dataType: 'json',
                success: function(json) {
                    $('.alert').remove();
                    if (json.fail) {
                        $('#content').parent().before('<div class="alert alert-danger"><i class="fa fa-check-circle"></i> ' + json.fail + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    } else {
                        $('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json.success + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'slow');
                }
            });
            return false;
        }
    }
</script>	

<h3><?php echo $newsletter_title; ?></h3>
<form action="" onsubmit="return validatemail();" method="post">
		<div class="form-group required">		  	
              <input type="email" name="email" id="email" placeholder="<?php echo $newsletter_placeholder; ?>" class="form-control input-lg" required />      
			       <br />
			  <button type="submit" class="btn btn-default btn-lg"><?php echo $subscribe; ?></i></button>  
        </div>
</form>
