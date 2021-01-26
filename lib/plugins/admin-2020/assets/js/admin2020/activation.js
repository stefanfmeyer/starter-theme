function check_licence_key(network) {
  jQuery("#a2020-licence-key").removeClass("uk-form-danger");
  var key = jQuery("#a2020-licence-key").val();

  if (!key || key == "") {
    jQuery("#a2020-licence-key").addClass("uk-form-danger");
    return;
  }

  jQuery.ajax({
    url: admin2020_activation_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_check_licence_key",
      security: admin2020_activation_ajax.security,
      key: key,
      network: network,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          jQuery("#a2020-licence-key").addClass("uk-form-danger");
          UIkit.notification(data.error_message, "danger");
          console.log(data.error_message);
        } else {
          jQuery("#a2020-licence-key").addClass("uk-form-success");
          jQuery("#activationpanel").fadeOut(300);
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}
