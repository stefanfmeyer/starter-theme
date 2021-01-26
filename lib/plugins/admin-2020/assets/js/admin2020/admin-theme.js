jQuery(document).ready(function ($) {
  $(".check-column input").on("change", function () {
    var thecount = $(".check-column input:checked").length;

    if (thecount > 0) {
      $(".tablenav.top .actions.bulkactions").addClass("a2020_flex");
    } else {
      $(".tablenav.top .actions.bulkactions").removeClass("a2020_flex");
    }
  });
});
