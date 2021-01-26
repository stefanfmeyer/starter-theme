function a2020_check_for_updates() {
  jQuery.ajax({
    url: admin2020_update_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_check_for_updates",
      security: admin2020_update_ajax.security,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
          location.reload();
        }
      }
    },
  });
}
