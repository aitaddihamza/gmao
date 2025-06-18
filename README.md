# GMAO - Gestion des Équipements Biomédicaux

Une application web moderne développée avec Laravel et Filament pour la gestion complète des équipements biomédicaux, incluant la maintenance préventive, corrective et le suivi des interventions techniques.

## 📋 Description

Cette application GMAO (Gestion de Maintenance Assistée par Ordinateur) permet aux établissements de santé et aux services biomédicaux de :

- Gérer l'inventaire complet des équipements biomédicaux
- Planifier et suivre les maintenances préventives
- Enregistrer et tracer les interventions correctives
- Générer des rapports de maintenance et de performance
- Optimiser la disponibilité des équipements critiques

## ✨ Fonctionnalités principales

### 🔧 Gestion des équipements
- Inventaire détaillé des équipements biomédicaux
- Fiche technique complète (références, dates, garanties)
- Classification par service et criticité
- Historique complet des interventions

### 📅 Maintenance préventive
- Planification automatique des interventions
- Calendrier de maintenance personnalisable
- Notifications et alertes automatiques
- Modèles de protocoles de maintenance

### 🚨 Maintenance corrective
- Déclaration et suivi des pannes
- Gestion des demandes d'intervention
- Traçabilité des réparations
- Gestion des pièces détachées

### 📊 Reporting et analyses
- Tableaux de bord en temps réel
- Indicateurs de performance (MTBF, MTTR)
- Rapports de maintenance
- Analyses de coûts

### 👥 Gestion des utilisateurs
- Système de rôles et permissions
- Interface d'administration Filament
- Profils utilisateurs personnalisés

## 🛠️ Prérequis

Avant d'installer l'application, assurez-vous d'avoir :

- **PHP** >= 8.1
- **Composer** >= 2.0
- **Node.js** >= 16.x
- **NPM** ou **Yarn**
- **MySQL** >= 8.0 ou **PostgreSQL** >= 13
- **Git**

### Extensions PHP requises
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML

## 🚀 Installation

### 1. Cloner le projet

```bash
# Via Git
git clone [URL_DU_DEPOT]
cd gmao

# Ou télécharger le projet en .zip et l'extraire
```

### 2. Donner les permissions au script d'installation

```bash
chmod +x script.sh
```

### 3. Exécuter le script d'installation

```bash
./script.sh
```

### 4. Installer les dépendances

```bash
composer install
```

### 5. Configuration de l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans le fichier .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=gmao
# DB_USERNAME=votre_utilisateur
# DB_PASSWORD=votre_mot_de_passe
```

### 6. Migrations et données de test

```bash
# Exécuter les migrations
php artisan migrate

# (Optionnel) Charger les données de démonstration
php artisan db:seed
```

### 7. Lancer le serveur de développement

```bash
php artisan serve
```

### 8. Accéder à l'application

- **Application** : [http://localhost:8000](http://localhost:8000)
- **Panel d'administration** : [http://localhost:8000/admin](http://localhost:8000/admin)

## 🔐 Comptes par défaut

Après l'installation avec les données de démonstration :

- **Administrateur** : admin@example.com / password
- **Technicien** : technicien@example.com / password

## 📱 Utilisation

### Interface d'administration (Filament)

L'interface d'administration permet de :

1. **Gérer les équipements** : Ajouter, modifier, supprimer des équipements
2. **Planifier la maintenance** : Créer des calendriers de maintenance préventive
3. **Traiter les interventions** : Gérer les demandes et interventions correctives
4. **Consulter les rapports** : Analyser les performances et coûts
5. **Administrer les utilisateurs** : Gérer les comptes et permissions

### Workflow type

1. **Enregistrement d'équipement** → Ajout dans l'inventaire
2. **Planification maintenance** → Création du calendrier préventif
3. **Intervention** → Exécution et enregistrement
4. **Suivi** → Analyse des performances et reporting

## 🏗️ Architecture

### Stack Technique

```
Frontend:
├── Filament 3.3 (Interface d'administration)
├── Tailwind CSS (Framework CSS)
├── Alpine.js (Interactions JavaScript)
└── Vite (Build tool)

Backend:
├── Laravel 11.x (Framework PHP)
├── PHP 8.2+
├── Eloquent ORM (Base de données)
└── Queue System (Tâches asynchrones)

Services:
├── AIService (Intelligence Artificielle - Prism/OpenAI)
├── ReportService (Génération de rapports)
└── NotificationService (Alertes système)

Base de données:
├── MySQL 8.0+ / PostgreSQL 13+
├── Migrations (Structure)
└── Seeders (Données de test)
```

### Modèle de Données

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│     User        │    │   Equipement     │    │     Ticket      │
├─────────────────┤    ├──────────────────┤    ├─────────────────┤
│ id              │    │ id               │    │ id              │
│ name            │◄──┤│ designation      │◄──┤│ titre           │
│ email           │    │ marque           │    │ description     │
│ role            │    │ modele           │    │ priorite        │
└─────────────────┘    │ numero_serie     │    │ statut          │
                       │ date_acquisition │    │ date_creation   │
                       │ etat             │    └─────────────────┘
                       └──────────────────┘
                              │
                              ▼
                    ┌──────────────────┐    ┌─────────────────┐
                    │      Bloc        │    │MaintenancePreventive│
                    ├──────────────────┤    ├─────────────────┤
                    │ id               │    │ id              │
                    │ nom              │    │ frequence       │
                    │ localisation     │    │ prochaine_date  │
                    │ responsable      │    │ duree_estimee   │
                    └──────────────────┘    └─────────────────┘
```

### Architecture Filament

```
app/Filament/
├── Resources/           # Ressources utilisateur
│   └── UserResource.php
├── SharedResources/     # Ressources partagées
│   ├── Equipement/
│   ├── Ticket/
│   ├── MaintenancePreventive/
│   ├── MaintenanceCorrective/
│   ├── Bloc/
│   ├── Piece/
│   ├── TypeEquipement/
│   └── TypeBloc/
├── Widgets/            # Tableaux de bord
├── Pages/              # Pages personnalisées
└── SharedPages/        # Pages partagées
```

### Services Métier

#### 🤖 AIService
- **Fonction** : Génération de recommandations intelligentes
- **Provider** : OpenAI/Gemma via Prism
- **Usage** : Analyse des pannes et suggestions de réparation

#### 📊 ReportService  
- **Fonction** : Génération de rapports automatisés
- **Formats** : PDF (DomPDF), Word (PHPWord)
- **Types** : Rapports de maintenance, bilans de performance

### Flux de Données

```
┌─── Interface Filament ───┐
│                          │
│  ┌─── Resources ───┐     │
│  │ CRUD Operations │     │
│  └─────────────────┘     │
│           │               │
│           ▼               │
│  ┌─── Models (Eloquent) ─┐│
│  │ Business Logic       ││
│  └──────────────────────┘│
│           │               │
│           ▼               │
│  ┌─── Services ──────────┐│
│  │ AIService           ││
│  │ ReportService       ││
│  │ NotificationService ││
│  └─────────────────────┘│
│           │               │
│           ▼               │
│  ┌─── Database ──────────┐│
│  │ MySQL/PostgreSQL     ││
│  └──────────────────────┘│
└──────────────────────────┘
```

### Patterns de Conception

- **MVC** : Séparation des responsabilités
- **Repository Pattern** : Via Eloquent ORM  
- **Service Layer** : Logique métier centralisée
- **Observer Pattern** : Événements Eloquent
- **Factory Pattern** : Création d'objets de test
- **Strategy Pattern** : Services interchangeables

## 🖼️ Screenshots

*Section à compléter avec les captures d'écran de l'application*

<!-- 
Exemples de screenshots à ajouter :
- Dashboard principal
- Liste des équipements
- Formulaire d'ajout d'équipement
- Calendrier de maintenance
- Rapport de maintenance
-->

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 📞 Support

Pour toute question ou problème :

- Ouvrir une issue sur GitHub
- Consulter la documentation technique
- Contacter l'équipe de développement

---

**Version** : 1.0.0  
**Dernière mise à jour** : 2024

*Développé avec ❤️ en utilisant Laravel et Filament*