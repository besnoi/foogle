var timer=0;

$(document).ready(function(){
	initMasonry();
	fancyBox();
})

function loadImage(url,className){
	let image=$("<img>")
	image.on('load',function(){
		$(`.${className} a`).append(image)

		clearTimeout(timer);

		timer=setTimeout(() => {
			$(".imageResults").masonry()
		},500);
	})
	image.on('error',function(){
		$(`.${className}`).remove()
		$.post("ajax/imgrmv.php",{
			"imgURL":url
		})
	})
	image.attr('src',url);
}

function initMasonry(){
	let grid=$('.imageResults').masonry({
		itemSelector: ".gridItem",
		isInitLayout:true,
		transformDuration : 0.5,
		gutter: 5
	})

	grid.masonry( 'on', 'layoutComplete', function() {
		$(".gridItem img").css("visibility","visible")
	});

	grid.masonry();
}

function fancyBox(){
	$("[data-fancybox]").fancybox({
		caption:function(_,item){

			let caption=$(this).data('caption') || ''
			let id=$(this).data('id') || ''

			console.log('working'+caption)

			if (item.type==='image')
				caption = `<h2 style='font-weight:normal'>${(caption.length>0 ? caption + '<br/>' : '')}</h2>` +
	            	`<a target="new" href="${item.src}">View Image</a> &nbsp;&nbsp;|&nbsp;&nbsp; `+
	            	`<a target="new" href="redirect.php?type=images&id=${id}">Visit Page</a>`;

			return caption

		}
	});
}


//For Infinite scroll: TODO
function getMoreImages(event,isInview){
	if (!isInview) return;
	// console.log('WORKING FOR ME<BR/>');
	let id=$(this).attr('data-id');
	let url=$(this).attr('href');
	
	$.ajax({
		type: 'POST',
		url: 'ajax/inclk.php',
		data: {
			"id": id
		},
		success: function(data){
			$('#pageno')
		}
	})
}