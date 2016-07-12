/* JQUERY PREVENT CONFLICT */
(function ($) {
    $(document).ready(function () {
        var productId = $('#upload-dropzone').data('productid');

        Dropzone.autoDiscover = false;
        $("#upload-dropzone").dropzone({
            url: '/admin/products/image/' + productId,
            maxFilesize: 8,
            acceptedFiles: 'image/png, image/gif, image/jpeg',
            success: function (file, response) {
                this.removeFile(file);
                updateImageListAjax();
            }
        });
    });
})(jQuery);

var updateImageListAjax = function () {
    var $imageListArea = $(".image-manager .existing-image");
    $imageListArea.html('<div class="loading" ></div>');
    $.ajax({
        type: "POST",
        url: imageListUrl,
        statusCode: {
            404: function () {
                $imageListArea.html(
                    imageListErrorMessage
                );
            }
        }
    }).done(function (data) {
        $imageListArea.html(data);
        // $.imageUploadManager.onClickDeleteImage();
        // $.imageUploadManager.sortImage();
    });
};
