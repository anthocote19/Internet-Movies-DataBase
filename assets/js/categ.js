document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.ajout-panier'); 

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
                    const cartCountSpan = document.getElementById('cart-count');
                    if (cartCountSpan && data.total !== undefined) {
                        cartCountSpan.textContent = data.total;
                    }

                    
                    if (confirm(`${data.message} (Total dans le panier : ${data.total})\n\nSouhaitez-vous voir votre panier ? Cliquez sur OK pour voir votre panier !`)) {
                        window.location.href = 'cart.php';
                    }
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(error => {
                alert("Erreur lors de l'ajout : " + error);
            });
        });
    });

   
    const toggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (toggle && navLinks) {
        toggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }
});
