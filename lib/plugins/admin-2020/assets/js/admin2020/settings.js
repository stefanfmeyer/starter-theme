function a2020_save_modules(network) {
  var options = [];

  jQuery("#a2020_all_modules .a2020_module").each(function () {
    option_name = jQuery(this).attr("name");
    value = jQuery(this).val();

    if (jQuery(this).is(":checkbox")) {
      if (jQuery(this).is(":checked")) {
        value = "true";
      } else {
        value = "false";
      }
    }

    data = [];
    data.push(option_name);
    data.push(value);
    options.push(data);
  });

  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_modules",
      security: admin2020_settings_ajax.security,
      options: options,
      network: network,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

function a2020_save_settings(network) {
  var options = [];

  jQuery(".a2020_setting").each(function () {
    module_name = jQuery(this).attr("module-name");
    setting_name = jQuery(this).attr("name");
    value = jQuery(this).val();

    if (jQuery(this).is(":checkbox")) {
      if (jQuery(this).is(":checked")) {
        value = "true";
      } else {
        value = "false";
      }
    }

    data = [];
    data.push(module_name);
    data.push(setting_name);
    data.push(value);
    options.push(data);
  });

  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_settings",
      security: admin2020_settings_ajax.security,
      options: options,
      network: network,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}
///SAVE ANALYTIC ACCESS
function admin2020_set_google_data(view, code, module) {
  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_analytics",
      security: admin2020_settings_ajax.security,
      view: view,
      code: code,
      module: module,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

///REMOVE ANALYTIC ACCESS
function a2020_remove_analytics(module) {
  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_remove_analytics",
      security: admin2020_settings_ajax.security,
      module: module,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

////REMOVE LICENCE
function a2020_remove_licence(network) {
  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_remove_licence",
      security: admin2020_settings_ajax.security,
      network: network,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

///saves new videos
function a2020_add_new_video() {
  jQuery(".uk-form-danger").removeClass("uk-form-danger");

  name = jQuery("#a2020_overview_settings #video_name").val();
  url = jQuery("#a2020_overview_settings #video_url").val();
  category = jQuery("#a2020_overview_settings #video_category").val();
  type = jQuery("#a2020_overview_settings #video_type").val();

  if (name == "" || url == "" || type == "") {
    jQuery("#a2020_overview_settings #video_name").addClass("uk-form-danger");
    jQuery("#a2020_overview_settings #video_url").addClass("uk-form-danger");
    jQuery("#a2020_overview_settings #video_type").addClass("uk-form-danger");
    return;
  }

  data = [name, url, category, type];

  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_videos",
      security: admin2020_settings_ajax.security,
      data: data,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          jQuery("#all_videos").html(data.content);
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

///deletes videos
function a2020_delete_video(name) {
  if (name == "") {
    return;
  }

  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_delete_video",
      security: admin2020_settings_ajax.security,
      name: name,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          jQuery("#all_videos").html(data.content);
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

////EXPORT SETTINGS
function a2020_export_settings() {
  jQuery.ajax({
    url: admin2020_settings_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_export_settings",
      security: admin2020_settings_ajax.security,
    },
    success: function (response) {
      data = response;

      var today = new Date();
      var dd = String(today.getDate()).padStart(2, "0");
      var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
      var yyyy = today.getFullYear();

      date_today = mm + "_" + dd + "_" + yyyy;
      filename = "admin2020_settings_" + date_today + ".json";

      var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(data);
      var dlAnchorElem = document.getElementById("admin2020_download_settings");
      dlAnchorElem.setAttribute("href", dataStr);
      dlAnchorElem.setAttribute("download", filename);
      dlAnchorElem.click();
    },
  });
}

function a2020_import_settings() {
  var thefile = jQuery("#admin2020_export_settings")[0].files[0];

  if (thefile.type != "application/json") {
    window.alert("Please select a valid JSON file.");
    return;
  }

  if (thefile.size > 100000) {
    window.alert("File is to big.");
    return;
  }

  var file = document.getElementById("admin2020_export_settings").files[0];
  var reader = new FileReader();
  reader.readAsText(file, "UTF-8");

  reader.onload = function (evt) {
    json_settings = evt.target.result;
    parsed = JSON.parse(json_settings);

    if (parsed != null) {
      jQuery.ajax({
        url: admin2020_settings_ajax.ajax_url,
        type: "post",
        data: {
          action: "a2020_import_settings",
          security: admin2020_settings_ajax.security,
          settings: parsed,
        },
        success: function (response) {
          message = response;
          UIkit.notification(message, "success");
          location.reload();
        },
      });
    }
  };
}
