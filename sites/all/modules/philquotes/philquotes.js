var Philquotes = {};

if (Drupal.jsEnabled) {
    $(document).ready(
            function() {
                $("#philquotes-origin").after("<a>Next &raquo;</a>")
                        .next().click(Philquotes.randQuote);;
            }
    );

    /**
     * A function to fetch quotes from the server, and display in the
     * designated area.
     */
    Philquotes.randQuote = function() {
        $.get(Drupal.settings.philquotes.json_url, function(data) {
            myQuote = Drupal.parseJson(data);
            if (!myQuote.status || myQuote.status == 0) {
                $("#philquotes-origin").text(myQuote.quote.origin);
                $("#philquotes-text").text(myQuote.quote.text);
            }
        }); // End inline function
    }
}