<?php
// regexp to search all methods ^(?!(.*)\}\/)(.*)function\s+([^\(\r\n\=]+)\(
!defined('DS') or define('DS',DIRECTORY_SEPARATOR);
require __DIR__ . '/../../vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
class ClassFirebaseActions{	
	private static $inst = null;
	private $serviceAccountPath = __DIR__ . '/FCMCrredentials.json';
	public static function getInstance()
	{
		if(self::$inst == null)
		{
			self::$inst = new ClassFirebaseActions();
		}//if($inst == null)
		return self::$inst;
	}//public static getInstance()
	private function getMessagingObj()
	{
		
		$factory = (new Factory)->withServiceAccount($this->serviceAccountPath);
		$messaging = $factory->createMessaging();
		return $messaging;
	}//private function getMessagingObj()
	public function saveToken($tokenObj2Add /*{ fcmToken:fcmToken, host:host, browser:browser }	*/)
	{
		$tokenAlreadyAdded = false;
		$message = "Token saved for host: {$tokenObj2Add->host} &  browser:{$tokenObj2Add->browser}";
			
		$savedTokensJSON = $this->getSavedTokensJSON();
		foreach($savedTokensJSON->tokenObjects as $tokenObject)
		{
			if(($tokenObject->host == $tokenObj2Add->host) && ($tokenObject->browser == $tokenObj2Add->browser))
			{
				$tokenAlreadyAdded = true;
				$message = "Token was already for host: {$tokenObj2Add->host} and  browser: {$tokenObj2Add->browser}";
				break;
			}//if(($tokenObject->host == $tokenObj2Add->host) && ($tokenObject->browser == $tokenObj2Add->browser))
				
		}//foreach($savedTokensJSON->tokenObjects as $tokenObject)
		if(!$tokenAlreadyAdded)
		{
			$savedTokensJSON->tokenObjects[] = $tokenObj2Add;
			file_put_contents("savedTokens.json", json_encode($savedTokensJSON));
		}//if(!$tokenAlreadyAdded)
		header('Content-Type: application/json');
		$returnJSON = new stdClass();
		$returnJSON->status = "Success";
		$returnJSON->message = $message;
		$returnJSON->tokenObj2Add = $tokenObj2Add;
		$returnJSON->deviceTokensRegistered = $this->getDeviceTokensRegistered($savedTokensJSON);
		$returnJSON->topicSubscriptions = $this->getSavedTopicSubscriptions();
		die(json_encode($returnJSON));
	}//public function saveToken($tokenObj2Add)
	/*
	@return {"deviceTokensRegistered":[{"host":"localhost","browser":"Edge"}]} from {"tokenObjects":[{"host":"localhost","browser":"Edge","fcmToken":"DSDSD"}]}
	*/
	public function getDeviceTokensRegistered($savedTokensJSON)
	{
		$deviceTokensRegistered = new stdClass();
		$deviceTokensRegistered->deviceTokensRegistered = [];
		foreach($savedTokensJSON->tokenObjects as $tokenObject)
		{
			$registeredDeviceTokenObj =  new stdClass();
			$registeredDeviceTokenObj->host = $tokenObject->host;
			$registeredDeviceTokenObj->browser = $tokenObject->browser;
			$deviceTokensRegistered->deviceTokensRegistered[] = $registeredDeviceTokenObj;
		}//foreach($savedTokensJSON->tokenObjects as $tokenObject)
		return $deviceTokensRegistered;
	}//public function getDeviceTokensRegistered($savedTokensJSON)
	/*
	@return {topicSubscriptions:[{topic2Subscribe:topic2Subscribe,devices:[{broswer,host}]}]}
	*/
	public function getSavedTopicSubscriptions()
	{
		$topicSubsciptionsJSON = new stdClass();
		$topicSubsciptionsJSON->topicSubscriptions = [];
		if(file_exists("topicSubscriptions.json"))
		{
			$topicSubsciptionsJSONText = file_get_contents("topicSubscriptions.json");
			//echo("topicSubsciptionsJSONText<pre>".print_r($topicSubsciptionsJSONText,true)."</pre>");
			$topicSubsciptionsJSON = json_decode($topicSubsciptionsJSONText);
			//echo("topicSubsciptionsJSON<pre>".print_r($topicSubsciptionsJSON,true)."</pre>");
		}//if(file_exists("topicSubscriptions.json"))
		
		return $topicSubsciptionsJSON;
	}//public function getSavedTopicSubscriptions()
	public function getSavedTokensJSON()
	{
			$savedTokensJSON = new stdClass();
			$savedTokensJSON->tokenObjects  = [];
			if(file_exists("savedTokens.json"))
			{
				$savedTokensJSONText = file_get_contents("savedTokens.json");
				$savedTokensJSON =  json_decode($savedTokensJSONText);
			}//if(file_exists("savedTokens.json"))
			return $savedTokensJSON;
	}//public function getSavedTokensJSON()
	/*
	save {topicSubscriptions:[{topic2Subscribe,devices:[{broswer,host}]}]}
	*/
	public function saveSubscribedTopics($topicSubscriptions)
	{
		$topicSubscriptionsFile = "topicSubscriptions.json";
		$topicSubscriptionsJSONEncoded = json_encode($topicSubscriptions,true);
		//echo("topicSubscriptionsJSONEncoded<pre>".print_r($topicSubscriptionsJSONEncoded,true)."</pre>");
		file_put_contents($topicSubscriptionsFile,$topicSubscriptionsJSONEncoded);
	}//public function saveSubscribedTopics($topicSubscriptions)
	private function getRearrangedTopicsubscriptions2Save($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,checkedDevices:[{browser,host}]}*/,
														$action = "subscribe" /*or unsubscribe*/
														)
	{
		$topic2Subscribe = $topic2SubscribeObj->topic2Subscribe;
		$checkedDevices = $topic2SubscribeObj->checkedDevices;
		$savedTopicSubscriptions = $this->getSavedTopicSubscriptions();
		//echo("<pre>".print_r($savedTopicSubscriptions,true)."</pre>");
		/*
		rearrange {topicSubscriptions:[{topic2Subscribe,checkedDevices:[{browser,host}]}]} for  $topic2Subscribe
		*/
		$topic2SubscribeExists = false;
		foreach($savedTopicSubscriptions->topicSubscriptions as $savedTopicSubscription)
		{
			$topicInSavedTopicSubscription = $savedTopicSubscription->topic2Subscribe;
			if($topicInSavedTopicSubscription == $topic2Subscribe)
			{
				$topic2SubscribeExists = true;
				$devicesInSavedTopicSubscription = $savedTopicSubscription->checkedDevices;
				if($action == "subscribe")
				{
					/* echo "devicesInSavedTopicSubscription<pre>".print_r($devicesInSavedTopicSubscription, true)."</pre>";
					echo "checkedDevices<pre>".print_r($checkedDevices, true)."</pre>";exit; */
					//$newlySubscribedDevices = array_diff($devicesInSavedTopicSubscription,$checkedDevices);
					$newlySubscribedDevices = array_udiff($checkedDevices,$devicesInSavedTopicSubscription,
																		  function ($obj_a, $obj_b) {
					/*
					https://www.php.net/array_udiff
					The comparison function must return an integer less than, equal to, or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second.
					*/					
					/* echo "devicesInSavedTopicSubscription<pre>".print_r($obj_a, true)."</pre>";
					echo "checkedDevices<pre>".print_r($obj_b, true)."</pre>";exit; */
					
					
																			$value1 = $obj_a->browser . " : " . $obj_a->host;
																			$value2 = $obj_b->browser . " : " . $obj_b->host;
																			return ($value1 != $value2)?1:0;
																		  }
																		);
					//echo "newlySubscribedDevices<pre>".print_r($newlySubscribedDevices, true)."</pre>";exit;
					$savedTopicSubscription->checkedDevices = array_merge($devicesInSavedTopicSubscription,$newlySubscribedDevices);
				}//if($action == "subscribe")
				if($action == "unsubscribe")
				{
					/* $unsubscribedInSaved = array_uintersect($devicesInSavedTopicSubscription,$checkedDevices,
																		  function ($obj_a, $obj_b) {
																			$value1 = $obj_a->browser . " : " . $obj_a->host;
																			$value2 = $obj_b->browser . " : " . $obj_b->host;
																			return ($value1 == $value2)?1:0;
																		  }); */
					//die("devicesInSavedTopicSubscription<pre>".print_r($devicesInSavedTopicSubscription,true)."</pre>");
					$stillSubscribedDevices = array_udiff($devicesInSavedTopicSubscription,$checkedDevices,
																		  function ($obj_a, $obj_b) {
																			$value1 = $obj_a->browser . " : " . $obj_a->host;
																			$value2 = $obj_b->browser . " : " . $obj_b->host;
																			//return ($value1 != $value2)?1:0;
																			return ($value1 <=> $value2);//?0:1;
																		  });
					//echo("stillSubscribedDevices<pre>".print_r($stillSubscribedDevices,true)."</pre>");
					$savedTopicSubscription->checkedDevices =  array_merge([],$stillSubscribedDevices);// array_merge is not redundant, it is used to get part some bug which converts array to object on json_encode
					//die("savedTopicSubscription<pre>".print_r($savedTopicSubscription,true)."</pre>");
				}//if($action == "unsubscribe")
			}//if($topicInSavedTopicSubscription == $topic2Subscribe)
		}//foreach($savedTopicSubscriptions as $savedTopicSubscription)
		if(!$topic2SubscribeExists && ($action == "subscribe"))
		{
			$newItem =  new stdClass();
			$newItem->topic2Subscribe = $topic2Subscribe;
			$newItem->checkedDevices = $checkedDevices;
			$savedTopicSubscriptions->topicSubscriptions[] = $newItem;
		}//if(!$topic2SubscribeExists && ($action == "subscribe"))
		//die("savedTopicSubscriptions<pre>".print_r($savedTopicSubscriptions,true)."</pre>");
		return $savedTopicSubscriptions;
	}//	private function getRearrangedTopicsubscriptions2Save($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,checkedDevices:[{browser,host}]}*/)
	public function unsubscribeFromTopic($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,checkedDevices:[{browser,host}]}*/)
	{
		$rearrangedTopicSubscriptions2Save = $this->getRearrangedTopicsubscriptions2Save($topic2SubscribeObj,"unsubscribe");
		//die("rearrangedTopicSubscriptions2Save<pre>".print_r($rearrangedTopicSubscriptions2Save,true)."</pre>");
		$this->saveSubscribedTopics($rearrangedTopicSubscriptions2Save);
		
		
		$this->subscribeOrUnSubscribeTopic($topic2SubscribeObj,true);
	}//public function unsubscribeFromTopic($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,topicSubsciptions:{topic,subScribedDevices:[{browser,host}]}}*/)
	public function subscribe2Topic($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,action:"checkedDevices:[{browser,host}]}*/)
	{
		$rearrangedTopicSubscriptions2Save = $this->getRearrangedTopicsubscriptions2Save($topic2SubscribeObj,"subscribe");
		$this->saveSubscribedTopics($rearrangedTopicSubscriptions2Save);
		$this->subscribeOrUnSubscribeTopic($topic2SubscribeObj);
	}//public function subscribe2Topic($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,topicSubsciptions:[{topic,browser,host}]}*/)
	public function subscribeOrUnSubscribeTopic($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe,action:"checkedDevices:[{browser,host}]}*/, $unsubscribeFromTopic = false)
	{
		$topicName = $topic2SubscribeObj->topic2Subscribe;
		$savedTopicSubscriptions = $this->getSavedTopicSubscriptions();
		//die("<pre>".print_r($savedTopicSubscriptions,true)."</pre>");
		$savedTokensJSON = $this->getSavedTokensJSON();
		$savedFCMTokens = [];
		foreach($savedTokensJSON->tokenObjects as $tokenObject)
		{
			$savedFCMTokens[$tokenObject->browser . " : " . $tokenObject->host] = $tokenObject->fcmToken;
		}
		//$returnJSON->topicSubscriptions = $this->getSavedTopicSubscriptions();
		$fcmTokens = [];
		$browsersSubscribedList = [];
		foreach($savedTopicSubscriptions->topicSubscriptions as $tokenObject)
		{
			//die("<pre>".print_r($tokenObject,true)."</pre>");
			if($tokenObject->topic2Subscribe == $topicName)
			{
				$checkedDevices = $unsubscribeFromTopic ? $topic2SubscribeObj->checkedDevices : $tokenObject->checkedDevices;
				foreach($checkedDevices as $checkedDevice)
				{
					$index2Search = $checkedDevice->browser . " : " . $checkedDevice->host;
					$browsersSubscribedList[] = $index2Search;
					$fcmTokens[] = $savedFCMTokens[$index2Search];
				}//foreach($checkedDevices as $checkedDevice)
			}//if($tokenObject->topic2Subscribe == $topic2Subscribe)
		}//foreach($savedTopicSubscriptions->topicSubscriptions as $tokenObject)
		$messaging = $this->getMessagingObj();
		$subscribeDevicesMessageText =  join("<br />", $browsersSubscribedList);
		// Subscribe device to topic
		//$response = $messaging->subscribeToTopic($topicName,$fcmTokens);
		//die(json_encode(['status' => 'Success', 'message' => 'Subscribed To Topic ' . $topicName."<br />" . $subscribeDevicesMessageText ,'fcmResponse' => $response]));
		if($unsubscribeFromTopic)//if($unsubscribeFromTopic)
		{
			// Subscribe device to topic
			$response = $messaging->unsubscribeFromTopic($topicName,$fcmTokens);
			die(json_encode(['status' => 'Success', 
							'message' => 'Successfully unsubscribed from topic:' . $topicName ."<br />" . $subscribeDevicesMessageText."",
							'fcmResponse' => $response,
							'topicSubscriptions' => $savedTopicSubscriptions
							]));
		}
		else
		{
			// Subscribe device to topic
			$response = $messaging->subscribeToTopic($topicName,$fcmTokens);
			die(json_encode(['status' => 'Success', 
							'message' => 'Subscribed To Topic ' . $topicName."<br />" . $subscribeDevicesMessageText ,
							'fcmResponse' => $response,
							'topicSubscriptions' => $savedTopicSubscriptions
							]));
		}//if($unsubscribeFromTopic)
		
	}//public function subscribeOrUnSubscribeTopic
	
	public function subscribeOrUnSubscribeTopicOld($topic2SubscribeObj/*{topic2Subscribe:topic2Subscribe}*/, $unsubscribeFromTopic = false)
	{
		$topicName = $topic2SubscribeObj->topic2Subscribe;
		// derive $fcmTokens
		$savedTokensJSON = $this->getSavedTokensJSON();
		$fcmTokens = [];
		$browsersSubscribedList = [];
		foreach($savedTokensJSON->tokenObjects as $tokenObject)
		{
			$browsersSubscribedList[] = $tokenObject->browser . " : " . $tokenObject->host;
			if(!is_null($tokenObject))
			{
				$fcmTokens[] = $tokenObject->fcmToken;
			}
			else
			{
				die("<pre>" .  print_r($browsersSubscribedList,true). "</pre>");
			}//if(!is_null($tokenObject))
		}//foreach($savedTokensJSON->tokenObjects as $tokenObject)
		if (empty($fcmTokens)) {
			http_response_code(400);
			die(json_encode(['status' => 'Error', 'message' => 'Atleast one FCM token is required. Save atoken first']));
		}
		
		
		$messaging = $this->getMessagingObj();
		//https://firebase.google.com/docs/cloud-messaging/js/topic-messaging#subscribe_the_client_app_to_a_topic
		
		$subscribeDevicesMessageText =  join("<br />", $browsersSubscribedList);
		if($unsubscribeFromTopic)//if($unsubscribeFromTopic)
		{
			// Subscribe device to topic
			$response = $messaging->unsubscribeFromTopic($topicName,$fcmTokens);
			die(json_encode(['status' => 'Success', 'message' => 'Successfully unsubscribed from topic:' . $topicName ."<br />" . $subscribeDevicesMessageText."",'fcmResponse' => $response]));
		}
		else
		{
			// Subscribe device to topic
			$response = $messaging->subscribeToTopic($topicName,$fcmTokens);
			die(json_encode(['status' => 'Success', 'message' => 'Subscribed To Topic ' . $topicName."<br />" . $subscribeDevicesMessageText ,'fcmResponse' => $response]));
		}//if($unsubscribeFromTopic)
		//{"satus":"Success","message":"Subscribed To Topic testTopic","fcmResponse":{"testTopic":{"evqQykzO0YGSLRSOUneWHb:APA91bG4m4zDCQQ63ZV0ya2bIYzM9YXzp--YqiU4cw4lcA6HgpEH_r4sAmODdAJ03TXr0lJQ0E-ULp3E2UBghasvMK8S3MT8H3LyrfnlhbePS5rzY3JHpYU":"OK","fC-ZORFxwbfJFNfLLVK8sT:APA91bFydPiuwLqJ45zo3Tj_aWFHCZNMZaFcu7oIhfdf3aA8kEa1dCGT5aV-L52a1kn3eOfv7XhuBftVqPTmP-rRIWKo9mM-3QYxOTqNY809A8_JHZu9V2w":"OK"}}}
	}//public function subscribe2Topic($topicName,$fcmTokens)
	public function sendMessage2Token($topicMessageObj/*{token:token,messageBody:messageBody,title:title}*/)
	{
		
		$messaging = $this->getMessagingObj();
		
		// Example usage
		$title = $topicMessageObj->title;//"Hello";
		$body = $topicMessageObj->messageBody;//"This is a test notification!";
		$token = $topicMessageObj->token;

		// Create a CloudMessage object
		$message = CloudMessage::withTarget(
			'token', // Target type: 'topic', 'token', or 'condition'
			$token   // Target value (e.g., topic name or device token)
		)->withNotification(
			Notification::create($title, $body)
		)->withData([
			'key' => 'value' // Optional custom data
		]);

		// 4. Send the message
		try {
			$response = $messaging->send($message);
			//echo "Message sent successfully!";
			die(json_encode(['status' => 'Success', 'message' => 'Message sent to token ' . $token ,'fcmResponse' => $response]));
		} catch (\Kreait\Firebase\Exception\MessagingException $e) {
			//echo "Error: " . $e->getMessage();
			die(json_encode(['status' => 'Error', 'message' => 'Failed sending message to token : ' . 
																$token . ", reason : " . $e->getMessage() /* ,
																'fcmResponse' => $response*/] ));
		}
	}//public function sendMessage2Token($topicMessageObj/*{topic:topic,messageBody:messageBody,title:title}*/)
	public function sendMessage2Topic($topicMessageObj/*{topic:topic,messageBody:messageBody,title:title}*/)
	{
		
		$messaging = $this->getMessagingObj();
		
		// Example usage
		$title = $topicMessageObj->title;//"Hello";
		$body = $topicMessageObj->messageBody;//"This is a test notification!";
		$topic = $topicMessageObj->topic;//"news";

		// Create a CloudMessage object
		$message = CloudMessage::withTarget(
			'topic', // Target type: 'topic', 'token', or 'condition'
			$topic   // Target value (e.g., topic name or device token)
		)->withNotification(
			Notification::create($title, $body)
		)->withData([
			'key' => 'value' // Optional custom data
		]);

		// 4. Send the message
		try {
			$response = $messaging->send($message);
			//echo "Message sent successfully!";
			die(json_encode(['status' => 'Success', 'message' => 'Message sent to topic ' . $topic ,'fcmResponse' => $response]));
		} catch (\Kreait\Firebase\Exception\MessagingException $e) {
			//echo "Error: " . $e->getMessage();
			die(json_encode(['status' => 'Error', 'message' => 'Failed sending message to topic : ' . 
																$topic . ", reason : " . $e->getMessage() ,
																'fcmResponse' => $response]));
		}
	}//public function sendMessage2Topic($topicMessageObj/*{topic:topic,message:message,title:title}*/)
}//ClassFirebaseActions{
?>