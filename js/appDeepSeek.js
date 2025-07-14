// Import the functions you need from the SDKs you need
	  import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
	  import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-analytics.js";
	  import { getMessaging,getToken, onMessage    } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging.js";

	  
	  
	  var fcmToken;
	  
	  // TODO: Add SDKs for Firebase products that you want to use
	  // https://firebase.google.com/docs/web/setup#available-libraries

	  // Your web app's Firebase configuration
	  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
	  const firebaseConfig = {
		apiKey: "AIzaSyAsI9zxp6MvkMwRpnctpHc3gnwz6Eq4UQw",
		authDomain: "easelex-1b2ef.firebaseapp.com",
		projectId: "easelex-1b2ef",
		storageBucket: "easelex-1b2ef.firebasestorage.app",
		messagingSenderId: "435028378666",
		appId: "1:435028378666:web:eec9fb4e05f1552bebef53",
		measurementId: "G-97RX99R8EG"
	  };

	  // Initialize Firebase
	  const app = initializeApp(firebaseConfig);
	  const analytics = getAnalytics(app);

		//Initialize Firebase Cloud Messaging and get a reference to the service
		const messaging = getMessaging(app);//firebase.messaging();
		
		
		
		export function getFCMTokenValue() // exporting from module https://stackoverflow.com/a/69888825
		{
			return fcmToken;
		}//export function getFCMTokenValue()
		export function getFCMToken() // exporting from module https://stackoverflow.com/a/69888825
		{
		
			// Add the public key generated from the console here. //private key : Suff9CXMuzTTSA6yblZ1wwJ8XHHeWKWCzVVHS7SkjAs
			getToken(messaging, {vapidKey: "BOKCHMGJp-tyWrfGRKn_n-bUeobYwF3huJR4YjGlWCEDelW-x8zRX2XCgj8bjtsBKvW41KWR05aLAU3awY7Bp7I"}).then((currentToken) => {
						console.log('getToken called');
					  if (currentToken) {
						// Send the token to your server and update the UI if necessary
						// ...
						console.log(" will call sendToken2Server. currentToken is, " + currentToken);
						fcmToken = currentToken;
						//changeRequestLink("sendToken2Server");
						clsFCMInst.hideGetFCMTokenLink();
						//return fcmToken;
						
					  } else {
						// Show permission request UI
						console.log('No registration token available. Request permission to generate one.');
						// ...
					  }
					}).catch((err) => {
					  console.log('An error occurred while retrieving token. ', err);
					  // ...
					});
					//return null;
						console.log('calling onMessage');
		}//export function getFCMToken()
  /* function showNotification(notiTitle, notiBody) {
	const notification = new Notification(notiTitle, {
	body:notiBody 
	})
	notification.onclick =(event) =>{alert("Notification clicked");};
  } */
	/* if (Notification.permission === "granted") { //granted, denied, default
		getFCMToken();
		showNotification("New Desktop notification","Frontend stuff");
	} else if (Notification.permission !== "denied") {
		Notification.requestPermission().then(permission=>{
		if (permission === "granted") {
			getFCMToken();
			showNotification("New Desktop notification","Frontend stuff");
		}
		})
	} */
	
						onMessage(messaging, (payload) => {
						  console.log("Message received. ", JSON.stringify(payload));
						  // ...
						  //showNotification("Message received. ",JSON.stringify(payload));
						});
		