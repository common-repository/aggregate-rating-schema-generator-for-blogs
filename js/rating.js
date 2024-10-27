(function($) {
    $(document).ready(function() {
        // Retrieve post ID from the container
        var postID = $('.strpgn-rating-container').data('post-id');

        // Click event for star rating
        $('.strpgn-stars .strpgn-star').on('click', function() {
            var ratingValue = $(this).data('value');

            $.ajax({
                url: strpgn_ajax_object.ajax_url, // Updated to 'strpgn_ajax_object'
                type: 'POST',
                data: {
                    action: 'save_star_rating', // Ensure this matches the PHP action
                    post_id: postID,
                    rating: ratingValue,
                    nonce: strpgn_ajax_object.nonce // Updated to match localized object
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('THANK YOU FOR RATING!! 😊', 'success');
                        updateAverageRating(postID);
                        triggerConfetti(); // Trigger confetti only on successful rating
                        $('#thank-you-message').fadeIn().delay(3000).fadeOut(); // Show thank you message
                        $('.strpgn-star-rating').fadeOut(); // Hide the rating stars after rating
                    } else {
                        if (response.data === 'You have already rated this post 🙂') {
                            showMessage('You have already rated this post 🙂', 'error');
                        } else {
                            showMessage('An error occurred. Please try again later.', 'error');
                        }
                        console.error('Error saving rating:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    showMessage('An error occurred. Please try again later.', 'error');
                    console.error('AJAX Error:', status, error);
                }
            });
        });

        // Function to show messages
        function showMessage(message, type) {
            var $messageContainer = $('#strpgn-message-container');
            if ($messageContainer.length === 0) {
                $('.strpgn-rating-container').prepend('<div id="strpgn-message-container"></div>');
                $messageContainer = $('#strpgn-message-container');
            }

            var messageClass = type === 'success' ? 'strpgn-success-message' : 'strpgn-error-message';
            $messageContainer.html('<div class="' + messageClass + '">' + message + '</div>');
            $messageContainer.show();

            setTimeout(function() {
                $messageContainer.fadeOut();
            }, 3000);
        }

        // Function to trigger confetti
        function triggerConfetti() {
            if (typeof confetti === 'function') { // Check if confetti is loaded
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 },
                    disableForReducedMotion: true
                });
            } else {
                console.error('Confetti library is not loaded');
            }
        }

        // Hover effect to change star colors
        $('.strpgn-stars .strpgn-star').hover(
            function() {
                var hoverValue = $(this).data('value');
                $('.strpgn-stars .strpgn-star').each(function(index) {
                    if (index < hoverValue) {
                        $(this).css('color', '#ffed25');
                    } else {
                        $(this).css('color', '#9d9d9d');
                    }
                });
            },
            function() {
                var averageRating = parseFloat($('.strpgn-average-number').text());
                $('.strpgn-stars .strpgn-star').each(function(index) {
                    if (index < averageRating) {
                        $(this).css('color', '#26f553');
                    } else {
                        $(this).css('color', '#9d9d9d');
                    }
                });
            }
        );

        // Update average rating display
        function updateAverageRating(postID) {
            $.ajax({
                url: strpgn_ajax_object.ajax_url, // Updated to 'strpgn_ajax_object'
                type: 'POST',
                data: {
                    action: 'get_average_rating', // Ensure this matches the PHP action
                    post_id: postID,
                    nonce: strpgn_ajax_object.nonce // Added nonce for security
                },
                success: function(response) {
                    if (response.success) {
                        $('.strpgn-average-number').text(response.data.average_rating);
                        // Update star colors based on new average rating
                        $('.strpgn-stars .strpgn-star').each(function(index) {
                            if (index < response.data.average_rating) {
                                $(this).addClass('strpgn-star-filled').removeClass('strpgn-star-empty').css('color', '#26f553');
                            } else {
                                $(this).removeClass('strpgn-star-filled').addClass('strpgn-star-empty').css('color', '#9d9d9d');
                            }
                        });
                    } else {
                        console.error('Error fetching average rating:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        }

        // Initialize average rating display
        if (postID) {
            updateAverageRating(postID);
        }
    });
})(jQuery);

// Ensure confetti library is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof confetti === 'undefined') {
        console.error('Confetti library is not loaded');
    }
});
