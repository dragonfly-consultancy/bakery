<html>
<head>
<style>
button:not(.close),
.btn,
button[type="button"]:not(.close),
button[type="submit"]:not(.close),
input[type="button"],
input[type="submit"],
input[type="reset"],
a.btn,
[class*="btn-"] {
    background: var(--accent-soft, #f6ece0) !important;
    color: var(--ink, #2b2218) !important;
    font-weight: 500 !important;
    border-color: var(--accent-soft, #f6ece0) !important;
}

button:not(.close):hover,
.btn:hover,
button:not(.close):focus,
.btn:focus,
input[type="button"]:hover,
input[type="submit"]:hover,
input[type="button"]:focus,
input[type="submit"]:focus,
a.btn:hover,
a.btn:focus,
[class*="btn-"]:hover,
[class*="btn-"]:focus {
    background: var(--accent-soft, #f6ece0) !important;
    color: var(--ink, #2b2218) !important;
    border-color: var(--accent-soft, #f6ece0) !important;
    opacity: 0.9;
}
</style>
</head>
<body style="background:#faf6f0;">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<label>Department Name</label></br>

<div class="input-group">
                                                          <span class="input-group-btn">
                                                            <button type="button" class="btn btn-info">Barcode</button>
                                                          </span>
                                                        <input type="text" name="barcode" id="department_name" class="form-control barcode" placeholder="Scan Product Barcode">
                                                     <textarea class="crazytext"></textarea>
                                                    </div>
                                                    <div id="result"></div>
<script type="text/javascript">
$(function() {
    var availableTags = <?php include('search.php'); ?>;
    $("#department_name").autocomplete({
        source: availableTags,
        autoFocus:true
    });
});
</script>

<script>
$(document).ready(function () {
    $('.barcode').keydown(function (event) {
        if (event.keyCode == 13) {
        	event.preventDefault();
            $.ajax({
                url: 'search.php',
                type: 'POST',
                data: {
                    html: $(this).val()
                },
                success: function(result) {
                    $("#result").text(result);
                }
            });
        }
    });
});
</script>

</body>
</html>



