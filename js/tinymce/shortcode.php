<?php
		function get_country_field(){
		echo "<div class='th'>Country</div><select name='country'><option value=''>All Countries</option>";
		foreach(get_countries() as $key=>$value) { 
			echo "<option value='$key'>$value</option>" ;
		}
		echo "</select>";	
	};
	
	function get_groupby_field(){
		echo "<div class='th'>Group By</div><select name='group_by'>
				<option value=''>No</option>
				<option value='ARTIST'>Artist</option>
				<option value='DATE'>Date</option>
			  </select>";
	}
	
	function show_if_not_headline(){
		echo "<div class='th'>Show event if not headline</div><select name='show_event_if_not_headline'>
				<option value='1'>YES</option>
				<option value='0'>NO</option>
			  </select>";
	}
	
	function get_groupbytour_field(){
		echo '<div class="th">Group By Tour</div><select name="group_by_tour">
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select>';
	}
	
	function get_sort_field(){
		echo '<div class="th">Sort</div><select name="order">
				<option value="ASC">Past To Future</option>
				<option value="DESC">Future To Past</option>
			 </select>';
	}
	
	function get_limit_field(){
		echo '<div class="th">Limit</div><select name="limit">
				<option value="">No Limit</option>
				<option value="5">5 Events</option>
				<option value="10">10 Events</option>
				<option value="15">15 Events</option>
				<option value="20">20 Events</option>
				<option value="25">25 Events</option>
			  </select>';
	}
	
	function get_nav_field(){
		echo "<div class='th'>Pagination</div><select name='navigation'><option value='1'>Yes</option><option value='0' selected='true'>No</option></select>";	
	}
	
	function get_order_field(){
		echo '<div class="th">Order By</div><select name="order_by"><option value="">Name</option><option value="ORDER">Page Order</option></select>';	
	}
	
	function get_datetype_field(){
		echo '<select id="date-type" style="width:200px;">
				<option value="range" selected=true>Date By Range</option>
				<option value="static">Fixed Date</option>
			 </select>';	
	}
	
	function get_startdate_field(){
		echo '<div class="th">Start Date</div><input type="text" id="start_date"></input>
			 <input type="hidden" name="start_date"></input>';	
	}
	
	function get_enddate_field(){
		echo '<div class="th">End Date</div><input type="text" id="end_date"></input>
			 <input type="hidden" name="end_date"></input>';	
	}
	
	function get_artist_field(){
		echo '<select id="wt-sc-artist" name="artist" size="7" style="height:auto;width:100%;" MULTIPLE="true" >
				<option value="" selected="true">All Artists</option>';
				$artists = WT_Artist::all() ;
				foreach($artists as $artist) {
					echo "<option value=".$artist["artist_id"].">".$artist["artist_name"]."</option>";
				}
		echo '</select>';
	}
	
	function get_tour_field(){
			echo '<select id="wt-sc-tour" name="tour" size="7" style="height:auto;width:100%;" MULTIPLE="true">
					<option selected="true">All Tour</option>';
					$tours = WT_Tour::all() ;
					foreach($tours as $tour) {
						echo "<option value=".$tour["tour_id"].">".$tour["tour_name"]."</option>";
					}	
			echo '</select>';
	}
	
	function get_template_field(){
		echo '<div class="th">Theme File Name</div><select name="theme_file_name" style="width:100%;"><option value=""></option>';
		foreach(wt_get_themes_files() as $file) {
			echo "<option value='$file'>$file</option>";
		}
		echo "</select></div>";	
	}
?>


<!--<div>-->
<!--	<select>-->
<!--		<option value="wordtour_events">Events</option>-->
<!--		<option value="wordtour_artists">Artists</option>-->
<!--		<option value="wordtour_tours">Tours</option>-->
<!--		<option value="wordtour_venues">Venues</option>-->
<!--	</select>	-->
<!--</div>-->
<div id="shortcode-generator">
		
		<table cellspacing="0" width="99%">
			<tbody  style="display:none;">
			<tr>
				<td colspan="5" style="background-color:#FFFFE0;">
					<div id="shortcode-result" style="padding:5px;font-style:italic;"></div>
				</td>
			</tr>
			</tbody>
			<tr class="row-1">
				<td><?php get_country_field();?></td>
				<td><?php get_groupby_field()?></td>
				<td><?php get_groupbytour_field()?></td>
				<td colspan="2"><?php get_template_field();?></td>
			</tr>
			<tr class="row-1">
				<td><?php get_limit_field()?></td>
				<td><?php get_nav_field()?></td>
				<td><?php get_order_field()?></td>
				<td colspan="2"><?php get_sort_field()?></td>
			</tr>
			<tr>
				<td  class="row-1" colspan="1">
				<?php show_if_not_headline();?>
				</td>
			</tr>
			<tbody>
			<tr class="row-2">
				<td colspan="6"><?php get_datetype_field()?></td>
			</tr>
			<tr class="row-2">
				<td name="date-range">
					<select id="date-dropdown">
						<option value="ALL">All</option>
						<option value="UPCOMING" selected="true">Upcoming Shows</option>
						<option value="TODAY">Today</option>
						<option value="ARCHIVE">Archive</option>
					</select>
				</td>
				<td name="date-range" colspan="4" style="text-align:center;padding-top:10px;">
					<div id="slider-date-range" style="width:270px;"></div>
					<div id="slider-date-display" style="width:270px;color:#000000;font-weight:normal;font-size:10px;text-align:center;padding:3px;"></div>
					<input type="hidden" name="date_range"></input>
				</td>
				<td name="date-static"><?php get_startdate_field();?></td>
				<td name="date-static" colspan="4"><?php get_enddate_field();?></td>
			</tr>
			</tbody>
			<tbody>
			<tr class="row-1">
				<td colspan="6"><b>Include</b></td>
			</tr>
			<tr class="row-2">
				<td colspan="2"><?php get_artist_field();?></td>
				<td colspan="2"><?php get_tour_field();?></td>
				<td></td>
			</tr>
			</tbody>
		</table>
		<div style="margin:5px;">
			<input id="shortcode-create" type="button" class="button" value="Create Shortcode"></input>
			<input id="shortcode-insert" type="button" class="button" value="Insert Into Post"></input>
		</div>
</div>

<script>
jQuery(function($){
	(function slider(){
		function sliderCaption(start,end) {
			var v;		
			if(start===0 && end === 0) $("#date-dropdown").val("TODAY");
			if(start >= 0 && end > 0) $("#date-dropdown").val("UPCOMING");
			if(start < 0 && end <= 0) $("#date-dropdown").val("ARCHIVE");
			if(start < 0 && end > 0) $("#date-dropdown").val("ALL");

			v = [start,end].join(",");
			if(start===0 && end === 0) v="TODAY"; 
			if(start===0 && end === 13) v="UPCOMING";
			if(start===-13 && end === 0) v="ARCHIVE";
			if(start===-13 && end === 13) v="ALL";
			
			$("[name=date_range]").val(v);

			if(start===13) start = "Upcoming";
			if(end===13) end = "Upcoming"; 
			if(start===-13) start = "Archive";
			if(end===-13) end = "Archive";
			if(start===0) start = "Today"; 
			if(end===0) end = "Today";
			if(start<0) start = "Last "+(start*-1)+" month";
			if(end<0) end = "Last "+(end*-1)+" month";
			if(start>0) start = "Next "+start+" month";
			if(end>0) end = "Next "+end+" month";
			
			$("#slider-date-display").html(start + ' - '+ end);
		}

		$("#slider-date-range").slider({
			range: true,
			min: -13,
			max: 13,
			values: [0, 13],
			slide: function(event, ui) {
				sliderCaption(ui.values[0],ui.values[1]);	
			}
		});

		$("#date-dropdown").change(function(){
			var v = this.value;
			switch(v) {
				case "ALL":
					$("#slider-date-range").slider("values",0,-13);
					$("#slider-date-range").slider("values",1,13);
					sliderCaption(-13,13);
				break;
				case "TODAY":
					$("#slider-date-range").slider("values",0,0);
					$("#slider-date-range").slider("values",1,0);
					sliderCaption(0,0);
				break;
				case "UPCOMING":
					$("#slider-date-range").slider("values",0,0);
					$("#slider-date-range").slider("values",1,13);
					sliderCaption(0,13);
				break;
				case "ARCHIVE":
					$("#slider-date-range").slider("values",0,-13);
					$("#slider-date-range").slider("values",1,0);
					sliderCaption(-13,0);
					
				break;
			} 
			
		});
		sliderCaption(0,13);
	}());

	(function range(){
		function setDateType(){
			var v = $("#date-type").val();
			if(v=="range") {
				$("[name=date-range]").show();
				$("[name=date-static]").hide();
			} else {
				$("[name=date-range]").hide();
				$("[name=date-static]").show();
			}
		}
		
		$("#date-type").change(function(){
			setDateType();
		}).trigger("change");
		
		

		$("#start_date").datepicker({altField: '[name=start_date]', altFormat: 'yy-mm-dd'});
		$("#end_date").datepicker({altField: '[name=end_date]', altFormat: 'yy-mm-dd'});	
	}());

	(function group(){
		$("[name=group_by]").change(function(){
			var v = this.value;
			if(v=="DATE") {
				$("[name=group_by_tour]").val("0").attr("disabled","true");
			} else {
				$("[name=show_event_if_not_headline]").attr("disabled","true");
				$("[name=group_by_tour]").removeAttr("disabled");
			}

			if(v=="ARTIST") {
				$("[name=show_event_if_not_headline]").removeAttr("disabled");
			}
		}).trigger("change");
	}());


	(function include(){
		var show_map = <?php echo wt_group_artists_tour_json();?>;

		function get_tour_by_artist(map,id) {
			var all = (id=="") ? true : false;
			if(all) {
				var tour = []; 
				for(var j = 0;j<map.length ; j++) {
					var m = map[j];
					tour = tour.concat(m["tour"]);
				}	
			} else {
				if(!$.isArray(id)) id = [id];
				var artists = id;
				var tour = [];
				for(var i = 0;i<artists.length ; i++) {
					var artist = artists[i];
					for(var j = 0;j<map.length ; j++) {
						var m = map[j];
						if(m.artist.id==artist) tour = tour.concat(m["tour"]) ;	
					}
				}
			}
			
			return tour;		
		}

		function get_tour_dropdown(data){
			var dd = $("#wt-sc-tour");
			dd.find("option").each(function(){
				$(this).remove();
			});
			dd.append("<option value='' selected='true'>All Tour</option>");
			for(var i = 0;i<data.length ; i++) {
				var tour =  data[i];
				if(tour) {
					if(tour.id) dd.append("<option value='"+tour.id+"'>"+tour.name+"</option>");
				}
			}						
		}

		$("#wt-sc-artist").change(function(){
			get_tour_dropdown(get_tour_by_artist(show_map,$(this).val()));
		});
		
		
		get_tour_dropdown(get_tour_by_artist(show_map,""));
	}());	
	
	var WordTourDialog = {
		init : function(ed) {
			//tinyMCEPopup.resizeToInnerSize();
		},
		insert : function insertEmotion(code) {
	    	tinyMCEPopup.execCommand('mceInsertContent', false, code);
			tinyMCEPopup.close();
		}
	};
	tinyMCEPopup.onInit.add(WordTourDialog.init,WordTourDialog);
	/* Shortcode */
	function createShortCode() {
		var form = $("#shortcode-generator");
		var target = $("#shortcode-result");
		var attrStr = [];
		form.find("td:visible input:text[name],td:visible input:hidden[name],td:visible select[name],input:checked[name]").each(function(){
			var value,name ;
			if(this.tagName.toUpperCase() == "INPUT") {
				value = $(this).val();
				name =  $(this).attr("name");
				if(value!="") attrStr.push(name+"=\""+value+"\""); 
			} else {
				value = $(this).val(); 
				name =  $(this).attr("name");
				if(value!="") attrStr.push(name+"=\""+($.isArray(value) ? value.join(",") : value)+"\"");
			}
			
		});
		return "[wordtour_events "+attrStr.join(" ")+"]";
	}
	
	$("#shortcode-create").click(function(){
		var code = createShortCode(); 
		//WordTourDialog.insert(code);
		$("#shortcode-result").html(code).parents("tbody").show();
	});

	$("#shortcode-insert").click(function(){
		var code = createShortCode(); 
		WordTourDialog.insert(code);
	});

	

});
</script>


