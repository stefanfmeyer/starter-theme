function open_icon_chooser(item) {
  UIkit.modal(jQuery("#icon-list")).show();

  ///ADD A ONE TIME RUN FUNCTION ON SELECT
  jQuery("#icon_selected").one("click", function () {
    icon = jQuery("#admin2020_icon_select .icon_selected span").attr("uk-icon");

    if (icon == "noicon") {
      icon = "";
    }

    jQuery(item).next().attr("uk-icon", icon);

    jQuery(item).parent().find(".a2020_icon_holder").attr("value", icon);

    UIkit.modal(jQuery("#icon-list")).hide();
  });
}

jQuery(document).ready(function ($) {
  $("#admin2020_icon_select li").on("click", function () {
    $("#admin2020_icon_select .icon_selected").removeClass("icon_selected");
    $(this).addClass("icon_selected");
  });
});

function a2020_save_menu_editor() {
  var menuSettings = {};
  jQuery(".a2020_menu_item").each(function (index, element) {
    ///TOP LEVEL ITEMS
    var menu_name = jQuery(element).attr("name");
    menu_object = menuSettings[menu_name] = {};
    menuSettings[menu_name]["order"] = jQuery(element).index();
    jQuery(element)
      .find(".a2020_top_level_settings .menu_setting")
      .each(function (index, item) {
        setting_name = jQuery(item).attr("name");
        value = jQuery(item).val();

        menu_object[setting_name] = value;
      });

    ///SUBLEVELS
    if (jQuery(element).find(".a2020_sub_menu_item").length > 0) {
      submenu_object = menu_object["submenu"] = {};
      jQuery(element)
        .find(".a2020_sub_menu_item")
        .each(function (index, subitem) {
          var sub_menu_name = jQuery(subitem).attr("name");
          submenu_item = submenu_object[sub_menu_name] = {};
          submenu_item["order"] = jQuery(subitem).index();

          jQuery(subitem)
            .find(".menu_setting")
            .each(function (index, subsubitem) {
              sub_setting_name = jQuery(subsubitem).attr("name");
              sub_value = jQuery(subsubitem).val();
              submenu_item[sub_setting_name] = sub_value;
            });
        });
    }
  });

  jQuery.ajax({
    url: admin2020_admin_menu_editor_ajax.ajax_url,
    type: "post",
    dataType: "json",
    data: {
      action: "a2020_save_menu_settings",
      security: admin2020_admin_menu_editor_ajax.security,
      options: menuSettings,
    },
    success: function (response) {
      if (response) {
        data = response;
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

function a2020_delete_menu_settings() {
  jQuery.ajax({
    url: admin2020_admin_menu_editor_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_delete_menu_settings",
      security: admin2020_admin_menu_editor_ajax.security,
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

////EXPORT SETTINGS
function a2020_export_menu() {
  jQuery.ajax({
    url: admin2020_admin_menu_editor_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_export_menu",
      security: admin2020_admin_menu_editor_ajax.security,
    },
    success: function (response) {
      data = response;

      var today = new Date();
      var dd = String(today.getDate()).padStart(2, "0");
      var mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
      var yyyy = today.getFullYear();

      date_today = mm + "_" + dd + "_" + yyyy;
      filename = "a2020_menu_settings_" + date_today + ".json";

      var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(data);
      var dlAnchorElem = document.getElementById("admin2020_download_settings");
      dlAnchorElem.setAttribute("href", dataStr);
      dlAnchorElem.setAttribute("download", filename);
      dlAnchorElem.click();
    },
  });
}

//////IMPORT MENU SETTINGS
function a2020_import_menu() {
  var thefile = jQuery("#admin2020_export_menu")[0].files[0];

  if (thefile.type != "application/json") {
    window.alert("Please select a valid JSON file.");
    return;
  }

  if (thefile.size > 100000) {
    window.alert("File is to big.");
    return;
  }

  var file = document.getElementById("admin2020_export_menu").files[0];
  var reader = new FileReader();
  reader.readAsText(file, "UTF-8");

  reader.onload = function (evt) {
    json_settings = evt.target.result;
    parsed = JSON.parse(json_settings);

    if (parsed != null) {
      jQuery.ajax({
        url: admin2020_admin_menu_editor_ajax.ajax_url,
        type: "post",
        data: {
          action: "a2020_import_menu",
          security: admin2020_admin_menu_editor_ajax.security,
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
