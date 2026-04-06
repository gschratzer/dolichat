$(function () {
    var i = 1;
    var ul = $('#upload ul');

    $('#drop a').click(function () {
        $(this).parent().find('input').click();
    });

    $('#upload').fileupload({
        dropZone: $('#drop'),

        add: function (e, data) {
            var jqXHR = null;

            if (window.parent && typeof window.parent.validateBeforeUpload === 'function') {
                if (!window.parent.validateBeforeUpload()) {
                    return;
                }
            }

            if (window.parent && typeof window.parent.addimgstart === 'function') {
                window.parent.addimgstart();
            }

            var r = confirm('Upload:
  ' + data.files[0].name + '  
  ' + formatFileSize(data.files[0].size) + '');
            if (r !== true) {
                if (window.parent && typeof window.parent.imgload_faild === 'function') {
                    window.parent.imgload_faild('Upload cancelled.');
                }
                return;
            }

            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48" data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><div id="picload' + i + '"></div><span style="color:red;">x</span></li>');

            tpl.find('div').text(data.files[0].name).append(': ' + formatFileSize(data.files[0].size));
            data.context = tpl.appendTo(ul);
            tpl.find('input').knob();

            tpl.find('span').click(function () {
                if (jqXHR && tpl.hasClass('working')) {
                    jqXHR.abort();
                }

                tpl.fadeOut(function () {
                    tpl.remove();
                });
            });

            jqXHR = data.submit();
            $('#count').val(i);
            i++;
        },

        progress: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            data.context.find('input').val(progress).change();

            if (window.parent && typeof window.parent.imgload === 'function') {
                window.parent.imgload(progress);
            }

            if (progress === 100) {
                data.context.removeClass('working');
            }
        },

        done: function (e, data) {
            var response = data.result || {};
            if (response.status !== 'success') {
                if (window.parent && typeof window.parent.imgload_faild === 'function') {
                    window.parent.imgload_faild(response.message || 'Upload failed.');
                }
                if (data.context) {
                    data.context.addClass('error');
                }
                return;
            }

            if (window.parent && typeof window.parent.showSubmitStatus === 'function') {
                window.parent.showSubmitStatus('success', response.message || 'Upload completed.');
            }
        },

        fail: function (e, data) {
            if (window.parent && typeof window.parent.imgload_faild === 'function') {
                window.parent.imgload_faild('Upload failed.');
            }
            if (data.context) {
                data.context.addClass('error');
            }
        }
    });

    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }
        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }
        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }
        return (bytes / 1000).toFixed(2) + ' KB';
    }
});
