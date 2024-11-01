var Lib = {} ;

function getDataFromStr(str) {
	var s = str.split(":");
	var p = {} ;
	jQuery.each(s,function(){
		var s = this.match(/(\w*)-([\d|\D]*)/);
		//var s = this.split("-");
		p[s[1]] = s[2]; 
	});
	return p;
}



function openAlertDialog(title,content,okHandler){
	var id = "wordtour-alert-dialog";
	var title = title || "Alert";
	var content = content || "";
	if($("#"+id)) $("body").append("<div id='"+id+"' title='"+title+"'><p>"+content+"</p></div>")
	$("#"+id).dialog({
		modal: true,
		buttons: {
			Ok: function() {
				if(okHandler) okHandler.call(this); 
				$(this).dialog('close').dialog('destroy').remove();
			}
		}
	});
}

function openAllDataDialog(dialogId,fieldSelector,url,title) {
	var that = this ;
	new Lib.Dialog(dialogId,{
		markupUrl : url,
		ready     : function(){
			var that = this;
			$(this.selector).find("div:first").click(function(e){
				var v = false;
				if($(e.target).is("a")) {
					v = $(e.target).find("strong").text(); 
				};
				if($(e.target).is("small")) {
					v = $(e.target).parents("a").find("strong").text();
				};
				if($(e.target).is("strong")) {
					v = $(e.target).text();
				};
				if(v) {
					$(fieldSelector).val(v);
					$(that.options.target).dialog("close");
				}
				return false;
			});
		}
	})
	.wrap()
	.setDialog({
		title     : title,
		width     : 350,
		height    : 400
	}).setButtons({
		'Done': function() {
			$(this).dialog('close');
		}
	})
	.open();
}

function openEditEventDialog(data,success) {
	new Lib.EventDialog("quickedit-event",{
		markupData : data
	}).wrap().setDialog({
		title: "Quick Edit Event"
	})
	.setButtons({
		'Save': function() {
			if(success) success.call(this);
		}
	})
	.open();
}

function openInsertArtistDialog(data,success) {
	var that = this ;
	new Lib.ArtistDialog("artists-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Add New Artist"
	}).setButtons({
		'Add Artist': function() {
			var form = $(this).find("form");
			var data = $.extend({action:"insert_artist"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				if(success) success.call(this,r);
				$.ajaxSetup({cache: false});
				$(dialog).dialog('close');
			});				
		}
	}).open();
}

function openEditArtistDialog(data,success) {
	var that = this ;
	new Lib.ArtistDialog("artists-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Artist"
	}).setButtons({
		'Update Artist': function() {
			if(success) success.call(this);	
		}
	}).open();
}


function openEditAlbumDialog(data,success) {
	var that = this ;
	new Lib.AlbumDialog("album-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Album"
	}).setButtons({
		'Update Album': function() {
			if(success) success.call(this);	
		}
	}).open();
}

function openEditTrackDialog(data,success) {
	var that = this ;
	new Lib.TrackDialog("track-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Track"
	}).setButtons({
		'Update Track': function() {
			if(success) success.call(this);	
		}
	}).open();
}

function openEditCommentDialog(data,success) {
	var that = this ;
	new Lib.CommentDialog("comments-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Comment"
	}).setButtons({
		'Update Comment': function() {
			if(success) success.call(this);	
		}
	}).open();
}

function openInsertTourDialog(data,success) {
	var that = this ;
	new Lib.TourDialog("tour-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Add New Tour"
	}).setButtons({
		'Add Tour': function() {
			var form = $(this).find("form");
			var data = $.extend({action:"insert_tour"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				if(success) success.call(this,r);
				$.ajaxSetup({cache: false});
				$(dialog).dialog('close');
			});				
		}
	}).open();
}

function openEditTourDialog(data,success) {
	var that = this ;
	new Lib.TourDialog("tour-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Tour"
	}).setButtons({
		'Update Tour': function() {
			if(success) success.call(this);	
		}
	}).open();
}

function openInsertVenueDialog(data,success) {
	var that = this ;
	new Lib.VenueDialog("venue-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Add New Venue"
	}).setButtons({
		'Add Venue': function() {
			var form = $(this).find("form");
			var data = $.extend({action:"insert_venue"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				if(success) success.call(this,r);
				$.ajaxSetup({cache: false});
				$(dialog).dialog('close');
			});				
		}
	}).open();
}

function openEditVenueDialog(data,success) {
	var that = this ;
	new Lib.VenueDialog("venue-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Venue"
	}).setButtons({
		'Update Venue': function() {
			if(success) success.call(this);	
		}
	}).open();
}

function openTwitterDialog(data,handler) {
	var selector = "tweet-dialog";
	new Lib.TwitterDialog("tweet-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Twitter Status"
	}).open();
}

function openPostDialog(data,handler) {
	new Lib.PostDialog("post-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Add Post"
	}).open();
}

function openFacebookDialog(data,success) {
	var that = this ;
	new Lib.FacebookDialog("facebook-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Publish To Facebook"
	}).open();
}

function openEventbriteDialog(data,success) {
	var that = this ;
	new Lib.EventbriteDialog("eventbrite-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Publish To Eventbrite"
	}).open();
}

function openImportEventbriteDialog(data,success) {
	var that = this ;
	new Lib.EventbriteImportDialog("eventbrite-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Import Eventbrite Events and Venues"
	}).open();
}



function openInsertGalleryDialog(data,success) {
	var that = this ;
	new Lib.GalleryDialog("gallery-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Add New Gallery"
	}).setButtons({
		'Add Gallery': function() {
			var form = $(this).find("form");
			var data = $.extend({action:"insert_gallery"},form.wordtourform("serialize"));
			var dialog = this;						
			form.wordtourform("ajax",data,function(r){
				if(success) success.call(this,r);
				$.ajaxSetup({cache: false});
				$(dialog).dialog('close');
			});				
		}
	}).open();
}

function openEditGalleryDialog(data,success) {
	var that = this ;
	new Lib.GalleryDialog("gallery-dialog",{
		markupData : data
	}).wrap().setDialog({
		title   : "Update Gallery"
	}).setButtons({
		'Update Gallery': function() {
			if(success) success.call(this);	
		}
	}).open();
}

Lib.Map = Base.extend({
	constructor : function(selector){
		if (GBrowserIsCompatible()) {
			var map = this.map = new GMap2($(selector)[0]);
			//map.setUIToDefault(); 
			//map.removeMapType(G_NORMAL_MAP);
	        map.addControl(new GLargeMapControl3D());
	        //map.addControl(new GMapTypeControl());
	        //map.setMapType(G_NORMAL_MAP);
	        this.geocoder = new GClientGeocoder();
	    }
	},
	showAddress:function(address,countryCode,errorHandler) {
		var that = this ;
		var geocoder = this.geocoder;
		var map = this.map;
		var addressMarker = this.addressMarker || null;
		if (geocoder) {
	        geocoder.setBaseCountryCode(countryCode);
	        geocoder.getLatLng(address,
	          function(point) { 
		        if (!point) {
	            	errorHandler(address);
	            } else {
		          if(that.addressMarker) map.removeOverlay(that.addressMarker);
		          that.addressMarker = addressMarker = new GMarker(point);
	              map.checkResize();
	              map.setCenter(point,14);
	              map.addOverlay(addressMarker);	
	              addressMarker.openInfoWindowHtml(address,{maxWidth:50});
	            }
	          }
	        );
	      }
	}
});




