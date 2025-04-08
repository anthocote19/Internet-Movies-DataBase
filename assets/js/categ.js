document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.add-to-cart');

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const movieId = this.getAttribute('data-id');

            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `movie_id=${movieId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message + " (Total dans le panier : " + data.total + ")");
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(error => {
                alert("Erreur lors de l'ajout : " + error);
            });
        });
    });
});
