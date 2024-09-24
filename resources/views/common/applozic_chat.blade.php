<?php
      $login_user_id      = isset($arr_login_user_details['user_id']) ? $arr_login_user_details['user_id'] :0;
      $login_user_role    = isset($arr_login_user_details['user_type']) ? $arr_login_user_details['user_type'] :'';
      $user_full_name     = isset($arr_login_user_details['full_name']) ? $arr_login_user_details['full_name'] :'';
      $user_profile_image = isset($arr_login_user_details['profile_image']) ? $arr_login_user_details['profile_image'] :'';
      $user_email         = isset($arr_login_user_details['email']) ? $arr_login_user_details['email'] :'';
      
      $arr_users_json = get_specific_user_users_for_applozic_chat($login_user_id,$login_user_role);
      
?>

<!-- wholseler categories of products hide page end here -->
<script type="text/javascript">
   (function(d, m){var s, h;       
   s = document.createElement("script");
   s.type = "text/javascript";
   s.async=true;
   s.src="https://apps.applozic.com/sidebox.app";
   h=document.getElementsByTagName('head')[0];
   h.appendChild(s);
   window.applozic=m;
   m.init=function(t){m._globals=t;}})(document, window.applozic || {});
</script>

<script type="text/javascript">
  
  var arr_json   = <?php echo $arr_users_json; ?>;
  

  /* var arr_json = [{"userId": "1", "displayName": "Devashish",
                          "imageLink": "https://www.applozic.com/resources/images/applozic_icon.png", // image url (optional)
                          "imageData" :"Base64 encoded image data"  // or image data (optional)
                          },
                         {"userId": "3", "displayName": "Adarsh",
                          "imageLink": "https://www.applozic.com/resources/images/applozic_icon.png", // image url (optional)
                          "imageData" :"Base64 encoded image data"  // or image data (optional)
                         }
                      ];

    console.log(arr_json);
*/
  window.applozic.init({

      appId: '2d5e5b0eedf9bd6ec1d5406ef415ffb4d',      //Get your application key from https://www.applozic.com
      userId: '{{$login_user_id}}', //Logged in user's id, a unique identifier for user
      userName: '{{$user_full_name}}',                 //User's display name
      imageLink : '{{$user_profile_image}}',                     //User's profile picture url
      email : '{{$user_email}}',                         //optional
      contactNumber: '',                  //optional, pass with internationl code eg: +13109097458
      desktopNotification: true,
      source: '1',                          // optional, WEB(1),DESKTOP_BROWSER(5), MOBILE_BROWSER(6)
      notificationIconLink: 'https://www.applozic.com/favicon.ico',    //Icon to show in desktop notification, replace with your icon
      authenticationTypeId: 1,          //1 for password verification from Applozic server and 0 for access Token verification from your server
      accessToken: '',                    //optional, leave it blank for testing purpose, read this if you want to add additional security by verifying password from your server https://www.applozic.com/docs/configuration.html#access-token-url
      locShare: true,
      googleApiKey: "AIzaSyDKfWHzu9X7Z2hByeW4RRFJrD9SizOzZt4",   // your project google api key 
      googleMapScriptLoaded : true,   // true if your app already loaded google maps script
      mapStaticAPIkey: "AIzaSyCWRScTDtbt8tlXDr6hiceCsU83aS2UuZw",
      autoTypeSearchEnabled : true,     // set to false if you don't want to allow sending message to user who is not in the contact list
      loadOwnContacts : true, //set to true if you want to populate your own contact list (see Step 4 for reference)
      olStatus: true,         //set to true for displaying a green dot in chat screen for users who are online
      maxAttachmentSize : 25, //max attachment size in MB
      onInit : function(response) {
         if (response === "success") {
            // login successful, perform your actions if any, for example: load contacts, getting unread message count, etc
            $applozic.fn.applozic('loadContacts', {"contacts":arr_json});
         } else {
         
         }
     },
     contactDisplayName: function(otherUserId) {
           //return the display name of the user from your application code based on userId.
           return "";
     },
     contactDisplayImage: function(otherUserId) {
           //return the display image url of the user from your application code based on userId.
           return "";
     },
     onTabClicked: function(response) {
           // write your logic to execute task on tab load
           //   object response =  {
           //    tabId : userId or groupId,
           //    isGroup : 'tab is group or not'
           //  }
     }
  });
 
 $(document).ready(function(){

     $(".logOutUser").on('click', function(){
             $applozic.fn.applozic('logout');
      });

 });

</script>