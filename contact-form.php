<?php
/*
Plugin Name: Simple Contact Form
Plugin URI: 
Description: Simple, nimble contact form.
Author: Antal Tettinger
Author URI: https://antaltettinger.com
Text Domain: feeling-lucky-table-solutions
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace AntalTettinger\SimpleContactForm;

add_shortcode( 'simple_contact_antal', 'clovio_contact_function' );

function clovio_contact_function() {
  global $responsepop;
  //response generation function
  //function to generate response
  function my_contact_form_generate_response($type, $message){

    global $responsepop;
    if($type == "success") $responsepop = "<div class='clovio-form-success'>{$message}</div>";
    else $responsepop = "<div class='clovio-form-error'>{$message}</div>";
    }
  //response messages
$not_human       = "Incorrect verification.";
$missing_content = "Please supply all information.";
$email_invalid   = "Email Address Invalid. Please enter a correct email address!";
$message_unsent  = "Message was not sent. Please try Again.";
$message_sent    = "Thank you! Your message has been sent.";
 
//user posted variables
$name = $_POST['message_name'];
$email = $_POST['message_email'];
$message = $_POST['message_text'];
$human = $_POST['message_human'];
 
//php mailer variables
$to = get_option('admin_email');
$subject = "Someone sent a message from ".get_bloginfo('name');
$headers = 'From: '. $email . "\r\n" .
  'Reply-To: ' . $email . "\r\n";

  
if(!$human == 0){
  if($human != 2) {
    my_contact_form_generate_response("error", $not_human);
   } //not human!
  else {
 
    //validate email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        my_contact_form_generate_response("error", $email_invalid);
        else //email is valid
        {
        //validate presence of name and message
              if(empty($name) || empty($message)){
                my_contact_form_generate_response("error", $missing_content);
              }
              else //ready to go!
              {
                //send email
                $sent = wp_mail($to, $subject, strip_tags($message), $headers);
                if($sent) {
                  my_contact_form_generate_response("success", $message_sent);
                  $_POST['message_name'] = '';
                  $_POST['message_email'] = '';
                  $_POST['message_text'] = '';
                  $_POST['message_human'] = '';
                }//message sent!
                else my_contact_form_generate_response("error", $message_unsent); //message wasn't sent
               
              }
        }
  }
}
else if ($_POST['submitted']) my_contact_form_generate_response("error", $missing_content);  


  ?>
  <style type="text/css">
  .clovio-form-error{
    padding: 5px 9px;
    border: 1px solid red;
    color: red;
    border-radius: 3px;
    font-size: 16px;
    font-weight: 600;
  }
 
  .clovio-form-success{
    padding: 5px 9px;
    border: 1px solid green;
    color: green;
    border-radius: 3px;
    font-size: 18px;
    font-weight: 800;
  }

  .clovio-form-content {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
  }

  .clovio-form-content label {
    font-weight: 700;
    font-size: 16px;
    line-height: 20px;
    margin-bottom: 10px;
  }

  .clovio-form-content textarea , .clovio-form-content input {
    border-radius: 0;
  }

  .clovio-form-content textarea {
    height: 130px;
  }

  main , h1, .clovio-form-title {
    font-size: 18px;
  }

  form span{
    color: red;
  }

 
</style>
  <div class="clovio-form-content">
  <?php echo $responsepop; ?>
  <h1 id="clovio-form-title">Contact Form</h1>
  <form action="<?php the_permalink(); ?>" method="post">
    <p><label for="name">Name: <span>*</span> <br><input type="text" name="message_name" value="<?php echo esc_attr($_POST['message_name']); ?>"></label></p>
    <p><label for="message_email">Email: <span>*</span> <br><input type="text" name="message_email" value="<?php echo esc_attr($_POST['message_email']); ?>"></label></p>
    <p><label for="message_text">Message: <span>*</span> <br><textarea type="text" name="message_text"><?php echo esc_textarea($_POST['message_text']); ?></textarea></label></p>
    <p><label for="message_human">Verification: <span>*</span> <br><input type="text" style="width: 60px;" name="message_human"> + 3 = 5</label></p>
    <input type="hidden" name="submitted" value="1">
    <p><input type="submit"></p>
  </form>
</div>
<?php

}