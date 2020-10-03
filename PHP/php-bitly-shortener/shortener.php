<?php

class Shortener
{
  private $result;
  private $formatQR;
  private $httpCode;
  private $accessToken = "";  // Paste your access token from bit.ly here
  private $endpoint = "https://api-ssl.bitly.com/v4/shorten";

  public function __construct($params)
  {
    if (isset($params[2]) && $params[2] == 'qr') {
      $this->request($params[1])->makeQR()->generate();
      return;
    }

    $this->request($params[1])->generate();
  }

  public function generate()
  {
    $res = json_decode($this->result);

    if ($this->formatQR) {
      $this->qrResponse($res);
      return;
    }

    $this->createResponse($res);
  }

  protected function qrResponse($result)
  {
    if ($this->httpCode == 402) {
      echo "Error : " . $result->description;
      return;
    }

    if (isset($result->errors)) {
      echo "Error : " . $result->description;
      return;
    }

    echo "Here your qr_code : " . $result->qr_code;
  }

  protected function createResponse($result)
  {
    if (isset($result->errors)) {
      echo $result->description;
      return;
    }

    echo "Here your link : " . $result->link;
  }

  public function makeQR()
  {
    $res = json_decode($this->result);

    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => "https://api-ssl.bitly.com/v4/bitlinks/" . $res->id . "/qr",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . $this->accessToken
      ),
    ));

    $this->result = curl_exec($ch);
    $this->formatQR = true;
    $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $this;
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
    $this->formatQR = false;
    $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return $this;
  }
}

(new Shortener($argv));
