jQuery(document).ready(function ($) {
  ////TOGGLE ADMIN MENU
  $("#a2020_menu_toggle").on("click", function () {
    $("body").toggleClass("a2020_menu_small");
    if ($("body").hasClass("a2020_menu_small")) {
      value = "true";
    } else {
      value = "false";
    }
    a2020_save_user_prefences("menu_collapse", value, false);
  });

  $("#a2020_menu_mobile_toggle").on("click", function () {
    if ($("#adminmenumain").is(":visible")) {
      $("#adminmenumain").hide();
    } else {
      $("#adminmenumain").show();
    }
  });
  $("#close_mobile_nav").on("click", function () {
    $("#adminmenumain").hide();
  });

  $("#a2020_menu_searcher").on("input", function () {
    searchstring = $(this).val().toLowerCase();
    if (searchstring == "") {
      $(".admin2020_menu ul li").show();
      $(".admin2020_menu ul li .uk-nav-sub li").show();
      $(".admin2020_menu .uk-nav-sub").attr("hidden", true);
      $(".admin2020_menu .uk-open .uk-nav-sub").attr("hidden", false);
      return;
    }
    $(".admin2020_menu ul li").each(function () {
      if (!$(this).hasClass("a2020_menu_searcher_wrap")) {
        text = $(this).text().toLowerCase();
        subtext = $(this).find(".uk-nav-sub").text().toLowerCase();

        if (text.includes(searchstring) || subtext.includes(searchstring)) {
          $(this).show();

          $(this)
            .find(" .uk-nav-sub li")
            .each(function () {
              sub_item_text = $(this).text().toLowerCase();

              if (sub_item_text.includes(searchstring)) {
                $(this).show();
                //$(this).closest(".uk-parent").addClass("uk-open");
                $(this).closest(".uk-nav-sub").attr("hidden", false);
              } else {
                $(this).hide();
              }
            });
        } else {
          $(this).hide();
          $(this).find(".uk-nav-sub").attr("hidden", true);
        }
      }
    });
  });
});
