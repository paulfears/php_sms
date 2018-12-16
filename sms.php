<?php
class sms{
  private $curl;
  public $known_numbers;
  private $user_agents;

  function __construct(){
    $this->curl;
    $this->user_agents = array(
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36",

    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36",

    "Google Chrome Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36",

    "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36",

    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0.1 Safari/605.1.15",

    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36",

    "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:63.0) Gecko/20100101 Firefox/63.0"
  );
  $this->user_agent = $this->user_agents[array_rand($this->user_agents)];
  $this->know_numbers = array();
  $this->errors = array();
  }

  function __destruct(){
    $this->curl->close();
  }

  private function set_up(){
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->user_agent);
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

  public function get_errors(){
    return $this->errors;
  }

  public function get_all_emails(){
    return $this->know_numbers;
  }

  public function get_email($phonenum){
    $phonenum = (string)$phonenum;
    if(key_exists($phonenum, $this->know_numbers)){
      return $this->know_numbers[$phonenum];
    }

    if(strlen($phonenum) != 10 || !is_numeric($phonenum)){
      $this->know_numbers[$phonenum] = false;
      $this->errors[$phonenum] = "invalid phonenum";
      return false;
    }

    //this part gets cookies
    $this->curl = curl_init("https://freecarrierlookup.com");
    $this->set_up();
		curl_setopt($this->curl, CURLOPT_POST, false);
		curl_exec($this->curl); 
    //finish getting cookies

    //scrapes data
    $this->curl = curl_init("https://freecarrierlookup.com/getcarrier.php");
    $test = (string)rand(300, 480);
    $arr = ["phonenum"=>(string)$phonenum, "cc"=>"1", "test"=>$test];
    $this->set_up();
		curl_setopt($this->curl, CURLOPT_POST, True);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $arr);
		$data = curl_exec($this->curl);
    //finished scraping data
    
    if(substr($data, 0, strlen('{"status":"error"')) === '{"status":"error"'){
      $this->know_numbers[$phonenum] = false;
      $this->errors[$phonenum] = "invalid phonenum";
      return false;
    }
    if(strpos($data, "SMS Gateway Address:") == false){
      $this->know_numbers[$phonenum] = false;
      $this->errors[$phonenum] = "non wireless number";
      return false;
    }

    //starts parsing
    $parts = explode('@', $data);
    $email_part_one = explode('<p>', $parts[0])[count(explode('<p>',$parts[0]))-1];
    $email_part_two = explode('<\/p>',$parts[1])[0];
    $email = $email_part_one."@".$email_part_two;
    //finishes parseing
    $this->know_numbers[$phonenum] = $email;
    return $email;

  }

  public function send_sms($phonenum, $subject, $messege){
    $email = $this->get_email($phonenum);
    mail($email, $subject, $phonenum);
    
  }
}