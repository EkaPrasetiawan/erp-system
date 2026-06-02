$(document).ready(function () {
  $("#loginForm").submit(function (e) {
    e.preventDefault();

    $.ajax({
      url: "assets/modul.php",
      type: "POST",
      data: $(this).serialize() + "&login=true",
      dataType: "json",
      success: function (response) {
        console.log("AJAX Success", response);
        if (response.status === "success") {
          window.location.href = response.redirect;
        } else {
          $("#loginAlert").text(response.message);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", xhr.responseText);
        $("#loginAlert").text("Terjadi kesalahan. Silakan coba lagi.");
      },
    });
  });
});
