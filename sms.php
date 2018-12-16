<?php
class sms{
  private $curl;

  function __construct(){
    $this->curl;
  }

  function __destruct(){
    $this->curl->close();
  }

  private function set_up(){
		curl_setopt($this->curl, CURLOPT_USERAGENT,'Google Chrome Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
    curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
    'Origin: https://freecarrierlookup.com',
    'Host: freecarrierlookup.com',
    'Referer: https://freecarrierlookup.com/'
    ]);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
    $this->cookie_jar = 'cookies.txt';
    curl_setopt ($this->curl, CURLOPT_COOKIEJAR, $this->cookie_jar); 
    curl_setopt ($this->curl, CURLOPT_COOKIEFILE, $this->cookie_jar); 
	}

  public function get_email($phonenum){

    //this part gets cookies
    $this->curl = curl_init("https://freecarrierlookup.com");
    $this->set_up();
		curl_setopt($this->curl, CURLOPT_POST, false);
		curl_exec($this->curl); 
    //finish getting cookies

    //scrapes data
    $this->curl = curl_init("https://freecarrierlookup.com/getcarrier.php");
    $arr = ["phonenum"=>(string)$phonenum, "cc"=>"1", "test"=>"400"];
    $this->set_up();
		curl_setopt($this->curl, CURLOPT_POST, True);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $arr);
		$data = curl_exec($this->curl);
    //finished scraping data


    //starts parsing
    $parts = explode('@', $data);
    $email_part_one = explode('<p>', $parts[0])[count(explode('<p>',$parts[0]))-1];
    $email_part_two = explode('<\/p>',$parts[1])[0];
    $email = $email_part_one."@".$email_part_two;
    //finishes parseing

    return $email;

  }

  public function send_sms($phonenum, $subject, $messege){
    $email = $this->get_email($phonenum);
    echo $email;
    mail($email, $subject, $phonenum);
    
  }
}


/** example useage
$smser = new sms();
$smser->send_sms("5551230000", "hello", "hello");
**/

?>
