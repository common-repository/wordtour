jQuery(function($){
$.widget("ui.wordtourform", {
   items  : [],
   options: {
		alert    : ".wordtour-alert",
		overlay  : true,
		// events
		init      : $.noop,
		beforeSend: $.noop,
		success   : $.noop,
		error     : $.noop,
		complete  : $.noop,
		items     : []
   },
   items : function() {
	   var that = this ;
		if($.isArray(this.options.items)) {
			$(this.options.items).each(function(){
				that._addItem(this);	
			});
		}
   },
   serialize : function() {
		var qs = {}, that = this;
		$.each(this.options.items,function(){
			if(!this.name && this.name!="") return;
			var item = this, name = item.name,value = "" ;
			var dom = $(this.selector);
			var type = item.type.toLowerCase(); 
			switch(type) {
				case "button":
					
					if(item.inputType == "checkbox") {
						value = dom.attr("checked") ? dom.val() : "0" ;
					}
				break;
				case "postermanager":
					value = dom.postermanager("value");
				break;
				case "gallerymanager":
					value = dom.gallerymanager("value");
					value = JSON.stringify(value);
				break;
				case "categorymanager":
					value = dom.gallerymanager("value");
					value = JSON.stringify(value);
				break;
				case "thumbnailmanager":
					value = dom.thumbnailmanager("value");
					value = value.length > 0 ? JSON.stringify(value) : "";
				break;
				// VIDEO MANAGER
				case "videomanager":
					value = dom.videomanager("value");
					value = JSON.stringify(value);
				break;
				case "autocomplete":
					value = dom.data("value") || dom.val();
					if(item.inputType == "time") {
						try {
							$.each(item.options.source,function(){
								if(value == this.value) value = this.term;
							});
						} catch(e){}; 
					}
				break;
				case "datepicker":
					try {
						value = dom.datepicker("getDate").toString("yyyy-MM-dd");
					} catch(e){
						value = "" ;
					};
					
				break ;
				default:
				value = dom[type]("value");
			}
			qs[name] = value ;				
		});
		return qs;
	},
	_addItem : function(item) {
		var that = this ;
		// name - the name to send when submitting form
		var n = item.name || "" ;
		var s = item.selector || "[name="+item.name+"]";
		var t = item.type || false;
		var o = item.options || {};
		$.extend(item,{selector:s,jPath: item.jPath ? item.jPath : item.name});
		if(t!="select") {
			var widget = $(s)[t](o);
			if(t == "autocomplete") {
				var required = item.required;
				if(required || item.inputType == "time") {
					$(s).bind("autocompleteselect",function(event, ui){
						$(this).data("text",ui.item.value || "");
						$(this).data("value",ui.item.term || "");
					})
					.bind("blur",function(){
						if(required) {
							$(this).val($(this).data("text") || "");
						} else {
							if($(this).val() == "") {
								$(this).data("text","");
								$(this).data("value","");	
							};
						}		
					});
				}
			}
		} 

		if(t=="select") {
			if(o.change) {
				$(s).change(function(event,u){		
					o.change.call(this);
				});
			}
		} 
		
		if(item.listeners) {
			$.each(item.listeners,function(event){
				var handler = this;
				$(s).bind(event,handler);
			})
			
		}
		
		return widget;
	},
	set : function(values) {
		var that = this ;
		$.each(this.options.items,function(){
			var item = this ;
			
			if(item && (item.name || item.jPath)) {
				var value = values[item.jPath] || "" ;
				if(this.renderer) value = this.renderer.call(this,value); 
				var selector = item.selector || "[name="+item.name+"]";
				if(item.jPath.indexOf(",")!=-1) {
					value = $(item.jPath.split(",")).map(function(){
						return values[this] || "";
					});
				};
				// DATEPICKER
				if(item.type === "datepicker") {
					if(value!="") {
						try {
							value = Date.parseExact(value,"yyyy-MM-dd").toString($CONSTANT.ADMIN_DATE_FORMAT == "m/d/y" ? "MM/dd/yyyy" : "dd/MM/yyyy");
						} catch(e){};
						$(selector).datepicker("setDate",value);
					} else {
						$(selector).val("");
					}	
				};
				// GOOGLE MAP
				if(item.type === "googlemap") {
					var map = [];
					if(value[0]!="") map.push(value[0]);
					if(value[1]!="") map.push(value[1]);
					if(value[2]!="") map.push(value[2]);
					$(item.selector).googlemap("value",map.join(","),value[3]);	
				};
				// ARTIST MANAGER
				if(item.type === "artistsmanager") {
					$(item.selector).artistsmanager("value",value);	
				};
				// TRACK MANAGER
				if(item.type === "tracksmanager") {
					$(item.selector).tracksmanager("value",value);	
				};
				if(item.type === "genremanager") {
					$(item.selector).genremanager("value",value);	
				};
				// GALLERY MANAGER
				if(item.type === "gallerymanager") {
					$(item.selector).gallerymanager("value",value);	
				};
				if(item.type === "categorymanager") {
					$(item.selector).gallerymanager("value",value);	
				};
				// THUMBNAIL MANAGER
				if(item.type === "thumbnailmanager") {
					$(item.selector).thumbnailmanager("value",value);	
				};
				// RSVP
				if(item.type === "rsvpmanager") {
					if(value!="") $(item.selector).rsvpmanager("value",value);	
				};
				
				// VIDEO MANAGER
				if(item.type === "videomanager") {
					if($.isArray(value)) {
						$(item.selector).videomanager("value",value);
					}
				};
				// POSTER MANAGER
				if(item.type === "postermanager") {
					try {
						$(item.selector).postermanager("value",value[0],value[1].url);
					} catch(e) {
						$(item.selector).postermanager("value",0);
					}
				};
				// AUTOCOMPLETE
				if(item.type === "autocomplete") {
					var aText,aValue ;
					if(typeof value === "string") {
						if(item.inputType === "time") {
							try{
								value = Date.parseExact(value,"HH:mm:ss").toString(item.format);
							} catch(e){}
						}		
						aText = aValue = value;
					} else {
						if(item.inputType === "time") {
							try{							 
								value[0] = Date.parseExact(value[0],"HH:mm:ss").toString(item.format);
							} catch(e){}
						}		
						aText  = value[0];
						aValue = value[1];
					};					
					$(item.selector).val(aText);//.data("value",aValue).data("text",aText);
				};
				// INPUT TEXT
				if(item.type === "inputtext") {
					$(item.selector).inputtext("value",value);	
				}
				// READONLY TEXT
				if(item.type === "readonlytext") {
					$(item.selector).readonlytext("value",value);	
				}
				// COMPONENT
				if(item.type === "component") {
					$(item.selector).component("value",value);	
				}
				// BUTTON
				if(item.type === "button" && item.inputType === "checkbox") {
					var isChecked = $(item.selector).val() == value ;
					$(item.selector).attr( "checked",isChecked);
					$(item.selector).next()[(isChecked ? "add" : "remove") + "Class"]("ui-state-active").button("refresh");
				};
				// SELECT
				if(item.type === "dropdown") {
					var selectItem = $("[name="+this.name+"]"); 
					$("[name="+this.name+"]").val(values[this.name]||"").trigger("change");
				}
					
			} else {
//				if($("[name="+this.name+"]").attr("type").toUpperCase() == "CHECKBOX") {
//					
//				} else {;
//					$("[name="+this.name+"]").val(values[this.name]||"");
//				}
			}
		});
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
		
		if(typeof this.options.overlay == "string") {
			$(this.options.overlay).block(options);
		} else {
			$.blockUI(options);
		}
		
	},
	hideOverlay: function(){
		if(typeof this.options.overlay == "string") {
			$(this.options.overlay).unblock();
		} else {
			$.unblockUI();
		}
			
	},
	ajax: function(data,success,error){
		var that = this ;
		var success = success || $.noop;
		var error = error || $.noop;
		var o = $.extend({
			data       : data,
			cache      : false,
			type       : 'POST',
			url        : $CONSTANT.PLUGIN_AJAX,
			beforeSend : function() {
				if(that.options.overlay) that.showOverlay();
				that.clean();
				that.alert("hide");
				that._trigger("beforeSend",null);
			},
			success: function(response){
				try {
					if(response.type == "success" || !response.type) {
						success.call(that,response);
						if(that.options.overlay) that.hideOverlay();
						that._trigger("success",null,response);
					} else if(response.type == "error" && response.type) {
						if(response.data) that.invalid(response.data);
						if(that.options.overlay) that.hideOverlay();
						if(response.msg) that.alert("show","error",response.msg);
						error.call(that,response);
					}
				} catch(e) {
					that.alert("show","error","Error - Error as occured<br/>" + e);
					if(that.options.overlay) that.hideOverlay();
				}
			},
			complete : function(response) {
				that._trigger("complete",null);
			},
			error : function(response) {
				error.call(that,response);
				that._trigger("error",null);
				if(that.options.overlay) that.hideOverlay();
				that.alert("show","error","Error as occured, please try again");
			},
			dataType: "json" 
		},{});

		$.ajax(o);
	},
	alert: function(state,mode,msg) {
		var elem = $(this.options.alert);
		elem[state]();
		if(state=="show") {
			if(msg) elem.html(msg);
			elem.attr("class","wordtour-alert");
			elem.addClass("wordtour-alert-"+mode);
		}
		
	},
	invalid: function(errors){
		var that = this;
		var msg = ["Information was invalid","<ul>"];
		$(this.options.items).each(function(){
			if(this.name) {
				var elem = $(this.selector);
				if(elem.length>0) {
					var isField = elem[0].tagName.toLowerCase() == "input" || elem[0].tagName.toLowerCase() == "textarea";
					if(errors[this.name]){
						if(isField) {
							elem.addClass("wordtour-field-invalid");
						}
						if(errors[this.name].txt) msg.push("<li>"+errors[this.name].txt+"</li>");
					} else {
						if(isField) {
							elem.removeClass("wordtour-field-invalid");
						}		
					}
				}
			}
		});
		msg.push("</ul");
		that.alert("show","error",msg.join(""));
		this.element.find(".wordtour-field-invalid:first").focus(); 
	},
	clean : function() {
		this.element.find(".wordtour-field-invalid").removeClass("wordtour-field-invalid");
	},
   _create: function(options) {
	  $.extend(this.options,options);
	  if($.isArray(this.options.items)) this.items();
	  this._trigger("init",null);
	  this.element.submit(function(){
		  return false;
	  });
   },
   destroy: function() {
       $.Widget.prototype.destroy.apply(this, arguments); 
   }
 });

$.extend($.ui.wordtourform, {
	eventPrefix: "wordtourform"	
});
});


