$(document).on("click", ".play-button", function () {
     var record_uuid = $(this).data();
     $(".modal-body #recId").val( record_uuid );

     var data = {
          recId: record_uuid
        };

    $.post("videos.php", data);
});
