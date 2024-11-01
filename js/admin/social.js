Lib.Facebook = function(appId) {
	return {
		load  : function load(appId){
			if(appId) {
				$("body").append("<div id='fb-root'></div>");
				window.fbAsyncInit = function() {	
				    FB.init({appId:appId, status: true, cookie: true,xfbml: true});
				};
			
				(function() {
					var e = document.createElement('script'); e.async = true;
				    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
				    document.getElementById('fb-root').appendChild(e);
				}());
			}
		},
		login : function(options){
			var that = this;
			FB.getLoginStatus(function(response) {
			  if (response.session) {
				  if(options.success) options.success.call(that,response);
			  } else {
			  // not logged in
				  FB.login(function(response) {
						if (response.session) {
							if (response.perms) {
									if(options.success) options.success.call(that,response);
				    	    } else {
				    	    	if(options.error) options.error.call(that,response,"User did not grant any permissions");
				    	    }
				      	} else {
				      		if(options.error) options.error.call(that,response,"You are not logged in");
				      	}
				    },{perms:options.perms || ""});
			  }
			});
		},
		getEventUrl : function(id){
			return "http://www.facebook.com/event.php?eid="+id;
		}
	}
}();

Lib.Twitter = function(appId) {
	return {
		load  : function load(appId){
			if(appId) {
				var e = document.createElement('script'); e.async = true;
			    e.src = document.location.protocol + '//platform.twitter.com/anywhere.js?id='+appId;
			    document.body.appendChild(e);
				
			}
		},
		login : function(options){
			var that = this;
			//twttr.anywhere.config({callbackURL:"http://127.0.0.1/wordpress/wp-content/plugins/wordtour/admin/plugin-ajax.php" });
//			twttr.anywhere.config({
//		      assetHost: 'twitter-any.s3.amazonaws.com'
//		    });
			twttr.anywhere(function (T) {
				 if (T.isConnected()) {
					 if(options.connected) options.connected.call(T);
				 } else {
					 T.signIn();
					 T.bind("authComplete", function (e, user) {
						 if(options.connected) options.connected.call(T);
					 });
				 }
				 
				 T.bind("signOut", function (e) {
					 if(options.notconnected) options.notconnected.call(T);
				 });
				  
			});
		}
	}
}();
	
	