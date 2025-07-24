/*
regexp for ES6 methods
^\t([^\s,\r,\n,\.\}]+)([^\(\r\n\=]+)\(
*/
class ClassFCM{
	// private fields
	#notificationAllowed = false;
	#tokenSent = false;
	deviceTokensRegisteredJSON =  null;
	topicSubscriptionsJSON = null;
	static #inst = null;
	constructor() {  // Constructor
	 }//constructor() {
	static getInstance()
	{
		if(ClassFCM.#inst == null)
		{
			ClassFCM.#inst = new ClassFCM();
		}//if(this.inst == null)
		return ClassFCM.#inst;
	}//static getInstance()		
	initialize(){
	}//initialize(){
	showNotification(notiTitle, notiBody) {
		const notification = new Notification(notiTitle, {
			body:notiBody 
		})
		notification.onclick =(event) =>{alert("Notification clicked");};
	}//showNotification(notiTitle, notiBody)
	notificationPermissionGranted()
	{
		if ("Notification" in window) {
		  // Check if notifications are supported
		  if (Notification.permission === "granted") {
			console.log("Notification permission already granted.");
			return true;
		  }
		} else {
		  console.error("This browser does not support notifications.");
		}
		return false;
	}//notificationPermissionGranted()
	showOrHideRelevantLinks()
	{
			if(this.notificationPermissionGranted())
			{
				this.hideAllowNotificationLink();
				//changeRequestLink("getFCMToken");
			}//if(notificationPermissionGranted())
	}//showOrHideRelevantLinks()	
	hideAllowNotificationLink()
	{
		
		let allowNotificationLink = document.getElementById("allowNotificationLink");
		let allowNotificationDesciption = document.getElementById("allowNotificationDesciption");
		allowNotificationLink.style.display = "none";
		allowNotificationDesciption.innerText = "Notification permission already granted.";
		
		let getFCMTokenTab = document.getElementById("getFCMTokenTab");
		getFCMTokenTab.classList.remove('disabled');
	}// hideAllowNotificationLink()
	hideGetFCMTokenLink(){
		let getFCMTokenLink = document.getElementById("getFCMTokenLink");
		getFCMTokenLink.style.display = "none";
		let getFCMTokenDescription = document.getElementById("getFCMTokenDescription");
		getFCMTokenDescription.innerText = "FCM Token Received Already";
		let sendToken2ServerTab = document.getElementById("sendToken2ServerTab");
		sendToken2ServerTab.classList.remove('disabled');
	}//hideGetFCMTokenLink(){
	
	hideSendToken2ServerLink(){
		let sendToken2ServerLink = document.getElementById("sendToken2ServerLink");
		sendToken2ServerLink.style.display = "none";
		let sendToken2ServerDescription = document.getElementById("sendToken2ServerDescription");
		sendToken2ServerDescription.innerText = "FCM Token Sent to Server and Saved Already";
		let subscribeUnSubscribeTopicTab = document.getElementById("subscribeUnSubscribeTopicTab");
		subscribeUnSubscribeTopicTab.classList.remove('disabled');		
		
		/* let sendMessage2TokenTab = document.getElementById("sendMessage2TokenTab");
		sendMessage2TokenTab.classList.remove('disabled'); */
	}//hideGetFCMTokenLink(){
	alterSubscribeUnSubscribeTopicDescription(message)
	{
		/* let subscribeUnSubscribeTopicContainer = document.getElementById("subscribeUnSubscribeTopicContainer");
		subscribeUnSubscribeTopicContainer.style.display = "none"; */
		let txtTopic2Subscribe = document.getElementById("txtTopic2Subscribe");
		let topic = txtTopic2Subscribe.value;
		let subscribeUnSubscribeTopicDescription = document.getElementById("subscribeUnSubscribeTopicDescription");
		subscribeUnSubscribeTopicDescription.innerHTML = message;//`Topic ${topic} is subscribed`;
		
		
		console.log("Enabled sendMessage2TokenTab");
		
		this.setSendMessage2TopicTab();
	}//alterSubscribeUnSubscribeTopicDescription()
	setSendMessage2TopicTab()
	{
		let sendMessage2TopicTab = document.getElementById("sendMessage2TopicTab");
		sendMessage2TopicTab.classList.remove('disabled');
		//populate list with subscribed topics
		const selectElement = document.getElementById('sltChooseTopic');
		// Optional: Clear all existing options first:
		//selectElement.innerHTML = "";
		while (selectElement.options.length > 0) {
			selectElement.remove(0); // Removes the option at index 0
		}//while (selectElement.options.length > 0)
		let topicSubscriptions = this.topicSubscriptionsJSON.topicSubscriptions;//{"topicSubscriptions":[{"topic2Subscribe":"news","checkedDevices":[{"host":"localhost","browser":"Opera"}]}]}
		//[{"topic2Subscribe":"news","checkedDevices":[{"host":"localhost","browser":"Firefox"}]}]
		topicSubscriptions.forEach(item => {
			/* const option = document.createElement('option');
			option.textContent = item.topic2Subscribe; // Set the visible text of the option
			option.value = item.topic2Subscribe;       // Set the value of the option
			selectElement.appendChild(option); // Add the option to the select element */
			var $newOpt = $("<option>").attr("value",item.topic2Subscribe).text(item.topic2Subscribe)
			$("#sltChooseTopic").append($newOpt);
		// fire custom event anytime you've updated select
		//$("#sltChooseTopic").trigger('contentChanged');
		});
		resetSelects();
   
		
		

	}//setSendMessage2TopicTab()
	addToken2Topic(){
		/*
		document.querySelector('input[name="deviceType2Add2Topic"]:checked').value;
		$('input[name="deviceType2Add2Topic"]:checked').val();
		{appName:'My android App Name', deviceName, token} or 
		{host:host, browser:browser,fcmToken:fcmTokenValue } as in sendToken2Server()
		
		*/
		const deviceType2Add2TopicElement = document.querySelector('input[name="deviceType2Add2Topic"]:checked');		
		const tokenElement = document.getElementById('txtToken2Add2Topic');
		const appNameElement = document.getElementById('txtAppName2Add2Topic');
		const deviceNameElement = document.getElementById('txtdeviceName2Add2Topic');
		let deviceType2Add2Topic = deviceType2Add2TopicElement.value;
		let token = tokenElement.value;		
		let appName = appNameElement.value;
		let deviceName = deviceNameElement.value;
		//if(deviceType2Add2Topic == "Browser")
		{
			this.sendToken2Server({host:appName, browser:deviceName,fcmToken:token });
			return;
		}
		
	}//addToken2Topic()
	sendMessage2Token(){
		const target2SendMessageElement = document.querySelector('input[name="target2SendMessage"]:checked');	
		const tokenElement = document.getElementById('txtToken2SendMessage');
		const messageBodyElement = document.getElementById('messageBody4Token');
		const titleElement = document.getElementById('txtTitle4TokenMessage');
		let target2SendMessage = target2SendMessageElement.value;
		let messageBody = messageBodyElement.value;
		let title = titleElement.value;
		let token = tokenElement.value;
		this.openAJAXModal();
		fetch('?action=sendMessage2Token', {
			  method: 'POST',
			  headers: { 'Content-Type': 'application/json' },
			  body: JSON.stringify({target2SendMessage:target2SendMessage,token:token, messageBody:messageBody,title:title })
			})
			.then(res => res.json())
			.then(data => 
			{
				if(data.status == "Success")
				{
					console.log('Message sent to token!!!,data is ' , data);
					this.closeAJAXModal(data.message);
				}//if(data.status == "Success")
			})
			.catch(err => console.error('Token send Failed, Error:', err));
	}//sendMessage2Token(){
	sendMessage2Topic(){
		const selectElement = document.getElementById('sltChooseTopic');
		const messageBodyElement = document.getElementById('messageBody4Topic');
		const titleElement = document.getElementById('txtTitle4TopicMessage');
		let messageBody = messageBodyElement.value;
		let topic = selectElement.value;
		let title = titleElement.value;
		this.openAJAXModal();
		fetch('?action=sendMessage2Topic', {
			  method: 'POST',
			  headers: { 'Content-Type': 'application/json' },
			  body: JSON.stringify({topic:topic, messageBody:messageBody,title:title })
			})
			.then(res => res.json())
			.then(data => 
			{
				if(data.status == "Success")
				{
					console.log('Topic sent for sending message!!!,data is ' , data);
					this.closeAJAXModal(data.message);
				}//if(data.status == "Success")
			})
			.catch(err => console.error('Token send Failed, Error:', err));
			
					
	}//sendMessage2Topic()
	showDialog4RequestPermission() {
			var result = confirm("This site would like to send you notification. Click Ok, if you agree");

			if (result) {
			  // Code to execute if user clicks OK
			  console.log("User clicked OK");
			  this.requestNotificationPermission();
			} else {
			  // Code to execute if user clicks Cancel
			  console.log("User clicked Cancel");
			}
	}//function showDialog4$equestPermission()
	requestNotificationPermission() {
	  console.log('Requesting permission...');
	  if ("Notification" in window) {
		  // Check if notifications are supported
		  if (Notification.permission === "granted") {
			console.log("Notification permission already granted.");
		  } else if (Notification.permission !== "denied") {
			// Request notification permission
			Notification.requestPermission(
			//use the below callback for older versions, deprecated though
			//https://developer.mozilla.org/en-US/docs/Web/API/Notifications_API/Using_the_Notifications_API
			/* (result) => {
				  console.log(result);
				} */
			).then(permission => {
			  if (permission === "granted") {
				console.log("Notification permission granted.");
				//changeRequestLink("getFCMToken");
				this.hideAllowNotificationLink();
			  } else {
				console.log("Notification permission denied.");
			  }
			}).catch(error => {
			  console.error("Error requesting notification permission:", error);
			});
		  } else {
			console.log("Notification permission was previously denied.");
		  }
		} else {
		  console.error("This browser does not support notifications.");
		}
		
		
		
	}//requestNotificationPermission()
	sendToken2Server()
	{
		let host,browser,fcmTokenValue
		let json2Server =  arguments[0];//{host:host, browser:browser,fcmToken:fcmTokenValue }
		if(typeof json2Server == 'undefined')// not an external call
		{
			host = document.location.host;//https://stackoverflow.com/a/19550497
			browser = this.detectBrowser();
			fcmTokenValue = getFCMTokenValue();
			json2Server = {host:host, browser:browser,fcmToken:fcmTokenValue };
		}
		else //if(typeof arguments[0] == 'undefined')
		{
			host = json2Server.host;
			browser = json2Server.browser;
			fcmTokenValue = json2Server.fcmTokenValue;
		}//if(typeof arguments[0] == 'undefined')
		console.log("Your browser is: " + browser);
		console.log("Your fcmToken is: " + fcmTokenValue);
		this.openAJAXModal();
		fetch('?action=saveToken', {
			  method: 'POST',
			  headers: { 'Content-Type': 'application/json' },
			  body: JSON.stringify(json2Server)
			})
			.then(res => res.json())
			.then(data => 
			{
				if(data.status == "Success")
				{
					console.log('Token Sent!!!,data is ' , data);
					this.closeAJAXModal(data.message);
					this.hideSendToken2ServerLink();
					this.deviceTokensRegisteredJSON = data.deviceTokensRegistered;
					this.topicSubscriptionsJSON = data.topicSubscriptions;
					this.updateDevicesList4TopicSubscriptions();
				}
				else
				{
					console.log('Token send Failed');
				}//if(data.status == "Success")
			})
	.catch(err => console.error('Token send Failed, Error:', err));
	}//sendToken2Server()
	/*
	@param  deviceTokensRegistered : {"deviceTokensRegistered":[{"host":"localhost","browser":"Edge"}]}
	display subscibed devices/browsers for the topic
	- savedTokens.json should be returend with token submission success result.
	- topicSubsciptions.json should also be returned with token submission success result.
	- it should be displayed as a list 
	- on onchange of the topic text box, or onChecked `unsubscribe`, call method `ClassFCM.updateDevicesList4TopicSubscriptions` alter the list such that...
		- check if already in topicSubsciptions, only those not subscribed should be shown as check boxes in the list
		- if `unsubscribe` checkbox clicked, show only those  subscribed as check boxes in the list
	*/
	updateDevicesList4TopicSubscriptions(/* {"deviceTokensRegistered":[{"host":"localhost","browser":"Edge"}]}*/)
	{
		var topic2Subscribe = document.getElementById("txtTopic2Subscribe").value;
		var unSubscribe = document.getElementById("chkUnSubscribe").checked;
		let deviceTokensRegistered = this.deviceTokensRegisteredJSON.deviceTokensRegistered;
		let topicSubscriptions = this.topicSubscriptionsJSON.topicSubscriptions
		let checkedDevices = null;
		topicSubscriptions.forEach((value/*{topic2Subribe:topic2Subribe,checkedDevices:[{browser,host}]}*/)=>{ 
			if(topic2Subscribe == value.topic2Subscribe)
			{
				checkedDevices = value.checkedDevices;
			}//if(topic2Subscribe == value.topic2Subscribe)
		});
		let deviceTokensList = "<b>Subscribed Devices, (Class Var)</b>\r\n<ul>\r\n";
		let checkedDeviceCount = 0;
		deviceTokensRegistered.forEach((value)=>{
			let deviceDetailsTxt = value.browser + " : " + value.host;
			//check if topic is already subscribed. if not, show as check box
			/*
			
				  <label>
					<input type="checkbox" class="filled-in"  id="chkUnSubscribe" name="chkUnSubscribe" value="unSubscribe" />
					<span>UnSubscribe?</span>
				  </label>
			*/
			// check if in checkedDevices
			let isInCheckedDevices = [];
			if(checkedDevices != null)
			{
				isInCheckedDevices = checkedDevices.filter((checkedDevice) => (deviceDetailsTxt == (checkedDevice.browser + " : " + checkedDevice.host)));
			}//if(checkedDevices != null)
			if((!unSubscribe && isInCheckedDevices.length == 0) || (unSubscribe && isInCheckedDevices.length > 0))
			{
				checkedDeviceCount++;
				//console.log("value is " , value);
				let valueJSONText = this.escapeJSON(value);
				//console.log("valueJSONText is ",valueJSONText);
				deviceDetailsTxt =`			
				  <label>
					<input type="checkbox" class="filled-in"  id="checkedDevice${checkedDeviceCount}" name="checkedDevice" value="${valueJSONText}" />
					<span>${deviceDetailsTxt}?</span>
				  </label>`;
			}//if(isInCheckedDevices.length >0)
			
			deviceTokensList = deviceTokensList + "<li> " + deviceDetailsTxt + "<ul>\r\n";
		});
		deviceTokensList = deviceTokensList + "<ul>";
		$("#subscribedDevicesList").html(deviceTokensList);
		
	}//updateDevicesList4TopicSubscriptions(deviceTokensRegistered)
	escapeJSON(myJSON)
	{
		var myJSONString = JSON.stringify(myJSON);
		var myEscapedJSONString = myJSONString.replace(/\"/g, "'");
											  /* .replace(/\\&/g, "\\&")
											  .replace(/\\r/g, "\\r")
											  .replace(/\\t/g, "\\t")
											  .replace(/\\b/g, "\\b")
											  .replace(/\\f/g, "\\f")
											  .replace(/\\n/g, "\\n")
											  .replace(/\\'/g, "\\'")
											  ; */
		return myEscapedJSONString;
	}//escapeJSON(myJSON)
	static unEscapeJSON(myEscapedJSONString){
		var myJSONString = myEscapedJSONString.replace(/'/g,'"');
		return myJSONString;
	}//unEscapeJSON(myEscapedJSONString)
	subscribeUnSubscribeTopic()
	{
		var unSubscribe = document.getElementById("chkUnSubscribe").checked;
		let txtTopic2Subscribe = document.getElementById('txtTopic2Subscribe');
		let topic2Subscribe = txtTopic2Subscribe.value;
		if(topic2Subscribe.trim().length == 0)
		{
			alert("Please enter a topic");
			return;
		}//if(topic2Subscribe.trim().length == 0)
		this.openAJAXModal();
		// create checkedDevices variable
		let checkedDevices = [];
		$("input:checkbox[name=checkedDevice]:checked").each(function(){
			let checkedDeviceVal = ClassFCM.unEscapeJSON($(this).val());
			console.log("checkedDeviceVal is ",checkedDeviceVal);
			checkedDevices.push(JSON.parse(checkedDeviceVal));
		});
		let postVarsAsString = JSON.stringify({topic2Subscribe:topic2Subscribe,unSubscribe:unSubscribe,checkedDevices:checkedDevices });
		console.log("postVarsAsString : " + postVarsAsString);
		let postAction  = "subscribe2Topic";
		fetch(`?action=${postAction}`, {
			  method: 'POST',
			  headers: { 'Content-Type': 'application/json' },
			  body: postVarsAsString
			})
			.then(res => res.json())
			.then(data => 
			{
				if(data.status == "Success")
				{
					console.log('topic2Subscribe Sent!!!,' + data.message);
					this.closeAJAXModal(data.message);
					this.alterSubscribeUnSubscribeTopicDescription(data.message);
					this.topicSubscriptionsJSON = data.topicSubscriptions;
					this.updateDevicesList4TopicSubscriptions();
				}
				else
				{
					console.log('topic2Subscribe send Failed');
				}//if(data.status == "Success")
			})
			.catch(err => console.error('topic2Subscribe send Failed, Error:', err));
	}//subscribeUnSubscribeTopic()
	detectBrowser() {
	   const userAgent = navigator.userAgent;
	   console.log("userAgent is " + userAgent);
	   if (userAgent.indexOf("Edg/") > -1) {
		 return "Edge";
	   } else if (userAgent.indexOf("Edge") > -1) {// older Edge versions
		   return "Microsoft Edge";
	   } else if (userAgent.indexOf("Firefox") > -1) {
		 return "Firefox";
	   } else if (userAgent.indexOf("OPR/") > -1) {
		 return "Opera";
	   } else if (userAgent.indexOf("Safari") > -1) {
		 return "Safari";
	   } else if (userAgent.indexOf("Trident") > -1 || userAgent.indexOf("MSIE") > -1) {
		 return "Internet Explorer";
	   } else if (userAgent.indexOf("Chrome") > -1) {
		 return "Chrome";
	   }
	   return "Unknown";
	 }//detectBrowser()
	openAJAXModal()
	{
		
		const modalInstance = M.Modal.getInstance($('#ajaxModal'));
		$('#responseMsg').hide();
		$('.preloader-wrapper').show();
		modalInstance.open();
	}//openAJAXModal()
	closeAJAXModal(message)
	{
		
		$('.preloader-wrapper').hide();
		$('#responseMsg').html(message).fadeIn();
	}//closeAJAXModal(message)
	
	
	
}//class ClassFCM{