function a2020_save_user_prefences(pref, value, notification = null) {
  if (pref == "") {
    return;
  }

  jQuery.ajax({
    url: admin2020_utilities_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_user_prefences",
      security: admin2020_utilities_ajax.security,
      pref: pref,
      value: value,
    },
    success: function (response) {
      if (response) {
        console.log(response);
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          if (notification != false) {
            UIkit.notification(data.message, "success");
          }
        }
      }
    },
  });
}

