var cansearch;

jQuery(document).ready(function ($) {
  ///DELAY NOTIFICATION FILTER
  setTimeout(admin2020_move_notifications, 10);
  $("#maAdminSwitchDarkMode").on("click", function () {
    darkmode = $("#maAdminSwitchDarkMode").is(":checked");
    a2020_save_user_prefences("darkmode", darkmode);
    $(".a2020-admin-bar").toggleClass("a2020_night_mode");
    $("body").toggleClass("a2020_night_mode");
    $(".a2020_dark_anchor").toggleClass("uk-light");
  });
  ///USER SCREENOPTION TOGGLE
  $("#showscreenoptions").on("click", function () {
    value = $("#showscreenoptions").is(":checked");
    a2020_save_user_prefences("screen_options", value);
  });
  ///LEGACY LINKS TOGGLE
  $("#hiddelegacylinks").on("click", function () {
    value = $("#hiddelegacylinks").is(":checked");
    a2020_save_user_prefences("legacy_admin_links", value);
  });
  ///////MASTER SEARCH
  $("#a2020_master_search").on("input", function () {
    a2020_master_search($("#a2020_master_search").val());
  });
});
function a2020_master_search(searchTerm) {
  if (cansearch == false) {
    return;
  }

  if (searchTerm == "") {
    return;
  }

  var postTypes = [];
  jQuery("#admin2020_search_post_types input").each(function () {
    if (jQuery(this).is(":checked")) {
      postTypes.push(jQuery(this).val());
    }
  });

  var categories = [];
  jQuery("#admin2020_search_categories input").each(function () {
    if (jQuery(this).is(":checked")) {
      categories.push(jQuery(this).val());
    }
  });

  var users = [];
  jQuery("#admin2020_search_users input").each(function () {
    if (jQuery(this).is(":checked")) {
      users.push(jQuery(this).val());
    }
  });

  jQuery.ajax({
    url: admin2020_admin_bar_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_master_search",
      security: admin2020_admin_bar_ajax.security,
      search: searchTerm,
      posttypes: postTypes,
      categories: categories,
      users: users,
    },
    beforeSend: function (xhr) {
      cansearch = false;
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          //UIkit.notification(data.message, "success");
          jQuery("#a2020_master_search_results").html(data.html);
          cansearch = true;
        }
      }
    },
  });
}
////FILTERS AND MOVES WP NOTIFICATIONS
function admin2020_move_notifications() {
  var style_array = [".error", ".is-dismissible", ".notice", ".update-nag", ".updated"];

  for (i = 0; i < style_array.length; i++) {
    jQuery("#wpbody-content " + style_array[i]).each(function () {
      if (jQuery(this).length > 0) {
        if (jQuery(this).attr("id") == "activationpanel") {
          return;
        }
        if (jQuery(this).parent().attr("id") == "wpbody-content" || jQuery(this).parent().hasClass("wrap")) {
          if (jQuery(this).hasClass("notice") && jQuery(this).attr("id") != "yoast-indexation-warning") {
            console.log(jQuery(this));
            if (jQuery(this).hasClass("notice-success") || jQuery(this).hasClass("updated")) {
              content = jQuery(this).text();

              if (content.length > 100) {
                jQuery(this).prependTo("#admin2020_notification_center");
              } else {
                jQuery(this).prependTo("#admin2020_notification_center");
                UIkit.notification(content, "success");
              }
            } else {
              jQuery(this).prependTo("#admin2020_notification_center");
            }
          } else {
            jQuery(this).prependTo("#admin2020_notification_center");
          }
        }
      }
    });
  }

  count = jQuery("#admin2020_notification_center").children().length;
  total = Number(jQuery(".admin2020notificationBadge").text()) + count;

  if (total > 0) {
    jQuery(".admin2020notificationBadge").text(Number(jQuery(".admin2020notificationBadge").text()) + count);
    jQuery(".admin2020notificationBadge").show();
  }
}
