<?php

class Shortener
{
  private $result;
  private $accessToken = "";  // Paste your access token from bit.ly here
  private $endpoint = "https://api-ssl.bitly.com/v4/shorten";

  public function __construct($domain)
  {
    $this->request($domain)->generate();
  }

  public function generate()
  {
    $res = json_decode($this->result);

    if (isset($res->errors)) {
      echo $res->description;
      return;
    }

    echo "Here your link : " . $res->link;
  }

  public function request($domain)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["long_url" => $domain]));

    $headers = array();
    $headers[] = 'Authorization: Bearer ' . $this->accessToken;
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $this->result = curl_exec($ch);

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $this;
  }
}

(new Shortener($argv[1]));
