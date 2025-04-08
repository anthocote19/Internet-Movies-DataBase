document.addEventListener("DOMContentLoaded", function () {

    document.querySelector('.menu-toggle').addEventListener('click', () => {
        document.querySelector('.nav-links').classList.toggle('menu-active');
    });

    $(".add-to-cart").click(function () {
        var movieId = $(this).data("id");

        $.ajax({
            url: "pages/ajax.php",
            type: "POST",
            data: { movie_id: movieId },
            dataType: "json",
            success: function (response) {
                const $message = $("#cart-message");
                $message.removeClass("success error");
            
                if (response.success) {
                    $message
                        .addClass("success")
                        .text(response.message)
                        .fadeIn()
                        .delay(1000)
                        .fadeOut();
            
                    if (response.total !== undefined) {
                        $("#cart-count").text(response.total);
                    }
                } else {
                    $message
                        .addClass("error")
                        .html(response.message)
                        .fadeIn()
                        .delay(1000)
                        .fadeOut();
                }
            },
            
            error: function () {
                const $message = $("#cart-message");
                $message
                    .removeClass("success")
                    .addClass("error")
                    .text("Erreur lors de l'ajout au panier.")
                    .fadeIn()
                    .delay(2000)
                    .fadeOut();
            }
        });
    });

    $(".user-initials").on("click", function (e) {
        e.stopPropagation();
        $(this).siblings(".dropdown-menu").slideToggle();
    });

    $(document).on("click", function () {
        $(".dropdown-menu").slideUp();
    });

    $.get('pages/get_cart_count.php', function (response) {
        if (response.total !== undefined) {
            $("#cart-count").text(response.total);
        }
    }, 'json');
});
