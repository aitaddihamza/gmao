document.addEventListener('DOMContentLoaded', function() {
    // Créer l'élément audio une seule fois
    const notificationSound = document.createElement('audio');
    notificationSound.src = '/sounds/notification.mp3';
    notificationSound.preload = 'auto';
    document.body.appendChild(notificationSound);

    // Observer les changements dans la liste des notifications
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Vérifier si une nouvelle notification a été ajoutée
                const newNotifications = Array.from(mutation.addedNodes).filter(node =>
                    node.nodeType === Node.ELEMENT_NODE &&
                    node.classList.contains('fi-notification')
                );

                if (newNotifications.length > 0) {
                    // Jouer le son
                    notificationSound.play();
                }
            }
        });
    });

    // Observer le conteneur de notifications avec une configuration adaptée
    const config = { childList: true, subtree: true };

    // Attendre un peu pour s'assurer que le conteneur de notifications est chargé
    setTimeout(() => {
        const notificationsContainer = document.querySelector('.fi-notifications');
        if (notificationsContainer) {
            observer.observe(notificationsContainer, config);
        }
    }, 1000);
});
