var canBeLoaded = true;
var bottomOffset = 1500;
var canSearch = true;

jQuery(document).ready(function ($) {
  $("#a2020_atachment_size").on("input", function () {
    height = $(this).val();
    $(".a2020_attachment").css("height", height + "px");
  });
  $("#a2020_atachment_size").on("change", function () {
    height = $(this).val();
    a2020_save_user_prefences("attachment_size", height, false);
  });
  $("#a2020_list_view").on("click", function () {
    jQuery("#a2020_media_items").toggleClass("a2020_list_view");

    if (jQuery("#a2020_media_items").hasClass("a2020_list_view")) {
      value = "list";
    } else {
      value = "grid";
    }
    a2020_save_user_prefences("content_list_view", value, false);
  });
  $("#a2020_folder_toggle").on("click", function () {
    if (jQuery("#folder_panel").prop("hidden")) {
      jQuery("#folder_panel").prop("hidden", false);
      value = "visible";
    } else {
      jQuery("#folder_panel").prop("hidden", true);
      value = "hidden";
    }
    a2020_save_user_prefences("content_folder_view", value, false);
  });
  ////SEARCH CONTENT
  $("#a2020_search_content").on("input", function () {
    term = $(this).val();

    if (canSearch == false) {
      return;
    }

    $.ajax({
      url: admin2020_admin_content_ajax.ajax_url,
      type: "post",
      data: {
        action: "a2020_search_content",
        security: admin2020_admin_content_ajax.security,
        term: term,
      },
      beforeSend: function (xhr) {
        canSearch = false;
      },
      success: function (response) {
        if (response) {
          admin2020_admin_content_ajax.current_page = 1;
          canSearch = true;
          jQuery("#a2020_media_items").html(response);
          options = [];
          options.target = "#a2020_media_items";
          UIkit.filter("#a2020_content_filter", options);
        }
      },
    });
  });

  $(window).scroll(function () {
    if ($(document).scrollTop() > $(document).height() - bottomOffset && canBeLoaded == true) {
      a2020_load_more();
    }
  });
});

function a2020_load_more() {
  if (!admin2020_admin_content_ajax.current_page) {
    admin2020_admin_content_ajax.current_page = 1;
  }

  term = jQuery("#a2020_search_content").val();

  if (jQuery("#admin2020folderswrap .folder_title.uk-active").length > 0) {
    folder_id = jQuery("#admin2020folderswrap .folder_title.uk-active").attr("folder-id");
  }

  if (jQuery(".admin2020allFolders a.uk-active").length > 0) {
    folder_id = jQuery(".admin2020allFolders a.uk-active").attr("folder-id");
  }

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_fetch_more_media",
      security: admin2020_admin_content_ajax.security,
      page: admin2020_admin_content_ajax.current_page,
      term: term,
      folder_id: folder_id,
    },
    beforeSend: function (xhr) {
      canBeLoaded = false;
      jQuery("#admincontentloader").show();
    },
    success: function (response) {
      if (response) {
        canBeLoaded = true;
        admin2020_admin_content_ajax.current_page++;
        jQuery("#a2020_media_items").append(response);
        options = [];
        options.target = "#a2020_media_items";
        UIkit.filter("#a2020_content_filter", options);
      } else {
        canBeLoaded = false;
      }
      jQuery("#admincontentloader").hide();
    },
  });
}

function a2020_select_media_item(item, press) {
  if (press.shiftKey) {
    this_item_index = jQuery(item).parent().index();
    last_item_index = jQuery(".a2020_selected").last().parent().index();
    attachments = jQuery("#a2020_media_items").children();
    ////selecting forward
    if (this_item_index > last_item_index) {
      for (i = last_item_index; i < this_item_index + 1; ++i) {
        if (jQuery(attachments[i]).is(":visible")) {
          jQuery(attachments[i]).find(".a2020_attachment").addClass("a2020_selected");
        }
      }
    }

    ////selecting backward
    if (this_item_index < last_item_index) {
      for (i = this_item_index; i < last_item_index + 1; ++i) {
        if (jQuery(attachments[i]).is(":visible")) {
          jQuery(attachments[i]).find(".a2020_attachment").addClass("a2020_selected");
        }
      }
    }
  } else {
    jQuery(item).toggleClass("a2020_selected");
  }

  if (jQuery(".a2020_selected").length > 0) {
    jQuery(".a2020_bulk_actions").show();
  } else {
    jQuery(".a2020_bulk_actions").hide();
  }
}

function a2020_fetch_attachment_modal(attachmentid) {
  jQuery(this).removeClass("a2020_selected");
  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_fetch_attachment_modal",
      security: admin2020_admin_content_ajax.security,
      attachmentid: attachmentid,
    },
    success: function (response) {
      if (response) {
        jQuery("#admin2020MediaViewer_content").html(response);
        UIkit.modal("#admin2020MediaViewer").show();

        ///BUILD WP EDITOR
        var settings = {
          tinymce: {
            toolbar1: "bold,italic,bullist,link",
          },
          quicktags: {
            buttons: "strong,em,link,ul,li,code",
          },
        };
        wp.editor.remove("post_preview_editor");
        wp.editor.initialize("post_preview_editor", settings);
      }
    },
  });
}

///SET FEATURED IMAGE
function admin2020_set_featured_image() {
  var mediaUploader;
  var mediaUploaderDark;
  var mediaUploaderBackground;

  if (mediaUploaderBackground) {
    mediaUploaderBackground.open();
    return;
  }
  mediaUploaderBackground = wp.media.frames.file_frame = wp.media({
    title: "Choose Image",
    button: {
      text: "Choose Image",
    },
    multiple: false,
  });
  mediaUploaderBackground.on("select", function () {
    var attachment = mediaUploaderBackground.state().get("selection").first().toJSON();
    jQuery("#admin2020_post_image_select #admin2020_post_image").attr("data-src", attachment.url);
    jQuery("#admin2020_post_image_select #admin2020_post_image").attr("data-id", attachment.id);
  });
  mediaUploaderBackground.open();
}
///STARTS SAVE PROCESS
function admin2020_save_post() {
  UIkit.switcher("#admin2020_post_switcher").show(0);
  setTimeout(admin2020_save_post_send, 1);
}
//SAVES POST
function admin2020_save_post_send() {
  categories = [];

  jQuery(".admin2020_categories input:checkbox:checked").each(function (index, element) {
    categories.push(jQuery(element).val());
  });

  title = jQuery("#admin2020_viewer_title").val();
  content = wp.editor.getContent("post_preview_editor");
  postid = jQuery("#admin2020_viewer_currentid").text();
  status = jQuery("#admin2020_post_status").val();

  image = jQuery("#admin2020_post_image_select #admin2020_post_image").attr("data-id");

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_post",
      security: admin2020_admin_content_ajax.security,
      title: title,
      content: content,
      postid: postid,
      categories: categories,
      status: status,
      image: image,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);

        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          jQuery("#a2020_media_items [attachment_id='" + postid + "']").replaceWith(data.html);
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}
///DELETES STUFF
function a2020_delete_item(post_id) {
  if (post_id == "") {
    return;
  }
  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_delete_item",
      security: admin2020_admin_content_ajax.security,
      post_id: post_id,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);

        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          UIkit.notification(data.message, "success");
          jQuery("#a2020_media_items [attachment_id='" + post_id + "']").remove();
          UIkit.modal("#admin2020MediaViewer").hide();
        }
      }
    },
  });
}
/////SAVE ATTACHMENT
function a2020_save_attachment(imgid) {
  title = jQuery("#admin2020_viewer_input_title").val();
  imgalt = jQuery("#admin2020_viewer_altText").val();
  caption = jQuery("#admin2020_viewer_caption").val();
  description = jQuery("#admin2020_viewer_description").val();

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_save_attachment",
      security: admin2020_admin_content_ajax.security,
      title: title,
      imgalt: imgalt,
      caption: caption,
      description: description,
      imgid: imgid,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);

        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          jQuery("#a2020_media_items [attachment_id='" + imgid + "']").replaceWith(data.html);
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

function switchinfo(direction, id) {
  current = jQuery("#a2020_media_items [attachment_id='" + id + "']");

  if (direction == "right") {
    if (jQuery(current).next().hasClass("attachment_wrap")) {
      next_id = jQuery(current).next().attr("attachment_id");
    } else {
      next_id = jQuery(current).next().next().attr("attachment_id");
    }
  }

  if (direction == "left") {
    if (jQuery(current).prev().hasClass("attachment_wrap")) {
      next_id = jQuery(current).prev().attr("attachment_id");
    } else {
      next_id = jQuery(current).prev().prev().attr("attachment_id");
    }
  }
  if (!next_id) {
    return;
  }

  a2020_fetch_attachment_modal(next_id);
}

/////DUPLICATE ATTACHMENT
function a2020_duplicate_post(postid) {
  if (!postid || postid == "") {
    return;
  }

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_duplicate_post",
      security: admin2020_admin_content_ajax.security,
      postid: postid,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          jQuery("#a2020_media_items .attachment_wrap").first().before(data.html);
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

function a2020_delete_multiple() {
  var theids = [];

  jQuery(".a2020_selected").each(function () {
    theids.push(jQuery(this).parent().attr("attachment_id"));
  });

  if (theids.length < 1) {
    return;
  }

  UIkit.modal.confirm("Delete " + theids.length + " items?").then(
    function () {
      jQuery.ajax({
        url: admin2020_admin_content_ajax.ajax_url,
        type: "post",
        data: {
          action: "a2020_delete_item",
          security: admin2020_admin_content_ajax.security,
          post_id: theids,
        },
        success: function (response) {
          if (response) {
            data = JSON.parse(response);

            if (data.error) {
              UIkit.notification(data.error_message, "danger");
            } else {
              UIkit.notification(data.message, "success");

              jQuery.each(theids, function (index, value) {
                jQuery("#a2020_media_items [attachment_id='" + value + "']").remove();
              });
              jQuery(".a2020_bulk_actions").hide();
            }
          }
        },
      });
    },
    function () {
      ///CANCELLED
    }
  );
}

///adds batch rename items
function add_batch_rename_item() {
  itemtoadd = jQuery("#batch_name_chooser").val();

  if (itemtoadd == "") {
    return;
  }

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_add_batch_rename_item",
      security: admin2020_admin_content_ajax.security,
      itemtoadd: itemtoadd,
    },
    success: function (response) {
      if (response) {
        jQuery("#batch_rename_builder").append(response);
        build_batch_rename_preview();
      }
    },
  });
}

///previews batch rename
function build_batch_rename_preview() {
  name_preview = "";

  jQuery("#batch_rename_builder .rename_item").each(function () {
    type = jQuery(this).find(".batch_rename_option").attr("name");

    if (type == "date" || type == "text") {
      value = jQuery(this).find("input").val();
    } else {
      value = "{" + type + "}";
    }

    name_preview = name_preview + value;
  });

  jQuery("#batch_rename_preview").text(name_preview);
}
///processes batch rename
function batch_rename_process() {
  if (jQuery("#a2020_media_items .a2020_selected").length < 1) {
    UIkit.notification("No Items Selected", "warning");
    return;
  }

  if (jQuery("#batch_rename_builder .rename_item").length < 1) {
    UIkit.notification("You must add at least one naming item", "warning");
    return;
  }

  theids = [];
  jQuery("#a2020_media_items .a2020_selected").each(function (index, element) {
    theid = jQuery(element).parent().attr("attachment_id");
    theids.push(theid);
  });

  var temp_type = [];
  var temp_value = [];

  jQuery("#batch_rename_builder .rename_item").each(function () {
    type = jQuery(this).find(".batch_rename_option").attr("name");

    if (type == "date" || type == "text" || type == "sequence" || type == "meta") {
      value = jQuery(this).find("input").val();
    } else {
      value = "";
    }

    temp_type.push(type);
    temp_value.push(value);
  });

  item_to_rename = jQuery("#form-stacked-select").val();

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "a2020_process_batch_rename",
      security: admin2020_admin_content_ajax.security,
      structure: temp_type,
      values: temp_value,
      ids: theids,
      item_to_rename: item_to_rename,
    },
    success: function (response) {
      if (response) {
        data = JSON.parse(response);
        if (data.error) {
          UIkit.notification(data.error, "danger");
        } else {
          UIkit.notification(data.message, "success");
        }
      }
    },
  });
}

/////////////////
/////UPLOAD//////
/////////////////
jQuery(function () {
  jQuery.fn.filepond.registerPlugin(FilePondPluginFileEncode);
  jQuery.fn.filepond.registerPlugin(FilePondPluginFileValidateSize);
  jQuery.fn.filepond.registerPlugin(FilePondPluginImageExifOrientation);
  jQuery.fn.filepond.registerPlugin(FilePondPluginFileValidateType);

  jQuery.fn.filepond.setDefaults({
    acceptedFileTypes: JSON.parse(admin2020_admin_content_ajax.a2020_allowed_types),
    allowRevert: false,
  });
});
jQuery(document).ready(function ($) {
  $("#a2020_file_upload").filepond();

  FilePond.setOptions({
    server: {
      url: admin2020_admin_content_ajax.ajax_url,
      type: "post",
      process: {
        url: "?action=a2020_process_upload&security=" + admin2020_admin_content_ajax.security,
        method: "POST",
        onload: (res) => {
          // select the right value in the response here and return
          if (res) {
            data = JSON.parse(res);

            if (data.error) {
              return res;
            }

            html = data.html;
            jQuery("#a2020_media_items .attachment_wrap").first().before(html);
            options = [];
            options.target = "#a2020_media_items";
            UIkit.filter("#a2020_content_filter", options);
          }
          return res;
        },
      },
    },
  });
});
////
//// REQUERY FROM FOLDER  CLICK
function admin2020_set_content_folder_query(folder_id) {
  term = jQuery("#a2020_search_content").val();

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: {
      action: "admin2020_set_content_folder_query",
      security: admin2020_admin_content_ajax.security,
      term: term,
      folder_id: folder_id,
    },
    beforeSend: function (xhr) {
      canBeLoaded = false;
    },
    success: function (response) {
      if (response) {
        canBeLoaded = true;
        admin2020_admin_content_ajax.current_page = 1;
        jQuery("#a2020_media_items").html(response);
        options = [];
        options.target = "#a2020_media_items";
        UIkit.filter("#a2020_content_filter", options);
      } else {
        canBeLoaded = false;
      }
    },
  });
}

function a2020_edit_image(imagelink, imageTitle) {
  imageEditor = new tui.ImageEditor("#admin2020_image_edit_area", {
    includeUI: {
      loadImage: {
        path: imagelink,
        name: "Blank",
      },
      theme: blackTheme, // or whiteTheme
      initMenu: "filter",
      menuBarPosition: "right",
    },
    cssMaxWidth: 700,
    cssMaxHeight: 500,
    usageStatistics: false,
  });

  //imageEditor.loadImageFromURL(imagelink, imageTitle);
  window.onresize = function () {
    imageEditor.ui.resizeEditor();
  };

  jQuery(".tui-image-editor-header-buttons .tui-image-editor-download-btn").replaceWith(
    '<span class="uk-padding-small" style="float:left"><a onclick="admin2020_save_edited_as_copy(this);" href="#" class="uk-button uk-button-primary uk-margin-right" >Save as copy</a><a href="#" onclick="admin2020_save_edited(this);" class="uk-button uk-button-primary" >Save</a></span>'
  );
  jQuery(".tui-image-editor-header-logo").hide();
  // $('.tui-image-editor-menu').hide();

  // $('.tui-image-editor-header-buttons div:first').hide();
  var loadBtn = jQuery(".tui-image-editor-header-buttons div:first");
  loadBtn.hide();

  jQuery(".admin2020_image_edit_wrap").show();
}

function admin2020_save_edited(item) {
  jQuery(item).html('Saving <div class="image_save_spinner" uk-spinner></div>');

  imageurl = jQuery("#admin2020_viewer_fullLink").val();
  parts = imageurl.split("/");
  filename = parts[parts.length - 1];
  var imageid = jQuery("#admin2020_viewer_currentid").text();

  img = imageEditor.toDataURL();
  blob = dataURLtoBlob(img);

  fd = new FormData();
  fd.append("ammended_image", blob, filename);
  fd.append("attachmentid", imageid);
  fd.append("security", admin2020_admin_content_ajax.security);
  fd.append("action", "a2020_upload_edited_image");

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: fd,
    async: true,
    cache: false,
    contentType: false,
    processData: false,
    success: function (response) {
      if (response) {
        data = JSON.parse(response);

        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          jQuery("#a2020_media_items [attachment_id='" + imageid + "']").replaceWith(data.html);
          jQuery(".admin2020_image_edit_wrap").hide();
          UIkit.modal("#admin2020MediaViewer").hide();
          UIkit.notification(data.message, "success");
        }
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function admin2020_save_edited_as_copy(item) {
  jQuery(item).html('Saving <div class="image_save_spinner" uk-spinner></div>');

  imageurl = jQuery("#admin2020_viewer_fullLink").val();
  parts = imageurl.split("/");
  filename = parts[parts.length - 1];
  var imageid = jQuery("#admin2020_viewer_currentid").text();

  img = imageEditor.toDataURL();
  blob = dataURLtoBlob(img);

  fd = new FormData();
  fd.append("ammended_image", blob, filename);
  fd.append("attachmentid", imageid);
  fd.append("file_name", filename);
  fd.append("security", admin2020_admin_content_ajax.security);
  fd.append("action", "a2020_upload_edited_image_as_copy");

  jQuery.ajax({
    url: admin2020_admin_content_ajax.ajax_url,
    type: "post",
    data: fd,
    async: true,
    cache: false,
    contentType: false,
    processData: false,
    success: function (response) {
      if (response) {
        data = JSON.parse(response);

        if (data.error) {
          UIkit.notification(data.error_message, "danger");
        } else {
          html = data.html;
          jQuery("#a2020_media_items .attachment_wrap").first().before(html);
          jQuery(".admin2020_image_edit_wrap").hide();
          UIkit.modal("#admin2020MediaViewer").hide();
          UIkit.notification(data.message, "success");
        }
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function dataURLtoBlob(dataurl) {
  var arr = dataurl.split(","),
    mime = arr[0].match(/:(.*?);/)[1],
    bstr = atob(arr[1]),
    n = bstr.length,
    u8arr = new Uint8Array(n);
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n);
  }
  return new Blob([u8arr], {
    type: mime,
  });
}
