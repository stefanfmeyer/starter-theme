jQuery(document).ready(function ($) {
  $("#adminoverviewloader").hide();
  ///GET DASHCARD ORDER
  $(document).on("moved", "#a2020_overview_cards", function (e) {
    function_array = [];
    $("#a2020_overview_cards")
      .children()
      .each(function (index, element) {
        var function_name = $(element).attr("id");
        if (!function_array.includes(function_name)) {
          function_array.push(function_name);
        }
      });

    a2020_save_user_prefences("dash_order", function_array, "false");
  });

  ///start overview date range
  $("#admin2020-date-range").daterangepicker(
    {
      autoApply: true,
      maxSpan: {
        days: 60,
      },
      locale: {
        format: "DD/MM/YYYY",
      },
      ranges: {
        Today: [moment(), moment()],
        Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
      },
      alwaysShowCalendars: true,
      startDate: moment().subtract(7, "day"),
      endDate: moment(),
      opens: "left",
    },
    function (start, end, label) {
      admin2020_refresh_view();
    }
  );
});

////refreshes overview page
function admin2020_refresh_view() {
  startdate = jQuery("#admin2020-date-range").data("daterangepicker").startDate.format("YYYY-MM-DD");
  enddate = jQuery("#admin2020-date-range").data("daterangepicker").endDate.format("YYYY-MM-DD");
  jQuery("#adminoverviewloader").show();

  jQuery.ajax({
    url: admin2020_admin_overview_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_refresh_overview",
      security: admin2020_admin_overview_ajax.security,
      startdate: startdate,
      enddate: enddate,
    },
    success: function (response) {
      if (response) {
        jQuery("#a2020_overview_cards").html(response);
        options = [];
        options.target = "#a2020_overview_cards;";
        UIkit.filter(".a2020_filter_wrap", options);
        jQuery("#adminoverviewloader").hide();
      }
    },
  });
}

////SAVE CARD VISIBILITY
function admin2020_save_visibility() {
  thecards = [];
  jQuery("#admin2020-visible-cards input:checkbox:not(:checked)").each(function () {
    var funcname = jQuery(this).attr("name");
    thecards.push(funcname);
  });

  a2020_save_user_prefences("dash_visibility", thecards);
}

////SET CARD PRIMARY
function a2020_make_primary_card(functionname) {
  jQuery("#" + functionname)
    .find(".uk-card")
    .toggleClass("uk-card-primary");

  jQuery.ajax({
    url: admin2020_admin_overview_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_make_primary_card",
      security: admin2020_admin_overview_ajax.security,
      cardname: functionname,
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
////SET CARD PRIMARY
function a2020_hide_single_card(functionname) {
  jQuery("#" + functionname)
    .find(".uk-card")
    .fadeOut(300);

  jQuery.ajax({
    url: admin2020_admin_overview_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_hide_single_card",
      security: admin2020_admin_overview_ajax.security,
      cardname: functionname,
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

///CREATE CHART
function a2020_new_chart(target, type, labels, chart_data) {
  senddata = [];
  var ctx = document.getElementById(target).getContext("2d");

  if (chart_data.gradient == "true") {
    var gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, chart_data.gradient_start);
    gradient.addColorStop(0.7, chart_data.gradient_end);
    background = gradient;
  } else {
    background = chart_data.backgroundColor;
  }
  var temp = {
    label: chart_data.label,
    data: chart_data.data,
    backgroundColor: background,
    borderColor: chart_data.borderColor,
    pointBorderWidth: 1,
    borderWidth: 2,
    pointBackgroundColor: chart_data.pointBorderColor,
    pointBorderColor: chart_data.pointBackgroundColor,
    clip: 100,
    lineTension: 0.3,
    spanGaps: true,
    pointRadius: 3,
    pointHoverRadius: 3,
  };

  senddata.push(temp);

  if (chart_data.label == "Devices") {
    the_labels = true;
  } else {
    the_labels = false;
  }

  var myChart = new Chart(ctx, {
    type: type,
    data: {
      labels: labels,
      datasets: senddata,
    },
    options: {
      cornerRadius: 5,
      cutoutPercentage: 80,
      elements: {
        arc: {
          borderWidth: 12,
        },
      },

      legend: {
        display: the_labels,
        position: "right",
        labels: {
          align: "start",
          boxWidth: 5,
          fontColor: "#999",
          usePointStyle: true,
          padding: 10,
          fontSize: 14,
        },
      },
      plugins: {
        datalabels: {
          display: false,
          backgroundColor: ["#8300ad"],
        },
      },
      maintainAspectRatio: true,
      scales: {
        yAxes: [
          {
            stacked: true,
            ticks: {
              display: false,
              padding: 20,
              fontColor: "#999",
              autoSkip: true,
              maxTicksLimit: 5,
              beginAtZero: true,
            },
            gridLines: {
              display: false,
              drawBorder: false,
              tickMarkLength: 0,
            },
          },
        ],
        xAxes: [
          {
            stacked: true,
            gridLines: {
              display: false,
              drawBorder: false,
            },
            ticks: {
              display: false,
              padding: 0,
              fontColor: "#999",
              beginAtZero: true,
            },
          },
        ],
      },
    },
  });
}
