// TODO: when to delete span is clicked toggle deleting css and checkbox input.
// TODO: use ui-sortable to organize Order of Widgets

jQuery(document).ready(function($){

	$up_btn = $('<div/>').text('Up').addClass('move_btn up');
	$down_btn = $('<div/>').text('Down').addClass('move_btn down');
	
	$up_down_btn = $('<p/>').addClass('up_down');//<div class="up" >up</div><div class="down" >down</div></p>'
	$up_down_btn.append( $up_btn, $down_btn );
	
	/*	
	$up_btn = $("<div/>", {
	  "class": "move_btn up",
	  text: "Up",
	  click: function(){
	  	
	  }
	});*/
	
	move_btn_click = function(){
		
		$social_links = $(this).closest(".widget-content").find('.social_link');
		
		// find this's parent social link
		
		$to_move = $(this).closest('.social_link');
		
		my_index = $('.social_link').index($to_move)
		
	
		if($(this).hasClass('up')){
			
			if( my_index > 0 ){
				new_index = my_index-1;
				$social_links.eq( new_index ).before( $to_move );
			}
			
		}else if($(this).hasClass('down')){
			
			if( my_index <= $social_links.length-1 ){
				new_index = my_index+1;
				$social_links.eq( new_index ).after( $to_move );
			}
			
		}
		
	}
	
	apply_up_down = function( widget ){
		
		if( $(this).hasClass('.ui-draggable') )
			return;
		
		$('.widget-content .social_link', widget ).append( $up_down_btn );
		
		$('.move_btn', widget).click( move_btn_click );
		
		$('.social_link textarea.widefat', widget ).autoResize();
	}
	
	$(window).load( apply_up_down(this) );

	$('div[id*=social_links_widgets].widget').ajaxSuccess( function(){
		try{
			apply_up_down(this);
	  }catch(err){
			console.error(err);
	  }
	});
	
	//http://stackoverflow.com/questions/2246317/jquery-live-doesnt-appear-to-bind-click-event/2246890#2246890
	function livetoggle(selector, f0, f1) {
    $(selector).live('click', function(event) {
        var t= $(this).data('livetoggle');
        $(this).data('livetoggle', !t);
        (t? f1 : f0).call(this, event);
    });
	}
	
	// toggle for 
	livetoggle('.social_link .to_delete', function(){
		$(this).addClass('deleting').children('span').text('will delete')
		$(this).children(':checkbox').attr("checked","checked");
	},function(){
		$(this).removeClass('deleting').children('span').text('click to delete')
		$(this).children(':checkbox').removeAttr("checked");
	})
	
	// autoexpand text fields
	
	//$('.social_link textarea.widefat').autoResize();//
	
});