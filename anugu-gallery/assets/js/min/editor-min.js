jQuery(document).ready(function($){$(document).on("click","a.anugu-gallery-choose-gallery, a.anugu-albums-choose-album, .anugu-gallery-modal-trigger",function(e){e.preventDefault();var a=$(this).data("action");AnuguGalleryModalWindow.content(new AnuguGallerySelectionView({action:a,multiple:!0,modal_title:anugu_gallery_editor.modal_title,insert_button_label:anugu_gallery_editor.insert_button_label})),AnuguGalleryModalWindow.open()})});