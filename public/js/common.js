
$(document).ready(function () {
    $('body').on('click', '#copy_link_data', function () {
        let $tmp = $("<textarea>");
        $("body").append($tmp);
        const copy_text = $(this).parent().parent().find('#link_data').text();
        $tmp.val(copy_text).select();
        document.execCommand("copy");
        $tmp.remove();

        return false;
    });
});
