//fonction js pour copier dans le presse-papier la commande
document.querySelectorAll('.copy-btn').forEach(button => {
    button.addEventListener('click', function() {
        const text = button.getAttribute('data-clipboard-text');
        
        // Utilisation de l'API Clipboard pour copier
        navigator.clipboard.writeText(text).then(() => {
            alert('Commande copiée : ' + text);
        }).catch(err => {
            alert('Échec de la copie : ' + err);
        });
    });
});