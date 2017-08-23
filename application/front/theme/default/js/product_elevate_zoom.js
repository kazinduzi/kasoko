$("#image").elevateZoom({
	responsive: true,
	zoomType: 'inner',
	containLensZoom: true,
	gallery: 'image-additional',
	cursor: 'crosshair',
	galleryActiveClass: 'active',
	borderSize: 1
});

//pass the images to Fancybox
$("#image").bind("click", function (e) {
	var ez = $('#image').data('elevateZoom');
	$.fancybox(ez.getGalleryList());
	return false;
});