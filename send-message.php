<?php

use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsAdvancedTextualRequest;

require __DIR__ . "/vendor/autoload.php";

$number = $_POST["contact"];
$message = $_POST["message"];

$base_url = "3glvm1.api.infobip.com";
$api_key = "ffd6784c802310f922e8733a0b1e5727-a4d378eb-ce6e-4693-a1a9-740cd4c88efe";

$configuration = new Configuration(host: $base_url, apiKey: $api_key);

$api = new SmsApi(config: $configuration);

$destination = new SmsDestination(to: $number);

$message = new SmsTextualMessage(
    destinations: [$destination],
    text: $message,
    from: "Seniorita Laundry Care"
);

$request = new SmsAdvancedTextualRequest(messages: [$message]);

$response = $api->sendSmsMessage($request);
header('location: ./queue.php?type=success&message=Message sent!');