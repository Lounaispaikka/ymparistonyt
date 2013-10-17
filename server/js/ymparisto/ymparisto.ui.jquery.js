$(function(){
        
	//showBigImages();
      addA();
});

function addA()  {
       /* if ( typeof selector == 'undefined' ) selector = '#middle';
	var $imgs = $(selector+' img');*/
        var $imgs = $('#middle img');
	/*
	$imgs.each(function (index, img){
		var $img = $(img);
       // $img.click(function(e) {*/
               // $('img').each(function() {
                $imgs.each(function() {
                
                var $img = $(this),
                href = $img.attr('src');
                if ( !$img.hasClass('sulje') ) 
                    {
                    $img.wrap('<a href="' + href + '" rel="prettyPhoto"></a>');
                }    
        });
       // });
       // });
        }


function showBigImages( selector ) {
	
	var animTime = 1000;
	
	if ( typeof selector == 'undefined' ) selector = '#middle';
	var $imgs = $(selector+' img');
	
	$imgs.each(function (index, img){
		var $img = $(img);
		
		if ( !$img.hasClass('close') && !$img.hasClass('sulje') && img.id != 'closenewmsg' && ( $img.naturalWidth() > $img[0].clientWidth || $img.naturalHeight() > $img[0].clientHeight ) ) {
			//$img.mouseenter(function(ev){
                         $img.click(function(ev) {
				var $img = $(this);
				var imgEl = $img[0];
				var $parent = $img.parent();
                                var $clone = $img.clone();
                               
				var animStyles = { 
					width: $img.naturalWidth(), 
					height: $img.naturalHeight(),
					opacity: 1.0
				};
				var ioff = $img.offset();
				var moff = $('#middle').offset();
				
				if (  ioff.left-moff.left > 460  ) {
					animStyles["margin-left"] = animStyles.width*(-1)+imgEl.clientWidth+'px';
				}
				
				$clone.addClass('big-hover-image');
				$clone.css({
					display: 'block',
					position: 'absolute',
					margin: 0,
					top: ioff.top,
					left: ioff.left,
					opacity: 0.0
				});
                              /*  $('.image-title').one('click', function() {
                                        $(this).css({
					display: 'block',
					position: 'absolute',
					margin: 0,
					top: ioff.top,
					left: ioff.left,
					opacity: 0.0
				});*/
                                 //$('body').addClass('bigImage');
                                var titleTop = ioff.top - 40;
                                var titleLeft = ioff.left;
                            //    var imgTitle = '<div class="image-title" style="display:block;position:relative;top:-30px;margin:0;"><p>text</p> </div>';
                                
                                // var imgTitle = '<div class="image-title" style="display:block;position:absolute;margin:0;top:'+titleTop+'px;left:'+titleLeft+'px;"><p>text</p> </div>';
                                
                                ev.stopPropagation();
                                $clone.bind( "clickoutside click", function(ev){
                                
					animStyles.width = imgEl.clientWidth;
					animStyles.height = imgEl.clientHeight;
					animStyles.opacity = 0.0;
					if ( ioff.left-moff.left > 460  ) {
						animStyles["margin-left"] = 0;
					}
                                        $(this).animate(animStyles, {
						duration: animTime,
						complete: function( ev ){
                                                        $('.image-title').remove();
                                                        $('.image-wrap').remove();
                                                        $(this).remove();
                                                     //   $('body').removeClass('bigImage');
                                                       
						}
						
					});
                                        
                                        
                                        //$('.close-image').remove();
				});
                                
				
				$clone.prependTo($parent);
                              $clone.wrap('<div class="image-wrap" />');
                               var cpar = $clone.parent();
                                
                                //cpar.prepend(imgTitle);
                                $clone.animate(animStyles, animTime);
				
			});
		}
	}) ;
	
}



/*
var $img = $('#content img[class!="close"]');

*/

function posti(box, domain) {
	
	window.location = 'mailto:'+box+'@'+domain;
	
}

// adds .naturalWidth() and .naturalHeight() methods to jQuery
// for retreaving a normalized naturalWidth and naturalHeight.
(function($){
  var
  props = ['Width', 'Height'],
  prop;

  while (prop = props.pop()) {
    (function (natural, prop) {
      $.fn[natural] = (natural in new Image()) ? 
      function () {
        return this[0][natural];
      } : 
      function () {
        var 
        node = this[0],
        img,
        value;

        if (node.tagName.toLowerCase() === 'img') {
          img = new Image();
          img.src = node.src,
          value = img[prop];
        }
        return value;
      };
    }('natural' + prop, prop.toLowerCase()));
  }
}(jQuery));

