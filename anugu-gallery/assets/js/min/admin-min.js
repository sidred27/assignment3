jQuery(document).ready(function($){$("#screen-meta-links").prependTo("#anugu-header-temp"),$("#screen-meta").prependTo("#anugu-header-temp"),"undefined"!=typeof Clipboard&&$(document).on("click",".anugu-clipboard",function(e){var n=new Clipboard(".anugu-clipboard");e.preventDefault()}),$("div.anugu-notice").on("click",".notice-dismiss",function(e){e.preventDefault(),$(this).closest("div.anugu-notice").fadeOut(),$(this).hasClass("is-dismissible")&&$.post(anugu_gallery_admin.ajax,{action:"anugu_gallery_ajax_dismiss_notice",nonce:anugu_gallery_admin.dismiss_notice_nonce,notice:$(this).parent().data("notice")},function(e){},"json")})});