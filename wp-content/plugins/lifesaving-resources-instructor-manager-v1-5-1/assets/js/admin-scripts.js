console.log('Starting LSIM script load');

jQuery(document).ready(function($) {
    console.log('LSIM: Scripts loaded');
    
    window.LSIM = {};
    
    const courseManager = initializeCourseManagement();
    initializeCertificationHandling();
    initializeFormValidation();
    initializeExportHandling();
    initializeSubmissionHandling();

    window.LSIM.loadCourseData = courseManager.loadCourseData;
    window.LSIM.deleteCourse = courseManager.deleteCourse;
    window.LSIM.saveCourse = courseManager.saveCourse;

    function initializeCertificationHandling() {
        $('.add-recert-date').on('click', function() {
            const type = $(this).data('type');
            const template = `
                <div class="recert-date">
                    <input type="date" name="${type}_recert_dates[]">
                    <button type="button" class="button-link remove-date" title="Remove date">&times;</button>
                </div>
            `;
            $(`#${type}-recert-dates`).append(template);
        });

        $(document).on('click', '.remove-date', function() {
            $(this).closest('.recert-date').remove();
        });

        $('.save-certification').on('click', function() {
            const type = $(this).data('type');
            const $section = $(this).closest('.certification-section');
            const $spinner = $section.find('.spinner');
            
            $spinner.addClass('is-active');
            
            $.ajax({
                url: lsimVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_certification',
                    nonce: lsimVars.nonce,
                    cert_type: type,
                    post_id: $('#post_ID').val(),
                    original_date: $section.find(`input[name="${type}_original_date"]`).val(),
                    recert_dates: $section.find(`input[name="${type}_recert_dates[]"]`).map(function() {
                        return $(this).val();
                    }).get()
                },
                success: function(response) {
                    if (response.success) {
                        $section.removeClass('active expired inactive')
                            .addClass(response.data.status);
                        
                        if (response.data.expiration) {
                            $section.find('.expiration-info').text('Expires: ' + response.data.expiration);
                        }
                    } else {
                        alert(response.data.message || 'Error saving certification');
                    }
                },
                error: function() {
                    alert('Error saving certification');
                },
                complete: function() {
                    $spinner.removeClass('is-active');
                }
            });
        });
    }

   function initializeCourseManagement() {
    console.log('LSIM: Initializing course management...');
    
    // Debug field existence - put it here
    console.log('Form fields found:', {
        course_date: $('#course_date').length,
        course_type: $('#course_type').length,
        course_location: $('#course_location').length,
        location: $('#location').length  // Also check for this ID
    });

    function validateCourseForm() {
        const required = ['course_date', 'course_type', 'course_location'];
        let isValid = true;
        let firstError = null;

			required.forEach(field => {
				const $field = $(`#${field}`);
				console.log(`Checking field ${field}:`, { 
					exists: $field.length > 0, 
					value: $field.val() 
				}); // Add debug logging
				
				const $error = $field.next('.error-message');
				
				if (!$field.val() || !$field.val().trim()) {
					isValid = false;
					$field.addClass('error');
					if (!$error.length) {
						$field.after('<span class="error-message">This field is required</span>');
					}
					if (!firstError) firstError = $field;
				} else {
					$field.removeClass('error');
					if ($error.length) $error.remove();
				}
			});

			if (firstError) {
				firstError.focus();
			}
			
			console.log('Form validation result:', isValid); // Add debug logging
			return isValid;
		}

		function collectCourseData() {
			console.log('LSIM: Collecting course data...');
			
			const participants_data = {
				awareness: parseInt($('#count_awareness').val()) || 0,
				operations: parseInt($('#count_operations').val()) || 0,
				technician: parseInt($('#count_technician').val()) || 0,
				surf_swiftwater: $('#course_type').val() === 'water' ? 
					(parseInt($('#count_surf_swiftwater').val()) || 0) : 0
			};

			const data = {
				action: 'save_course',
				nonce: lsimVars.nonce,
				instructor_id: $('#post_ID').val(),
				course_id: $('#course_id').val() || '',
				course_date: $('#course_date').val(),
				course_type: $('#course_type').val(),
				location: $('#course_location').val(), // Make sure this matches the ID
				participants_data: JSON.stringify(participants_data),
				assistants: []
			};

			// Collect and validate assistant data
			let hasIncompleteAssistant = false;
			$('.assistant-entry').each(function() {
				const first_name = $(this).find('input[name="assistant_first_name[]"]').val().trim();
				const last_name = $(this).find('input[name="assistant_last_name[]"]').val().trim();
				const email = $(this).find('input[name="assistant_email[]"]').val().trim();
				
				// If any field is filled, all fields must be filled
				if (first_name || last_name || email) {
					if (!first_name || !last_name || !email) {
						hasIncompleteAssistant = true;
						// Highlight incomplete fields
						$(this).find('input').each(function() {
							if (!$(this).val().trim()) {
								$(this).addClass('error');
							} else {
								$(this).removeClass('error');
							}
						});
						return false; // Break the each loop
					}
					
					data.assistants.push({
						first_name: first_name,
						last_name: last_name,
						email: email
					});
				}
			});

			if (hasIncompleteAssistant) {
				throw new Error('Please complete all assistant fields or remove incomplete assistants');
			}

			console.log('LSIM: Collected course data:', data);
			return data;
		}

		function saveCourseData() {
			const $button = $('#save-course');
			const originalText = $button.text();
			
			try {
				if (!validateCourseForm()) {
					return;
				}

				$button.prop('disabled', true).text('Saving...');
				
				const courseData = collectCourseData();
				
				$.ajax({
					url: lsimVars.ajaxurl,
					type: 'POST',
					data: courseData,
					beforeSend: function() {
						console.log('LSIM: Sending course save request...');
					}
				})
				.then(function(response) {
					console.log('LSIM: Course save response:', response);
					if (response.success) {
						location.reload();
					} else {
						throw new Error(response.data.message || 'Error saving course');
					}
				})
				.catch(function(error) {
					console.error('LSIM: Course save error:', error);
					alert(error.message || 'Error saving course');
				})
				.always(function() {
					$button.prop('disabled', false).text(originalText);
				});
			} catch (error) {
				alert(error.message);
				$button.prop('disabled', false).text(originalText);
			}
		}

        function loadCourseData(courseId) {
            console.log('LSIM: Loading course data for ID:', courseId);
            
            return $.ajax({
                url: lsimVars.ajaxurl,
                type: 'GET',
                data: {
                    action: 'get_course',
                    nonce: lsimVars.nonce,
                    course_id: courseId
                }
            })
            .then(function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Fill in course fields
                    $('#course_id').val(data.id);
                    $('#course_date').val(data.course_date);
                    $('#course_type').val(data.course_type).trigger('change');
                    $('#course_location').val(data.location);
                    
                    // Fill in participant counts
                    const counts = JSON.parse(data.participants_data);
                    Object.keys(counts).forEach(type => {
                        $(`#count_${type}`).val(counts[type]);
                    });
                    
                    // Clear and repopulate assistants
                    $('#assistant-list').empty();
                    if (data.assistants && data.assistants.length) {
                        data.assistants.forEach(assistant => {
                            const template = $('#assistant-template').html()
                                .replace(/\[INDEX\]/g, new Date().getTime());
                            const $entry = $(template);
                            $entry.find('input[name="assistant_first_name[]"]').val(assistant.first_name);
                            $entry.find('input[name="assistant_last_name[]"]').val(assistant.last_name);
                            $entry.find('input[name="assistant_email[]"]').val(assistant.email);
                            $('#assistant-list').append($entry);
                        });
                    }
                    
                    $('#cancel-edit').show();
                } else {
                    throw new Error(response.data?.message || 'Error loading course data');
                }
            })
            .catch(function(error) {
                console.error('LSIM: Course load error:', error);
                alert('Error loading course data: ' + error.message);
            });
        }

        function deleteCourse(courseId) {
            if (!confirm('Are you sure you want to delete this course?')) {
                return Promise.reject('Delete cancelled');
            }
            
            return $.ajax({
                url: lsimVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_course',
                    nonce: lsimVars.nonce,
                    course_id: courseId
                }
            })
            .then(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    throw new Error(response.data?.message || 'Error deleting course');
                }
            })
            .catch(function(error) {
                console.error('LSIM: Course delete error:', error);
                alert('Error deleting course: ' + error.message);
                return Promise.reject(error);
            });
        }

        // Initialize save buttons and form handlers
        initializeSaveButtons();
        initializeFormHandlers();

        // Event handlers for course management
        $('#save-course').on('click', function(e) {
            e.preventDefault();
            saveCourseData();
        });

        $('.add-assistant').on('click', function(e) {
            e.preventDefault();
            const template = $('#assistant-template').html();
            if (template) {
                $('#assistant-list').append(template);
            }
        });

        $(document).on('click', '.remove-assistant', function(e) {
            e.preventDefault();
            $(this).closest('.assistant-entry').remove();
        });

        $('#course_type').on('change', function() {
            const isWater = $(this).val() === 'water';
            $('.surf-swiftwater-count')[isWater ? 'show' : 'hide']();
            if (!isWater) {
                $('#count_surf_swiftwater').val(0);
            }
        }).trigger('change');

        $('#cancel-edit').on('click', function() {
            $('#course_id').val('');
            $('#course_date, #course_type, #course_location').val('');
            $('#assistant-list').empty();
            $('.certification-counts input').val(0);
            $(this).hide();
            $('.error-message').remove();
            $('.error').removeClass('error');
        });

        return {
            loadCourseData,
            deleteCourse,
            saveCourseData
        };
    }

    function initializeFormValidation() {
        $('form#post').on('submit', function(e) {
            e.preventDefault();
            return false;
        });
    }

    function initializeExportHandling() {
        $('#export-data').on('click', function(e) {
            e.preventDefault();
            window.location.href = `${lsimVars.ajaxurl}?action=export_data&nonce=${lsimVars.nonce}`;
        });
    }

    function initializeSubmissionHandling() {
        $('.dismiss-submission').on('click', function() {
            const $button = $(this);
            const submissionId = $button.data('submission-id');
            
            $.ajax({
                url: lsimVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'dismiss_submission',
                    nonce: lsimVars.nonce,
                    submission_id: submissionId
                },
                success: function(response) {
                    if (response.success) {
                        $button.closest('.submission-item').fadeOut();
                    } else {
                        alert(response.data?.message || 'Error dismissing submission');
                    }
                },
                error: function() {
                    alert('Error dismissing submission');
                }
            });
        });
    }

    function initializeSaveButtons() {
        // Add watchers for course form fields
        $('#course_date, #course_type, #course_location').on('change', function() {
            updateSaveButtonStatus();
        });

        function updateSaveButtonStatus() {
			const hasCourseData = $('#course_date').val() && $('#course_type').val() && $('#course_location').val();
			$('.save-notice').remove(); // Remove any existing notices
			
			if (hasCourseData) {
				$('#publish').after(`
					<div class="save-notice notice notice-info inline">
						<p>Course data detected. Both instructor and course data will be saved.</p>
					</div>
				`);
			}
		}

        // Modify the publish button click handler
        $('#publish').on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			if (!validateInstructorForm()) {
				return false;
			}

			const $button = $(this);
			const courseDate = $('#course_date').val();
			const courseType = $('#course_type').val();
			const courseLocation = $('#course_location').val();
			
			// Check if any course field is filled but not all
			if ((courseDate || courseType || courseLocation) && 
				!(courseDate && courseType && courseLocation)) {
				alert('Please either complete all required course fields or clear them before saving.');
				return false;
			}

			$button.prop('disabled', true).addClass('updating-message');
			
			const hasCourseData = courseDate && courseType && courseLocation;
            
            $.ajax({
                url: lsimVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_instructor',
                    nonce: lsimVars.nonce,
                    form_data: $('form#post').serialize()
                },
                success: function(response) {
                    if (response.success) {
                        if (hasCourseData) {
                            const courseData = collectCourseData();
                            courseData.instructor_id = response.data.instructor_id;
                            
                            $.ajax({
                                url: lsimVars.ajaxurl,
                                type: 'POST',
                                data: courseData,
                                success: function(courseResponse) {
                                    if (courseResponse.success) {
                                        window.location.href = `post.php?post=${response.data.instructor_id}&action=edit&message=1`;
                                    } else {
                                        alert('Instructor saved but course save failed: ' + 
                                            (courseResponse.data?.message || 'Unknown error'));
                                        window.location.href = `post.php?post=${response.data.instructor_id}&action=edit`;
                                    }
                                },
                                error: function() {
                                    alert('Instructor saved but course save failed');
                                    window.location.href = `post.php?post=${response.data.instructor_id}&action=edit`;
                                }
		});
                        } else {
                            window.location.href = `post.php?post=${response.data.instructor_id}&action=edit&message=1`;
                        }
                    } else {
                        $button.prop('disabled', false).removeClass('updating-message');
                        if (response.data?.type === 'duplicate_email') {
                            handleDuplicateEmail(response);
                        } else {
                            alert(response.data?.message || 'Error saving instructor');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    $button.prop('disabled', false).removeClass('updating-message');
                    alert('Error saving instructor: ' + error);
                }
            });
            
            return false;
        });
    }

    function initializeFormHandlers() {
        // Handle form input validation
        $('.required-field').on('blur', function() {
            validateField($(this));
        });

        // Handle email format validation
        $('input[type="email"]').on('blur', function() {
            validateEmailFormat($(this));
        });

        // Handle phone format validation
        $('input[name="phone"]').on('blur', function() {
            validatePhoneFormat($(this));
        });
    }

    function validateField($field) {
        const value = $field.val().trim();
        const $error = $field.next('.error-message');
        
        if (!value) {
            $field.addClass('error');
            if (!$error.length) {
                $field.after('<span class="error-message">This field is required</span>');
            }
            return false;
        } else {
            $field.removeClass('error');
            if ($error.length) {
                $error.remove();
            }
            return true;
        }
    }

    function validateEmailFormat($field) {
        const email = $field.val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const $error = $field.next('.error-message');

        if (email && !emailRegex.test(email)) {
            $field.addClass('error');
            if (!$error.length) {
                $field.after('<span class="error-message">Please enter a valid email address</span>');
            }
            return false;
        } else {
            if ($error.length) {
                $error.remove();
            }
            return true;
        }
    }

    function validatePhoneFormat($field) {
        const phone = $field.val().trim();
        const phoneRegex = /^\+?[\d\s-()]{10,}$/;
        const $error = $field.next('.error-message');

        if (phone && !phoneRegex.test(phone)) {
            $field.addClass('error');
            if (!$error.length) {
                $field.after('<span class="error-message">Please enter a valid phone number</span>');
            }
            return false;
        } else {
            if ($error.length) {
                $error.remove();
            }
            return true;
        }
    }

    function validateInstructorForm() {
        let isValid = true;
        let firstError = null;

        // Validate required fields
        $('.required-field').each(function() {
            if (!validateField($(this))) {
                isValid = false;
                if (!firstError) {
                    firstError = $(this);
                }
            }
        });

        // Validate email fields
        $('input[type="email"]').each(function() {
            if (!validateEmailFormat($(this))) {
                isValid = false;
                if (!firstError) {
                    firstError = $(this);
                }
            }
        });

        // Validate phone fields
        $('input[name="phone"]').each(function() {
            if (!validatePhoneFormat($(this))) {
                isValid = false;
                if (!firstError) {
                    firstError = $(this);
                }
            }
        });

        if (firstError) {
            firstError.focus();
        }

        return isValid;
    }

    function handleDuplicateEmail(response) {
        const message = response.data?.message || 'A user with this email already exists.';
        const existingId = response.data?.existing_id;
        
        if (existingId) {
            if (confirm(`${message}\nWould you like to view the existing instructor?`)) {
                window.location.href = `post.php?post=${existingId}&action=edit`;
            }
        } else {
            alert(message);
        }
    }
}); // End of jQuery ready						