<?php
/*
//http://localhost/pjtsreehp/easelex/OSOLMVC/tests/firebase/adv/fcmRecepientFrontend.php
https://modestoffers.com/Demo2025/firebase/adv/fcmRecepientFrontend.php#!
C:\projects\easelex\OSOLMVC\tests\firebase
Features to add
- subscribe to topic
- unsubscribe from topic
- send message to topic

This project involves the following files
1. fcmRecepientFrontend.php
2. js/appDeepSeek.js
3. ClassFirebaseActions.php
4. savedTokens.json
5. topicsAndSubscribedDevices.json {topicsAndSubscribedDevices:[{topic:topic,tokenObjects:[{host,browser,fcmTokens}]}]}

Advanced features to be added
1. modal with preloader while ajax
2. modal with message showing AJAX result
3. Reorganize all js to tidy up
4. re arrange HTML with proper descriptions
5. Save topicSubsciptions.json {topicSubsciptions:[{topic:topic,subscruptions:[{host,browser}]}]}
6. display subscibed devices/browsers for the topic
	- savedTokens.json should be returend with token submission success result.
	- topicSubsciptions.json should also be returned with token submission success result. {topicSubsciptions:[{topic,broswer,host}]}
	- it should be displayed as a list 
	- on onchange of the topic text box, or onChecked `unsubscribe`, call method `ClassFCM.updateDevicesList4TopicSubscriptions` alter the list such that...
		- check if already in topicSubsciptions, only those not subscribed should be shown as check boxes in the list
		- if `unsubscribe` checkbox clicked, show only those  subscribed as check boxes in the list
*/
require_once("ClassFirebaseActions.php");
//CHECK FOR $_REQUEST['action']
if(isset($_REQUEST['action']) && $_REQUEST['action'] !=="")
{
	$action = $_REQUEST['action'];
	
	$jsonData = file_get_contents("php://input");
	$jsonDataObj = json_decode($jsonData);//die("<pre>".print_r($tokenObj2Add,true)."</pre>");
	$tokenAlreadyAdded =  false;
	switch($action)
	{
		case "sendMessage2Topic":
		/*
		{topic:topic,messageBody:messageBody,title:title}
		- create a class ClassFirebaseActions : done
		- add html for a text and submit link
		- add js code to submit
		- send message to topic with class
		- send JSON reponse with {status:,message:,fcmResponse:}
		*/
			$topicMessageObj = $jsonDataObj;
			$clsFirebaseActions = ClassFirebaseActions::getInstance();
			$clsFirebaseActions->sendMessage2Topic($topicMessageObj);
			break;
		case "subscribe2Topic":
		/*
		{topic2Subscribe:topic2Subscribe}
		- create a class ClassFirebaseActions
		- add html for a text and submit link
		- add js code to submit
		- subscribe to topic with class
		- send JSON reponse with {status:,message:,fcmResponse:}
		
		Update: feature added to save subscribed topics
		$jsonDataObj will contain additional variable checkedDevices
		*/
			$tokenObj2Add = $jsonDataObj;
			$clsFirebaseActions = ClassFirebaseActions::getInstance();
			if($tokenObj2Add->unSubscribe)
			{
				$clsFirebaseActions->unsubscribeFromTopic($tokenObj2Add);
			}
			else
			{
				$clsFirebaseActions->subscribe2Topic($tokenObj2Add);
			}//if($tokenObj2Add->unSubscribe)
			break;
		case "saveToken":
			//{ fcmToken:fcmToken, host:host, browser:browser }	
			/*
			get saved tokens array from savedTokens.json
			check if token exists for host and browser
			if doesnt exist add new token object { fcmToken:fcmToken, host:host, browser:browser } to array 
			*/
			$tokenObj2Add = $jsonDataObj;		
			$clsFirebaseActions = ClassFirebaseActions::getInstance();
			$clsFirebaseActions->saveToken($tokenObj2Add);
			
			break;
	}//switch($action)
}//if(isset($_REQUEST['action'] && $_REQUEST['action'] !==")

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Firebase Cloud Messaging Text Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
    <!--  Material I cons from Google Fonts. -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   
   <script type="module" src="js/appDeepSeek.js"></script>
   <script  src="js/ClassFCM.js"></script>
   <script type="module">
	  // importing from above module script https://stackoverflow.com/a/69888825
	  import { getFCMToken,getFCMTokenValue } from "./js/appDeepSeek.js";
	  window.getFCMToken = getFCMToken;
	  window.getFCMTokenValue = getFCMTokenValue;
	</script>
   <script type="module">
   
		
		function sendMessage2Topic()
		{
			var unSubscribe = document.getElementById("unSubscribe").checked;
			let txtTopic2Subscribe = document.getElementById('txtTopic2Subscribe');
			let txtMessageTitle = document.getElementById('txtMessageTitle');
			let txtMessageBody = document.getElementById('txtMessageBody');
			let topic = txtTopic2Subscribe.value;
			let title = txtMessageTitle.value;
			let messageBody = txtMessageBody.value;
			if((topic.trim().length == 0) || 
				(title.trim().length == 0) || 
				(messageBody.trim().length == 0))
			{
				alert("Please enter  topic, title and message");
				return;
			}//if(topic2Subscribe.trim().length == 0)
			let postAction  = "sendMessage2Topic";
			fetch(`?action=${postAction}`, {
				  method: 'POST',
				  headers: { 'Content-Type': 'application/json' },
				  body: JSON.stringify({topic:topic,messageBody:messageBody,title:title})
				})
				.then(res => res.json())
				.then(data => 
				{
					if(data.status == "Success")
					{
						console.log('sendMessage2Topic Sent!!!,' + data.message);
					}
					else
					{
						console.log('sendMessage2Topic send Failed');
					}//if(data.status == "Success")
				})
				.catch(err => console.error('sendMessage2Topic send Failed, Error:', err));
		}//function sendMessage2Topic()
		window.sendMessage2Topic = sendMessage2Topic;// exporting from module https://stackoverflow.com/a/69888825
   </script>
   
	
	
	<style>
      /* Hide pointer and dim the disabled tab */
      .collapsible-header.disabled {
        pointer-events: none;
        background-color: #e0e0e0;
        color: #9e9e9e;
      }
    </style>
</head>
<body>
  
<div class="container">
  <h1>Firebase Cloud Messaging Text Example</h1>
  <p>This is some text.</p>
  
					<div class="input-field col s12">
					    <button class="waves-effect waves-light btn" id="myButton">
						  Add New Option to Dropdown
						</button>

						<select id="myDropdown" class="materialSelect">
						  <option value="Happy Floof">Happy Floof</option>
						  <option value="Derpy Biscuit">Derpy Biscuit</option>
						</select>
					</div>
  

  
  
  
  
      <h5>Firebase Cloud Messaging</h5>
      <ul class="collapsible">
        <li>
          <div id="allowNotificationTab"  class="collapsible-header"><i class="material-icons">help_outline</i>Allow Notification?</div>
          <div class="collapsible-body">
			<span id="allowNotificationDesciption" >
				In order to recieve notification form Firebase cloud messaging, you need to request "allow" permission for notifications
			</span><br />
			<a class="btn" id="allowNotificationLink" href="javascript:clsFCMInst.showDialog4RequestPermission()" >
				Request Notification Permission
			</a>
		  </div>
        </li>
        <li>
          <div id="getFCMTokenTab" class="collapsible-header  disabled"><i class="material-icons">code</i>Get FCM Token</div>
          <div class="collapsible-body">
			<span id="getFCMTokenDescription">
				Firebase cloud Messaging issues a unique token to each app/browser for each site. While making push notification, these tokens are used to target the device
			</span><br />
			<a class="btn" id="getFCMTokenLink" href="javascript:getFCMToken()" >
				Get FCM Token
			</a>
		  </div>
        </li>
        <li>
          <div id="sendToken2ServerTab" class="collapsible-header disabled"><i class="material-icons">devices</i>Send Token to Server</div>
          <div class="collapsible-body">
			<span id="sendToken2ServerDescription">
				Firebase cloud Messaging issues a unique token to each app/browser for each site. While making push notification, these tokens are used to target the device
			</span>
			<br />
			<a class="btn" id="sendToken2ServerLink" href="javascript:clsFCMInst.sendToken2Server()" >
				Send FCM Token To Server
			</a>
		  </div>
        </li>
        <li>
          <div id="subscribeUnSubscribeTopicTab" class="collapsible-header disabled"><i class="material-icons">devices</i>Subscribe/UnSubscribe Topic</div>
          <div class="collapsible-body">
			<span  id="subscribeUnSubscribeTopicDescription" >
				Firebase cloud Messaging has a feature to send messages to a paritucular topic. Browser(in specific devices) should subscribe to such topics to recieve topic specific messages.
			</span>
			<p id="subscribeUnSubscribeTopicContainer"  >
			  Enter Topic to subscribe/unsubscribe <input type="text" id="txtTopic2Subscribe"  placeholder="news" onchange="clsFCMInst.updateDevicesList4TopicSubscriptions()" />
			  <p>
				  <label>
					<input type="checkbox" class="filled-in"  id="chkUnSubscribe" name="chkUnSubscribe" value="unSubscribe"  onchange="clsFCMInst.updateDevicesList4TopicSubscriptions()"/>
					<span>UnSubscribe?</span>
				  </label>
				</p>
				<div  id="subscribedDevicesList" >
				</div>
				<a class="btn" id="subscribeUnSubscribeTopicLink" href="javascript:clsFCMInst.subscribeUnSubscribeTopic()" >
					Subscribe/UnSubscribe Topic
				</a>
			</p>
		  </div>
        </li>
        <li>
          <div id="sendMessage2TopicTab" class="collapsible-header disabled"><i class="material-icons">devices</i>Send Message to Topic</div>
          <div class="collapsible-body">
			<span id="sendMessage2TopicDescription" >
				Firebase cloud Messaging could send message to a particular topic. A topic might be subscribed by many machines/browsers with their FCM Token. Message to topic will notify all devices subscribed to the topic.
			</span>
				<div  id="subscribedTopicsList" >
					<div class="input-field col s12">
						<select class=".materialSelect" name="sltChooseTopic" id="sltChooseTopic">
						  <option value="Happy Floof">Happy Floof</option>
						  <option value="Derpy Biscuit">Derpy Biscuit</option>
						</select>
						<label for="sltChooseTopic">Choose a Topic:</label>
					</div>
				</div>
					<div class="input-field col s12">
			
						Enter Text for Topic Message 
						<input type="text" id="txtTitle4TopicMessage"  placeholder="Enter Topic Mesage Title here..."  />
			 
					</div>
					<div class="input-field col s12">
						<textarea id="messageBody4Topic" placeholder="Hi , this is message for selected topic"></textarea>
					</div>
					<a class="btn" id="sendMessage2TopicLink" href="javascript:clsFCMInst.sendMessage2Topic()">
						Send 
					</a>
		  </div>
        </li>
      </ul>
</div><!--class="container"-->


    <!-- Modal Structure -->
    <div id="ajaxModal" class="modal">
      <div class="modal-content" id="modalContent">
        <div class="preloader-wrapper active">
          <div class="spinner-layer spinner-blue-only">
            <div class="circle-clipper left"><div class="circle"></div></div>
            <div class="gap-patch"><div class="circle"></div></div>
            <div class="circle-clipper right"><div class="circle"></div></div>
          </div>
        </div>
        <p id="responseMsg" style="margin-top:20px;"></p>
      </div>
      <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
      </div>
    </div>
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  <script>
  let clsFCMInst;
  window.onload = (event) => { //https://developer.mozilla.org/en-US/docs/Web/API/Window/load_event
	  console.log("page is fully loaded");
	  clsFCMInst =  ClassFCM.getInstance();
	  clsFCMInst.showOrHideRelevantLinks();	
				
		//initialize accordian sections	
		const elems = document.querySelectorAll('.collapsible');
		M.Collapsible.init(elems);
			
		//initialize modals	
        const modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);
		
		
		  // setup listener for custom event to re-initialize on change
		  
		  $('.materialSelect').on('contentChanged', function() {
			resetSelects();
		  });
		
	

};// window.onload = (event) => {
$(function() {

  //initialize
  //$('.materialSelect').material_select();
  resetSelects()


  //update function for demo purposes
  $("#myButton").click(function() {
    
    // add new value
    var newValue = getNewDoggo();
    var $newOpt = $("<option>").attr("value",newValue).text(newValue)
    $("#myDropdown").append($newOpt);

    //fire custom event anytime you've updated select
    $("#myDropdown").trigger('contentChanged');
    
  });

});

function getNewDoggo() {
  var adjs =  ['Floofy','Big','Cute','Cuddly','Lazy'];
  var nouns = ['Doggo','Floofer','Pupper','Fluffer', 'Nugget'];
  var newOptValue = adjs[Math.floor(Math.random() * adjs.length)] + " " + 
                    nouns[Math.floor(Math.random() * nouns.length)];
  return newOptValue;
}
function resetSelects()
{
		console.log("resetSelects called");
		var selectElems = document.querySelectorAll('select');
		var selectInstances = M.FormSelect.init(selectElems); 
}//function resetSelects()
	
	  
	
  </script>
</body>
</html> 