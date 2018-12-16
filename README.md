# php_sms
send text messages for free using php
<h2>installation</h2>

 ```php
 require_once('path_to_sms.php');
 ```
<hr>

<h2>useage</h2>

send an sms

  this directly sends a text message to a given number
  
```php
$sms = new sms();
$example_number = "5551230000";
$example_subject = "";
$example_message = "hello world";

$sms->send_sms($example_number, $example_subject, $example_message);

```

get look up email to phone number
  
  this returns the email_address that forewards an email to an sms number


```php
$sms = new sms();
$example_number = "5551230000";
$email = $sms->get_email($example_number);
echo $email;
// >>> 5551230000@att.messaging.com, or whatever
```

<hr>

<H2>Other Important infomation</H2>

This soultion is based on webscraping. Therefore it isn't perfect. a change to an api could cause this code to break.
Also it may return a wrong messing number this is uncommon but it does happen especially if the person changes there phone 
provider often and keeps their number.

thank you and have a nice day :)


