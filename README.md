# MedShakeST
Aide à l'analyse des effectifs en service interentreprises pour l'équipe santé travail.

## Démonstration
L'application est en production sur https://st.medshake.net/

## Utilisation via Docker

Fichiers dockerfile et docker-compose en racine ne devraient pas poser de problème. Par défaut toutes les données présentes dans /data_sql sont chargées en base. 

## Utilisation sur serveur LAMP

### Environnement de production nécessaire
- Apache
- PHP 8 ou supérieur
- MariaDB

### Mise en production 
- Cloner le repository
- Executer composer update en racine et dans /public_html
- Créer une base de données dédiée à l'aide du dump /data_sql/base.sql
- Peupler la base avec les données AT/MP/A de trajet dans /data_sql (sources : https://assurance-maladie.ameli.fr/etudes-et-donnees )
- Renommer /config/config.exemple.yml en config.yml et ajuster les paramètres
