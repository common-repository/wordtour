Lib.Dialog = Base.extend({
	constructor : function(id,options) {
		var that = this ,id = this.id = id || "dialog";
		this.selector = "#"+id;
		
		if($(this.selector).length == 0) $("body").append("<div id='"+id+"' style='dispaly:none;'></div>");
		
		this.options = $.extend({
			markupUrl  : "",
			markupData : {},
			target   : this.selector,
			close    : $.noop
		},options);
		
		if(!this.options.open) {
			this.options.open = function() {
				that.getMarkup(that.options.ready || $.noop);
			}
		}
		return this;
	},
	wrap : function(markup,handler) {
		if(!$(this.selector).length) {
			$("body").append("<div id='"+this.id+"' style='display:none;'></div>");
			if(markup) $(this.selector).html(markup || "");
			if(handler) handler.call(this);
		}
		return this ;
	},
	showOverlay : function() {
		$.blockUI.defaults.fadeOut = 0; 
		$.blockUI.defaults.fadeIn = 0;
		var options = { message: 'Processing...',css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff'
        	}
		}; 
		$(".ui-dialog").block(options);
		
	},
	hideOverlay: function(){
		$(".ui-dialog").unblock();	
	},
	getMarkup : function(handler) {
		var that = this ;
		$.get(that.options.markupUrl,that.options.markupData,function(markup) {
			var target = $(that.options.target); 
			target.append(markup);
			if(handler) handler.call(that);
		});
		
		return this ;
	},
	setDialog:function(options) {
		var that = this;
		this.dialogOptions = $.extend({
			bgiframe: true,
			autoOpen: true,
			modal   : true,
			open : function(){
				that.options.open();
			},
			close : function(){
				that.options.close();
				//$(this).dialog("destroy")
			},
			beforeclose : function(){
				//return $(this).data("loader").css("display") =="none" ;
			}
		},options || {});
		
		return this ;
	},
	showLoader : function() {
		$(this.selector).data("loader").show();
	},
	hideLoader : function() {
		$(this.selector).data("loader").hide();
	},
	setButtons:function(buttons) {
		this.buttonOptions = $.extend(buttons || {},{
			Cancel   : function() {
				$(this).dialog('close');
			}
		});
		return this ;
	},
	open : function(handler) {
		$(this.options.target || this.selector).html("");
		$(this.selector).dialog($.extend(this.dialogOptions,{buttons:this.buttonOptions}));
		if(handler) handler.call(this);
		return $(this.selector);
	}
});

Lib.AjaxDialog  = Base.extend({
	constructor : function(selector) {
		this.selector = selector || "#dialog";
		return this ;
	},
	ajax :function(options) {
		var options = options || {} ;
		var d = $(this.selector); 
		var form = d.find("form");
		
		// need to remove after change
		if($("#wordtour-gallery-dialog-form").length > 0) {
			var queryStr = $("#wordtour-gallery-dialog-form").wordtourform("serialize");
			var data = $.extend(queryStr,{action:options.action});
		} else {
			var queryStr = form.serialize();
			var data = options.action+"&"+queryStr;
		}
		
		d.find("div.error").html("").hide();
		$("#error-msg").hide();
		var loader = d.data("loader");
		if(loader) loader.show(); 
		$.ajax({
			type: 'POST',
			cache      : false,
			url     : $CONSTANT.PLUGIN_AJAX,
			data    : data,
			success : function(r){
				if(loader) loader.hide();
				form.find("[name]").parents("div.dialog-field").removeClass("form-invalid").find("div.field-error").remove();
				if(r.type == "success") {
					if(options.success) options.success.call(d,r);
					if(options.closeOnSuccess) d.dialog("close"); 
				}
				if(r.type == "error") {
					if(r.data) {
						$.each(r.data,function(name,data){
							var field = form.find("[name="+name+"]");
								field.parents("div.dialog-field").addClass("form-invalid").append("<div class='field-error'>"+data.txt+"</div>");
						})	
					}

					if(r.msg) {
						d.find("div.error").html(r.msg).show();
					}				
				}		
			},
			error : function(){
				if(loader) loader.hide();
			},
			dataType: "json"
		});	
	}
});

Lib.ListWithDialog  = Base.extend({
	constructor: function(options) {
		var that = this ;
		var o = options || {} ; 
		this.options = $.extend(o,{
			list : $(o.selector), 
			form : $(o.selector).parents("form")
		});

		this.handlers = {};
		
		this.options.list.click(function(e){
			var elem = e.target;
			if($(elem).is("a")) {
				var id = $(elem).parents("tr").find(that.options.idSelector).val();
				var className = $(elem).parent().attr("class");
				if(that.handlers[className]) that.handlers[className].call(that,id,elem);
			}
		});
	},
  insert:function(selector,handler) {
		var that = this ;
		var s = selector || "#b-new" 
		$(selector).button({icons: {primary:'ui-icon-plus'}}).click(function(){
				handler.call(that);		
		});
		return this ;
   },
   edit:function(handler) {
	   this.action("edit",handler || $.noop)
		return this;
   },
   del:function(handler) {
	   this.action("delete",handler);
		return this;
   },
   action : function(id,handler) {
	   this.handlers[id] = handler ;
	   return this ;
   },	   
   deleteButton:function(selector,action) {
		var that = this ;
		var s = selector || "#b-delete" ; 
		$(s).button({icons: {primary:'ui-icon-close'}}).click(function(){
			var id = $.map(that.options.form.find("input:checked[name]"),function(c){
				return $(c).val();	
			}).join(",");
			that.deleteAll(action+"&id="+id);
		});	
		
		return this ;
	},
	deleteRow:function(params,tr) {
		$.ajax({
			type: 'POST',
			url     : $CONSTANT.PLUGIN_AJAX,
			data    : params,
			success : function(r){
				if(r.type == "success") {
					$(tr).effect("highlight",{color:"#FFEBE8",mode:"hide"},400,function(){
						$(this).remove();
					});
				}	
				if(r.type == "error") {
					// to do
					$("#error-msg").html("Error as ocurred "+r.msg).effect("slide",{mode:"show",direction:"up"});			
				}		
			},
			failure : function(){},
			dataType: "json"
		});	
	},
	deleteAll:function(params) {
		var that = this ;
		$.ajax({
			type: 'POST',
			url     : $CONSTANT.PLUGIN_AJAX,
			data    : params,
			success : function(r){
				$.each(r,function(id,data){
					if(data.type == "success") {
						that.options.form.find("input:checkbox[value="+id+"]").parents("tr").effect("highlight",{color:"#FFEBE8",mode:"hide"},400,function(){
							$(this).remove();
						});	
					} 
					if(data.type == "error") {
						// to do
						$("#error-msg").html("Error as ocurred "+r.msg).effect("slide",{mode:"show",direction:"up"});
					}
				})		
			},
			failure : function(){},
			dataType: "json"
		});	
	},
	insertRow:function(action,handler) {
		var that = this ;
		var dSelector = this.options.dialogSelector;
		
		if($("#wordtour-gallery-dialog-form").length > 0) {
			var form = $("#wordtour-gallery-dialog-form");
			var data = $.extend({action:action},form.wordtourform("serialize"));						
			form.wordtourform("ajax",data,function(r){
				if(r.type=="success") {
					that.options.list.find("tbody tr.empty").remove() ; 
					var rowToUpdate = that.options.list.find("tbody").prepend(r.html).find("tr:first").effect("highlight",null,600);
					if(handler) handler.call(that,r,rowToUpdate);
					$(dSelector).dialog("close");
				} else {
					
				}
			});
		} else {
			new Lib.AjaxDialog(dSelector || null).ajax({
				url      : $CONSTANT.PLUGIN_AJAX,
				action   : action,
				success  : function(r) {
					that.options.list.find("tbody tr.empty").remove() ; 
					var rowToUpdate = that.options.list.find("tbody").prepend(r.html).find("tr:first").effect("highlight",null,600);
					if(handler) handler.call(that,r,rowToUpdate);
				},
				closeOnSuccess : true
			});
		}
		
		
								
	},
	updateRow:function(action,rowToUpdate,handler) {
		var that = this ;
		var dSelector = this.options.dialogSelector;
		if($("#wordtour-gallery-dialog-form").length > 0) {
			var form = $("#wordtour-gallery-dialog-form");
			var data = $.extend({action:action},form.wordtourform("serialize"));						
			form.wordtourform("ajax",data,function(r){
				if(r.type=="success") {
					that.options.list.find("tbody tr.empty").remove() ; 
					that.replaceRowHtml(rowToUpdate,r.html);
					if(handler) handler.call(that,r,rowToUpdate);
					$(dSelector).dialog("close");
				} else {
					
				}
			});
		} else {
			new Lib.AjaxDialog(dSelector || null).ajax({
				url      : $CONSTANT.PLUGIN_AJAX,
				action   : action,
				success  : function(r) {
					that.replaceRowHtml(rowToUpdate,r.html);
					if(handler) handler.call(that,r,rowToUpdate);
				},
				closeOnSuccess : true
			});	
		}
	},
	replaceRowHtml: function(rowToUpdate,html){
		var tbody = $(rowToUpdate).parent();
		var rowIndex = $(rowToUpdate).attr("rowIndex")-1  ;
		$(rowToUpdate).replaceWith(html);
		tbody.find("tr:eq("+rowIndex+")").effect("highlight",null,600);
	}

});


Lib.VenueDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl : $CONSTANT.DIALOG_VENUES,
			//target    : "#venue-details",
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_venue",venue_id:options.markupData.venue_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){
						},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
							{ 
								name      : "venue_gallery_status",
								selector  : "#venue_gallery_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "venue_flickr_status",
								type      : "button",
								selector  : "#venue_flickr_status",
								inputType :"checkbox"
							},
							{ 
								name      : "venue_video_status",
								selector  : "#venue_video_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "venue_post_status",
								selector  : "#venue_post_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "venue_tour_status",
								selector  : "#venue_tour_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								type     : "autocomplete",
								name     : "venue_name",
								options  : {
									source: function(request, response) {
										if($CONSTANT["LASTFM_API_KEY"]!="") {
											var term =  request.term;
											$.ajax({
												url: "http://ws.audioscrobbler.com/2.0/",
												dataType: "jsonp",
												data: {
													format : "json",
													api_key: $CONSTANT["LASTFM_API_KEY"],
													limit  : 10,
													method :"venue.search",
													venue  : term
												},
												success: function(data) {
													var venues = data.results.venuematches.venue ;
													if(venues) {
														if(!$.isArray(venues)) venues = [venues];
														response($.map(data.results.venuematches.venue, function(venue) {
															var r = {
																name    : venue.name,
																phone   : venue.phonenumber,
																url     : venue.website,
																country : venue.location.country,
																city    : venue.location.city,
																address  : venue.location.street
															}
														
															var location = [];
															if(r.address!="") location.push(r.address);
															if(r.city!="") location.push(r.city);
															if(r.country!="") location.push(r.country);
															
															return {
																label: r.name+"<div style='font-size:90%;color:#AAAAAA'>"+location.join(", ")+"</div>",
																value: r.name,
																term : r
															}
														}))
													} else {
														response([
												          {
															label: "No Results",
															value: term,
															term : term
												          }
														]);
													}
												}
											});
										};
									},
									select: function(event, ui) {
										var term = ui.item.term;
										if(term) {
											$("#venue_url").val(term.url);
											$("#venue_phone").val(term.phone);
											$("#venue_address").val(term.address);
											$("#venue_city").val(term.city);
											$("#venue_country").val(term.country);
										}
									},
									minLength: 1
								}
							},
							{ 
								type     : "autocomplete",
								name     : "venue_country",
								selector : "#venue_country",
								options  : {
									source   : $CONSTANT.AUTOCOMPLETE_COUNTRY,
									minLength: 1
								}
							},
							{type: "inputtext",name: "venue_city",selector: "#venue_city"},
							{ 
								type     : "autocomplete",
								name     : "venue_state",
								selector : "#venue_state",
								options  : {
									source   : $CONSTANT.AUTOCOMPLETE_STATE,
									minLength: 1
								}
							},
							{type: "inputtext",name: "venue_address"},
							{type: "inputtext",name: "venue_id"},
							{type: "inputtext",name: "_nonce",selector:"[name=_venue_nonce]"},
							{type: "inputtext",name: "venue_order"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
					
				});
			}
		},options || {}));
		this.wrap();
		return this ;
		
	},
	setDialog : function(options){
		return this.base($.extend({
			height  : 550,
			width   : 700
		},options || {}));
	},
	getAddress:function(target) {
		var address = {};
		$(this.selector +" form").find("[name=venue_address],[name=venue_city],[name=venue_state],[name=venue_country]").each(function(){
			if($(this).val()!="") address[$(this).attr("name")] = $(this).val(); 
		});
		return address ; 
	},
	getMap : function(ui) {
		var that = this;
		$("#map").hide();
		var address = that.getAddress();
		if(!address.venue_country) {
			$(ui.panel).find(".error").html("Can't display map : Country is missing, please fill in the blank").show();
			return; 	
		}
		
		$.ajax({
			type: 'POST',
			url     : $CONSTANT.PLUGIN_AJAX,
			cache      : false,
			data    : "action=get-key&country_name="+address.venue_country,
			dataType: "json",
			success : function(r){
				if(r.country_code!="") {
					$("#map").show();
					addressArr = [];
					$.each(address,function(key,value){
						addressArr.push(value);
					});
					that.setMap(410,285).showAddress(addressArr.join(","),r.country_code,function(address){
						$(ui.panel).find(".error").html("Can't find address:<i>"+address+"</i>").show();
						$("#map").hide();
					});	
				} else {
					$(ui.panel).find(".error").html("Can't display map : Country name doesn't exist, please try again").show();
				}	
			},
			failure : function() {
				$(ui.panel).find(".error").html("Error as Occured, Please try again").show();
			}
		});
	},
	setMap:function(w,h) {
		if(!$("#map").data("map")) {
			$("#venue-map").append("<div id='map' style='width:"+w+"px;height:"+h+"px;'></div>");
			$("#map").data("map",new Lib.Map("#map"));
		}
		
		return $("#map").data("map") ;
	}
});



Lib.ArtistDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_ARTISTS,
			close : function(){
			}, 
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_artist",artist_id:options.markupData.artist_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
							{ 
								name      : "artist_gallery_status",
								selector  : "#artist_gallery_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "artist_flickr_status",
								type      : "button",
								selector  : "#artist_flickr_status",
								inputType :"checkbox"
							},
							{ 
								name      : "artist_video_status",
								selector  : "#artist_video_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "artist_post_status",
								selector  : "#artist_post_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "artist_tour_status",
								selector  : "#artist_tour_status",
								type      : "button",
								inputType :"checkbox"
							},
							{type: "inputtext",name: "artist_name"},
							{type: "inputtext",name: "artist_order"},
							{type: "inputtext",name: "artist_id"},
							{type: "inputtext",name: "_nonce",selector:"[name=_artist_nonce]"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({
			height  : 400,
			width   : 600
		},options || {}));
	}
});

Lib.TourDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_TOUR,
			close : function(){}, 
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_tour",tour_id:options.markupData.tour_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
							{ 
								name      : "tour_gallery_status",
								selector  : "#tour_gallery_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "tour_flickr_status",
								type      : "button",
								selector  : "#tour_flickr_status",
								inputType :"checkbox"
							},
							{ 
								name      : "tour_video_status",
								selector  : "#tour_video_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "tour_post_status",
								selector  : "#tour_post_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "tour_tour_status",
								selector  : "#tour_tour_status",
								type      : "button",
								inputType :"checkbox"
							},
							{type: "inputtext",name: "tour_name"},
							{type: "inputtext",name: "tour_order"},
							{type: "inputtext",name: "tour_id"},
							{type: "inputtext",name: "_nonce",selector:"[name=_tour_nonce]"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({
			height  : 400,
			width   : 600
		},options || {}));
	}
});

Lib.AlbumDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_ALBUMS,
			close : function(){
			}, 
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_album",album_id:options.markupData.album_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
							{type: "inputtext",name: "album_title"},
							{type: "inputtext",name: "album_id"},
							{type: "inputtext",name: "_nonce",selector:"[name=_album_nonce]"},
							{type: "inputtext",name: "album_order"},
							{ 
								name      : "album_similar_status",
								selector  : "#album_similar_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "album_tracks_status",
								type      : "button",
								selector  : "#album_tracks_status",
								inputType :"checkbox"
							},
							{ 
								type     : "autocomplete",
								name     : "artist_name",
								selector : "#album_artist_name",
								required : false,
								options  : {
									source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
									minLength: 1
								}
							}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({
			height  : 450,
			width   : 500
		},options || {}));
	}
});

Lib.TrackDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_TRACKS,
			close : function(){
			}, 
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_track",track_id:options.markupData.track_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
							{type: "inputtext",name: "track_title"},
							{type: "inputtext",name: "track_id"},
							{type: "inputtext",name: "_nonce",selector:"[name=_track_nonce]"},
							{type: "inputtext",name: "track_label"},
							{type: "inputtext",name: "track_about"},
							{type: "inputtext",name: "track_credits"},
							{
								type       : "datepicker",
								name       : "track_release_date",
								selector   : "#track_release_date",
								options    : {
									dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy" 
								} 
							},
							{ 
								type     : "autocomplete",
								name     : "artist_name",
								selector : "#track_artist_name",
								required : false,
								options  : {
									source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
									minLength: 1
								}
							}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({
			height  : 650,
			width   : 500
		},options || {}));
	}
});

Lib.CommentDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_COMMENT,
			close : function(){
			}, 
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_comment",comment_id:options.markupData.comment_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						items: [
							{type: "inputtext",name: "_nonce",selector:"[name=_comment_nonce]"},
							{type: "inputtext",name: "comment_author"},
							{type: "inputtext",name: "comment_author_email"},
							{type: "inputtext",name: "comment_content"},
							{type: "inputtext",name: "comment_event_id"},
							{type: "inputtext",name: "comment_user_id"},
							{type: "inputtext",name: "comment_approved"},
							{type: "inputtext",name: "comment_id"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 500,
			width   : 500
		},options || {}));
	}
});

Lib.GalleryDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_GALLERY,
			close : function(){
				$("#gallery-dialog-tabs").tabs("destroy");
				$("#gallery-widget").gallery("destroy");
			}, 
			open : function() {
				that.getMarkup(function(){
					
					var thumbManager = $("#wordtour-gallery-dialog-details .wordtour-thumbnailmanager-wrap");
					var mediaManager = $("#wordtour-gallery-dialog-search");
					(function init_tabs(){
						$("#wordtour-gallery-dialog-form .dialog-tabs").tabs();
					})();
					
					(function initMediaLibrary() {
						mediaManager.medialibrary({
							select : function(e,ui){
								thumbManager.thumbnailmanager("addItem",ui.id,ui.src);
							}	
						});
					})();
					
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							var form = $(this);
							var galleryId = options.markupData.gallery_id;
							//if(galleryId && galleryId!="") {
								var data = {action:"get_gallery",gallery_id:galleryId};
								$(this).wordtourform("ajax",data,function(r){
									this.element.wordtourform("set",r);
									mediaManager.medialibrary("option","exclude",thumbManager.thumbnailmanager("selected"));
									mediaManager.medialibrary("refresh");
								},false);
							//}	
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
					        {
								type      : "inputtext",
								name      : "gallery_name",
								selector  :  "#wordtour-gallery-dialog-details [name=gallery_name]"
					        },
					        {
					        	type      : "thumbnailmanager",
					        	name      : "gallery_attachment",
					        	selector  : "#wordtour-gallery-dialog-details .wordtour-thumbnailmanager-wrap",
					        	options    : {
					        		remove: function(){
					        			mediaManager.medialibrary("option","exclude",thumbManager.thumbnailmanager("selected"));
					        			mediaManager.medialibrary("refresh");
					        		}
					        	}	
					        	
					        },
					        {
								type      : "inputtext",
								name      : "gallery_id",
								selector  :  "#wordtour-gallery-dialog-form [name=gallery_id]"
					        },
					        {
								type      : "inputtext",
								name      : "_nonce",
								selector  :  "[name=_gallery_nonce]"
					        }
					     ]
					});
					
					if(that.options.ready) that.options.ready.call(this);
					
				});
				
				if(that.options.ready) that.options.ready.call(this);
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({
			title     : "Quick Edit Event",
			width     : 770,
			height    : 550
		},options || {}));
	}
});

Lib.EventDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl : $CONSTANT.DIALOG_EVENTS, 
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){
							$(this).wordtourform("ajax",{action:"get_event",event_id:options.markupData.event_id},function(r){
								this.element.wordtourform("set",r);
							},false);
						},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						complete : function(e,response) {},
						beforeSend: function(){},
						items: [
							{ 
								name      : "gallery_status",
								selector  : "#gallery_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "post_status",
								selector  : "#post_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "comment_status",
								selector  : "#comment_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "rsvp_status",
								selector  : "#rsvp_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								name      : "flickr_status",
								type      : "button",
								selector  : "#flickr_status",
								inputType :"checkbox"
							},
							{ 
								name      : "video_status",
								selector  : "#video_status",
								type      : "button",
								inputType :"checkbox"
							},
							{ 
								type     : "autocomplete",
								name     : "venue_name",
								selector : "#event_venue_name",
								required : false,
							  	options  : {
									source   : $CONSTANT.AUTOCOMPLETE+"?type=venues&maxRows=10",
									minLength: 1
							  	}
							}
							,
							{ 
								type     : "autocomplete",
								name     : "artist_name",
								selector : "#event_artist_name",
								required : false,
								options  : {
									source   : $CONSTANT.AUTOCOMPLETE+"?type=artists&maxRows=10",
									minLength: 1
								}
							},
							{
								name     : "tour_name",
								selector : "#event_tour_name",
								type     : "autocomplete",
								options  : {
									source   : $CONSTANT.AUTOCOMPLETE+"?type=tour&maxRows=10",
									minLength: 1
								}
							},
							{
								type     : "autocomplete",
								name     : "event_start_time",
								inputType: "time",
								format   : "hh:mm tt",
								options  : {
									source   : function(){
										var source = [];
										for(var i = 1 ; i <= 12 ; i++) {
											source.push({term:(i<10 ? "0"+i : i) + ":00:00",value:(i<10 ? "0"+i : i) + ":00AM"});
											source.push({term:(i<10 ? "0"+i : i) + ":30:00",value:(i<10 ? "0"+i : i) + ":30AM"});
											source.push({term:((i+12) == 24 ? "00" : (i+12))+":00:00",value:(i<10 ? "0"+i : i) + ":00PM"});
											source.push({term:((i+12) == 24 ? "00" : (i+12))+":30:00",value:(i<10 ? "0"+i : i) + ":30PM"});
										};
										
										return source ;
									}(),
									minLength: 1
								}
								
							},
							{
								name     : "event_end_time",
								type     : "autocomplete",
								inputType: "time",
								format   : "hh:mm tt",
								options  : {
									source   : function(){
										var source = [];
										for(var i = 1 ; i <= 12 ; i++) {
											source.push({term:(i<10 ? "0"+i : i) + ":00:00",value:(i<10 ? "0"+i : i) + ":00AM"});
											source.push({term:(i<10 ? "0"+i : i) + ":30:00",value:(i<10 ? "0"+i : i) + ":30AM"});
											source.push({term:((i+12) == 24 ? "00" : (i+12)) + ":00:00",value:(i<10 ? "0"+i : i) + ":00PM"});
											source.push({term:((i+12) == 24 ? "00" : (i+12)) + ":30:00",value:(i<10 ? "0"+i : i) + ":30PM"});
										};
										
										return source ;
									}(),
									minLength: 1
								}
							},
							//datepicker
							{
								type       : "datepicker",
								name       : "event_start_date",
								options    : {
									dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy" 
								} 
							},
							{
								type       : "datepicker",
								name       : "event_end_date",
								options    : {
									dateFormat : $CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "mm/dd/yy" : "dd/mm/yy"
								} 
							},
							{type: "inputtext",name: "event_title"},
							{type: "inputtext",name: "event_id"},
							{type: "inputtext",name: "_nonce",selector:"[name=_event_nonce]"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({
			height  : 600,
			width   : 750
		},options || {}));
	}
});


Lib.FacebookDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_FACEBOOK,
			close : function(){
			}, 
			open : function() {
				that.getMarkup(function(){
					$("#fb-dialog-mode").change(function(){
						$("#fb-dialog-status")[(this.value==="status" ? "show":"hide")]();
						$("#fb-dialog-event")[(this.value==="status" ? "hide":"show")]();
					});
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		this.setButtons();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 600,
			width   : 500
		},options || {}));
	},
	setButtons: function(buttons) {
		var obj = this;
		return this.base($.extend({ 
			"Publish To Facebook": function() {
				var d = $(this),that = this;
				try {
					if(FB) {
						d.find("div.wordtour-alert").hide();
						
						var showError = function(msg){
							d.find("div.wordtour-alert").html(msg)
							.attr("class","wordtour-alert")
							.addClass("wordtour-alert-error")
							.show();
						};
						
						obj.showOverlay();
						
						Lib.Facebook.login({
							perms   : ["create_event","publish_stream"],
							success: function(){
								var mode = $("#fb-dialog-mode").val();
								var eventId = d.find("form [name=event_id]").val();
								var nonce = d.find("form [name=_nonce]").val();
								var successRegister = $.noop;								
								switch(mode) {
									case "status":
										var status = d.find("form [name=status]").val();
										successRegister = function(r){
											$("#wordtour-button-facebook .update:first").show();
											$("#facebook_status_date").html(r.result.social_publish_time);
										};
										FB.api('/me/feed', 'post', { message: status }, function(response) {
											if (!response || response.error) {
												obj.hideOverlay();
												showError(response.error.message);
											} else {
												$.ajax({
													type    : 'POST',
													url     : $CONSTANT.PLUGIN_AJAX,
													data : {action:"update-facebook",_nonce:nonce,event_id:eventId,type:"fbstatus"},
													success : function(r){
														if(r.type=="success") {
															d.dialog("close");
															if(successRegister) successRegister(r);
														} else {
															showError(r.msg);
														}
													},
													error : function(){
														obj.hideOverlay();
														showError("Error Occured, Please try again");
													},
													beforeSend: function(){
													},
													complete: function(){
														obj.hideOverlay();
													},
													dataType: "json"
												});
											}
										});
									break;
									
									case "event":
										successRegister = function(r){
											$("#wordtour-button-facebook .update:last").show();
											$("#facebook_event_id").attr("href",Lib.Facebook.getEventUrl(r.result.social_ref_id));
										};
										
										FB.api('/me/events','post',{
											name        :d.find("form [name=title]").val(),
											description :d.find("form [name=description]").val(),
											start_time  :d.find("form [name=start_time]").val(),
											end_time    :d.find("form [name=end_time]").val(),
											location    : d.find("form [name=location]").val(),
											street      : d.find("form [name=address]").val(),
											city        : d.find("form [name=city]").val()
										}, function(response) {
											if (!response || response.error) {
												obj.hideOverlay();
												showError(response.error.message);
											} else {
												$.ajax({
													type    : 'POST',
													url     : $CONSTANT.PLUGIN_AJAX,
													data : {action:"update-facebook",_nonce:nonce,event_id:eventId,ref_id:response.id,type:"fbevent"},
													success : function(r){
														if(r.type=="success") {
															d.dialog("close");
															if(successRegister) successRegister(r);
														} else {
															showError(r.msg);
														}
													},
													error : function(){
														obj.hideOverlay();
														showError("Error Occured, Please try again");
													},
													beforeSend: function(){
													},
													complete: function(){
														obj.hideOverlay();
													},
													dataType: "json"
												});
											}
										});
									
									break;
								}
							},
							error: function(r,msg){
								obj.hideOverlay();
								showError("Error Occured, Please try again: "+msg);
							}
						});
					} 
					
				} catch(e) {
					obj.hideOverlay();
					showError("Error Loading Facebook, Please reload the page");
					return false;
				}
				return ;
			}
		},buttons));
	}	
});

Lib.TwitterDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_TWITTER,
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						items: [
							{type: "inputtext",name: "_twitter_nonce"},
							{type: "inputtext",name: "twitter_status"},
							{type: "inputtext",name: "twitter_event_id"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		this.setButtons();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 550,
			width   : 500
		},options || {}));
	},
	setButtons: function(buttons) {
		var obj = this;
		return this.base($.extend({ 
			"Tweet": function() {
				var form = $(this).find("form");
				var data = $.extend({action:"update_twitter"},form.wordtourform("serialize"));
				var d = $(this);
				d.find("div.wordtour-alert").hide();
				var showError = function(msg){
					d.find("div.wordtour-alert").attr("class","wordtour-alert").addClass("wordtour-alert-error").show().html(msg);
				};
				
				obj.showOverlay();
				
				Lib.Twitter.login({
					connected: function(){
						this.Status.update(data.twitter_status,{
							error: function(r){
								if(r.error) {
									var msg = r.reason;
									try{
										if(r.response) {
											msg+= " ["+r.response.error+"]";
										}
									}catch(e){};
									showError(msg);
								}
								obj.hideOverlay();
						 	},
						 	success: function(r){
						 		// register status to history
						 		$.ajax({
									type    : 'POST',
									url     : $CONSTANT.PLUGIN_AJAX,
									data : {action:data.action,_nonce:data["_twitter_nonce"],event_id:data.twitter_event_id,type:"twitter",ref_id:r.id},
									success : function(r,status,xhr){
										var r = $.isPlainObject(r) ? r : $.parseJSON(r);
										if(r.type=="success") {
											$("#wordtour-button-twitter .update:first").show();
											$("#twitter_status_date").html(r.result.social_publish_time);
										} 
									},
									complete: function(){
										obj.hideOverlay();
										d.dialog("close");
									},
									error : function(){
										showError("Status was updated succefully, but couldn't be saved to further monitoring");
									},
									dataType: "json"
								});
						 	}
						});
					},
					notconnected: function(){
						showError("You are not logged in to Twitter, Can't update status");
						obj.hideOverlay();
					}
				});
			}
		},buttons));
	}	
});

Lib.PostDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_POST,
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						items: [
							{type: "inputtext",name: "_post_nonce"},
							{type: "inputtext",name: "post_title"},
							{type: "inputtext",name: "post_event_id"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		this.setButtons();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 550,
			width   : 500
		},options || {}));
	},
	setButtons: function(buttons) {
		var obj = this;
		return this.base($.extend({ 
			"Add Post": function() {
				var form = $(this).find("form");
				var data = $.extend({action:"add_post"},form.wordtourform("serialize"));
				var dialog = this;						
				form.wordtourform("ajax",data,function(r){
					try {
						$("#wordtour-button-post .update:first").show();
						$("#post_ref_id").attr("href",r.result.post_link);
					} catch(e){};
					$(dialog).dialog('close');
				});				
			}
		},buttons));
	}	
});

Lib.EventbriteDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_EVENTBRITE,
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						items: [
							{type: "inputtext",name: "eventbrite_event_id"},
							{type: "inputtext",name: "eventbrite_title"},
							{type: "inputtext",name: "eventbrite_description"},
							{type: "dropdown",name: "eventbrite_privacy"},
							{type: "inputtext",name: "eventbrite_personalized_url"},
							{type: "dropdown",name: "eventbrite_status"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		this.setButtons();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 600,
			width   : 500
		},options || {}));
	},
	setButtons: function(buttons) {
		var obj = this;
		return this.base($.extend({ 
			"Publish To Eventbrite": function() {
				var form = $(this).find("form");
				var data = $.extend({action:"save_eventbrite_event"},form.wordtourform("serialize"));
				
				var dialog = this;	
				form.wordtourform("ajax",data,function(r){
					try {
						$("#wordtour-button-eventbrite .update").show();
						$("#eventbrite_status_date").html(r.publish_date);
						$("#eventbrite_ref_id").attr("href",r.url);
						$("#event_tkts_url").val(r.tickets);
					} catch(e){};
					$(dialog).dialog('close');
				});				
			}
		},buttons));
	}	
});

Lib.EventbriteImportDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_IMPORT_EVENTBRITE,
			open : function() {
				that.getMarkup(function(){
					var f = $(this.selector).find("form");
					$(f).wordtourform({
						init     : function(){},
						overlay: ".ui-dialog",
						alert  : "#dialog-alert",
						success: function(that,r){},
						items: [
							{type: "dropdown",name: "artist_id"}
						]
					});
					
					if(that.options.ready) that.options.ready.call(this);
				});
			}
		},options || {}));
		this.wrap();
		this.setButtons();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 300,
			width   : 500
		},options || {}));
	},
	setButtons: function(buttons) {
		var obj = this;
		return this.base($.extend({ 
			"Import": function() {
				var form = $(this).find("form");
				var data = $.extend({action:"import-eventbrite-events"},form.wordtourform("serialize"));
				var dialog = this;	
				form.wordtourform("ajax",data,function(r){
					var msg = "<p>Import process ended successfully</p><ul>";
					try {
						if(r.log.venues) {
							$.each(r.log.venues,function(){
								try {
									msg+= "<li>Error saving venue <b>"+this.venue.name +"</b>, "+this.venue.region+","+this.venue.country+": ";
									$.each(this.msg.data,function(){
										msg+= this.txt;
									})
									msg+="</li>"
								} catch(e){}
							}); 
						}
						
						if(r.log.events) {
							$.each(r.log.events,function(){
								try {
									msg+= "<li>Error saving event <b>"+this.event.title+"</b> : ";
									$.each(this.msg.data,function(){
										msg+= this.txt;
									})
									msg+="</li>"
								} catch(e){}
							});  
						}
						
					} catch(e){};
					msg+="</ul>"
					form.wordtourform("alert","show","success",msg);
				},function(r){
					var msg = "";
					try {
						if(r.log.error) {
							msg = r.log.error.error_message + " ["+r.log.error.error_type+"]"; 
						}
					} catch(e){};
					form.wordtourform("alert","show","error",msg == "" ? "Error as Occured" : msg);
				});				
			}
		},buttons));
	}	
});

Lib.ImportAlbumInfoDialog = Lib.Dialog.extend({
	constructor : function(id,options) {
		var that = this ;
		this.base(id,$.extend({
			markupUrl  : $CONSTANT.DIALOG_IMPORT_ALBUM_INFO,
			open : function() {
				that.getMarkup(function(){
					
				});
			}
		},options || {}));
		this.wrap();
		this.setButtons();
		return this ;
	},
	setDialog : function(options){
		return this.base($.extend({			
			height  : 500,
			width   : 500
		},options || {}));
	},
	setButtons: function(buttons) {
		var obj = this;
		return this.base($.extend({ 
			"Import": function() {
				var dialog = $(this);
				var tracks = [];
				dialog.find("form input:checked").each(function(){
					var value = $(this).parent().next().find("textarea").val();
					switch($(this).attr("name")) {
						case "title":
							$("#album_title").val(value);
						break;
						case "artist":
							$("#album_artist_name").val(value);
						break;
						case "date":
							if(value!="") $("#album_release_date").val(value);
						break;
						case "about":
							if(value!="") $("#album_about").val(value);
						break;
						case "track":
							tracks.push({name:value});
						break;
					}
				});
				
				if(tracks.length>0) {
					$("#wordtour-panel-tracks .wordtour-panel-content").tracksmanager("value",tracks);
				}
				
				$(dialog).html("");
				$(dialog).dialog('close');
				
			}
		},buttons));
	}	
});