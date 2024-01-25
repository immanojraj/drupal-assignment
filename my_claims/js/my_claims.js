(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.myClaimsTabs = {
        attach: function (context, settings) {
        // Attach click event handler for Tab 1.
            $('#edit-tab-1').once('my-claims-tab-1').on('click', function () {
            // Handle the click event for Tab 1.
                alert('Tab 1 clicked!');
            // You can add additional JavaScript logic here.
            });
            // Attach click event handler for Tab 2.
            $('#tab-2').once('my-claims-tab-2').on('click', function () {
            // Handle the click event for Tab 2.
                alert('Tab 2 clicked!');
            // You can add additional JavaScript logic here.
            });
        }
    };
})(jQuery, Drupal, drupalSettings);
