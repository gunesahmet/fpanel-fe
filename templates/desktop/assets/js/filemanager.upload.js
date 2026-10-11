var fileUpload = function() {
  return {init:function(b) {
    $("#fileupload").fileupload({maxChunkSize:5E5, disableImageResize:!1, autoUpload:!1, maxRetries:100, retryTimeout:500, maxFileSize:2E9, fail:function(a, c) {
      $.ajax({url:b, dataType:"json", data:{file:c.files[0].name}, type:"DELETE"});
    }});
    $("#fileupload").fileupload("option", "redirect", window.location.href.replace(/\/[^\/]*$/, "/cors/result.html?%s"));
    $.support.cors && $.ajax({type:"HEAD"}).fail(function() {
      $('<div class="alert alert-danger"/>').text("Upload server currently unavailable - " + new Date).appendTo("#fileupload");
    });
    $("#fileupload").addClass("fileupload-processing");
    $.ajax({url:$("#fileupload").attr("action"), dataType:"json", context:$("#fileupload")[0]}).always(function() {
      $(this).removeClass("fileupload-processing");
    }).done(function(a) {
      $(this).fileupload("option", "done").call(this, $.Event("done"), {result:a});
    });
  }};
}();
$("#close").on("click", function() {
  getFolderLists("#folder_lists");
  initFileList();
  $(".files").html("");
});
