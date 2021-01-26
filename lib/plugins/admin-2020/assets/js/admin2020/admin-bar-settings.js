jQuery(document).ready(function ($) {
  var mediaUploader;
  var mediaUploaderDark;
  var mediaUploaderBackground;
  ///LIGHT LOGO
  $("#a2020_select_light_logo").click(function (e) {
    e.preventDefault();
    if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: false,
    });
    mediaUploader.on("select", function () {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      $("#a2020_light_logo_preview").attr("src", attachment.url);
      $("#light-logo-url").val(attachment.url);
    });
    mediaUploader.open();
  });
  ///DARK LOGO
  $("#a2020_select_dark_logo").click(function (e) {
    e.preventDefault();
    if (mediaUploaderDark) {
      mediaUploaderDark.open();
      return;
    }
    mediaUploaderDark = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: false,
    });
    mediaUploaderDark.on("select", function () {
      var attachment = mediaUploaderDark
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#a2020_dark_logo_preview").attr("src", attachment.url);
      $("#dark-logo-url").val(attachment.url);
    });
    mediaUploaderDark.open();
  });
  $("#a2020_select_login_background").click(function (e) {
    e.preventDefault();
    if (mediaUploaderDark) {
      mediaUploaderDark.open();
      return;
    }
    mediaUploaderDark = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
        text: "Choose Image",
      },
      multiple: false,
    });
    mediaUploaderDark.on("select", function () {
      var attachment = mediaUploaderDark
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#a2020_login_background_preview").attr("src", attachment.url);
      $("#login-background").val(attachment.url);
    });
    mediaUploaderDark.open();
  });
});


