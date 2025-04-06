function addToCart(movieId) {
    $.post('ajax.php', { movie_id: movieId }, function(response) {
        if (response.success) {
            alert(response.message + " (Total articles : " + response.total + ")");
        } else {
            alert("Erreur : " + response.message);
        }
    }, 'json');
}
