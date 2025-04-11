document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".add-to-cart-btn");

    buttons.forEach(button => {
        button.addEventListener("click", function () {
            const movieId = this.dataset.id;

            const formData = new FormData();
            formData.append("movie_id", movieId);

            fetch("pages/ajax.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.total);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Erreur AJAX :", error);
            });
        });
    });

    function updateCartCount(count) {
        const counter = document.getElementById("cart-count");
        if (counter) {
            counter.textContent = count;
        }
    }
});
