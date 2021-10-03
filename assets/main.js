$('#file').change(function () {
    $('.error-noti').hide();
    if (this.files.length > 10) {
        $('.error-noti').html('You can only choose 10 files once!');
        $('.error-noti').show();
        $(this).val('');
    }
    for (var i = 0; i < $(this).get(0).files.length; ++i) {
        var name = $(this).get(0).files[i].name;
        var ext = name.split('.').pop();
        if (ext != 'xmp' && ext != 'lrtemplate') {
            $('.error-noti').html('Only .xmp and .lrtemplate file is valid!');
            $('.error-noti').show();
            $(this).val('');
        }
    }
});