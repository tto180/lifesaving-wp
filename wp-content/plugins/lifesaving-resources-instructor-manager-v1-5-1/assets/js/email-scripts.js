(function($) {
    'use strict';

    // Store file attachments
    let attachments = new Map();
    let totalAttachmentSize = 0;

    $(document).ready(function() {
        initializeRecipientCount();
        initializeAttachments();
        initializePreview();
        initializeFormSubmission();
        initializeMergeTags();
        initializeValidation();
    });

    function initializeRecipientCount() {
        const $checkboxes = $('input[name="recipients[]"]');
        const $activeOnly = $('input[name="active_only"]');
        const $countDisplay = $('#recipient-count');

        function updateCount() {
            let total = 0;
            $checkboxes.each(function() {
                if ($(this).is(':checked')) {
                    total += parseInt($(this).data('count'), 10);
                }
            });

            if (total > 0) {
                $countDisplay.html(`Selected Recipients: <strong>${total}</strong>`);
                if (total > lsimEmailVars.maxRecipients) {
                    $countDisplay.append('<br><span class="description">Emails will be sent in batches</span>');
                }
            } else {
                $countDisplay.html('<span class="error-message">Please select at least one recipient group</span>');
            }
        }

        $checkboxes.on('change', updateCount);
        $activeOnly.on('change', updateCount);
    }

    function initializeAttachments() {
        $('#add-attachment').on('click', function(e) {
            e.preventDefault();
            const fileInput = $('<input type="file" multiple accept="' + lsimEmailVars.allowedTypes.join(',') + '">');
            fileInput.on('change', function(e) {
                handleFileSelect(e);
            });
            fileInput.trigger('click');
        });

        const dropZone = $('#attachment-list');
        dropZone.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('drag-over');
        }).on('dragleave drop', function(e) {
            e.preventDefault();
            $(this).removeClass('drag-over');
            if (e.type === 'drop') {
                handleFileSelect(e.originalEvent);
            }
        });
    }

    function handleFileSelect(e) {
        const files = e.target.files || e.dataTransfer.files;
        
        for (let file of files) {
            if (!isAllowedFileType(file)) {
                showError(`File type not allowed: ${file.name}`);
                continue;
            }

            if (totalAttachmentSize + file.size > lsimEmailVars.maxAttachmentSize) {
                showError('Total attachment size would exceed limit');
                continue;
            }

            const fileId = Date.now() + Math.random();
            attachments.set(fileId, file);
            totalAttachmentSize += file.size;
            addAttachmentToDisplay(fileId, file);
        }
    }

    function addAttachmentToDisplay(id, file) {
        const $item = $(`
            <div class="attachment-item" data-id="${id}">
                <span class="filename">${file.name}</span>
                <span class="filesize">(${formatFileSize(file.size)})</span>
                <span class="remove dashicons dashicons-trash"></span>
            </div>
        `);

        $item.find('.remove').on('click', function() {
            attachments.delete(id);
            totalAttachmentSize -= file.size;
            $item.remove();
        });

        $('#attachment-list').append($item);
    }

    function initializePreview() {
        const $modal = $('#preview-modal');
        
        $('#preview-email').on('click', function(e) {
            e.preventDefault();
            if (!validateForm()) {
                return;
            }

            const content = tinymce.get('email_content').getContent();
            const subject = $('#email_subject').val();
            const attachmentsList = Array.from(attachments.values())
                .map(file => file.name)
                .join(', ');

            $('#preview-content').html(`
                <div class="preview-email">
                    <div class="preview-subject">
                        <strong>Subject:</strong> ${subject}
                    </div>
                    <div class="preview-body">
                        ${content}
                    </div>
                    ${attachments.size ? `
                    <div class="preview-attachments">
                        <strong>Attachments:</strong><br>
                        ${attachmentsList}
                    </div>
                    ` : ''}
                </div>
            `);

            $modal.show();
        });

        $('.close, #preview-modal').on('click', function(e) {
            if (e.target === this) {
                $modal.hide();
            }
        });
    }

    function initializeFormSubmission() {
        $('#instructor-email-form').on('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }

            const $form = $(this);
            const formData = new FormData($form[0]);
            
            // Add files to FormData
            attachments.forEach((file, id) => {
                formData.append('attachments[]', file);
            });

            // Add editor content
            formData.append('content', tinymce.get('email_content').getContent());

            $.ajax({
                url: lsimEmailVars.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $form.addClass('loading');
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.data.message);
                        resetForm();
                    } else {
                        showError(response.data.message || 'Error sending emails');
                    }
                },
                error: function() {
                    showError('An error occurred while sending emails');
                },
                complete: function() {
                    $form.removeClass('loading');
                }
            });
        });
    }

    function initializeMergeTags() {
        $('.merge-tags code').on('click', function() {
            const tag = $(this).text();
            if (tinymce.get('email_content')) {
                tinymce.get('email_content').insertContent(tag);
            }
        });
    }

    function initializeValidation() {
        const $ccField = $('#cc');
        
        $ccField.on('change', function() {
            const emails = $(this).val().split(',').map(e => e.trim()).filter(e => e);
            if (emails.length > lsimEmailVars.maxCC) {
                showError(`Maximum ${lsimEmailVars.maxCC} CC recipients allowed`);
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
    }

    function validateForm() {
        let isValid = true;

        // Check recipients
        if (!$('input[name="recipients[]"]:checked').length) {
            showError('Please select at least one recipient group');
            isValid = false;
        }

        // Check subject
        if (!$('#email_subject').val().trim()) {
            showError('Please enter a subject');
            isValid = false;
        }

        // Check content
        if (!tinymce.get('email_content').getContent().trim()) {
            showError('Please enter email content');
            isValid = false;
        }

        // Validate CC emails
        const ccEmails = $('#cc').val().split(',').map(e => e.trim()).filter(e => e);
        if (ccEmails.length > lsimEmailVars.maxCC) {
            showError(`Maximum ${lsimEmailVars.maxCC} CC recipients allowed`);
            isValid = false;
        }

        for (const email of ccEmails) {
            if (!isValidEmail(email)) {
                showError(`Invalid CC email: ${email}`);
                isValid = false;
            }
        }

        return isValid;
    }

    function showError(message) {
        const $error = $(`<div class="error-message">${message}</div>`);
        $('.email-form-container').prepend($error);
        setTimeout(() => $error.fadeOut(() => $error.remove()), 5000);
    }

    function showSuccess(message) {
        const $success = $(`<div class="success-message">${message}</div>`);
        $('.email-form-container').prepend($success);
        setTimeout(() => $success.fadeOut(() => $success.remove()), 5000);
    }

    function resetForm() {
        $('#instructor-email-form')[0].reset();
        tinymce.get('email_content').setContent('');
        attachments.clear();
        totalAttachmentSize = 0;
        $('#attachment-list').empty();
        $('#recipient-count').empty();
    }

    function isAllowedFileType(file) {
        return lsimEmailVars.allowedTypes.some(type => 
            file.name.toLowerCase().endsWith(type)
        );
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

})(jQuery);